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
     * Authenticates the user and redirects them to their respective dashboard (admin or user).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Try to authenticate manually
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Error notification
            $notification = [
                'message' => 'Invalid login details. Please try again.',
                'alert-type' => 'error'
            ];

            return redirect()->back()->with($notification);
        }

        $request->authenticate();

        $request->session()->regenerate();

        // Admin Part
        $user = $request->user();
        if ($user->role === 'admin') {
            // Add the success notification for admin login
            $notification = array(
                'message' => 'Admin Login Successfully',
                'alert-type' => 'success'
            );

            return redirect()->intended(route('admin.dashboard', absolute: false))->with($notification);
        }

        // User Part
        $notification = [
            'message' => 'Login Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->intended(route('user.dashboard', absolute: false))->with($notification);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
