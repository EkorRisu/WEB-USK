@extends('layouts.admin') {{-- Ganti jika kamu pakai layout lain --}}

@section('content')
    <div class="container mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-4">Daftar Akun User</h1>

        <p class="mb-4 text-gray-600">Total akun: {{ $total }}</p>

        <table class="w-full bg-white rounded shadow text-black">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 text-left">#</th>
                    <th class="py-2 px-4 text-left">Nama</th>
                    <th class="py-2 px-4 text-left">Email</th>
                    <th class="py-2 px-4 text-left">Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="py-2 px-4">{{ $user->name }}</td>
                        <td class="py-2 px-4">{{ $user->email }}</td>
                        <td class="py-2 px-4">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

        <a href="{{ route('admin.dashboard') }}"
            class="inline-block mt-6 px-6 py-3 bg-green-300 text-green-900 font-bold rounded-full hover:bg-green-400 transition">
            ⬅️ Kembali ke Dashboard
        </a>
    </div>
@endsection