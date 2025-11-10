<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table = 'favorites';
    protected $fillable = ['user_id', 'produk_id'];
    
    // Tidak perlu relasi di sini, karena ini hanya tabel pivot
}