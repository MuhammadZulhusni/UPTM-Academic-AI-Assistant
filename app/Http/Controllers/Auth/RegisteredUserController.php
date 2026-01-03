<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate user input with enhanced password rules
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required', 
                'confirmed',
                'min:8',                    // Minimum 8 characters
                'max:64',                   // Maximum 64 characters
                'regex:/[a-z]/',            // Must contain at least one lowercase letter
                'regex:/[A-Z]/',            // Must contain at least one uppercase letter
                'regex:/[0-9]/',            // Must contain at least one number
                'regex:/[@$!%*#?&]/',       // Must contain at least one special character
            ],
            'role' => ['required', 'in:student,lecturer'], // Only allow student or lecturer
        ], [
            // Custom error messages for password validation
            'password.min' => 'Password must be at least 8 characters long.',
            'password.max' => 'Password must not exceed 64 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
        ]);

        // Create user with Diamond plan (ID 1) automatically
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan_id' => 1,           // Always assign Diamond plan
            'current_word_usage' => null, // null = unlimited
            'words_used' => 0,
            'role' => $request->role, // Save selected role
            'status' => '1',
        ]);

        event(new Registered($user));

        // Redirect to login page with success message
        return redirect()->route('login')->with([
            'message' => 'Registration successful! Please log in with your new account.',
            'alert-type' => 'success'
        ]);
    }
}