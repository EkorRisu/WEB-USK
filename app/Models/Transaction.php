<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'bukti_bayar',
        'alamat',
        'nomor_hp',
        'metode_pembayaran',
        'total',
        // pesan_admin ada di migrasi sebagai kolom yang menyimpan catatan admin
        'pesan_admin',
        // tambahkan 'note' supaya controller yang menggunakan 'note' bisa mass assign
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Accessor untuk properti 'note' yang dipakai di view/controller.
     * Database sebenarnya menyimpan di kolom 'pesan_admin', jadi kita map.
     */
    public function getNoteAttribute()
    {
        return $this->attributes['pesan_admin'] ?? null;
    }

    public function setNoteAttribute($value)
    {
        $this->attributes['pesan_admin'] = $value;
    }
}
