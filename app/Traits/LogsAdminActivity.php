<?php

namespace App\Traits;

use App\Models\AdminActivity;
use Illuminate\Support\Facades\Auth;

trait LogsAdminActivity
{
    /**
     * Log an admin activity
     */
    protected function logActivity($type, $description, $entityType = null, $entityId = null, $metadata = null)
    {
        $user = Auth::user();
        
        // Only log if user is admin
        if ($user && $user->role === 'admin') {
            AdminActivity::logActivity(
                $user->id,
                $type,
                $description,
                $entityType,
                $entityId,
                $metadata
            );
        }
    }

    /**
     * Log template creation
     */
    protected function logTemplateCreated($template)
    {
        $this->logActivity(
            'template_created',
            "Created template: {$template->title}",
            'template',
            $template->id,
            [
                'template_title' => $template->title,
                'category' => $template->category,
            ]
        );
    }

    /**
     * Log template update
     */
    protected function logTemplateUpdated($template, $oldData = null)
    {
        $this->logActivity(
            'template_updated',
            "Updated template: {$template->title}",
            'template',
            $template->id,
            [
                'template_title' => $template->title,
                'old_data' => $oldData,
            ]
        );
    }

    /**
     * Log template deletion
     */
    protected function logTemplateDeleted($templateTitle, $templateId)
    {
        $this->logActivity(
            'template_deleted',
            "Deleted template: {$templateTitle}",
            'template',
            $templateId
        );
    }

    /**
     * Log user creation
     */
    protected function logUserCreated($user)
    {
        $this->logActivity(
            'user_created',
            "Created user: {$user->name} ({$user->email})",
            'user',
            $user->id,
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
            ]
        );
    }

    /**
     * Log user update
     */
    protected function logUserUpdated($user, $oldData = null)
    {
        $this->logActivity(
            'user_updated',
            "Updated user: {$user->name} ({$user->email})",
            'user',
            $user->id,
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'old_data' => $oldData,
            ]
        );
    }

    /**
     * Log user deletion
     */
    protected function logUserDeleted($userName, $userEmail, $userId)
    {
        $this->logActivity(
            'user_deleted',
            "Deleted user: {$userName} ({$userEmail})",
            'user',
            $userId
        );
    }

    /**
     * Log document creation
     */
    // protected function logDocumentCreated($document)
    // {
    //     $this->logActivity(
    //         'document_created',
    //         "Created document from template",
    //         'document',
    //         $document->id
    //     );
    // }

    /**
     * Log document update
     */
    // protected function logDocumentUpdated($document)
    // {
    //     $this->logActivity(
    //         'document_updated',
    //         "Updated document",
    //         'document',
    //         $document->id
    //     );
    // }

    /**
     * Log document deletion
     */
    // protected function logDocumentDeleted($documentId)
    // {
    //     $this->logActivity(
    //         'document_deleted',
    //         "Deleted document",
    //         'document',
    //         $documentId
    //     );
    // }

    /**
     * Log admin login
     */
    protected function logLogin()
    {
        $this->logActivity(
            'login',
            "Admin logged in"
        );
    }

    /**
     * Log admin logout
     */
    protected function logLogout()
    {
        $this->logActivity(
            'logout',
            "Admin logged out"
        );
    }
}