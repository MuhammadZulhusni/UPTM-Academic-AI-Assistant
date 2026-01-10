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
        try {
            // Step 1: Basic validation
            $request->validate([
                'token' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', 'min:8', 'max:64'],
            ], [
                'password.min' => 'Password must be at least 8 characters.',
                'password.max' => 'Password must not exceed 64 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Step 2: Additional password strength validation with specific messages
            $password = $request->password;
            $errors = [];

            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = 'Password must contain at least one lowercase letter (a-z).';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'Password must contain at least one uppercase letter (A-Z).';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'Password must contain at least one number (0-9).';
            }
            if (!preg_match('/[@$!%*#?&]/', $password)) {
                $errors[] = 'Password must contain at least one special character (@$!%*#?&).';
            }

            if (!empty($errors)) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['password' => $errors]);
            }

            // Step 3: Reset password
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

            // Step 4: Handle reset status
            if ($status == Password::PASSWORD_RESET) {
                // Success: Redirect to login with Toastr notification
                $notification = array(
                    'message' => 'Your password has been successfully reset! You can now log in.',
                    'alert-type' => 'success'
                );

                return redirect()->route('login')->with($notification);
            }
            
            // Failure: Redirect back with errors
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors($e->validator);

        } catch (\Exception $e) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'An error occurred while resetting your password. Please try again.']);
        }
    }
}