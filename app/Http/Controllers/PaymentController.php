<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Log; // Digunakan untuk logging

class PaymentController extends Controller
{
    public function processCheckout(Request $request)
    {
        // 1. Validasi data input dari checkout form
        $request->validate([
            'alamat' => 'required|string|max:255',
            // Menambahkan validasi regex untuk format telepon
            'telepon' => ['required', 'string', 'max:15', 'regex:/^08\d{7,13}$/'], 
            'metode_pembayaran' => 'required|string',
            'items' => 'required|array', 
            // Memperbaiki validasi duplikat 'required'
            'items.*.produk_id' => 'required|exists:produks,id', 
            'items.*.jumlah' => 'required|integer|min:1', 
        ]);

        $user = Auth::user();
        $cartItems = $user->cart; 

        if ($cartItems->isEmpty() && empty($request->items)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong.');
        }

        // =================================================================
        // **LOGIKA PENCEGAHAN CHECKOUT: VALIDASI STOK MASSAL**
        // Pengecekan pertama dan paling penting sebelum memulai transaksi.
        // =================================================================
        
        // 1. Ambil semua Produk yang terlibat dan stoknya saat ini (dalam satu query)
        $produkIds = collect($request->items)->pluck('produk_id')->unique()->toArray();
        $produks = Produk::whereIn('id', $produkIds)->pluck('stok', 'id');

        foreach ($request->items as $item) {
            $produkId = $item['produk_id'];
            $jumlahBeli = $item['jumlah'];
            $stokTersedia = $produks[$produkId] ?? 0;

            if ($jumlahBeli > $stokTersedia) {
                // Cari nama produk untuk pesan error yang lebih jelas
                $produkNama = Produk::find($produkId)->nama ?? 'Produk tidak dikenal'; 
                // Mengembalikan pesan error dan data input yang sudah diisi
                return redirect()->back()->withInput($request->all())->with('error', 'Stok tidak mencukupi untuk item: ' . $produkNama . '. Tersedia: ' . $stokTersedia . '. Mohon koreksi jumlah di keranjang Anda.');
            }
        }
        
        // =================================================================
        // **LANJUTKAN KE TRANSAKSI JIKA STOK AMAN**
        // =================================================================


        // Mulai transaksi database untuk menjamin atomisitas (semua berhasil atau semua gagal)
        DB::beginTransaction();
        
        Log::info('Memulai checkout untuk User ID: ' . $user->id); // Log Awal

        try {
            // 2. Buat record transaksi utama
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'alamat_pengiriman' => $request->alamat,
                'telepon_penerima' => $request->telepon,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_harga' => 0, // Akan dihitung dalam loop
            ]);

            $totalHarga = 0;

            // 3. Proses setiap item yang dicheckout
            foreach ($request->items as $item) {
                // Mengunci produk untuk mencegah kondisi balapan (race condition)
                $produk = Produk::lockForUpdate()->find($item['produk_id']);

                // 4. Cek ketersediaan stok (Ini adalah check kedua/final di dalam lock, sebagai jaminan)
                if (!$produk || $produk->stok < $item['jumlah']) {
                    DB::rollBack();
                    // Pesan error lebih spesifik karena stok bisa berubah antara check pertama dan check kedua (lock)
                    return back()->with('error', 'Stok telah berubah dan tidak mencukupi untuk ' . ($produk ? $produk->nama : 'produk tidak dikenal') . '. Transaksi dibatalkan.');
                }
                
                // LOG DEBUG STOK: Catat stok sebelum pengurangan
                Log::info('Produk ID: ' . $produk->id . ' | Stok Awal: ' . $produk->stok . ' | Jumlah Beli: ' . $item['jumlah']);


                // 5. Kurangi stok produk secara atomik menggunakan metode decrement
                $produk->decrement('stok', $item['jumlah']);
                
                // MEMASTIKAN DATA TERBARU
                $produk->refresh(); 
                
                // LOG DEBUG STOK: Catat stok setelah pengurangan dan refresh
                Log::info('Produk ID: ' . $produk->id . ' | Stok Baru (Setelah Refresh): ' . $produk->stok);


                // 6. Simpan detail item transaksi (TransactionItem)
                $transaction->items()->create([
                    'produk_id' => $produk->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $produk->harga, // Ambil harga dari model Produk
                ]);

                $totalHarga += ($produk->harga * $item['jumlah']);
            }

            // 7. Update total harga pada transaksi
            $transaction->total_harga = $totalHarga;
            $transaction->save();

            // 8. Hapus item dari keranjang setelah pembayaran/checkout berhasil
            $user->cart()->delete(); 

            // 9. Commit transaksi jika semua langkah di atas berhasil
            DB::commit();

            Log::info('Checkout BERHASIL. Transaksi ID: ' . $transaction->id); // Log Sukses

            return redirect()->route('orders.index')->with('success', 'Checkout berhasil! Pesanan Anda telah dibuat.');
        } catch (\Exception $e) {
            // 10. Rollback transaksi jika terjadi kesalahan (misal: gagal koneksi, error I/O)
            DB::rollBack();
            // Catat ERROR secara detail
            Log::error('Checkout GAGAL: ' . $e->getMessage() . ' pada file ' . $e->getFile() . ' baris ' . $e->getLine()); 
            return back()->with('error', 'Terjadi kesalahan saat checkout. Silakan coba lagi.');
        }
    }
}
