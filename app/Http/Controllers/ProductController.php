<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk; // Asumsi model buku Anda bernama 'Produk'
use App\Models\Kategori; // Asumsi model kategori Anda bernama 'Kategori'
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Menangani permintaan API Live Search untuk produk.
     * Dipanggil melalui route: GET /api/search_products
     */
    public function searchApi(Request $request) 
    {
        try {
            // Mengambil query dari input 'query'
            $query = $request->input('query'); 
            
            // Validasi query kosong
            if (empty($query)) {
                return response()->json([]);
            }

            // Mencari produk yang cocok dengan nama atau deskripsi
            $products = Produk::select('id', 'nama', 'harga', 'foto', 'stok', 'deskripsi', 'kategori_id')
                ->where(function($q) use ($query) {
                    $q->where('nama', 'like', "%{$query}%")
                      ->orWhere('deskripsi', 'like', "%{$query}%");
                })
                ->where('stok', '>', 0) // Hanya tampilkan yang ready stock
                ->with('kategori:id,nama') // Eager load category
                ->limit(10) // Batasi 10 hasil
                ->get();

            // Memformat hasil untuk dikirim ke JavaScript
            $formattedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'harga' => $product->harga,
                    'harga_formatted' => 'Rp ' . number_format($product->harga, 0, ',', '.'),
                    'foto' => $product->foto,
                    'stok' => $product->stok,
                    'deskripsi' => $product->deskripsi,
                    'kategori' => $product->kategori ? $product->kategori->nama : 'N/A' // Ambil nama kategori
                ];
            });

            return response()->json($formattedProducts);
            
        } catch (\Exception $e) {
            // Logging jika terjadi error server
            Log::error('[API Search Error] Query: ' . $request->input('query') . ' | Message: ' . $e->getMessage());
            
            // Mengembalikan status 500
            return response()->json(['error' => 'Gagal memuat hasil pencarian dari server.'], 500);
        }
    }
}