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