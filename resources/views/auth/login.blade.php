{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <x-slot:title>Sign in</x-slot:title>

{{-- テスト用 --}}
@php
    $error = 0;
@endphp

{{-- 
PWリセット後、"Password has been reset. Please sign in."のトースト出す
Emailをログイン画面に渡して初期値にしてもいい。
--}}

    <main class="guest-main login">
        <div class="login-card">
            <h2 class="title">Sign in</h2>

            <div class="login-form">
                <form action="" method="">
                {{-- <form method="POST" action="{{ route('login') }}"> --}}
                    @csrf {{-- CSRF攻撃対策 --}}

                    {{-- Email --}}
                    <div class="email-field">
                        <x-input-label for="email" value="Email" />
                        <x-text-input
                            id="email"
                            class="textbox"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>

                    {{-- Password --}}
                    <div class="password-block">
                        <x-input-label for="password" value="Password" />

                        <div class="password-field">
                            <x-text-input
                                id="password"
                                class="textbox {{ $error ? 'is-invalid' : '' }}"
                                {{-- {{ $errors->has('email') || $errors->has('password') ? 'is-invalid' : '' }}" --}}
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                            />
                            
                        <button type="button" class="toggle-password" id="toggle-password" aria-label="Toggle password visibility">
                            <img src="/images/icons/eye-slash.svg" alt="" id="toggle-password-icon">
                        </button>
                    </div>

                    {{-- Submit --}}
                    <input class="button" type="submit" value="Sign in">
                </form>
            </div>

            {{-- Auth error --}}
            @if ($error)
            {{-- @if ($errors->has('email')) --}}
                <p class="error-message" id="auth-error">
                    The email address or password is incorrect.
                </p>
            @endif

            <a class="forgot-password" href="{{ route('password.request') }}">
                Forgot your password?
            </a>

            <hr>

            <div class="sign-up">
                <span>Don’t have an account?</span>
                <a href="{{ route('register') }}">Sign up</a>
            </div>
        </div>
    </main>

</x-guest-layout>