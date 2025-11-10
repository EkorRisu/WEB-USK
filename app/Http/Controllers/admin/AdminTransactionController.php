<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction; // Pastikan model Transaction diimport

class AdminTransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi (Admin Index).
     * Memuat relasi yang dibutuhkan untuk tampilan detail.
     */
    public function index()
    {
        // Pengecekan role diletakkan di middleware (diasumsikan sudah ada)
        // Jika tidak menggunakan middleware, letakkan pengecekan role di sini:
        // if (auth()->user()->role !== 'admin') { abort(403, 'Akses hanya untuk admin.'); }

        $transactions = Transaction::with(['user', 'items.produk', 'items.review'])
                                   ->orderBy('created_at', 'desc')
                                   ->get(); // Menggunakan get() karena view Anda menggunakan koleksi

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Mengubah status transaksi menjadi 'dikirim' (konfirmasi cepat).
     */
    public function konfirmasi($id)
    {
        // Pengecekan role diletakkan di middleware (diasumsikan sudah ada)

        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status === 'pending') {
            $transaction->update(['status' => 'dikirim']);
            return redirect()->back()->with('success', 'Transaksi berhasil dikonfirmasi dan status diubah menjadi DIKIRIM.');
        }

        return redirect()->back()->with('error', 'Transaksi tidak dapat dikonfirmasi karena statusnya bukan pending.');
    }

    /**
     * Mengubah status transaksi menjadi 'selesai' (complete cepat).
     */
    public function complete($id)
    {
        // Pengecekan role diletakkan di middleware (diasumsikan sudah ada)

        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status === 'dikirim') {
            $transaction->update(['status' => 'selesai']);
            return redirect()->back()->with('success', 'Transaksi berhasil diselesaikan.');
        }

        return redirect()->back()->with('error', 'Transaksi tidak dapat diselesaikan karena statusnya bukan dikirim.');
    }
    
    /**
     * Memperbarui status dan pesan pengiriman dari Modal Alpine.js (PUT request).
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Pengecekan role diletakkan di middleware (diasumsikan sudah ada)

        $request->validate([
            'status' => 'required|string|in:pending,dikirim,selesai,dibatalkan',
            'note' => 'nullable|string|max:500', // Kolom pesan admin untuk user
        ]);

        $transaction->update([
            'status' => $request->status,
            'note' => $request->note, // Menyimpan pesan/catatan pengiriman
        ]);

        return redirect()->route('admin.transactions.index')->with('success', 'Status transaksi dan pesan admin berhasil diperbarui.');
    }
}