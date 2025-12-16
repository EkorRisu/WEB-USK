<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bahan',
        'deskripsi',
        'stok_tersedia',
        'stok_minimum',
        'satuan',
        'harga_per_satuan',
        'kategori_bahan',
        'status',
        'tanggal_kadaluarsa',
        'supplier'
    ];

    protected $casts = [
        'stok_tersedia' => 'decimal:2',
        'stok_minimum' => 'decimal:2',
        'harga_per_satuan' => 'decimal:2',
        'tanggal_kadaluarsa' => 'date'
    ];

    // Auto update status berdasarkan stok
    protected static function booted()
    {
        static::saving(function ($inventory) {
            if ($inventory->stok_tersedia <= 0) {
                $inventory->status = 'habis';
            } elseif ($inventory->stok_tersedia <= $inventory->stok_minimum) {
                $inventory->status = 'menipis';
            } else {
                $inventory->status = 'tersedia';
            }
        });
    }

    // Accessor untuk format harga
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga_per_satuan, 0, ',', '.');
    }

    // Method untuk mengurangi stok
    public function reduceStock($quantity, $unit)
    {
        // Konversi quantity ke satuan yang sama dengan inventory
        $convertedQuantity = $this->convertUnit($quantity, $unit, $this->satuan);
        
        if ($convertedQuantity !== null && $convertedQuantity > 0) {
            $this->stok_tersedia = max(0, $this->stok_tersedia - $convertedQuantity);
            $this->save();
            
            return true;
        }
        
        return false;
    }

    // Method untuk konversi satuan (sama seperti di ProductRecipe)
    public function convertUnit($quantity, $fromUnit, $toUnit)
    {
        // Jika satuan sama, tidak perlu konversi
        if (strtolower($fromUnit) === strtolower($toUnit)) {
            return $quantity;
        }

        // Konversi berat: kg <-> gram
        if ((strtolower($fromUnit) === 'kg' && strtolower($toUnit) === 'gram') ||
            (strtolower($fromUnit) === 'kilogram' && strtolower($toUnit) === 'gram')) {
            return $quantity * 1000;
        }
        
        if ((strtolower($fromUnit) === 'gram' && strtolower($toUnit) === 'kg') ||
            (strtolower($fromUnit) === 'gram' && strtolower($toUnit) === 'kilogram')) {
            return $quantity / 1000;
        }

        // Konversi volume: liter <-> ml
        if ((strtolower($fromUnit) === 'liter' && strtolower($toUnit) === 'ml') ||
            (strtolower($fromUnit) === 'l' && strtolower($toUnit) === 'ml')) {
            return $quantity * 1000;
        }
        
        if ((strtolower($fromUnit) === 'ml' && strtolower($toUnit) === 'liter') ||
            (strtolower($fromUnit) === 'ml' && strtolower($toUnit) === 'l')) {
            return $quantity / 1000;
        }

        // Jika tidak bisa dikonversi, return null
        return null;
    }

    // Accessor untuk total nilai stok
    public function getTotalNilaiAttribute()
    {
        return $this->stok_tersedia * $this->harga_per_satuan;
    }

    public function getFormattedTotalNilaiAttribute()
    {
        return 'Rp ' . number_format($this->total_nilai, 0, ',', '.');
    }

    // Scope untuk bahan yang menipis
    public function scopeMenipis($query)
    {
        return $query->where('stok_tersedia', '<=', $query->raw('stok_minimum'));
    }

    // Scope berdasarkan kategori
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_bahan', $kategori);
    }

    /**
     * Relasi: Inventory digunakan dalam resep produk
     */
    public function productRecipes()
    {
        return $this->hasMany(ProductRecipe::class);
    }

    /**
     * Hitung berapa banyak produk yang menggunakan bahan ini
     */
    public function getUsedInProductsCountAttribute()
    {
        return $this->productRecipes()->distinct('product_id')->count('product_id');
    }
}
