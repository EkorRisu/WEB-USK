<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ isDark: localStorage.getItem('dark') === 'true' }"
      :class="{ 'dark': isDark }">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - User</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        if (localStorage.getItem('dark') === 'true' || 
           (!('dark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
          localStorage.setItem('dark', 'true'); 
        } else {
          document.documentElement.classList.remove('dark');
          localStorage.setItem('dark', 'false');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

       /* FITUR GOOGLE TRANSLATE - TIDAK ADA DI REQUIREMENTS
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
        } */
        
    </style>
</head>

<body class="font-sans antialiased">
    <div id="app">
        
        <nav class="bg-white dark:bg-black fixed w-full z-50 shadow dark:shadow-gray-700" x-data="{ isOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ url('/user/dashboard') }}" class="text-black dark:text-white font-bold text-lg">
                            AzurBook Store ( {{ Auth::user()->name }} )
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-6">

                        {{-- START: KELOMPOK FITUR AKSESORIS --}}
                        <div class="flex items-center space-x-2">

                            {{-- FITUR GOOGLE TRANSLATE - TIDAK ADA DI REQUIREMENTS --}}
                            {{-- 🌐 ICON TRANSLATOR DAN WIDGET 🌐 --}}
                            <div class="flex items-center relative hidden sm:flex">
                                <svg class="w-5 h-5 text-gray-800 dark:text-gray-300 absolute left-0 z-10 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h18m-14.25-8.25L21 7.5m0 0L16.5 12M21 7.5H3"></path>
                                </svg>
                                <div id="google_translate_element" class="text-sm -ml-2"></div>
                            </div>
                        </div>
                        {{-- END: KELOMPOK FITUR AKSESORIS --}}

                        {{-- Tombol Toggle --}}
                        <button @click="isDark = !isDark; localStorage.setItem('dark', isDark)" 
                                type="button" 
                                class="p-2 rounded-full text-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                            <svg x-show="!isDark" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <svg x-show="isDark" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                        
                        {{-- Link --}}
                        <a href="{{ url('/user/about') }}"
                            class="flex items-center px-4 py-2 bg-transparent text-gray-800 border-2 border-gray-400 dark:bg-black dark:text-white dark:border-white font-semibold rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 dark:hover:bg-blue-600 dark:hover:border-blue-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            {{ __('app.about_us') }}
                        </a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center px-4 py-2 bg-transparent text-gray-800 border-2 border-gray-400 dark:bg-black dark:text-white dark:border-white font-semibold rounded-lg hover:bg-red-600 hover:text-white hover:border-red-600 dark:hover:bg-red-600 dark:hover:border-red-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            {{ __('app.logout') }}
                        </a>
                    </div>
                    
                    {{-- Tombol Mobile --}}
                    <div class="flex md:hidden">
                        <button @click="isOpen = !isOpen" type="button"
                            class="text-gray-800 dark:text-white hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-gray-600"
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
            
            {{-- Menu Mobile Dropdown --}}
            <div x-show="isOpen" 
                 x-cloak 
                 @click.away="isOpen = false"
                 x-transition
                 class="md:hidden bg-white dark:bg-black border-t border-gray-200 dark:border-gray-700">
                <div class="px-3 pt-2 pb-3 space-y-2">
                    <a href="{{ url('/user/about') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        {{ __('app.about_us') }}
                    </a>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        {{ __('app.logout') }}
                    </a>

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
                </div>
            </div>
        </nav>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <main class="pt-16"> {{-- pt-16 karena navbar user h-16 --}}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    
    {{-- FITUR GOOGLE TRANSLATE - TIDAK ADA DI REQUIREMENTS --}}
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
    
</body>

</html>