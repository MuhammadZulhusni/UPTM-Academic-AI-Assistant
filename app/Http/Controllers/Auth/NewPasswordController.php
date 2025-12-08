<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // ... (password reset logic remains the same)
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // --- MODIFIED REDIRECT LOGIC ---
        
        if ($status == Password::PASSWORD_RESET) {
            // Success: Redirect to login with Toastr data and Laravel's status
            return redirect()->route('login')->with([
                'status' => __($status), // Laravel's built-in status (for standard display)
                'alert-type' => 'success', // Toastr type
                'message' => 'Your password has been successfully reset! You can now log in.' // Toastr message
            ]);
        }
        
        // Failure: Redirect back with input and errors
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}