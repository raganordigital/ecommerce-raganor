<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-4 border-b">
                <h1 class="text-xl font-bold text-gray-800">Admin Panel</h1>
                <p class="text-sm text-gray-600 mt-1">{{ auth()->user()->name }}</p>
            </div>

            <nav class="p-4">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 font-semibold' : '' }}">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>

                    <!-- Products -->
                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.products.*') ? 'bg-gray-100 font-semibold' : '' }}">
                        <i class="fas fa-box w-5"></i>
                        <span class="ml-3">Products</span>
                    </a>

                    <!-- Categories -->
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 font-semibold' : '' }}">
                        <i class="fas fa-tags w-5"></i>
                        <span class="ml-3">Categories</span>
                    </a>

                    <!-- Orders -->
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-100 font-semibold' : '' }}">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span class="ml-3">Orders</span>
                    </a>
                </div>

                <!-- Logout Form -->
                <div class="mt-8 pt-4 border-t">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 rounded-lg hover:bg-red-50">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="ml-3">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
