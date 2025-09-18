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


class UserTemplateController extends Controller
{
    public function UserTemplate(){
        $user = Auth::user();
        $templates = Template::latest()->get();
        return view('client.backend.template.all_template',compact('user','templates'));
    }

    public function UserDetailsTemplate($id){
        $template = Template::with('inputFields')->findOrFail($id);
        $user = Auth::user();
        return view('client.backend.template.details_template',compact('template','user')); 

    }

    public function UserContentGenerate(Request $request, $id)
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
            'language' => 'required|string|in:English,Malay',
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

    public function UserDocument(){
        $id = Auth::user()->id;
        // Use paginate() instead of get() to enable pagination
        $document = GeneratedContent::where('user_id', $id)->orderBy('id', 'desc')->paginate(10);
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