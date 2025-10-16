<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Template;
use Illuminate\Support\Facades\Log; 
use App\Models\TemplateInputFields;
use App\Models\GeneratedContent;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class TemplateController extends Controller
{
    // Fetches all templates and displays them in the admin view
    public function AdminTemplate(){
        $templates = Template::latest()->get();
        return view('admin.backend.template.all_template',compact('templates'));
    }

    // Displays the form for adding a new template
    public function AddTemplate(){
        return view('admin.backend.template.add_template');
    }

    // Handles the creation and storage of a new template
    public function StoreTemplate(Request $request){

        // Validate the incoming request data
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'icon' => 'required|string',
            'prompt' => 'required|string',
            // Update this rule to use 'is_active_checkbox' from the form
            'is_active_checkbox' => 'nullable|in:on', // "on" is the value for checked checkboxes
            'input_fields' => 'required|array', // Allow for one or more fields
            'input_fields.*.title' => 'required|string|max:255',
            'input_fields.*.description' => 'required|string',
            'input_fields.*.type' => 'required|in:text,textarea',
        ]);

        // Creates a new Template model instance
        $template = new Template();

        // Assigns validated data to the template properties
        $template->title = $validateData['title'];
        $template->description = $validateData['description'];
        $template->category = $validateData['category'];
        $template->icon = $validateData['icon'];
        $template->prompt = $validateData['prompt'];

        // Convert the checkbox value to 1 or 0
        $template->is_active = isset($validateData['is_active_checkbox']) ? 1 : 0;

        // Sets the creator to the current authenticated user's ID
        $template->created_by = Auth::id();
        $template->save();

        // Extracts and saves all input fields
        foreach ($validateData['input_fields'] as $inputField) {
            TemplateInputFields::create([
                'template_id' => $template->id,
                'title' => $inputField['title'],
                'description' => $inputField['description'],
                'type' => $inputField['type'],
                'is_required' => true, // Assuming all fields are required
            ]);
        }
        
        // Prepares a success notification message
        $notification = array(
            'message' => 'Template Created Successfully',
            'alert-type' => 'success'
        );

        // Redirects the user to the template list page with the success message
        return redirect()->route('admin.template')->with($notification);
    }


    // Handles displaying the form to edit an existing template.
    public function EditTemplate($id){
        // Finds the template by its ID ($id) and also loads its related input fields.
        // If the template is not found, it automatically returns a 404 error.
        $template = Template::with('inputFields')->findOrFail($id);
        
        // Returns the 'edit_template' view and passes the $template data to it.
        return view('admin.backend.template.edit_template',compact('template'));
    }


    // This function handles updating the template and its related input fields after a form submission.
    public function UpdateTemplate(Request $request, $id){

        /// Validates the incoming request data. The validation rules ensure that all fields are present and in the correct format.
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'icon' => 'required|string',
            'prompt' => 'required|string',
            'input_fields' => 'required|array|size:1',
            'input_fields.*.title' => 'required|string|max:255',
            'input_fields.*.description' => 'required|string',
            'input_fields.*.type' => 'required|in:text,textarea',  
        ]);

    // Finds the existing template by its ID.
    $template = Template::findOrFail($id);
    
    // Updates the template's properties with the validated data from the request.
    $template->title = $validateData['title']; 
    $template->description = $validateData['description'];
    $template->category = $validateData['category'];
    $template->icon = $validateData['icon'];
    $template->prompt = $validateData['prompt'];
    $template->save();

    // The code below handles updating the related input fields.
    // Gets the first (and in this case, only) input field from the validated data array.
    $inputField = $validateData['input_fields'][0];
     
    // Finds the corresponding input field in the database based on the template's ID.
    $templateInputField = TemplateInputFields::where('template_id',$template->id)->first();

    // Checks if a matching input field was found.
    if ($templateInputField ) {
       // If found, update its properties with the new validated data.
       $templateInputField->title = $inputField['title'];
       $templateInputField->description = $inputField['description']; 
       $templateInputField->type = $inputField['type']; 
       $templateInputField->is_required = true;
       $templateInputField->save();
    } 

    $notification = array(
        'message' => 'Template Updated Successfully',
        'alert-type' => 'success'
     );

    return redirect()->route('admin.template')->with($notification); 
    }

    public function DetailsTemplate($id){
        // Fetches a template by its ID and eagerly loads its related input fields.
        // `findOrFail` will automatically show a 404 page if the template isn't found.
        $template = Template::with('inputFields')->findOrFail($id);
        $user = Auth::user();

        return view('admin.backend.template.details_template',compact('template','user')); 
    }

    public function AdminContentGenerate(Request $request, $id)
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

        // Step 2: Check user word usage limit
        $estimatedWordCount = $validatedData['result_length'];
        if ($user->current_word_usage !== null && ($user->words_used + $estimatedWordCount) > $user->current_word_usage) {
            // If word usage exceeds limit, stop and return error
            return response()->json([
                'success' => false,
                'message' => 'Word limit exceeded',
            ], 400);
        }

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

    public function DeleteTemplate($id)
    {
        // Find the template by its ID and delete it
        $template = Template::findOrFail($id);
        $template->delete();
        
        $notification = [
            'message' => 'Template Deleted Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}