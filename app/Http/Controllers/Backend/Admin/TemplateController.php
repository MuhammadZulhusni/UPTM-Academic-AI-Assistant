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

        /// Validates the incoming request data
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'icon' => 'required|string',
            'prompt' => 'required|string',
            'is_active' => 'required|in:0,1',
            'input_fields' => 'required|array|size:1',
            'input_fields.*.title' => 'required|string|max:255', // Validates that the 'title' for each input field is a required string with a max length of 255 characters.
            'input_fields.*.description' => 'required|string', // Validates that the 'description' for each input field is a required string.
            'input_fields.*.type' => 'required|in:text,textarea', // Validates that the 'type' for each input field is a required string and its value must be either 'text' or 'textarea'.
        ]);

    // Creates a new Template model instance
    $template = new Template();
    // Assigns validated data to the template properties
    $template->title = $validateData['title'];
    $template->description = $validateData['description'];
    $template->category = $validateData['category'];
    $template->icon = $validateData['icon'];
    $template->prompt = $validateData['prompt'];
    $template->is_active = $validateData['is_active'];
    // Sets the creator to the current authenticated user's ID
    $template->created_by = Auth::id();
    $template->save();


    // Extracts the input field data
    $inputField = $validateData['input_fields'][0];
    // Creates a new entry in the TemplateInputFields table
    TemplateInputFields::create([
        'template_id' => $template->id,
        'title' => $inputField['title'],
        'description' => $inputField['description'],
        'type' => $inputField['type'],
        'is_required' => true,
    ]);

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
            'is_active' => 'required|in:0,1',
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
    $template->is_active = $validateData['is_active']; 
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
        // Fetches the template and its associated input fields from the database.
        $template = Template::with('inputFields')->findOrFail($id);

        // Retrieves a fresh instance of the authenticated user from the database.
        $user = User::find(Auth::id());
        
        // Verifies that a user was successfully found. Returns an error if not.
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 401);
        }

        // Validates static form inputs for language, AI model, and desired length.
        $validateData = $request->validate([
            'language' => 'required|string|in:English,Malay,Mandarin,Hindi',
            'ai_model' => 'required|string|in:gpt-4,gpt-3.5-turbo',
            'result_length' => 'required|integer|min:1|max:1000',
        ]);

        // Dynamically validates each input field based on the template's configuration.
        foreach($template->inputFields as $field) {
            // Replaces spaces in the field title with underscores to match the request keys.
            $fieldName = str_replace(' ', '_', $field->title);
            $request->validate([
                $fieldName => 'required|string',
            ]);
        }

        // Gets all user inputs except for the fixed form fields.
        $inputData = $request->except(['_token', 'language', 'ai_model', 'result_length']);
        Log::info('Input Data', ['inputData' => $inputData]);

        // Starts with the base prompt from the template.
        $prompt = $template->prompt;

        // Replaces placeholders in the prompt with the user's dynamic input values.
        foreach($template->inputFields as $field) {
            $fieldName = str_replace(' ', '_', $field->title);
            $fieldValue = $inputData[$fieldName] ?? '';
            // Replaces both underscore-separated and space-separated placeholders.
            $prompt = str_replace('{' . str_replace(' ', '_', $field->title) . '}', $fieldValue, $prompt);
            $prompt = str_replace('{' . $field->title . '}', $fieldValue, $prompt);
        }

        // Replaces the placeholder for the desired output length.
        $prompt = str_replace('{result_length}', $validateData['result_length'], $prompt);

        // Prepends language and length instructions to the final prompt.
        $prompt = "In {$validateData['language']}, {$prompt} Aim for approximately {$validateData['result_length']} words.";

        Log::info('Final Prompt', ['prompt' => $prompt]);

        // Estimates word count for the requested generation.
        $estimatedWordCount = $validateData['result_length'];

        // Checks if the user has a word usage limit and if they would exceed it.
        if ($user->current_word_usage !== null) {
            if ($user->words_used + $estimatedWordCount > $user->current_word_usage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Word limit exceeded',
                ], 400);
            }
        }

        try {
            // Sends the final prompt to the OpenAI API to generate content.
            $response = OpenAI::chat()->create([
                'model' => $validateData['ai_model'],
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            // Extracts the generated text from the AI's response.
            $output = $response->choices[0]->message->content;
            $wordCount = str_word_count($output);

            // Updates the user's word count in the database.
            $user->words_used += $wordCount;
            $user->save();

            // Saves the generation details to the database for historical tracking.
            GeneratedContent::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'input' => json_encode($inputData),
                'output' => $output,
                'word_count' => $wordCount,
            ]);

            // Returns a successful JSON response with the generated output.
            return response()->json([
                'success' => true,
                'output' => $output
            ]);

        } catch (\Exception $e) {
            // Catches any errors during the generation process and returns a JSON error message.
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage(),
            ], 500);
        } 
    }

}