<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'produk_id', 'jumlah', 'toppings'];

    protected $casts = [
        'toppings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Mendapatkan toppings yang dipilih untuk item cart ini
     */
    public function getSelectedToppings()
    {
        if (!$this->toppings || !is_array($this->toppings)) {
            return collect([]);
        }
        
        return \App\Models\Topping::whereIn('id', $this->toppings)->get();
    }
}
