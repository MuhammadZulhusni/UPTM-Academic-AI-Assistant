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
            'language' => 'required|string|in:English,Malay',
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
            $query->select('id', 'template_id', 'title'); // Only select needed columns
        }])
        ->select('id', 'prompt') // Only select needed template columns
        ->findOrFail($id);

        // Build dynamic validation rules efficiently
        $dynamicRules = [];
        $fieldNames = [];
        foreach ($template->inputFields as $field) {
            $fieldName = str_replace(' ', '_', $field->title);
            $dynamicRules[$fieldName] = 'required|string|max:1000'; // Add max length for security
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
        
        // Prepend language and length instructions
        $finalPrompt = "In {$validatedData['language']}, {$prompt} Aim for approximately {$validatedData['result_length']} words.";

        try {
            // OpenAI API call with optimized parameters
            $response = OpenAI::chat()->create([
                'model' => $validatedData['ai_model'],
                'messages' => [['role' => 'user', 'content' => $finalPrompt]],
                'max_tokens' => min(4000, $validatedData['result_length'] * 2), // Limit tokens for faster response
                'temperature' => 0.7, // Slightly lower for faster, more consistent responses
            ]);

            $output = $response->choices[0]->message->content;
            $wordCount = str_word_count($output);

            // Optimized database transaction
            DB::transaction(function () use ($user, $template, $inputData, $output, $wordCount) {
                // Use update instead of save for better performance
                User::where('id', $user->id)->increment('words_used', $wordCount);

                // Bulk insert for generated content
                GeneratedContent::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'input' => json_encode($inputData, JSON_UNESCAPED_UNICODE), // More efficient encoding
                    'output' => $output,
                    'word_count' => $wordCount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'output' => $output
            ]);

        } catch (\OpenAI\Exceptions\ErrorException $e) {
            // More specific OpenAI error handling
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