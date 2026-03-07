{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <x-slot:title>Sign in</x-slot:title>

{{-- 
PWリセット後、"Password has been reset. Please sign in."のトースト出す
Emailをログイン画面に渡して初期値にしてもいい。
--}}

    <main class="guest-main login">
        <div class="login-card">
            <h2 class="title">Sign in</h2>

            <div class="login-form">
                <form method="POST" action="{{ route('login') }}">
                    @csrf {{-- CSRF攻撃対策 --}}

                    {{-- Email --}}
                    <div class="email-block">
                        <label for="email">Email</label>
                        <div class="email-field">
                            <input id="email" class="textbox @error('email') is-invalid @enderror"
                                type="email"
                                name="email"
                                required
                                autofocus
                                autocomplete="username"
                                value="{{ old('email') }}"
                                data-error-target="email"
                            />
                        </div>
                        @error('email')
                            <p class="error-message" data-error-message="email">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="password-block">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input id="password" class="textbox @error('password') is-invalid @enderror"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                data-error-target="password"
                            />
                            <button type="button" class="toggle-password"
                                data-toggle-password
                                data-target="#password"
                                aria-label="Show password"
                            >
                                <img src="/images/icons/eye-slash.svg" alt="" id="toggle-password-icon">
                            </button>
                        </div>
                        @error('password')
                            <p class="error-message" data-error-message="password">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <input class="button" type="submit" value="Sign in">
                </form>
            </div>

            {{-- Auth error --}}
            @error('auth')
                <p class="error-message auth-error">{{ $message }}</p>
            @enderror

            @error('throttle')
                <p class="error-message auth-error">{{ $message }}</p>
            @enderror

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