<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - User</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#1e1e28d9] font-sans antialiased">
    <div id="app">
        <nav class="bg-black fixed w-full z-50 shadow" x-data="{ isOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ url('/user/dashboard') }}" class="text-white font-bold text-lg">
                            AzurBook Store ( {{ Auth::user()->name }} )
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ url('/user/about') }}"
                            class="flex items-center px-4 py-2 bg-black text-white border-2 border-white font-semibold rounded-lg hover:bg-blue-600 hover:border-blue-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            About Us
                        </a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center px-4 py-2 bg-black text-white border-2 border-white font-semibold rounded-lg hover:bg-red-600 hover:border-red-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            Logout
                        </a>
                    </div>
                    <div class="flex md:hidden">
                        <button @click="isOpen = !isOpen" type="button"
                            class="text-white hover:text-gray-300 focus:outline-none focus:text-gray-300"
                            aria-label="Toggle menu">
                            <svg x-show="!isOpen" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="isOpen" x-cloak class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div x-show="isOpen" 
                 x-cloak 
                 @click.away="isOpen = false"
                 x-transition
                 class="md:hidden bg-black border-t border-gray-700">
                <div class="px-3 pt-2 pb-3 space-y-2">
                    <a href="{{ url('/user/about') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-gray-700">
                        About Us
                    </a>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-gray-700">
                        Logout
                    </a>
                </div>
            </div>
        </nav>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <main class="pt-20">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
</body>

</html>