<?php

namespace App\Http\Controllers\Auth;

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

    /** @var \App\Models\User $user */
    $user = Auth::user();

    if ($user->hasRole('admin')) {
        return redirect()->intended(route('admin.dashboard'));
    }

    // If the intended URL is the dashboard (non-admin), clear it and redirect to home
    if (session()->has('url.intended') && str_contains(session()->get('url.intended'), '/dashboard')) {
        session()->forget('url.intended');
    }

    return redirect()->intended(route('home'));
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
}
