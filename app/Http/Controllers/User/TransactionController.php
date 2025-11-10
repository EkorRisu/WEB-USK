<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function checkoutForm()
    {
        $items = Cart::with('produk')->where('user_id', auth()->id())->get();
        return view('user.checkout', compact('items'));
    }



    public function processCheckout(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string',
            'telepon' => 'required|string',
            'metode_pembayaran' => 'required|string',
        ]);

        $user = auth()->user();
        $items = $user->cart()->with('produk')->get();

        if ($items->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kosong!');
        }

        $total = $items->sum(function ($item) {
            return $item->produk->harga * $item->jumlah;
        });

        // Simpan ke transactions
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total' => $total,
            'status' => 'pending',
        ]);

        // Simpan ke transaction_items
        foreach ($items as $item) {
            TransactionItem::create([
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'produk_id' => $item->produk_id,
                'nama_barang' => $item->produk ? $item->produk->nama : null,
                'jumlah' => $item->jumlah,
                'harga' => $item->produk->harga,
            ]);
        }

        // Hapus cart
        $user->cart()->delete();

        return redirect()->route('user.transactions')->with('success', 'Checkout berhasil! Pesanan sedang diproses.');
    }

    public function index()
    {
        // Gunakan paginate agar view bisa memanggil ->links()
        $perPage = 10;
        $transactions = Transaction::where('user_id', Auth::id())
                        ->with('items.produk')
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage)
                        ->appends(request()->except('page'));

        return view('user.transactions', ['transaksi' => $transactions]);
    }

    public function terimaPesanan($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $transaction->update(['status' => 'selesai']);
        return back()->with('success', 'Pesanan selesai.');
    }
}
