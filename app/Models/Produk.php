<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = ['kategori_id', 'nama', 'harga', 'deskripsi', 'foto', 'stok'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
