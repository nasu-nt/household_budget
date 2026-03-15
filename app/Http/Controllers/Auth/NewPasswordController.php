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
        $request->validate(
            [
                // rules
                'token' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'password_confirmation' => ['required', 'same:password'],
            ],
            [
                // messages
                'email.required' => __('validation.required'),
                'email.email' => __('validation.email'),
                'password.required' => __('validation.required'),
                'password_confirmation.required' => 'Please confirm your password.',
                'password_confirmation.same' => __('validation.same'),
            ],
            [
                // attributes
                'email' => 'mail address',
                'password' => 'password',
                'password_confirmation' => 'password confirmation',
            ]
        );

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
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

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()
                        ->route('login')
                        ->with('status', __('passwords.reset'))
                        ->withInput(['email' => $request->email])
                    : back()
                        ->withInput($request->only('email'))
                        ->withErrors(['reset' => __('passwords.token')]);
    }
}
