<x-guest-layout>
<style>
    @import url('https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap');
    .auth-wrap * { font-family: 'Figtree', sans-serif; box-sizing: border-box; }
    .grain { position: fixed; inset: 0; pointer-events: none; z-index: 0; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E"); }
    .glow { background: radial-gradient(ellipse 60% 50% at 50% -10%, rgba(251,191,36,0.15), transparent); }
    .auth-input { width: 100%; padding: 0.75rem 1rem; font-size: 0.875rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; color: #fff; outline: none; transition: border-color 0.15s, background 0.15s; }
    .auth-input::placeholder { color: rgba(255,255,255,0.25); }
    .auth-input:focus { border-color: rgba(251,191,36,0.6); background: rgba(255,255,255,0.08); }
    .auth-input:-webkit-autofill, .auth-input:-webkit-autofill:focus { -webkit-box-shadow: 0 0 0 1000px #18181b inset; -webkit-text-fill-color: #fff; }
    .auth-btn { width: 100%; padding: 0.85rem 1rem; background: #fbbf24; color: #111; font-size: 0.9rem; font-weight: 800; border-radius: 0.75rem; border: none; cursor: pointer; transition: background 0.15s, transform 0.1s; }
    .auth-btn:hover { background: #f59e0b; }
    .auth-btn:active { transform: scale(0.99); }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .fade-up { animation: fadeUp 0.4s ease both; }
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
        <div class="fade-up bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8" style="animation-delay:0.05s">
            {{-- Shield icon --}}
            <div class="w-12 h-12 bg-amber-400/10 border border-amber-400/20 rounded-2xl flex items-center justify-center mb-5">
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="mb-6">
                <h1 class="text-2xl font-black text-white mb-2">Confirm your password</h1>
                <p class="text-sm text-gray-400 leading-relaxed">
                    This is a secure area. Please re-enter your password to continue.
                </p>
            </div>
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">Password</label>
                    <input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" class="auth-input {{ $errors->get('password') ? 'border-red-500/50' : '' }}">
                    @error('password')<p class="mt-1.5 text-xs text-red-400 flex items-center gap-1"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>@enderror
                </div>
                <div class="pt-1"><button type="submit" class="auth-btn">Confirm &amp; continue</button></div>
            </form>
        </div>
        <div class="fade-up text-center mt-6" style="animation-delay:0.1s">
            <p class="text-xs text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-gray-400 transition-colors">← Back to store</a>
            </p>
        </div>
    </div>
</div>
</x-guest-layout>