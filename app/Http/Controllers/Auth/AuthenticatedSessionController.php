<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Log in as the demo user.
     */
    public function demo(Request $request): RedirectResponse
    {
        abort_unless(config('demo.enabled'), 404);

        $demoUser = User::query()
            ->where('email', config('demo.email'))
            ->first();

        if ($demoUser === null) {
            return back()->withErrors([
                'auth' => __('auth.demo_account_unavailable'),
            ]);
        }

        Auth::login($demoUser);

        // セッション固定攻撃への対策として、
        // ログイン後にセッションIDを再生成する
        $request->session()->regenerate();

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }
}
