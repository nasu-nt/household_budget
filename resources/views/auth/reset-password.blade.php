{{-- resources\views\auth\reset-password.blade.php --}}
<x-guest-layout>
    <x-slot:title>Reset password</x-slot:title>

    <main class="guest-main reset-password">
        <div class="reset-password-card">
            <h2 class="title">Reset password</h2>
            <div class="reset-password-form">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <div class="input-field">
                        {{-- Password Reset Token --}}
                        <input type="hidden" name="token" value="{{ request()->route('token') }}">

                        {{-- Email Address --}}
                        <div class="email-block">
                            <div class="field-row">
                                <label for="email">Email</label>
                                <input
                                    id="email"
                                    class="textbox @error('email') is-invalid @enderror" 
                                    type="email"
                                    name="email"
                                    value="{{ old('email', request()->query('email')) }}"
                                    required
                                    autocomplete="username"
                                    readonly
                                    data-error-target="email"
                                >
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
                                        class="textbox @error('password') is-invalid @enderror" 
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
                                        class="textbox @error('password_confirmation') is-invalid @enderror"
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
                    <input class="button" type="submit" value="Reset Password">
                </form>
            </div>

            {{-- Token error --}}
            @error('reset')
                <p class="error-message">{{ $message }}</p>
            @enderror

        </div>
    </div>

</x-guest-layout>
