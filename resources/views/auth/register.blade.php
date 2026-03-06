{{-- resources/views/auth/register.blade.php --}}
<x-guest-layout>
    <x-slot:title>Register</x-slot:title>

{{-- テスト用 --}}
@php
    $error = 0;
@endphp

    <div class="guest-main register">
        <div class="register-card">
            <h2 class="title">Create an Account</h2>
            <div class="register-form">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="input-field">

                        <!-- Display Name -->
                        <div class="name-block">
                            <div class="field-row">
                                <x-input-label for="name" class="field-label" :value="__('Display Name')" />
                                <x-text-input id="name" class="textbox form-input--sm" 
                                    type="text"
                                    name="name"
                                    :value="old('name')"
                                    required
                                    autofocus
                                />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="email-block">
                            <div class="field-row">
                                <x-input-label for="email" class="field-label" :value="__('Email')" />
                                <x-text-input id="email" class="textbox form-input--lg"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autocomplete="username" {{-- ブラウザやパスワードマネージャに「この欄はユーザー名（ログインID）」って教える --}}
                                    required
                                />
                            </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="password-block">
                            <div class="field-row">
                                <x-input-label for="password" class="field-label" :value="__('Password')" />
                                <div class="password-field">
                                    <x-text-input id="password" class="textbox form-input--lg {{ $error ? 'is-invalid' : '' }}"
                                        type="password"
                                        name="password"
                                        autocomplete="new-password" required
                                    />
                                    <button type="button" class="toggle-password"
                                        data-toggle-password
                                        data-target="#password"
                                        aria-label="Show password"
                                    >
                                        <img src="/images/icons/eye-slash.svg" alt="" id="toggle-password-icon">
                                    </button>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="password-confirmation-block">
                            <div class="field-row">
                                <x-input-label for="password_confirmation" class="field-label" :value="__('Confirm Password')" />
                                <div class="password-confirmation-field">
                                    <x-text-input id="password_confirmation" class="textbox form-input--lg {{ $error ? 'is-invalid' : '' }}"
                                        type="password"
                                        name="password_confirmation"
                                        autocomplete="new-password" required
                                    />
                                    <button type="button" class="toggle-password"
                                        data-toggle-password
                                        data-target="#password_confirmation"
                                        aria-label="Show password"
                                    >
                                        <img src="/images/icons/eye-slash.svg" alt="" id="toggle-password-icon">
                                    </button>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div> {{-- end input-field --}}

                    @if ($error)
                        <p class="error-message" id="password-error">
                            Passwords do not match. Please try again.
                        </p>
                    @endif
                    
                    <p class="password-hint">
                        Use at least 8 characters. A mix of letters, numbers, and symbols is recommended.
                    </p>
                    
                    {{-- Submit --}}
                    <input class="button" type="submit" value="Register">

                    <div class="log-in">
                        <span>Already have an account?</span>
                        <a href="{{ route('login') }}">Log in</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-guest-layout>