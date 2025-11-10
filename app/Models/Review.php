<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'transaction_item_id', // ⭐️ BARU
        'rating',
        'review_text',
    ];

    /**
     * ⭐️ RELASI DIPERBARUI
     * Satu review milik satu item transaksi
     */
    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }
}