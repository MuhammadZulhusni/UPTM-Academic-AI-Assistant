<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\AiOpsBriefService;

class SuperAdminAiOpsBriefController extends Controller
{
    public function index()
    {
        return redirect()->route('superadmin.dashboard');
    }

    public function generateNow(AiOpsBriefService $service)
    {
        try {
            $brief = $service->generate('manual');
        } catch (\Throwable $e) {
            return redirect()->route('superadmin.dashboard')->with([
                'message' => 'Could not generate the brief: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $message = $brief->status === 'success'
            ? 'AI ops brief generated from last 7 days of usage.'
            : 'Brief saved from system numbers. OpenAI was unavailable, so this is a fallback summary.';

        return redirect()->route('superadmin.dashboard')->with([
            'message' => $message,
            'alert-type' => $brief->status === 'success' ? 'success' : 'warning',
        ]);
    }
}
