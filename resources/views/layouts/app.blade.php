<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'E-Shop - Your Online Store')</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Shop the latest products at E-Shop. Find everything you need with fast shipping and great prices.')">
    <meta name="keywords" content="@yield('meta_keywords', 'ecommerce, online shopping, products, store')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-300">
                    <p>&copy; {{ date('Y') }} E-Shop. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
