<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use Illuminate\Http\Request;


class CartController extends Controller
{
    public function index()
    {
        $items = Cart::with('produk')
            ->where('user_id', Auth::id())
            ->get();

        return view('user.cart', compact('items'));
    }

    public function add($id)
    {
        // 1. Find the existing cart item or create a new one.
        $cartItem = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'produk_id' => $id,
        ]);

        // 2. Load the associated Produk model to check its stock.
        if (!$cartItem->relationLoaded('produk')) {
             $cartItem->load('produk');
        }
        $product = $cartItem->produk;

        // Safety check: ensure the product exists
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // 3. Calculate the quantity AFTER the increment.
        $newTotalQuantity = ($cartItem->jumlah ?? 0) + 1;

        // 4. Check if the new quantity exceeds the product's stock ('stok').
        if ($newTotalQuantity > $product->stok) {
            $currentQuantity = $cartItem->jumlah ?? 0;
            return redirect()->back()->with(
                'error',
                'Stok produk tidak mencukupi. Hanya tersedia ' . $product->stok . ' item. Anda sudah memiliki ' . $currentQuantity . ' item di keranjang.'
            );
        }

        // 5. Increment the quantity and save the cart item.
        $cartItem->jumlah = $newTotalQuantity;
        $cartItem->save();

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function remove($id)
    {
        Cart::where('id', $id)->where('user_id', Auth::id())->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function update(Request $request)
    {
        if ($request->has('increase')) {
            // Load relasi produk untuk cek stok
            $item = Cart::with('produk')->findOrFail($request->increase);
            
            // Cek stok saat increase
            if ($item->jumlah + 1 > $item->produk->stok) {
                 return redirect()->back()->with(
                    'error',
                    'Stok produk tidak mencukupi. Maksimal ' . $item->produk->stok . ' item.'
                );
            }
            
            $item->jumlah += 1;
            $item->save();
        }

        if ($request->has('decrease')) {
            $item = Cart::findOrFail($request->decrease);
            if ($item->jumlah > 1) {
                $item->jumlah -= 1;
                $item->save();
            }
        }

        return redirect()->back();
    }
}