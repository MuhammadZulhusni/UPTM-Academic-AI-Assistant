<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // Super Admin redirect
        if ($user->role === 'superadmin') {
            return redirect()->intended(route('superadmin.dashboard'))->with([
                'message' => 'Super Admin Login Successfully',
                'alert-type' => 'success'
            ]);
        }

        // Admin redirect
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))->with([
                'message' => 'Admin Login Successfully',
                'alert-type' => 'success'
            ]);
        }

        // Student & Lecturer redirect (user)
        if (in_array($user->role, ['student', 'lecturer'])) {
            return redirect()->intended(route('user.dashboard'))->with([
                'message' => 'Login Successfully',
                'alert-type' => 'success'
            ]);
        }

        // Invalid role
        Auth::logout();

        return redirect('/')->with([
            'message' => 'Role not recognized!',
            'alert-type' => 'error'
        ]);
    }

    /**
     * Logout the user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
