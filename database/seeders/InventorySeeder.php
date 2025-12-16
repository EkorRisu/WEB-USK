<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $inventories = [
            // Biji Kopi
            [
                'nama_bahan' => 'Biji Kopi Arabica Premium',
                'deskripsi' => 'Biji kopi arabica berkualitas tinggi dari daerah pegunungan',
                'stok_tersedia' => 50.0,
                'stok_minimum' => 10.0,
                'satuan' => 'kg',
                'harga_per_satuan' => 120000,
                'kategori_bahan' => 'biji_kopi',
                'tanggal_kadaluarsa' => '2026-06-30',
                'supplier' => 'CV Kopi Nusantara'
            ],
            [
                'nama_bahan' => 'Biji Kopi Robusta',
                'deskripsi' => 'Biji kopi robusta untuk espresso blend',
                'stok_tersedia' => 30.0,
                'stok_minimum' => 8.0,
                'satuan' => 'kg',
                'harga_per_satuan' => 85000,
                'kategori_bahan' => 'biji_kopi',
                'tanggal_kadaluarsa' => '2026-06-30',
                'supplier' => 'CV Kopi Nusantara'
            ],
            
            // Susu
            [
                'nama_bahan' => 'Susu Full Cream',
                'deskripsi' => 'Susu sapi segar untuk latte dan cappuccino',
                'stok_tersedia' => 25.0,
                'stok_minimum' => 5.0,
                'satuan' => 'liter',
                'harga_per_satuan' => 18000,
                'kategori_bahan' => 'susu',
                'tanggal_kadaluarsa' => '2025-12-20',
                'supplier' => 'Dairy Fresh'
            ],
            [
                'nama_bahan' => 'Susu Almond',
                'deskripsi' => 'Alternatif susu untuk pelanggan vegan',
                'stok_tersedia' => 15.0,
                'stok_minimum' => 3.0,
                'satuan' => 'liter',
                'harga_per_satuan' => 35000,
                'kategori_bahan' => 'susu',
                'tanggal_kadaluarsa' => '2025-12-15',
                'supplier' => 'Plant Based Co'
            ],
            
            // Sirup
            [
                'nama_bahan' => 'Sirup Vanilla',
                'deskripsi' => 'Sirup vanilla premium untuk flavoring',
                'stok_tersedia' => 8.0,
                'stok_minimum' => 2.0,
                'satuan' => 'botol',
                'harga_per_satuan' => 45000,
                'kategori_bahan' => 'sirup',
                'tanggal_kadaluarsa' => '2026-03-30',
                'supplier' => 'Flavor House'
            ],
            [
                'nama_bahan' => 'Sirup Caramel',
                'deskripsi' => 'Sirup karamel untuk minuman manis',
                'stok_tersedia' => 6.0,
                'stok_minimum' => 2.0,
                'satuan' => 'botol',
                'harga_per_satuan' => 48000,
                'kategori_bahan' => 'sirup',
                'tanggal_kadaluarsa' => '2026-03-30',
                'supplier' => 'Flavor House'
            ],
            [
                'nama_bahan' => 'Sirup Hazelnut',
                'deskripsi' => 'Sirup hazelnut untuk variasi rasa',
                'stok_tersedia' => 4.0,
                'stok_minimum' => 2.0,
                'satuan' => 'botol',
                'harga_per_satuan' => 50000,
                'kategori_bahan' => 'sirup',
                'tanggal_kadaluarsa' => '2026-03-30',
                'supplier' => 'Flavor House'
            ],
            
            // Topping
            [
                'nama_bahan' => 'Whipped Cream',
                'deskripsi' => 'Krim kocok untuk topping minuman',
                'stok_tersedia' => 12.0,
                'stok_minimum' => 3.0,
                'satuan' => 'botol',
                'harga_per_satuan' => 25000,
                'kategori_bahan' => 'topping',
                'tanggal_kadaluarsa' => '2025-12-31',
                'supplier' => 'Cream Co'
            ],
            [
                'nama_bahan' => 'Chocolate Chips',
                'deskripsi' => 'Serpihan coklat untuk topping',
                'stok_tersedia' => 5.0,
                'stok_minimum' => 1.0,
                'satuan' => 'kg',
                'harga_per_satuan' => 75000,
                'kategori_bahan' => 'topping',
                'tanggal_kadaluarsa' => '2026-08-30',
                'supplier' => 'Choco Delights'
            ],
            [
                'nama_bahan' => 'Marshmallow Mini',
                'deskripsi' => 'Marshmallow kecil untuk topping minuman coklat',
                'stok_tersedia' => 3.0,
                'stok_minimum' => 1.0,
                'satuan' => 'kg',
                'harga_per_satuan' => 45000,
                'kategori_bahan' => 'topping',
                'tanggal_kadaluarsa' => '2026-05-30',
                'supplier' => 'Sweet Things'
            ],
            
            // Kemasan
            [
                'nama_bahan' => 'Paper Cup 8oz',
                'deskripsi' => 'Gelas kertas untuk takeaway small',
                'stok_tersedia' => 500.0,
                'stok_minimum' => 100.0,
                'satuan' => 'pcs',
                'harga_per_satuan' => 350,
                'kategori_bahan' => 'kemasan',
                'supplier' => 'Packaging Pro'
            ],
            [
                'nama_bahan' => 'Paper Cup 12oz',
                'deskripsi' => 'Gelas kertas untuk takeaway medium',
                'stok_tersedia' => 300.0,
                'stok_minimum' => 80.0,
                'satuan' => 'pcs',
                'harga_per_satuan' => 400,
                'kategori_bahan' => 'kemasan',
                'supplier' => 'Packaging Pro'
            ],
            [
                'nama_bahan' => 'Plastic Lid',
                'deskripsi' => 'Tutup plastik untuk paper cup',
                'stok_tersedia' => 400.0,
                'stok_minimum' => 100.0,
                'satuan' => 'pcs',
                'harga_per_satuan' => 200,
                'kategori_bahan' => 'kemasan',
                'supplier' => 'Packaging Pro'
            ],
            
            // Lainnya
            [
                'nama_bahan' => 'Gula Pasir',
                'deskripsi' => 'Gula pasir putih untuk pemanis',
                'stok_tersedia' => 20.0,
                'stok_minimum' => 5.0,
                'satuan' => 'kg',
                'harga_per_satuan' => 15000,
                'kategori_bahan' => 'lainnya',
                'supplier' => 'Toko Sembako Jaya'
            ],
            [
                'nama_bahan' => 'Gula Aren',
                'deskripsi' => 'Gula aren untuk minuman tradisional',
                'stok_tersedia' => 8.0,
                'stok_minimum' => 2.0,
                'satuan' => 'kg',
                'harga_per_satuan' => 35000,
                'kategori_bahan' => 'lainnya',
                'supplier' => 'Gula Tradisional'
            ]
        ];

        foreach ($inventories as $inventory) {
            Inventory::create($inventory);
        }
    }
}
