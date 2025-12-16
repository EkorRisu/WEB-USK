<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'product_topping', 'topping_id', 'produk_id')->withPivot('price')->withTimestamps();
    }
}
