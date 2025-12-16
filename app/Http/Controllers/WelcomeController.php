<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        try {
            // Ambil 6 produk terbaru dengan relasi kategori
            $produk = Produk::with('kategori')
                ->where('stok', '>', 0) // hanya produk yang masih ada stoknya
                ->latest()
                ->take(6)
                ->get();

            // Ambil kategori untuk ditampilkan juga
            $kategori = Kategori::take(4)->get();
        } catch (\Exception $e) {
            // Fallback jika ada error dalam query
            $produk = collect([]);
            $kategori = collect([]);
        }

        return view('welcome', [
            'produk' => $produk,
            'kategori' => $kategori
        ]);
    }

    public function kategori($id)
    {
        try {
            // Ambil kategori berdasarkan ID
            $kategori = Kategori::findOrFail($id);
            
            // Ambil semua produk dari kategori ini yang masih ada stoknya
            $produk = Produk::with('kategori')
                ->where('kategori_id', $id)
                ->where('stok', '>', 0)
                ->latest()
                ->paginate(12);

            // Ambil semua kategori untuk navigation
            $allKategori = Kategori::all();
        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('error', 'Kategori tidak ditemukan');
        }

        return view('kategori-produk', [
            'kategori' => $kategori,
            'produk' => $produk,
            'allKategori' => $allKategori
        ]);
    }
}