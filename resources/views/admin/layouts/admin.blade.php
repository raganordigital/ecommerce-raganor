<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: true }">

<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-60' : 'w-16'"
           class="flex-shrink-0 bg-gray-950 text-white flex flex-col transition-all duration-200 min-h-screen sticky top-0">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 h-16 border-b border-white/10">
            <div class="w-8 h-8 bg-amber-400 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-gray-900 font-black text-sm">A</span>
            </div>
            <span x-show="sidebarOpen" class="font-black text-white text-lg tracking-tight">Admin</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                </svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            {{-- Section Label --}}
            <p x-show="sidebarOpen" class="text-xs font-bold tracking-widest uppercase text-gray-600 px-3 pt-5 pb-1">Catalogue</p>

            {{-- Products --}}
            <a href="{{ route('admin.products.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.products.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span x-show="sidebarOpen">Products</span>
            </a>

            {{-- Categories --}}
            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span x-show="sidebarOpen">Categories</span>
            </a>

            {{-- Section Label --}}
            <p x-show="sidebarOpen" class="text-xs font-bold tracking-widest uppercase text-gray-600 px-3 pt-5 pb-1">Sales</p>

            {{-- Orders --}}
            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.orders.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span x-show="sidebarOpen">Orders</span>
            </a>

            {{-- Section Label --}}
            <p x-show="sidebarOpen" class="text-xs font-bold tracking-widest uppercase text-gray-600 px-3 pt-5 pb-1">Content</p>

            {{-- Reviews --}}
            <a href="{{ route('admin.reviews.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs('admin.reviews.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span x-show="sidebarOpen">Reviews</span>
            </a>

            {{-- View Store --}}
            <div class="pt-4">
                <a href="{{ route('home') }}" target="_blank"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-500 hover:bg-white/5 hover:text-white transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span x-show="sidebarOpen">View Store</span>
                </a>
            </div>
        </nav>

        {{-- User + Logout --}}
        <div class="border-t border-white/10 p-3">
            <div x-show="sidebarOpen" class="flex items-center gap-3 px-3 py-2 mb-1">
                <div class="w-7 h-7 rounded-full bg-amber-400 flex items-center justify-center text-gray-900 font-black text-xs flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Bar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-10">
            <div class="flex items-center gap-4">
                {{-- Sidebar Toggle --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                <div class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-900">@yield('title', 'Dashboard')</span>
                </div>
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.create') }}"
                   class="hidden sm:inline-flex items-center gap-2 bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Product
                </a>
                <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-white font-black text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm font-medium px-4 py-3 rounded-xl">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm font-medium px-4 py-3 rounded-xl">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-medium px-4 py-3 rounded-xl">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>