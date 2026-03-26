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
        
        // Apply the filter to show ONLY active templates (is_active = 1)
        $query->where('is_active', 1);
        
        // If admin, allow category filter, otherwise filter by user role
        if ($user->role === 'admin') {
            // Admin can see all active templates or filter by category
            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', ucfirst($request->category));
            }
        } else {
            // Non-admin users only see active templates for their role
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
        
        // Sort filter = Added 'oldest' option
        $sort = $request->get('sort', 'newest');
        
        if ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'title-desc') {
            $query->orderBy('title', 'desc');
        } elseif ($sort === 'oldest') {
            // 🆕 NEW: Oldest first
            $query->orderBy('created_at', 'asc');
        } else {
            // Default: newest first
            $query->orderBy('created_at', 'desc');
        }
        
        // Paginate results
        $templates = $query->paginate(10)->withQueryString();

        return view('client.backend.template.all_template', compact('user', 'templates'));
    }

    public function UserDetailsTemplate($id){
        $template = Template::with('inputFields')->findOrFail($id);
        $user = Auth::user();
        return view('client.backend.template.details_template',compact('template','user')); 

    }

    public function UserContentGenerate(Request $request, $id)
    {
        // Step 1: Authenticate user
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Validate selected language and AI model to prevent invalid or unauthorized input. = Generator1
        $validatedData = $request->validate([
            'language' => 'required|string|in:English,Bahasa Melayu',
            'ai_model' => 'required|string|in:gpt-4,gpt-3.5-turbo',
        ]);

        // Step 2: Fetch template and validate dynamic input fields 
        // Retrieve selected template with dynamic input fields and predefined AI prompt structure (AI prompt already written or set by admin/superadmin) = Generator2
        $template = Template::with(['inputFields' => function ($query) {
            $query->select('id', 'template_id', 'title');
        }])
        ->select('id', 'prompt')
        ->findOrFail($id);

        // Generate validation rules dynamically based on template-defined input fields (no hardcoding). = Generator3
        // The system automatically shows input fields based on the chosen template, no hardcoding needed.
        $dynamicRules = [];
        $fieldNames = [];
        foreach ($template->inputFields as $field) {
            $fieldName = str_replace(' ', '_', $field->title);
            $dynamicRules[$fieldName] = 'required|string|max:1000';
            $fieldNames[] = $fieldName;
        }

        // Dynamic validation for input fields defined in the template
        $request->validate($dynamicRules);
        $inputData = $request->only($fieldNames);

        // Step 3: Build AI prompt with input replacements
        // Construct dynamic AI prompt by replacing template placeholders with validated user inputs = Generator5
        $replacements = [];
        foreach ($inputData as $key => $value) {
            $replacements['{' . $key . '}'] = $value;
            $replacements['{' . str_replace('_', ' ', $key) . '}'] = $value;
        }
        $prompt = strtr($template->prompt, $replacements);

        // Build messages without length constraint
        $messages = $this->buildLanguageSpecificMessages($validatedData, $prompt);

        // Step 4: Send request to OpenAI API
        try {
            // Determine appropriate max_tokens based on model
            $maxTokens = $validatedData['ai_model'] === 'gpt-4' ? 4096 : 3000;
            
            // Send structured prompt to selected OpenAI model for AI-based content generation = Generator6
            $response = OpenAI::chat()->create([
                'model' => $validatedData['ai_model'],
                'messages' => $messages,
                'max_tokens' => $maxTokens, // Allow full response
                'temperature' => 0.7,
            ]);

            $output = $response->choices[0]->message->content;

            // Perform post-generation language validation to ensure output quality and compliance = Generator7
            $languageCheck = $this->validateOutputLanguage($output, $validatedData['language']);
            if (!$languageCheck['valid']) {
                Log::warning('Generated content language mismatch', [
                    'user_id' => $user->id,
                    'requested_language' => $validatedData['language'],
                    'confidence' => $languageCheck['confidence']
                ]);
            }

            // Count words in generated content
            $wordCount = str_word_count($output);

            // Step 5: Save generated content and update user usage
            DB::transaction(function () use ($user, $template, $inputData, $output, $wordCount) {
                User::where('id', $user->id)->increment('words_used', $wordCount);

                // Store generated content within a database = Generator8
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

            // Return structured JSON response to frontend for dynamic content rendering. = Generator9
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
     * Build language-specific messages for OpenAI API (UPDATED - No length constraint)
     */
    private function buildLanguageSpecificMessages($validatedData, $prompt)
    {
        $messages = [];
        $isMalay = $validatedData['language'] === 'Bahasa Melayu';
        $isGPT4 = $validatedData['ai_model'] === 'gpt-4';

        // Add system message for better language control
        if ($isMalay) {
            $systemMessage = $isGPT4 
                ? 'Anda adalah pembantu AI yang MESTI menjawab dalam Bahasa Melayu sahaja. Jangan sekali-kali menggunakan bahasa Inggeris dalam jawapan anda. Berikan jawapan yang lengkap dan komprehensif.'
                : 'WAJIB: Anda MESTI menulis dalam Bahasa Melayu SAHAJA. Jangan campur atau guna bahasa Inggeris langsung. Berikan jawapan yang lengkap dan terperinci.';
                
            $messages[] = ['role' => 'system', 'content' => $systemMessage];
        } else {
            $systemMessage = 'You are a helpful AI assistant. Provide complete, comprehensive, and well-structured responses in English.';
            $messages[] = ['role' => 'system', 'content' => $systemMessage];
        }

        // Build user prompt WITHOUT length instructions
        if ($isMalay) {
            $finalPrompt = "ARAHAN UTAMA: Jawab dalam Bahasa Melayu sahaja dengan lengkap dan terperinci. {$prompt}";
        } else {
            $finalPrompt = "INSTRUCTION: Respond in English only with complete and comprehensive information. {$prompt}";
        }

        $messages[] = ['role' => 'user', 'content' => $finalPrompt];

        return $messages;
    }

    /**
     * Validate if the output matches the requested language (UNCHANGED)
     */
    private function validateOutputLanguage($output, $requestedLanguage)
    {
        if ($requestedLanguage === 'English') {
            return ['valid' => true, 'confidence' => 100];
        }

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

    /**
     * AI-powered suggestion for textarea inputs (BLOOM'S TAXONOMY FRAMEWORK)
     */
    public function AISuggestion(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Validates user input to ensure data integrity and prevent invalid data from reaching the AI API = Suggestion3
        $validated = $request->validate([
            'field_name' => 'required|string',
            'current_input' => 'required|string|min:3|max:500',
            'language' => 'required|string|in:English,Bahasa Melayu',
            'template_context' => 'nullable|string|max:200'
        ]);

        // The system dynamically constructs a structured prompt instructing the AI to generate six suggestions aligned with Bloom’s Taxonomy cognitive levels = Suggestion4
        try {
            // Build ACADEMIC-FOCUSED context-aware prompt with Bloom's Taxonomy
            $isMalay = $validated['language'] === 'Bahasa Melayu';
            $userRole = ucfirst($user->role);
            
            if ($isMalay) {
                $systemPrompt = 'Anda adalah pakar akademik AI yang membantu pelajar dan pensyarah dengan cadangan penulisan akademik berkualiti tinggi dalam Bahasa Melayu menggunakan kerangka Taksonomi Bloom. Cadangan anda mestilah profesional, terstruktur, dan sesuai untuk konteks akademik.';
                
                $userPrompt = "Pengguna ({$userRole}): \"{$validated['current_input']}\"\n\n";
                $userPrompt .= "Hasilkan 6 cadangan akademik yang spesifik dan profesional (15-25 patah perkataan setiap satu) berdasarkan 6 tahap Taksonomi Bloom secara berurutan:\n\n";
                $userPrompt .= "TAHAP 1 - INGAT (REMEMBER): Cadangan yang memfokuskan kepada pengulangan fakta, definisi, atau pengecaman konsep asas.\n";
                $userPrompt .= "TAHAP 2 - FAHAM (UNDERSTAND): Cadangan yang memfokuskan kepada penjelasan maksud, interpretasi, atau rumusan idea.\n";
                $userPrompt .= "TAHAP 3 - APLIKASI (APPLY): Cadangan yang memfokuskan kepada penggunaan teori dalam konteks praktikal atau penyelesaian masalah.\n";
                $userPrompt .= "TAHAP 4 - ANALISIS (ANALYZE): Cadangan yang memfokuskan kepada pemeriksaan mendalam, perbandingan, atau penguraian hubungan antara elemen.\n";
                $userPrompt .= "TAHAP 5 - NILAI (EVALUATE): Cadangan yang memfokuskan kepada penilaian kritikal, justifikasi keputusan, atau pertimbangan kekuatan dan kelemahan.\n";
                $userPrompt .= "TAHAP 6 - CIPTA (CREATE): Cadangan yang memfokuskan kepada inovasi, sintesis idea baharu, atau reka bentuk penyelesaian kreatif.\n\n";
                $userPrompt .= "ARAHAN FORMAT:\n";
                $userPrompt .= "- Setiap cadangan mesti diakhiri dengan label tahap dalam format: [TAHAP: NAMA]\n";
                $userPrompt .= "- Contoh: \"Cadangan anda di sini [TAHAP: INGAT]\"\n";
                $userPrompt .= "- Gunakan bahasa akademik yang formal dan profesional\n";
                $userPrompt .= "- Setiap cadangan mesti berbeza dan membina antara satu sama lain\n";
                $userPrompt .= "- Berikan hanya 6 cadangan tanpa sebarang penjelasan tambahan";
            } else {
                $systemPrompt = 'You are an expert academic AI assistant helping students and lecturers with high-quality academic writing suggestions in English using Bloom\'s Taxonomy framework. Your suggestions must be professional, structured, and appropriate for academic contexts.';
                
                $userPrompt = "User ({$userRole}): \"{$validated['current_input']}\"\n\n";
                $userPrompt .= "Generate 6 specific and professional academic suggestions (15-25 words each) based on the 6 levels of Bloom's Taxonomy in sequential order:\n\n";
                $userPrompt .= "LEVEL 1 - REMEMBER: Suggestion focusing on recalling facts, definitions, or identifying basic concepts.\n";
                $userPrompt .= "LEVEL 2 - UNDERSTAND: Suggestion focusing on explaining meaning, interpreting, or summarizing ideas.\n";
                $userPrompt .= "LEVEL 3 - APPLY: Suggestion focusing on using theory in practical contexts or problem-solving scenarios.\n";
                $userPrompt .= "LEVEL 4 - ANALYZE: Suggestion focusing on in-depth examination, comparison, or breaking down relationships between elements.\n";
                $userPrompt .= "LEVEL 5 - EVALUATE: Suggestion focusing on critical assessment, justifying decisions, or weighing strengths and weaknesses.\n";
                $userPrompt .= "LEVEL 6 - CREATE: Suggestion focusing on innovation, synthesizing new ideas, or designing creative solutions.\n\n";
                $userPrompt .= "FORMAT INSTRUCTIONS:\n";
                $userPrompt .= "- Each suggestion must end with the level label in format: [LEVEL: NAME]\n";
                $userPrompt .= "- Example: \"Your suggestion here [LEVEL: REMEMBER]\"\n";
                $userPrompt .= "- Use formal and professional academic language\n";
                $userPrompt .= "- Each suggestion must be distinct and build upon each other\n";
                $userPrompt .= "- Provide only the 6 suggestions without any additional explanation";
            }

            // Add template context for more targeted suggestions
            if (!empty($validated['template_context'])) {
                $contextNote = $isMalay 
                    ? "\n\nKonteks template: {$validated['template_context']}"
                    : "\n\nTemplate context: {$validated['template_context']}";
                $userPrompt .= $contextNote;
            }

            // The backend sends the structured prompt to the OpenAI API using the Chat Completion endpoint and retrieves AI-generated suggestions. = Suggestion5
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            $suggestions = $response->choices[0]->message->content;
            
            // Parse suggestions into array (split by line breaks or numbers)
            $suggestionArray = preg_split('/\n+|\d+\.\s*/', trim($suggestions), -1, PREG_SPLIT_NO_EMPTY);
            $suggestionArray = array_filter(array_map('trim', $suggestionArray));

            // The backend returns the AI suggestions in JSON format to the frontend for dynamic rendering. = Suggestion6
            return response()->json([
                'success' => true,
                'suggestions' => array_values($suggestionArray)
            ]);

        } catch (\Exception $e) {
            Log::error('AI suggestion error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'field_name' => $validated['field_name']
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate suggestions. Please try again.',
            ], 500);
        }
    }
}