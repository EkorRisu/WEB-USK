<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'produk_id',
        'nama_barang',
        'jumlah',
        'harga',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Relasi: satu TransactionItem bisa punya satu Review
     */
    public function review()
    {
        return $this->hasOne(\App\Models\Review::class, 'transaction_item_id');
    }
}
