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

        $notification = [
            'message' => 'Login Successfully',
            'alert-type' => 'success'
        ];

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'))->with([
                'message' => 'Admin Login Successfully',
                'alert-type' => 'success'
            ]);
        }

        // For both student and lecturer
        if (in_array($user->role, ['student', 'lecturer'])) {
            return redirect()->intended(route('user.dashboard'))->with($notification);
        }

        // If role is invalid
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
