<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vite (if applicable) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-700 bg-cover bg-center bg-fixed bg-no-repeat backdrop-blur-md">
    <div id="app">
        <!-- Navbar -->
        <!-- Navbar -->
        <nav class="bg-black fixed w-full z-50 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="{{ url('/admin/dashboard') }}" class="text-white font-bold text-lg">
                        AzurBook Store ( {{ Auth::user()->name }} )
                    </a>

                    <div class="flex items-center space-x-6">
                        <a href="{{ url('/user/about') }}"
                            class="flex items-center px-4 py-2 bg-black text-white border-2 border-white font-semibold rounded-lg hover:bg-blue-600 hover:border-blue-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            About Us
                        </a>

                        <!-- Logout Button -->
                        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center px-4 py-2 bg-black text-white border-2 border-white font-semibold rounded-lg hover:bg-red-600 hover:border-red-600 transform hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                            Logout
                        </button>
                    </div>
                </div>
        </nav>

        <!-- Hidden logout form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>


        <!-- Hidden logout form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- Page Content -->
        <main class="pt-16 min-h-screen">
            @yield('content')
        </main>
    </div>

    <!-- Dropdown toggle script -->
    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            menu.classList.toggle('hidden');
        }

        // Optional: Close dropdown when clicking outside
        window.addEventListener('click', function (e) {
            const btn = document.getElementById('dropdownButton');
            const menu = document.getElementById('dropdownMenu');
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>