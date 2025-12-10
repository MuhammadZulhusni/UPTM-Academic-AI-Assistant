<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use Illuminate\Http\Request;
use App\Models\GeneratedContent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SuperAdminDocumentController extends Controller
{
    public function Index(Request $request)
    {
        $id = Auth::user()->id;
        $sort = $request->query('sort', 'newest'); // Default to newest

        $document = GeneratedContent::with('template') // Add eager loading
            ->where('user_id', $id)
            ->when($sort === 'newest', function($query) {
                $query->orderBy('id', 'desc'); // Newest first
            })
            ->when($sort === 'oldest', function($query) {
                $query->orderBy('id', 'asc'); // Oldest first
            })
            ->paginate(10);
        return view('superadmin.backend.document.all_document', compact('document'));
    }

    public function Edit($id)
    {
        $document = GeneratedContent::with('template')->findOrFail($id);
        return view('superadmin.backend.document.edit_document', compact('document'));
    }

    public function Update(Request $request, $id)
    {
        $document = GeneratedContent::findOrFail($id);

        $request->validate([
            'output' => 'required|string'
        ]);

        $document->update([
            'output' => $request->output,
            'word_count' => str_word_count(strip_tags($request->output))
        ]);

        return redirect()->route('superadmin.document')->with([
            'message' => 'Document Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function Destroy($id)
    {
        GeneratedContent::findOrFail($id)->delete();

        return back()->with([
            'message' => 'Document Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }
}
