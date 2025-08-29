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
}