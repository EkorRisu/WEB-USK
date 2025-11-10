<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request; // ⭐️ PENTING: Pastikan 'use' ini ada
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Menampilkan halaman daftar wishlist (wishlist.blade.php)
     */
    public function index()
    {
        $userId = Auth::id();

        $wishlistItems = Wishlist::where('user_id', $userId)
            ->with('produk')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.wishlist', compact('wishlistItems'));
    }


    /**
     * Menyimpan produk ke wishlist via AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id'
            
        ]);

        $userId = Auth::id();

        $exists = Wishlist::where('user_id', $userId)
            ->where('produk_id', $request->produk_id)
            ->exists();

        if ($exists) {
            return response()->json(['info' => 'Produk ini sudah ada di wishlist Anda.'], 200);
        }

        Wishlist::create([
            'user_id' => $userId,
            'produk_id' => $request->produk_id
        ]);

        return response()->json(['message' => 'Buku ditambahkan ke wishlist!'], 201);
    }


    /**
     * ⭐️ FUNGSI YANG DIPERBARUI ⭐️
     * Menghapus produk dari wishlist.
     * Sekarang bisa menangani AJAX (JSON) dan HTML (Redirect)
     */
    public function destroy(Request $request, $id) // 1. Tambahkan 'Request $request'
    {
        $wishlistItem = Wishlist::find($id);

        if (!$wishlistItem) {
            // 2. Tambahkan logika IF
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Item tidak ditemukan.'], 404);
            }
            return redirect()->route('user.dashboard')->with('error', 'Item tidak ditemukan.');
        }

        if ($wishlistItem->user_id == Auth::id()) {
            $wishlistItem->delete();

            // 3. Tambahkan logika IF
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Buku dihapus dari wishlist.'], 200);
            }
            // Sesuai permintaan Anda: redirect ke dashboard
            return redirect()->route('user.dashboard')->with('success', 'Buku dihapus dari wishlist.');
        }

        // 4. Tambahkan logika IF
        if ($request->wantsJson()) {
            return response()->json(['error' => 'Anda tidak punya hak akses.'], 403);
        }
        return redirect()->route('user.dashboard')->with('error', 'Anda tidak punya hak akses.');
    }
}
