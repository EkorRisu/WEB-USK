<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\TransactionItem; // ⭐️ BARU
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan atau MENGUPDATE review untuk SATU ITEM TRANSAKSI.
     */
    public function store(Request $request)
    {
        $request->validate([
            // ⭐️ KITA VALIDASI transaction_item_id, BUKAN produk_id
            'transaction_item_id' => 'required|exists:transaction_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Cek keamanan: Apakah user yang login adalah pemilik item ini?
        $item = TransactionItem::find($request->transaction_item_id);
        if ($item->user_id != Auth::id()) {
            return back()->with('error', 'Anda tidak bisa mereview produk ini.');
        }

        // ⭐️ LOGIKA UPDATE/CREATE BARU
        Review::updateOrCreate(
            [
                // Kuncinya adalah ID item transaksi
                'transaction_item_id' => $item->id 
            ],
            [
                'rating' => $request->rating,
                'review_text' => $request->review_text
            ]
        );

        return redirect()->route('user.transactions')->with('success', 'Terima kasih atas review Anda!');
    }

    /**
     * API: Get reviews for a given product (used by AJAX in the dashboard view).
     * URL: /user/produk/{produk}/reviews
     */
    public function getProductReviews($produkId)
    {
        // Ambil semua review yang terkait dengan transaction_items untuk produk ini
        $reviews = Review::with(['transactionItem.user'])
                    ->whereHas('transactionItem', function ($q) use ($produkId) {
                        $q->where('produk_id', $produkId);
                    })->get();

        $reviewsArray = $reviews->map(function ($r) {
            return [
                'id' => $r->id,
                'user_name' => optional($r->transactionItem->user)->name ?? 'Pengguna Anonim',
                'rating' => $r->rating,
                'comment' => $r->review_text,
                'created_at' => $r->created_at ? $r->created_at->toDateTimeString() : null,
            ];
        })->toArray();

        $average = $reviews->count() ? ($reviews->sum('rating') / $reviews->count()) : 0;

        return response()->json([
            'reviews' => $reviewsArray,
            'reviews_count' => $reviews->count(),
            'average_rating' => $average,
        ]);
    }
}