<?php

namespace App\Http\Controllers\Backend\Admin;

use Illuminate\Http\Request;
use App\Models\GeneratedContent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Display a list of all documents for the admin.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function AdminDocument()
    {
        $id = Auth::user()->id;
        $document = GeneratedContent::where('user_id', $id)->orderBy('id', 'desc')->paginate(10);
        return view('admin.backend.document.all_document', compact('document'));
    }

    /**
     * Show the form to edit a specific document.
     *
     * @param int $id The ID of the document to edit.
     * @return \Illuminate\Contracts\View\View
     */
    public function EditAdminDocument($id){
        // Find the document by its ID or fail.
        $document = GeneratedContent::findOrFail($id);
        return view('admin.backend.document.edit_document',compact('document'));
    }

    /**
     * Handle the update of a document.
     *
     * @param \Illuminate\Http\Request $request The request instance.
     * @param int $id The ID of the document to update.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function AdminUpdateDocument(Request $request, $id){
        // Find the document by its ID or fail.
        $document = GeneratedContent::findOrFail($id);

        // Validate the incoming request data.
        $validateData = $request->validate([
            'output' => 'required|string',
            'input' => 'sometimes|array', // Optional input updates
        ]);

        $updateData = [
            'output' => $validateData['output'],
        ];

        // If input data is provided, encode it as JSON and add to update array.
        if (isset($validateData['input'])) {
            $updateData['input'] = json_encode($validateData['input']);
        }

        // Update word count based on the new output.
        $updateData['word_count'] = str_word_count(strip_tags($validateData['output']));

        // Update the document with the new data.
        $document->update($updateData);

        $notification = array(
            'message' => 'Document Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('admin.document')->with($notification); 
    }

    /**
     * Delete a specific document.
     *
     * @param int $id The ID of the document to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
     public function DeleteAdminDocument($id){
        // Find the document and delete it.
        GeneratedContent::find($id)->delete();
        $notification = array(
        'message' => 'Document Deleted Successfully',
        'alert-type' => 'success'
     );

     return redirect()->back()->with($notification); 
     }
}