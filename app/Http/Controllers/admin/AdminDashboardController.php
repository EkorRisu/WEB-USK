<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Produk;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data total
        $userCount = User::where('role', '!=', 'admin')->count();
        $bookCount = Produk::count();
        $transactionCount = Transaction::count();

        // ⭐️ TAMBAHKAN BARIS INI UNTUK TES ⭐️
        // dd($userCount, $bookCount, $transactionCount);

        // 2. Kirim data ke view (Kode ini tidak akan berjalan)
        return view('admin.dashboard', data: [
            'userCount' => $userCount,
            'bookCount' => $bookCount,
            'transactionCount' => $transactionCount,
        ]);
    }
}