<!doctype html>
{{-- DEFAULT MODE: GELAP (DARK MODE) --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ isDark: localStorage.getItem('dark') !== 'false' }"
      :class="{ 'dark': isDark }">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AzurCoffee Admin Panel - Kelola menu dan transaksi coffee shop">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Enhanced hamburger menu script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced hamburger toggle that works with or without Alpine.js
            const hamburgerBtn = document.querySelector('[aria-label="Toggle menu"]');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (hamburgerBtn && mobileMenu) {
                hamburgerBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Hamburger clicked'); // Debug log
                    
                    // Toggle visibility
                    if (mobileMenu.style.display === 'none' || mobileMenu.style.display === '') {
                        mobileMenu.style.display = 'block';
                        mobileMenu.classList.remove('hidden');
                    } else {
                        mobileMenu.style.display = 'none';
                        mobileMenu.classList.add('hidden');
                    }
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!hamburgerBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                        mobileMenu.style.display = 'none';
                        mobileMenu.classList.add('hidden');
                    }
                });
            } else {
                console.log('Hamburger elements not found'); // Debug log
            }
        });
    </script>

        {{-- DEFAULT MODE: GELAP (DARK MODE) --}}
    <script>
        // Default ke dark mode kecuali user explicitly set ke false
        if (localStorage.getItem('dark') === 'false') {
          document.documentElement.classList.remove('dark');
          localStorage.setItem('dark', 'false');
        } else {
          document.documentElement.classList.add('dark');
          localStorage.setItem('dark', 'true'); 
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }

        /* FITUR GOOGLE TRANSLATE - TIDAK ADA DI REQUIREMENTS */
        /* === STYLING GOOGLE TRANSLATE CUSTOM === */
     .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        #google_translate_element {
            z-index: 10;
        }

        #google_translate_element .goog-te-gadget-simple {
            background-color: transparent !important;
            border: none !important;
            padding: 0px 0px 0px 4px !important;
            line-height: 1.5 !important;
            font-size: 0.875rem;
            min-width: 0 !important;
            width: auto !important;
        }

        #google_translate_element .goog-te-gadget-simple span {
            display: none !important;
        }

        #google_translate_element .goog-te-menu-value span:nth-child(3),
        #google_translate_element .goog-te-menu-value img {
            display: none !important;
        }

        #google_translate_element {
            margin-right: 0 !important;
        } 
    </style>
</head>

<body class="min-h-screen font-sans antialiased">
    <div id="app">
        
        {{-- NAVBAR: FIXED di atas --}}
        <nav class="bg-yellow-900 dark:bg-gray-900 fixed top-0 left-0 w-full z-50 shadow dark:shadow-gray-700" x-data="{ isOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <a href="{{ url('/admin/dashboard') }}" class="text-white font-bold text-lg flex items-center gap-2">
  
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/lgo.png') }}" alt="AzurCoffee" class="h-8 w-auto" loading="eager" decoding="async">
                            <span>( {{ Auth::user()->name ?? 'Admin' }} )</span>
                        </div>
                    </a>

                    <div class="hidden md:flex items-center gap-4">
                        {{-- START: KELOMPOK FITUR AKSESORIS (Translator, Lang, Dark Mode) --}}
                        <div class="flex items-center space-x-2">

                            {{-- ICON TRANSLATOR DAN WIDGET --}}
                            @if(config('fitur.translate'))
                            <div class="hidden sm:flex items-center relative">
                                <svg class="w-5 h-5 text-gray-800 dark:text-gray-300 absolute left-0 z-10 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h18m-14.25-8.25L21 7.5m0 0L16.5 12M21 7.5H3"></path>
                                </svg>
                                <div id="google_translate_element" class="text-sm -ml-2"></div>
                            </div>
                            @endif
                          

                            {{-- Tombol Toggle --}}
                            @if(config('fitur.dark_mode'))
                            <button @click="isDark = !isDark; localStorage.setItem('dark', isDark)" 
                                    type="button" 
                                    aria-label="Toggle dark mode"
                                    class="p-2 rounded-full text-gray-800 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 focus:outline-none">
                                <svg x-show="!isDark" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <svg x-show="isDark" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </button>
                            @endif
                        </div>
                        {{-- END: KELOMPOK FITUR AKSESORIS --}}

                        <div class="w-px h-6 bg-gray-300 dark:bg-gray-700"></div> {{-- Separator --}}
                            
                        <div class="flex items-center gap-4">
                            @if(config('fitur.about'))
                            <a href="{{ url('/user/about') }}"
                                class="flex items-center px-4 py-2 bg-transparent text-gray-800 border-2 border-gray-400 dark:bg-black dark:text-white dark:border-white font-semibold rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 dark:hover:bg-blue-600 dark:hover:border-blue-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                {{ __('app.about_us') }}
                            </a>
                            @endif

                            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="flex items-center px-4 py-2 bg-transparent text-gray-800 border-2 border-gray-400 dark:bg-black dark:text-white dark:border-white font-semibold rounded-lg hover:bg-red-600 hover:text-white hover:border-red-600 dark:hover:bg-red-600 dark:hover:border-red-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                {{ __('app.logout') }}
                            </button>
                        </div>
                    </div>
                   
                    <div class="flex md:hidden">
                        <button @click="isOpen = !isOpen" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-white bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
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
            
            <div id="mobile-menu"
                 x-show="isOpen" 
                 x-cloak 
                 @click.away="isOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 style="display: none;"
                 class="md:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg">
                <div class="px-3 pt-2 pb-3 space-y-2">
                    <!-- Admin Navigation Links -->
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-300 dark:border-gray-600 my-2"></div>
                    
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="block px-3 py-2 rounded-md text-base font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                        🚪 {{ __('app.logout') }}
                    </a>

                    @if(config('fitur.dark_mode'))
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <button @click="isDark = !isDark; localStorage.setItem('dark', isDark)" 
                                type="button" 
                                class="w-full flex justify-start items-center px-3 py-2 rounded-md text-base font-medium text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                            
                            <svg x-show="!isDark" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg x-show="isDark" x-cloak class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            
                            <span>{{ __('app.change_theme') }}</span>
                        </button>
                    </div>
                    @endif
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

    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }
        window.addEventListener('click', function (e) {
            const btn = document.getElementById('dropdownButton');
            const menu = document.getElementById('dropdownMenu');
            if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

    
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false 
            }, 'google_translate_element');
        }
    </script>

    <script type="text/javascript" 
        src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>
    
    @stack('scripts')
</body>
</html>
