<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Carbon;
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

    /**
     * Show document retention settings page
     */
    public function DocumentSettings()
    {
        $retentionDays = SystemSetting::getDocumentRetentionDays();
        $autoCleanup = SystemSetting::isDocumentAutoCleanupEnabled();
        
        // Get statistics
        $totalDocuments = GeneratedContent::count();
        $oldestDocument = GeneratedContent::oldest()->first();
        $newestDocument = GeneratedContent::latest()->first();
        
        // Calculate documents that would be deleted
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        $documentsToDelete = GeneratedContent::where('created_at', '<', $cutoffDate)->count();
        
        // Get documents by user role
        $studentDocuments = GeneratedContent::whereHas('user', function($q) {
            $q->where('role', 'student');
        })->count();
        
        $lecturerDocuments = GeneratedContent::whereHas('user', function($q) {
            $q->where('role', 'lecturer');
        })->count();
        
        $adminDocuments = GeneratedContent::whereHas('user', function($q) {
            $q->where('role', 'admin');
        })->count();
        
        return view('superadmin.backend.document.document_settings', compact(
            'retentionDays',
            'autoCleanup',
            'totalDocuments',
            'oldestDocument',
            'newestDocument',
            'documentsToDelete',
            'studentDocuments',
            'lecturerDocuments',
            'adminDocuments'
        ));
    }

    /**
     * Update document retention settings
     */
    public function UpdateDocumentSettings(Request $request)
    {
        $request->validate([
            'retention_days' => 'required|integer|min:7|max:365',
        ]);

        // Update retention days
        SystemSetting::set(
            'document_retention_days',
            $request->retention_days,
            'integer',
            'Number of days to keep user-generated documents before auto-deletion'
        );

        // Always enable auto cleanup when settings are saved
        SystemSetting::set(
            'document_auto_cleanup',
            1,
            'boolean',
            'Enable automatic cleanup of old documents'
        );

        return back()->with([
            'message' => 'Settings saved! Document auto-cleanup is now active and will run every night at midnight.',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Manually trigger document cleanup
     */
    public function ManualDocumentCleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7|max:365',
        ]);

        try {
            $cutoffDate = Carbon::now()->subDays($request->days);
            $count = GeneratedContent::where('created_at', '<', $cutoffDate)->count();

            if ($count === 0) {
                return back()->with([
                    'message' => 'No documents found older than ' . $request->days . ' days',
                    'alert-type' => 'info'
                ]);
            }

            // Delete old documents
            $deleted = GeneratedContent::where('created_at', '<', $cutoffDate)->delete();

            return back()->with([
                'message' => "Successfully deleted {$deleted} old document(s)",
                'alert-type' => 'success'
            ]);

        } catch (\Exception $e) {
            return back()->with([
                'message' => 'Error during cleanup: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }
}
