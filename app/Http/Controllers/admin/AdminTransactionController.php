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
    public function index(Request $request)
    {
        // Pengecekan role diletakkan di middleware (diasumsikan sudah ada)
        // if (auth()->user()->role !== 'admin') { abort(403, 'Akses hanya untuk admin.'); }

        $query = Transaction::with(['user', 'items.produk']);

        // Filter berdasarkan status jika ada parameter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Mengubah status transaksi menjadi 'dikirim' (konfirmasi cepat).
     */
    public function konfirmasi(Request $request, $id)
    {
        // Pengecekan role diletakkan di middleware (diasumsikan sudah ada)

        $transaction = Transaction::findOrFail($id);
        
        // Allow konfirmasi for both pending and paid status
        if ($transaction->status === 'pending' || $transaction->status === 'paid') {
            $transaction->update(['status' => 'dikirim']);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transaksi berhasil dikonfirmasi untuk pengiriman.', 'status' => 'dikirim']);
            }
            return redirect()->back()->with('success', 'Transaksi berhasil dikonfirmasi dan status diubah menjadi DIKIRIM.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak dapat dikonfirmasi. Status harus pending atau paid.'], 422);
        }

        return redirect()->back()->with('error', 'Transaksi tidak dapat dikonfirmasi. Status harus pending atau paid.');
    }

    /**
     * Mengubah status transaksi menjadi 'selesai' (complete cepat).
     */
    public function complete(Request $request, $id)
    {
        // Pengecekan role diletakkan di middleware (diasumsikan sudah ada)

        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->status === 'dikirim') {
            $transaction->update(['status' => 'selesai']);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transaksi berhasil diselesaikan.', 'status' => 'selesai']);
            }
            return redirect()->back()->with('success', 'Transaksi berhasil diselesaikan.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak dapat diselesaikan karena statusnya bukan dikirim.'], 422);
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
            'status' => 'required|string|in:pending,paid,dikirim,selesai,dibatalkan',
            'note' => 'nullable|string|max:500', // Kolom pesan admin untuk user
        ]);

        $transaction->update([
            'status' => $request->status,
            'note' => $request->note, // Menyimpan pesan/catatan pengiriman
        ]);

        return redirect()->route('admin.transactions.index')->with('success', 'Status transaksi dan pesan admin berhasil diperbarui.');
    }
}