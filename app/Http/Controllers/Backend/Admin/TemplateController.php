<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TemplateInputFields;

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
}