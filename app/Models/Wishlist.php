<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    // Tentukan nama tabelnya secara eksplisit
    // karena nama model 'Wishlist' tidak jamak 'wishlists'
    protected $table = 'wishlist'; 

    /**
     * Tentukan kolom mana yang boleh diisi
     */
    protected $fillable = [
        'user_id',
        'produk_id',
    ];

    /**
     * Relasi: Satu item wishlist ini milik satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Satu item wishlist ini milik satu Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}