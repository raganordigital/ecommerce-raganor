<x-guest-layout>

<style>
    @import url('https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap');
    .login-wrap * { font-family: 'Figtree', sans-serif; }
    .grain {
        position: fixed; inset: 0; pointer-events: none; z-index: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    }
    .glow {
        background: radial-gradient(ellipse 60% 50% at 50% -10%, rgba(251,191,36,0.15), transparent);
    }
    .input-field {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        color: #fff;
        outline: none;
        transition: border-color 0.15s, background 0.15s;
    }
    .input-field::placeholder { color: rgba(255,255,255,0.25); }
    .input-field:focus {
        border-color: rgba(251,191,36,0.6);
        background: rgba(255,255,255,0.08);
    }
    .input-field:-webkit-autofill,
    .input-field:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px #18181b inset;
        -webkit-text-fill-color: #fff;
    }
    .btn-primary {
        width: 100%;
        padding: 0.85rem 1rem;
        background: #fbbf24;
        color: #111;
        font-size: 0.9rem;
        font-weight: 800;
        border-radius: 0.75rem;
        border: none;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
        letter-spacing: 0.01em;
    }
    .btn-primary:hover { background: #f59e0b; }
    .btn-primary:active { transform: scale(0.99); }
    .divider {
        display: flex; align-items: center; gap: 1rem;
        color: rgba(255,255,255,0.2); font-size: 0.75rem;
    }
    .divider::before, .divider::after {
        content: ''; flex: 1;
        height: 1px; background: rgba(255,255,255,0.08);
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .card { animation: fadeUp 0.4s ease both; }
</style>

<div class="login-wrap min-h-screen bg-gray-950 flex items-center justify-center px-4 relative overflow-hidden">

    {{-- Background effects --}}
    <div class="grain"></div>
    <div class="glow absolute inset-0"></div>
    <div class="absolute top-1/4 -left-32 w-64 h-64 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 -right-32 w-64 h-64 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md">

        {{-- Logo --}}
        <div class="card text-center mb-8" style="animation-delay: 0s">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                <div class="w-10 h-10 bg-amber-400 rounded-xl flex items-center justify-center group-hover:bg-amber-300 transition-colors">
                    <span class="text-gray-900 font-black text-base">E</span>
                </div>
                <span class="text-2xl font-black text-white tracking-tight">Shop</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="card bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8"
             style="animation-delay: 0.05s">

            <div class="mb-7">
                <h1 class="text-2xl font-black text-white mb-1">Welcome back</h1>
                <p class="text-sm text-gray-400">Sign in to your account to continue</p>
            </div>

            {{-- Session Status --}}
            @if(session('status'))
                <div class="mb-5 flex items-center gap-2.5 bg-green-500/10 border border-green-500/20 text-green-400 text-sm font-medium px-4 py-3 rounded-xl">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
                        Email address
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="you@example.com"
                           required autofocus autocomplete="username"
                           class="input-field @error('email') !border-red-500/60 @enderror">
                    @error('email')
                        <p class="mt-2 text-xs text-red-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="text-sm font-semibold text-gray-300">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs text-amber-400 hover:text-amber-300 font-medium transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <input id="password"
                           type="password"
                           name="password"
                           placeholder="••••••••"
                           required autocomplete="current-password"
                           class="input-field @error('password') !border-red-500/60 @enderror">
                    @error('password')
                        <p class="mt-2 text-xs text-red-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2.5">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-white/20 bg-white/5 text-amber-400 focus:ring-amber-400 focus:ring-offset-0">
                    <label for="remember_me" class="text-sm text-gray-400 cursor-pointer select-none">
                        Keep me signed in
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary">
                    Sign in to your account
                </button>
            </form>

            {{-- Divider --}}
            <div class="divider my-6">or</div>

            {{-- Register link --}}
            <p class="text-center text-sm text-gray-500">
                Don't have an account?
                <a href="{{ route('register') }}"
                   class="text-white font-semibold hover:text-amber-400 transition-colors ml-1">
                    Create one free →
                </a>
            </p>
        </div>

        {{-- Footer --}}
        <div class="card text-center mt-6" style="animation-delay: 0.1s">
            <p class="text-xs text-gray-600">
                Protected by SSL encryption &nbsp;·&nbsp;
                <a href="{{ route('home') }}" class="hover:text-gray-400 transition-colors">Back to store</a>
            </p>
        </div>
    </div>
</div>

</x-guest-layout>