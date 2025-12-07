<?php

namespace App\Http\Controllers\Backend\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Template;
use App\Models\TemplateInputFields;
use App\Models\GeneratedContent;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class UserTemplateController extends Controller
{

    // Method untuk filter template berdasarkan role user
    public function UserTemplate(Request $request)
    {
        $user = Auth::user();
        $userRole = ucfirst($user->role); // 'Student' or 'Lecturer'

        // Start query based on user role
        $query = Template::query();
        
        // If admin, allow category filter, otherwise filter by user role
        if ($user->role === 'admin') {
            // Admin can see all or filter by category
            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', ucfirst($request->category));
            }
        } else {
            // Non-admin users only see templates for their role
            $query->where('category', $userRole);
        }
        
        // Search filter (title or description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Sort filter - FIX: Use strict comparison and proper order
        $sort = $request->get('sort', 'newest');
        
        if ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'title-desc') {
            $query->orderBy('title', 'desc');
        } else {
            // Default: newest first
            $query->orderBy('created_at', 'desc');
        }
        
        // Paginate results
        $templates = $query->paginate(5)->withQueryString();

        return view('client.backend.template.all_template', compact('user', 'templates'));
    }

    public function UserDetailsTemplate($id){
        $template = Template::with('inputFields')->findOrFail($id);
        $user = Auth::user();
        return view('client.backend.template.details_template',compact('template','user')); 

    }

    public function UserContentGenerate(Request $request, $id)
    {
        // Get authenticated user once and cache it
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Validate static inputs first (fastest validation)
        $validatedData = $request->validate([
            'language' => 'required|string|in:English,Bahasa Melayu',
            'ai_model' => 'required|string|in:gpt-4,gpt-3.5-turbo',
            'result_length' => 'required|integer|min:1|max:1000',
        ]);

        // Early word limit check to avoid unnecessary processing
        $estimatedWordCount = $validatedData['result_length'];
        if ($user->current_word_usage !== null && ($user->words_used + $estimatedWordCount) > $user->current_word_usage) {
            return response()->json([
                'success' => false,
                'message' => 'Word limit exceeded',
            ], 400);
        }

        // Fetch template with input fields (optimized query with select)
        $template = Template::with(['inputFields' => function ($query) {
            $query->select('id', 'template_id', 'title');
        }])
        ->select('id', 'prompt')
        ->findOrFail($id);

        // Build dynamic validation rules efficiently
        $dynamicRules = [];
        $fieldNames = [];
        foreach ($template->inputFields as $field) {
            $fieldName = str_replace(' ', '_', $field->title);
            $dynamicRules[$fieldName] = 'required|string|max:1000';
            $fieldNames[] = $fieldName;
        }

        // Single validation call for all dynamic fields
        $request->validate($dynamicRules);

        // Get only the dynamic input data we need
        $inputData = $request->only($fieldNames);

        // Build prompt more efficiently using strtr for multiple replacements
        $replacements = [];
        foreach ($inputData as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
            $replacements['{' . str_replace('_', ' ', $key) . '}'] = $value;
        }
        $replacements['{result_length}'] = $validatedData['result_length'];

        // Single string replacement operation
        $prompt = strtr($template->prompt, $replacements);

        // Enhanced language-specific prompt generation
        $messages = $this->buildLanguageSpecificMessages($validatedData, $prompt);

        try {
            // OpenAI API call with optimized parameters
            $response = OpenAI::chat()->create([
                'model' => $validatedData['ai_model'],
                'messages' => $messages,
                'max_tokens' => min(4000, $validatedData['result_length'] * 2),
                'temperature' => 0.7,
            ]);

            $output = $response->choices[0]->message->content;
            
            // Language validation check
            $languageCheck = $this->validateOutputLanguage($output, $validatedData['language']);
            if (!$languageCheck['valid']) {
                Log::warning('Generated content language mismatch', [
                    'user_id' => $user->id,
                    'requested_language' => $validatedData['language'],
                    'confidence' => $languageCheck['confidence']
                ]);
            }

            $wordCount = str_word_count($output);

            // Optimized database transaction
            DB::transaction(function () use ($user, $template, $inputData, $output, $wordCount) {
                User::where('id', $user->id)->increment('words_used', $wordCount);

                GeneratedContent::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'input' => json_encode($inputData, JSON_UNESCAPED_UNICODE),
                    'output' => $output,
                    'word_count' => $wordCount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'output' => $output,
                'language_confidence' => $languageCheck['confidence'] ?? null
            ]);

        } catch (\OpenAI\Exceptions\ErrorException $e) {
            Log::error('OpenAI API error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'template_id' => $id,
                'model' => $validatedData['ai_model']
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'AI service temporarily unavailable. Please try again.',
            ], 503);
            
        } catch (\Exception $e) {
            Log::error('Content generation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'template_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.',
            ], 500);
        }
    }

    /**
     * Build language-specific messages for OpenAI API
     */
    private function buildLanguageSpecificMessages($validatedData, $prompt)
    {
        $messages = [];
        $isMalay = $validatedData['language'] === 'Bahasa Melayu';
        $isGPT4 = $validatedData['ai_model'] === 'gpt-4';

        // Add system message for better language control
        if ($isMalay) {
            $systemMessage = $isGPT4 
                ? 'Anda adalah pembantu AI yang MESTI menjawab dalam Bahasa Melayu sahaja. Jangan sekali-kali menggunakan bahasa Inggeris dalam jawapan anda.'
                : 'WAJIB: Anda MESTI menulis dalam Bahasa Melayu SAHAJA. Jangan campur atau guna bahasa Inggeris langsung. Ini sangat penting.';
                
            $messages[] = ['role' => 'system', 'content' => $systemMessage];
        }

        // Build user prompt with strong language instructions
        if ($isMalay) {
            $lengthInstruction = $isGPT4 
                ? "Sasaran kira-kira {$validatedData['result_length']} patah perkataan dalam Bahasa Melayu."
                : "PENTING: Tulis tepat {$validatedData['result_length']} patah perkataan dalam Bahasa Melayu sahaja.";
                
            $finalPrompt = "ARAHAN UTAMA: Jawab dalam Bahasa Melayu sahaja. {$prompt} {$lengthInstruction}";
        } else {
            $finalPrompt = "INSTRUCTION: Respond in English only. {$prompt} Aim for approximately {$validatedData['result_length']} words.";
        }

        $messages[] = ['role' => 'user', 'content' => $finalPrompt];

        return $messages;
    }

    /**
     * Validate if the output matches the requested language
     */
    private function validateOutputLanguage($output, $requestedLanguage)
    {
        if ($requestedLanguage === 'English') {
            return ['valid' => true, 'confidence' => 100]; // Skip validation for English
        }

        // Simple Malay language detection
        $malayWords = ['dan', 'atau', 'ini', 'itu', 'dengan', 'untuk', 'dari', 'ke', 'pada', 'di', 'yang', 'adalah', 'akan', 'telah', 'boleh', 'tidak', 'dalam', 'kepada', 'sebagai', 'juga'];
        $englishWords = ['the', 'and', 'or', 'this', 'that', 'with', 'for', 'from', 'to', 'in', 'of', 'is', 'are', 'was', 'were', 'will', 'would', 'can', 'could', 'not'];

        $outputLower = strtolower($output);
        $totalWords = str_word_count($outputLower);
        
        if ($totalWords === 0) {
            return ['valid' => false, 'confidence' => 0];
        }

        $malayCount = 0;
        $englishCount = 0;

        foreach ($malayWords as $word) {
            $malayCount += substr_count($outputLower, ' ' . $word . ' ') + 
                        (strpos($outputLower, $word . ' ') === 0 ? 1 : 0) +
                        (substr($outputLower, -strlen(' ' . $word)) === ' ' . $word ? 1 : 0);
        }

        foreach ($englishWords as $word) {
            $englishCount += substr_count($outputLower, ' ' . $word . ' ') + 
                            (strpos($outputLower, $word . ' ') === 0 ? 1 : 0) +
                            (substr($outputLower, -strlen(' ' . $word)) === ' ' . $word ? 1 : 0);
        }

        $malayConfidence = ($malayCount / $totalWords) * 100;
        $englishConfidence = ($englishCount / $totalWords) * 100;

        // Consider it valid Malay if Malay confidence > English confidence and Malay confidence > 10%
        $isValidMalay = $malayConfidence > $englishConfidence && $malayConfidence > 10;

        return [
            'valid' => $isValidMalay,
            'confidence' => round($malayConfidence, 2),
            'malay_words' => $malayCount,
            'english_words' => $englishCount
        ];
    }

    public function UserDocument(Request $request){
        $id = Auth::user()->id;
        $query = GeneratedContent::where('user_id', $id);

        if($request->sort == 'newest') {
            $query->orderBy('id', 'desc');
        } elseif($request->sort == 'oldest') {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc'); // default
        }

        $document = $query->paginate(10);
        return view('client.backend.document.all_document', compact('document'));
    }

    public function EditUserDocument($id){
        $document = GeneratedContent::findOrFail($id);
        return view('client.backend.document.edit_document',compact('document')); 
    }

    public function UserUpdateDocument(Request $request, $id){
        $document = GeneratedContent::findOrFail($id);

        $validateData = $request->validate([
            'output' => 'required|string',
        ]);

        $document->update([
            'output' => $validateData['output'],
        ]);

        $notification = array(
        'message' => 'Document Updated Successfully',
        'alert-type' => 'success'
     );

     return redirect()->route('user.document')->with($notification); 
    }

    public function DeleteUserDocument($id){

        GeneratedContent::find($id)->delete();
        $notification = array(
        'message' => 'Document Deleted Successfully',
        'alert-type' => 'success'
     );

     return redirect()->back()->with($notification); 
     }

}