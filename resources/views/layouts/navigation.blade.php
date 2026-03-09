<nav x-data="{ open: false, searchOpen: false }" class="bg-white sticky top-0 z-50 shadow-sm">

    <!-- Top Announcement Bar -->
    <div class="bg-gray-900 text-white text-xs text-center py-2 px-4 tracking-wide">
        🚚 Free shipping on orders over $50 &nbsp;·&nbsp; Use code <span class="font-semibold text-yellow-400">SAVE10</span> for 10% off
    </div>

    <!-- Main Nav -->
    <div class="border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center group-hover:bg-gray-700 transition-colors">
                        <span class="text-white font-black text-sm">E</span>
                    </div>
                    <span class="text-xl font-black text-gray-900 tracking-tight">Shop</span>
                </a>

                <!-- Center Nav Links (desktop) -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Home
                    </a>
                    <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Products
                    </a>
                    <a href="{{ route('about') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('about') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        About
                    </a>
                    <a href="{{ route('contact') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('contact') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Contact
                    </a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-1">

                    <!-- Search Toggle (desktop) -->
                    <button @click="searchOpen = !searchOpen" class="hidden md:flex p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <!-- Wishlist -->
                    <a href="{{ route('wishlist.index') }}" class="">
                        <livewire:wishlist.wishlist-counter />
                    </a>

                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="">
                        <livewire:cart.cart-counter />
                    </a>

                    <!-- Auth -->
                    @auth
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 ml-1 pl-3 pr-2 py-1.5 rounded-lg border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                                <div class="w-6 h-6 rounded-full bg-gray-900 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden lg:block max-w-24 truncate">{{ Auth::user()->name }}</span>
                                <svg class="h-3.5 w-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Signed in as</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <x-dropdown-link :href="route('orders.index')" class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    My Orders
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('wishlist.index')" class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    Wishlist
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile
                                </x-dropdown-link>

                                @if(Auth::user()->hasRole('admin'))
                                <div class="border-t border-gray-100 my-1"></div>
                                <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center gap-2 text-indigo-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                                    </svg>
                                    Admin Dashboard
                                </x-dropdown-link>
                                @endif
                            </div>

                            <div class="border-t border-gray-100 py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-2 text-red-600 hover:text-red-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Log Out
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                    @else
                    <div class="hidden sm:flex items-center gap-2 ml-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition-colors">
                            Register
                        </a>
                    </div>
                    @endauth

                    <!-- Mobile Hamburger -->
                    <button @click="open = !open" class="md:hidden ml-1 p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg x-show="!open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar (slides down on desktop) -->
    <div x-show="searchOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="hidden md:block border-b border-gray-100 bg-gray-50" style="display: none;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <form method="GET" action="{{ route('products.index') }}" class="flex gap-2 max-w-xl mx-auto">
                <input type="text" name="search" placeholder="Search for products..." value="{{ request('search') }}" autofocus class="flex-1 px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white">
                <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    Search
                </button>
                <button type="button" @click="searchOpen = false" class="px-3 py-2 text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded-lg transition-colors text-sm">
                    Cancel
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-gray-100 bg-white" style="display: none;">

        <!-- Mobile Search -->
        <div class="px-4 py-3 border-b border-gray-100">
            <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 bg-gray-50">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Mobile Nav Links -->
        <div class="px-2 py-2 space-y-0.5">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('home') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Home
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('products.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Products
            </a>
            <a href="{{ route('about') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                About
            </a>
            <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                Contact
            </a>
        </div>

        <!-- Mobile Auth -->
        <div class="border-t border-gray-100 px-2 py-2">
            @auth
            <div class="px-3 py-2 mb-1">
                <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                My Orders
            </a>
            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                Wishlist
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                Profile
            </a>
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm text-indigo-600 font-medium hover:bg-indigo-50 rounded-lg">
                Admin Dashboard
            </a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                    Log Out
                </button>
            </form>
            @else
            <div class="flex gap-2 px-1 py-1">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Login
                </a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-700">
                    Register
                </a>
            </div>
            @endauth
        </div>
    </div>
</nav>
