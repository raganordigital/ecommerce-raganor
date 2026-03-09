<x-guest-layout>
<style>
    @import url('https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap');
    .auth-wrap * { font-family: 'Figtree', sans-serif; box-sizing: border-box; }
    .grain { position: fixed; inset: 0; pointer-events: none; z-index: 0; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E"); }
    .glow { background: radial-gradient(ellipse 60% 50% at 50% -10%, rgba(251,191,36,0.15), transparent); }
    .auth-btn { width: 100%; padding: 0.85rem 1rem; background: #fbbf24; color: #111; font-size: 0.9rem; font-weight: 800; border-radius: 0.75rem; border: none; cursor: pointer; transition: background 0.15s, transform 0.1s; font-family: 'Figtree', sans-serif; }
    .auth-btn:hover { background: #f59e0b; }
    .auth-btn:active { transform: scale(0.99); }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .fade-up { animation: fadeUp 0.4s ease both; }
    @keyframes pulse-ring { 0%,100%{opacity:.4;transform:scale(1)} 50%{opacity:.15;transform:scale(1.15)} }
    .pulse-ring { animation: pulse-ring 2.5s ease-in-out infinite; }
</style>
<div class="auth-wrap min-h-screen bg-gray-950 flex items-center justify-center px-4 relative overflow-hidden">
    <div class="grain"></div>
    <div class="glow absolute inset-0"></div>
    <div class="absolute top-1/4 -left-32 w-64 h-64 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 -right-32 w-64 h-64 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10 w-full max-w-md">
        <div class="fade-up text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                <div class="w-10 h-10 bg-amber-400 rounded-xl flex items-center justify-center group-hover:bg-amber-300 transition-colors">
                    <span class="text-gray-900 font-black text-base">E</span>
                </div>
                <span class="text-2xl font-black text-white tracking-tight">Shop</span>
            </a>
        </div>
        <div class="fade-up bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 text-center" style="animation-delay:0.05s">

            {{-- Animated envelope icon --}}
            <div class="relative w-20 h-20 mx-auto mb-6">
                <div class="pulse-ring absolute inset-0 bg-amber-400/20 rounded-full"></div>
                <div class="relative w-20 h-20 bg-amber-400/10 border border-amber-400/30 rounded-full flex items-center justify-center">
                    <svg class="w-9 h-9 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-black text-white mb-2">Check your inbox</h1>
            <p class="text-sm text-gray-400 leading-relaxed mb-6">
                We sent a verification link to your email address.<br>
                Click it to activate your account and start shopping.
            </p>

            {{-- Success message --}}
            @if(session('status') == 'verification-link-sent')
                <div class="flex items-center gap-2.5 bg-green-500/10 border border-green-500/20 text-green-400 text-sm font-medium px-4 py-3 rounded-xl mb-6 text-left">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    A new verification link has been sent to your email.
                </div>
            @endif

            {{-- Resend --}}
            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button type="submit" class="auth-btn">Resend verification email</button>
            </form>

            {{-- Tips --}}
            <div class="bg-white/3 border border-white/5 rounded-xl p-4 text-left mb-6">
                <p class="text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wide">Didn't get it?</p>
                <ul class="space-y-1.5 text-xs text-gray-500">
                    <li class="flex items-start gap-2"><span class="text-amber-400/60 mt-0.5">•</span>Check your spam or junk folder</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400/60 mt-0.5">•</span>Make sure the email address is correct</li>
                    <li class="flex items-start gap-2"><span class="text-amber-400/60 mt-0.5">•</span>Wait a few minutes and try again</li>
                </ul>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-400 transition-colors font-medium">
                    Sign out of this account
                </button>
            </form>
        </div>
    </div>
</div>
</x-guest-layout>