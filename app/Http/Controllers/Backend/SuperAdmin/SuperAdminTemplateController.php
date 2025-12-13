<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Template;
use App\Models\TemplateInputFields;
use Illuminate\Support\Facades\Log; 
use App\Models\GeneratedContent;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SuperAdminTemplateController extends Controller
{
    public function Index(Request $request)
    {
        // Start the query with the base model
        $query = Template::query();
        
        // 1. Apply Sorting (Order By) 
        $sort = $request->input('sort', 'newest'); // Default to 'newest'
        
        if ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'title-desc') {
            $query->orderBy('title', 'desc');
        } else {
            $query->latest(); // Default: Newest first
        }

        // 2. Apply Category Filter 
        $category = $request->input('category');
        if ($category && $category !== 'all') {
            $query->where('category', ucfirst($category));
        }

        // 3. Apply Status Filter (NEW)
        $status = $request->input('status');
        if ($status && $status !== 'all') {
            if ($status === 'active') {
                $query->where('is_active', 1);
            } elseif ($status === 'inactive') {
                $query->where('is_active', 0);
            }
        }

        // 4. Apply Search Filter 
        $search = $request->input('search');
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                ->orWhere('description', 'like', $searchTerm);
            });
        }

        // 5. Get totals (without pagination)
        $totalTemplates  = $query->count(); // Total templates with applied filters
        $studentCount    = (clone $query)->where('category', 'Student')->count();
        $lecturerCount   = (clone $query)->where('category', 'Lecturer')->count();

        // 6. Paginate and preserve query string 
        $templates = $query->paginate(8)->withQueryString(); 

        return view('superadmin.backend.template.all_template', compact(
            'templates', 
            'totalTemplates', 
            'studentCount', 
            'lecturerCount'
        ));
    }

    public function Create()
    {
        return view('superadmin.backend.template.add_template');
    }

    public function Store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'icon' => 'required',
            'prompt' => 'required',
            'input_fields' => 'required|array'
        ]);

        $template = Template::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'icon' => $validated['icon'],
            'prompt' => $validated['prompt'],
            'is_active' => 1,
            'created_by' => Auth::id()
        ]);

        foreach ($validated['input_fields'] as $field) {
            TemplateInputFields::create([
                'template_id' => $template->id,
                'title' => $field['title'],
                'description' => $field['description'],
                'type' => 'textarea',
                'is_required' => true
            ]);
        }

        return redirect()->route('superadmin.template')->with([
            'message' => 'Template Created Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function Edit($id)
    {
        $template = Template::with('inputFields')->findOrFail($id);
        return view('superadmin.backend.template.edit_template', compact('template'));
    }

    public function Update(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $template->update($request->only('title','description','category','icon','prompt'));

        return redirect()->route('superadmin.template')->with([
            'message' => 'Template Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function Show($id)
    {
        $template = Template::with('inputFields')->findOrFail($id);
        return view('superadmin.backend.template.details_template', compact('template'));
    }

    public function ContentGenerate(Request $request, $id)
    {
        // Step 1: Authenticate user and validate static inputs
        $user = Auth::user();
        if (!$user) {
            // If user not logged in, return error
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Validate basic request inputs
        $validatedData = $request->validate([
            'language' => 'required|string|in:English,Bahasa Melayu', // must be one of these two
            'ai_model' => 'required|string|in:gpt-4,gpt-3.5-turbo',   // must be valid OpenAI model
            'result_length' => 'required|integer|min:1|max:1000',     // limit between 1–1000 words
        ]);

        // // Step 2: Check user word usage limit
        // $estimatedWordCount = $validatedData['result_length'];
        // if ($user->current_word_usage !== null && ($user->words_used + $estimatedWordCount) > $user->current_word_usage) {
        //     // If word usage exceeds limit, stop and return error
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Word limit exceeded',
        //     ], 400);
        // }

        // Step 3: Fetch template and validate dynamic input fields
        $template = Template::with(['inputFields' => function ($query) {
            $query->select('id', 'template_id', 'title'); // load only needed columns
        }])
        ->select('id', 'prompt') // select only necessary template columns
        ->findOrFail($id); // find template by ID or fail if not found

        // Build dynamic validation rules based on template input fields
        $dynamicRules = [];
        $fieldNames = [];
        foreach ($template->inputFields as $field) {
            $fieldName = str_replace(' ', '_', $field->title);
            $dynamicRules[$fieldName] = 'required|string|max:1000';
            $fieldNames[] = $fieldName;
        }

        // Validate all dynamic fields together
        $request->validate($dynamicRules);

        // Get only dynamic data that was validated
        $inputData = $request->only($fieldNames);

        // Step 4: Build AI prompt with input replacements
        $replacements = [];
        foreach ($inputData as $key => $value) {
            // Replace placeholders like {field_name} and {field name}
            $replacements['{' . $key . '}'] = $value;
            $replacements['{' . str_replace('_', ' ', $key) . '}'] = $value;
        }
        $replacements['{result_length}'] = $validatedData['result_length'];

        // Replace all placeholders in template prompt
        $prompt = strtr($template->prompt, $replacements);

        // Build final messages with language-specific rules
        $messages = $this->buildLanguageSpecificMessages($validatedData, $prompt);

        // Step 5: Send request to OpenAI API and get generated output
        try {
            $response = OpenAI::chat()->create([
                'model' => $validatedData['ai_model'],
                'messages' => $messages,
                'max_tokens' => min(4000, $validatedData['result_length'] * 2),
                'temperature' => 0.7,
            ]);

            $output = $response->choices[0]->message->content;

            // Step 6: Validate output language to match user selection
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

            // Step 7: Save generated content and update user usage (word) in database
            DB::transaction(function () use ($user, $template, $inputData, $output, $wordCount) {
                // Increase user’s used words
                User::where('id', $user->id)->increment('words_used', $wordCount);

                // Save the generated content record
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

            // Return successful response with generated output
            return response()->json([
                'success' => true,
                'output' => $output,
                'language_confidence' => $languageCheck['confidence'] ?? null
            ]);

        // Step 8: Error handling for OpenAI and general exceptions
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

    public function Destroy($id)
    {
        Template::findOrFail($id)->delete();

        return back()->with([
            'message' => 'Template Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function toggleStatus($id)
    {
        $template = Template::findOrFail($id);

        $template->is_active = !$template->is_active;
        $template->save();

        return redirect()->back()->with([
            'message' => 'Template status updated successfully!',
            'alert-type' => 'success'
        ]);
    }


}
