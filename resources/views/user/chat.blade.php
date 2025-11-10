@extends('layouts.user')

@section('content')
{{-- Card utama dibuat rounded-xl, shadow-lg, dan mendukung dark mode --}}
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg mt-10">
    
    {{-- [BARU] Header Card untuk Judul --}}
    <div class="border-b border-gray-200 dark:border-gray-700 p-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">💬 Chat dengan Admin</h2>
    </div>

    {{-- Badan Card --}}
    <div class="p-4">
        {{-- [DIUBAH] Box Pesan: diberi ID, background, dan padding --}}
        <div id="chat-box" class="h-[400px] overflow-y-auto space-y-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
            
            @forelse ($messages as $msg)
                
                @if ($msg->sender_id == auth()->id())
                    {{-- [DIUBAH] Pesan Terkirim (Pengguna) - Bubble Biru Modern --}}
                    <div class="flex justify-end">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="inline-block bg-blue-500 dark:bg-blue-600 text-white px-4 py-2 rounded-t-xl rounded-l-xl">
                                <p class="text-sm">{{ $msg->message }}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-right mt-1">{{ $msg->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @else
                    {{-- [DIUBAH] Pesan Diterima (Admin) - Bubble Abu-abu Modern --}}
                    <div class="flex justify-start">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-t-xl rounded-r-xl">
                                <p class="text-sm">{{ $msg->message }}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-left mt-1">{{ $msg->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @endif

            @empty
                {{-- [DIUBAH] Pesan Kosong dibuat lebih rapi di tengah --}}
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 dark:text-gray-400">Belum ada pesan. Mulai percakapan!</p>
                </div>
            @endforelse

        </div>
    </div>

    {{-- [BARU] Footer Card untuk Form Input --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <form action="{{ route('user.chat.store') }}" method="POST" class="flex gap-2">
            @csrf
            
            {{-- [TETAP] Fungsionalitas receiver_id tidak diubah, sesuai permintaan --}}
            <input type="hidden" name="receiver_id" value="{{ \App\Models\User::where('role', 'admin')->first()->id }}">
            
            {{-- [DIUBAH] Input text dibuat rounded-full dan mendukung dark mode --}}
            <input type="text" name="message" 
                   class="w-full border border-gray-300 rounded-full p-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" 
                   placeholder="Ketik pesan..." 
                   autocomplete="off" 
                   required>
            
            {{-- [DIUBAH] Tombol kirim dibuat rounded-full dan ikon di mobile --}}
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-5 py-2 rounded-full transition-colors duration-200 flex items-center justify-center">
                <span class="hidden sm:inline">Kirim</span>
                <span class="sm:hidden">➤</span> {{-- Ikon untuk mobile --}}
            </button>
        </form>
    </div>

</div>

{{-- [BARU] JavaScript untuk auto-scroll ke pesan terakhir --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.getElementById('chat-box');
        // Set scroll position ke paling bawah
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection