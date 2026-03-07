{{-- resources/views/auth/register.blade.php --}}
<x-guest-layout>
    <x-slot:title>Register</x-slot:title>

    <main class="guest-main register">
        <div class="register-card">
            <h2 class="title">Create an Account</h2>
            <div class="register-form">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="input-field">

                        {{-- Display Name --}}
                        <div class="name-block">
                            <div class="field-row">
                                <label for="name">Display Name</label>
                                <input id="name"
                                    class="textbox form-input--sm @error('name') is-invalid @enderror" 
                                    type="text"
                                    name="name"
                                    required
                                    autofocus
                                    value="{{ old('name') }}"
                                    maxlength="255"
                                    data-error-target="name"
                                />
                            </div>
                            @error('name')
                                <p class="error-message" data-error-message="name">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email Address --}}
                        <div class="email-block">
                            <div class="field-row">
                                <label for="email">Email</label>
                                <input id="email"
                                    class="textbox form-input--lg @error('email') is-invalid @enderror" 
                                    type="email"
                                    name="email"
                                    required
                                    autocomplete="username" {{-- ブラウザやパスワードマネージャに「この欄はユーザー名（ログインID）」って教える --}}
                                    value="{{ old('email') }}"
                                    maxlength="255"
                                    data-error-target="email"
                                />
                            </div>
                            @error('email')
                                <p class="error-message" data-error-message="email">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="password-block">
                            <div class="field-row">
                                <label for="password">Password</label>
                                <div class="password-field">
                                    <input id="password"
                                        class="textbox form-input--lg @error('password') is-invalid @enderror" 
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="new-password"
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
                            </div>
                            @error('password')
                                <p class="error-message" data-error-message="password">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="password-confirmation-block">
                            <div class="field-row">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="password-confirmation-field">
                                    <input id="password_confirmation"
                                        class="textbox form-input--lg @error('password_confirmation') is-invalid @enderror"
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        data-error-target="password_confirmation"
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
                            @error('password_confirmation')
                                <p class="error-message" data-error-message="password_confirmation">{{ $message }}</p>
                            @enderror
                        </div>
                    </div> {{-- end input-field --}}
                    
                    <p class="password-hint">
                        Use at least 8 characters. A mix of letters, numbers, and symbols is recommended.
                    </p>
                    
                    {{-- Submit --}}
                    <input class="button" type="submit" value="Register">
                </form>

                <div class="log-in">
                    <span>Already have an account?</span>
                    <a href="{{ route('login') }}">Log in</a>
                </div>

            </div>
        </div>
    </main>
</x-guest-layout>