<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    protected $fillable = [
        'product_id',
        'inventory_id',
        'quantity_needed',
        'unit',
        'notes'
    ];

    protected $casts = [
        'quantity_needed' => 'decimal:2',
    ];

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id');
    }

    // Relasi ke Inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Hitung berapa produk yang bisa dibuat berdasarkan stok inventory
    public function getMaxProductionAttribute()
    {
        if ($this->quantity_needed <= 0) {
            return 0;
        }

        // Konversi stok inventory ke satuan yang sama dengan resep
        $inventoryStock = $this->inventory->stok_tersedia;
        $inventoryUnit = $this->inventory->satuan;
        $recipeUnit = $this->unit;
        $recipeQuantity = $this->quantity_needed;

        // Konversi inventory stock ke satuan resep
        $convertedStock = $this->convertUnit($inventoryStock, $inventoryUnit, $recipeUnit);
        
        if ($convertedStock === null) {
            // Jika tidak bisa dikonversi, return 0
            return 0;
        }

        return floor($convertedStock / $recipeQuantity);
    }

    // Helper function untuk konversi satuan
    private function convertUnit($value, $fromUnit, $toUnit)
    {
        // Jika satuan sama, tidak perlu konversi
        if ($fromUnit === $toUnit) {
            return $value;
        }

        // Konversi berat
        $weightConversions = [
            'kg' => ['gram' => 1000],
            'gram' => ['kg' => 0.001],
        ];

        // Konversi volume
        $volumeConversions = [
            'liter' => ['ml' => 1000],
            'ml' => ['liter' => 0.001],
        ];

        // Cek konversi berat
        if (isset($weightConversions[$fromUnit][$toUnit])) {
            return $value * $weightConversions[$fromUnit][$toUnit];
        }

        // Cek konversi volume
        if (isset($volumeConversions[$fromUnit][$toUnit])) {
            return $value * $volumeConversions[$fromUnit][$toUnit];
        }

        // Jika tidak ada konversi yang cocok, return null
        return null;
    }

    // Format quantity dengan unit
    public function getFormattedQuantityAttribute()
    {
        return number_format((float) $this->quantity_needed, 2) . ' ' . $this->unit;
    }
}
