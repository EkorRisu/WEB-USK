<!doctype html>
{{-- DEFAULT MODE: GELAP (DARK MODE) --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ isDark: localStorage.getItem('dark') !== 'false' }" :class="{ 'dark': isDark }">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AzurBook</title>

    {{-- 2. HAPUS SCRIPT TAILWIND CDN --}}

    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- 3. TAMBAHKAN ALPINE.JS --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- 4. UBAH PATH VITE KE app.css (PERBAIKAN UTAMA) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        /* CSS hover-slide Anda (Tidak diubah) */
        .hover-slide {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .hover-slide::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: #000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: -1;
        }

        .hover-slide:hover::before {
            transform: translateX(0);
        }

        .hover-slide:hover {
            color: white;
        }

        /* Style untuk menyembunyikan elemen Alpine sebelum dimuat */
        [x-cloak] {
            display: none !important;
        }

        /* === STYLING GOOGLE TRANSLATE CUSTOM === */
        /* Menyembunyikan Top Bar Google Translate */
        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        /* Kontainer Dropdown */
        #google_translate_element {
            z-index: 10;
        }

        /* Targetkan dropdown Google Translate */
        #google_translate_element .goog-te-gadget-simple {
            background-color: transparent !important;
            border: none !important;
            padding: 0px 0px 0px 4px !important;
            line-height: 1.5 !important;
            font-size: 0.875rem;
            min-width: 0 !important;
            width: auto !important;
        }

        /* 1. Menyembunyikan teks "Pilih Bahasa" */
        #google_translate_element .goog-te-gadget-simple span {
            display: none !important;
        }

        /* 2. Menyembunyikan ikon panah dan gambar bawaan */
        #google_translate_element .goog-te-menu-value span:nth-child(3),
        #google_translate_element .goog-te-menu-value img {
            display: none !important;
        }

        /* 3. Menyesuaikan posisi dropdown agar sedikit menumpuk di ikon */
        #google_translate_element {
            margin-right: 0 !important;
        }
    </style>
</head>

{{-- 6. Bersihkan body. Style-nya sudah di-handle oleh app.css --}}

    <body>
        <div id="app">

            {{-- 7. Modifikasi Navbar agar support light/dark --}}
            <nav class="bg-white dark:bg-black fixed top-0 left-0 w-full z-50 shadow dark:shadow-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <a href="/" class="text-black dark:text-white font-bold text-lg">
                            Azur BookStore
                        </a>

                        @if (Route::has('login'))
                        <div class="flex items-center space-x-4">

                            {{-- 🛑 START: CONTAINER NAVIGASI KANAN (TERMASUK TRANSLATOR IKON) 🛑 --}}

                            <div class="flex items-center space-x-2">

                                {{-- Tombol Toggle Dark Mode --}}
                                <button @click="isDark = !isDark; localStorage.setItem('dark', isDark)" type="button"
                                    class="p-2 rounded-full text-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                                    <svg x-show="!isDark" class="w-6 h-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    <svg x-show="isDark" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                        </path>
                                    </svg>
                                </button>



                                {{-- 🌐 ICON TRANSLATOR DAN WIDGET 🌐 --}}
                                <div class="flex items-center relative hidden sm:flex">
                                    {{-- Ikon Globe SVG (Dasar) --}}
                                    <svg class="w-5 h-5 text-gray-800 dark:text-gray-300 absolute left-0 z-10 pointer-events-none"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h18m-14.25-8.25L21 7.5m0 0L16.5 12M21 7.5H3">
                                        </path>
                                    </svg>
                                    {{-- Widget Translate (Teks disembunyikan oleh CSS) --}}
                                    {{-- Margin negatif untuk menempatkan dropdown di atas ikon --}}
                                    <div id="google_translate_element" class="text-sm -ml-2"></div>
                                </div>

                            </div>

                            {{-- 🛑 END: CONTAINER NAVIGASI KANAN 🛑 --}}

                            @auth
                            @php
                            $role = Auth::user()->role ?? 'user';
                            @endphp

                            @if ($role === 'admin')
                            {{-- 9. Modifikasi Tombol Auth --}}
                            <a href="{{ route('admin.dashboard') }}"
                                class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white text-sm px-4 py-2 rounded transition hover:bg-gray-300 dark:hover:bg-gray-700">
                                {{ __('app.dashboard') }}
                            </a>
                            @elseif ($role === 'user')
                            <a href="{{ route('user.dashboard') }}"
                                class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white text-sm px-4 py-2 rounded transition hover:bg-gray-300 dark:hover:bg-gray-700">
                                {{ __('app.dashboard') }}
                            </a>
                            @else
                            <a href="{{ url('/dashboard') }}"
                                class="bg-gray-200 dark:bg-gray-600 text-black dark:text-white text-sm px-4 py-2 rounded transition hover:bg-gray-300 dark:hover:bg-gray-700">
                                {{ __('app.dashboard') }}
                            </a>
                            @endif
                            @else
                            {{-- 10. Modifikasi Tombol Guest (Login) --}}
                            <button id="loginButton" class="border border-gray-800 text-gray-800 hover:bg-gray-800 hover:text-white 
                                        dark:border-white dark:text-white dark:hover:bg-white dark:hover:text-black 
                                        text-sm px-4 py-2 rounded transition">
                                {{ __('app.login') }}
                            </button>

                            @if (Route::has('register'))
                            {{-- 11. Modifikasi Tombol Guest (Register) --}}
                            <button id="registerButton" class="border border-gray-800 text-gray-800 hover:bg-gray-800 hover:text-white 
                                        dark:border-white dark:text-white dark:hover:bg-white dark:hover:text-black 
                                        text-sm px-4 py-2 rounded transition">
                                {{ __('app.register') }}
                            </button>
                            @endif
                            @endauth
                        </div>
                        @endif
                    </div>
                </div>
            </nav>


            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>

            <main>
                @yield('content')

                {{-- 12. Modifikasi Background Modal --}}
                <div id="loginModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md relative text-black dark:text-white">
                        <button id="closeLoginModal"
                            class="absolute top-2 right-3 text-gray-600 dark:text-gray-300 hover:text-red-500 text-2xl">&times;</button>

                        <h2 class="text-2xl font-bold mb-4 text-center">{{ __('app.login_title') }}</h2>

                        @if(session('status'))
                        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('status') }}</div>
                        @endif
                        @if(session('registration_success'))
                        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('registration_success') }}
                        </div>
                        @endif
                        @if(session('error'))
                        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="block mb-1">{{ __('app.email') }}</label>
                                {{-- 13. Modifikasi Input Form --}}
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 rounded text-black dark:text-white">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block mb-1">{{ __('app.password') }}</label>
                                <input type="password" id="password" name="password" required
                                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 rounded text-black dark:text-white">
                            </div>

                            <button type="submit" class="w-full bg-black dark:bg-gray-200 hover:bg-gray-800 dark:hover:bg-white 
                                    text-white dark:text-black font-semibold py-2 px-4 rounded transition">
                                {{ __('app.login') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- 14. Modifikasi Background Modal --}}
                <div id="registerModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md relative text-black dark:text-white">
                        <button id="closeRegisterModal"
                            class="absolute top-2 right-3 text-gray-600 dark:text-gray-300 hover:text-red-500 text-2xl">&times;</button>

                        <h2 class="text-2xl font-bold mb-4 text-center">{{ __('app.register_title') }}</h2>

                        @if(session('success'))
                        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any() && request()->is('register'))
                        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="block mb-1">{{ __('app.name_full') }}</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 rounded text-black dark:text-white">
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block mb-1">{{ __('app.email') }}</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 rounded text-black dark:text-white">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block mb-1">{{ __('app.password') }}</label>
                                <input type="password" id="password" name="password" required
                                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 rounded text-black dark:text-white">
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="block mb-1">{{ __('app.password_confirmation')
                                    }}</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 rounded text-black dark:text-white">
                            </div>

                            <button type="submit" class="w-full bg-black dark:bg-gray-200 hover:bg-gray-800 dark:hover:bg-white 
                                    text-white dark:text-black font-semibold py-2 px-4 rounded transition">
                                {{ __('app.register') }}
                            </button>
                        </form>
                    </div>
                </div>

            </main>
        </div>

        @stack('scripts')

        {{-- Script Modal (Tidak diubah) --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');

        document.getElementById('loginButton')?.addEventListener('click', () => {
            loginModal?.classList.remove('hidden');
        });
        document.getElementById('closeLoginModal')?.addEventListener('click', () => {
            loginModal?.classList.add('hidden');
        });
        document.getElementById('registerButton')?.addEventListener('click', () => {
            registerModal?.classList.remove('hidden');
        });
        document.getElementById('closeRegisterModal')?.addEventListener('click', () => {
            registerModal?.classList.add('hidden');
        });
        @if ($errors->any())
            @if (request()->is('register'))
                registerModal?.classList.remove('hidden');
            @else
                loginModal?.classList.remove('hidden');
            @endif
        @endif
        @if(session('registration_success'))
            registerModal?.classList.add('hidden');
            loginModal?.classList.remove('hidden');
        @endif
        @if(session('error'))
            loginModal?.classList.remove('hidden');
        @endif
    });
        </script>

        {{-- SCRIPT GOOGLE TRANSLATE (Ditempatkan di akhir body) --}}
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
