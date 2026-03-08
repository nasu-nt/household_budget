<x-guest-layout>
    <x-slot:title>Forgot your password?</x-slot:title>

    <main class="guest-main forgot-password">
        <div class="forgot-password-card">

            <h2 class="title">Forgot your password?</h2>
            <p class="description">
                Enter your email address and we’ll send you a password reset link.
            </p>

            <div class="forgot-password-form">
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    {{-- Email Address --}}
                    <div class="email-field">
                        <label for="email">Email</label>
                        <input id="email"
                            class="textbox @error('email') is-invalid @enderror"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >

                        @error('email')
                            <div class="error-message">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="button">Send reset link</button>

                </form>
            </div>

            @if (session('status'))
                <div class="status-message">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </main>

</x-guest-layout>
