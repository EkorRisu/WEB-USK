<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = ['kategori_id', 'nama', 'harga', 'deskripsi', 'foto', 'stok'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi: Satu Produk punya BANYAK review
     */
    public function reviews()
    {
        // Note: Reviews are stored against TransactionItem (transaction_item_id),
        // so the direct hasMany here would expect a produk_id column on reviews which doesn't exist.
        // Keep this for backward-compat but prefer using reviewsThrough() when aggregating.
        return $this->hasMany(Review::class);
    }

    /**
     * Relasi: ambil ulasan lewat transaction_items (hasManyThrough)
     * Produk -> TransactionItem -> Review
     */
    public function reviewsThrough()
    {
        return $this->hasManyThrough(
            \App\Models\Review::class,
            \App\Models\TransactionItem::class,
            'produk_id', // Foreign key on transaction_items table...
            'transaction_item_id', // Foreign key on reviews table...
            'id', // Local key on produks table
            'id'  // Local key on transaction_items table
        );
    }

    /**
     * Relasi: Satu Produk bisa ada di BANYAK wishlist
     */
    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'produk_id', 'user_id');
    }

    /**
     * Helper untuk cek apakah user yang sedang login sudah me-like
     */
    public function isFavoritedByUser()
    {
        if (!Auth::check()) {
            return false;
        }
        // Cek di relasi 'favoritedBy' apakah ada ID user yang sedang login
        return $this->favoritedBy()->where('user_id', Auth::id())->exists();
    }

    /**
     * ⭐️ PASTIKAN RELASI INI ADA ⭐️
     *
     * Satu Produk bisa ada di banyak item transaksi (jika dibeli berkali-kali).
     * Ini adalah KUNCI untuk menghitung "Terlaris".
     */
    public function transactionItems()
    {
        // Ganti 'produk_id' jika nama kolom foreign key Anda berbeda
        return $this->hasMany(TransactionItem::class, 'produk_id');
    }
}
