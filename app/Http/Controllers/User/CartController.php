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

    public function add(Request $request, $id)
    {
        $toppings = $request->input('toppings', []); // array of topping ids

        // Load product to check existence and stock
        $product = \App\Models\Produk::find($id);
        if (!$product) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Produk tidak ditemukan.'], 404);
            }
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // Build a signature to uniquely identify cart item by product + toppings
        $signature = null;
        if (!empty($toppings) && is_array($toppings)) {
            sort($toppings);
            $signature = implode(',', $toppings);
        }

        // Try find existing cart item for this user/product with same toppings signature
        $existingQuery = Cart::where('user_id', Auth::id())->where('produk_id', $id);
        if ($signature) {
            $existingQuery = $existingQuery->whereJsonContains('toppings', $toppings);
        } else {
            $existingQuery = $existingQuery->whereNull('toppings');
        }

        $cartItem = $existingQuery->first();

        // If exists, we'll increment jumlah; else create new
        if ($cartItem) {
            $newTotalQuantity = ($cartItem->jumlah ?? 0) + 1;
            if ($newTotalQuantity > $product->stok) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Stok produk tidak mencukupi.'], 422);
                }
                return redirect()->back()->with('error', 'Stok produk tidak mencukupi.');
            }
            $cartItem->jumlah = $newTotalQuantity;
            $cartItem->save();
        } else {
            // Create new cart item
            if ($product->stok < 1) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Stok produk habis.'], 422);
                }
                return redirect()->back()->with('error', 'Stok produk habis.');
            }

            $cartItem = Cart::create([
                'user_id' => Auth::id(),
                'produk_id' => $id,
                'jumlah' => 1,
                'toppings' => !empty($toppings) ? $toppings : null,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Produk berhasil ditambahkan ke keranjang!']);
        }

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

    /**
     * API method untuk mendapatkan data keranjang untuk POS
     */
    public function getCartApi()
    {
        $items = Cart::with(['produk' => function($query) {
                $query->select('id', 'nama', 'harga', 'foto', 'stok');
            }])
            ->where('user_id', Auth::id())
            ->get();

        $total = 0;
        $cartData = [];

        foreach ($items as $item) {
            $itemSubtotal = ($item->produk->harga ?? 0) * $item->jumlah;
            
            // Get selected toppings
            $selectedToppings = $item->getSelectedToppings();
            
            // Add topping price
            if ($selectedToppings->count() > 0) {
                $toppingTotal = $selectedToppings->sum('price') * $item->jumlah;
                $itemSubtotal += $toppingTotal;
            }
            
            $cartData[] = [
                'id' => $item->id,
                'produk' => [
                    'id' => $item->produk->id,
                    'nama' => $item->produk->nama,
                    'harga' => $item->produk->harga,
                    'foto' => $item->produk->foto,
                    'stok' => $item->produk->stok
                ],
                'jumlah' => $item->jumlah,
                'toppings' => $selectedToppings->map(function($topping) {
                    return [
                        'id' => $topping->id,
                        'name' => $topping->name,
                        'price' => $topping->price
                    ];
                }),
                'subtotal' => $itemSubtotal
            ];
            
            $total += $itemSubtotal;
        }

        return response()->json([
            'items' => $cartData,
            'total' => $total,
            'count' => count($cartData)
        ]);
    }

    /**
     * Update quantity item di keranjang via API
     */
    public function updateQuantity(Request $request, $id)
    {
        $item = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $newQuantity = $request->input('jumlah');
        
        if ($newQuantity < 1) {
            return response()->json(['success' => false, 'message' => 'Quantity tidak valid']);
        }

        if ($newQuantity > $item->produk->stok) {
            return response()->json([
                'success' => false, 
                'message' => 'Stok tidak mencukupi. Maksimal ' . $item->produk->stok . ' item.'
            ]);
        }

        $item->jumlah = $newQuantity;
        $item->save();

        return response()->json(['success' => true, 'message' => 'Quantity berhasil diupdate']);
    }

    /**
     * Hapus item dari keranjang via API
     */
    public function removeItem($id)
    {
        $item = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item berhasil dihapus']);
    }

    /**
     * Hapus item berdasarkan produk ID via API
     */
    public function removeByProductId($produkId)
    {
        $deleted = Cart::where('user_id', Auth::id())
            ->where('produk_id', $produkId)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Item berhasil dihapus dari keranjang']);
        } else {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }
    }

    /**
     * Kosongkan seluruh keranjang via API
     */
    public function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true, 'message' => 'Keranjang berhasil dikosongkan']);
    }
}