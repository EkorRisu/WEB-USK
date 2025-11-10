@extends('layouts.navbar')

@section('content')
<section
    class="min-h-screen overflow-hidden bg-[url(https://c0.wallpaperflare.com/preview/469/70/385/background-book-bookcase-books.jpg)] dark:bg-[url(https://kuliahdimana.id/public/news/50be12618114819f4e43f5e31163c98a.jpg)] bg-cover bg-center bg-fixed bg-no-repeat">
    <div
        class="min-h-screen bg-gradient-to-b from-black/70 to-black/50 px-4 md:px-64 flex flex-col justify-center items-center">
        <!-- Hero Content -->
        <div class="text-center space-y-6">
            <h1 class="text-4xl md:text-7xl font-bold text-white mt-20 animate-fade-in">
                Welcome To Azur Book
            </h1>
            <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto">
                Discover a world of knowledge through our vast collection of books and resources
            </p>

            <!-- Buttons -->
            <div class="flex gap-4 justify-center mt-8">
                <button id="welcomeLoginBtn"
                        class="hover-slide px-8 py-3 bg-white text-black rounded-lg font-semibold
                               border border-black transition-all duration-300">
                    Login
                </button>

                <button id="welcomeRegisterBtn"
                        class="hover-slide px-8 py-3 bg-white text-black rounded-lg font-semibold
                               border border-black transition-all duration-300">
                    Register
                </button>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
            <div class="bg-white/10 backdrop-blur-sm p-6 rounded-lg text-white">
                <h3 class="text-xl font-semibold mb-2">Vast Collection</h3>
                <p>Access thousands of books across various genres</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm p-6 rounded-lg text-white">
                <h3 class="text-xl font-semibold mb-2">Easy Access</h3>
                <p>Read anywhere, anytime with our digital library</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm p-6 rounded-lg text-white">
                <h3 class="text-xl font-semibold mb-2">Community</h3>
                <p>Join our reading community and share your thoughts</p>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        
        // Welcome page buttons
        document.getElementById('welcomeLoginBtn')?.addEventListener('click', () => {
            loginModal?.classList.remove('hidden');
        });
        
        document.getElementById('welcomeRegisterBtn')?.addEventListener('click', () => {
            registerModal?.classList.remove('hidden');
        });
    });
</script>
@endpush
@endsection