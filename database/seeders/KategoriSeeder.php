<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'nama' => 'Coffee',
                'deskripsi' => 'Berbagai jenis minuman kopi berkualitas tinggi'
            ],
            [
                'nama' => 'Non-Coffee',
                'deskripsi' => 'Minuman non-kopi seperti teh, coklat, dan jus'
            ],
            [
                'nama' => 'Makanan Ringan',
                'deskripsi' => 'Aneka makanan ringan dan pastry'
            ],
            [
                'nama' => 'Dessert',
                'deskripsi' => 'Berbagai macam dessert dan kue manis'
            ]
        ];

        foreach ($categories as $category) {
            Kategori::create($category);
        }
    }
}