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
        $items = Cart::with(['produk:id,nama,harga,stok'])->where('user_id', auth()->id())->get();
        return view('user.checkout', compact('items'));
    }



    public function processCheckout(Request $request)
    {
        // Validasi untuk sistem pembayaran berdasarkan konfigurasi
        $allowedMethods = [];
        if (config('fitur.qris_payment')) {
            $allowedMethods[] = 'qris';
        }
        $allowedMethods[] = 'cash'; // Cash selalu tersedia

        $rules = [
            'metode_pembayaran' => 'required|in:' . implode(',', $allowedMethods),
            'nama_customer' => 'required|string|min:2',
        ];


        
        // Tambahan validasi untuk pembayaran tunai
        if ($request->metode_pembayaran === 'cash') {
            $rules['cash_amount'] = 'required|numeric|min:1';
        }

        $request->validate($rules);

        $user = auth()->user();
        $items = $user->cart()->with('produk')->get();

        if ($items->isEmpty()) {
            return redirect()->route('user.cart')->with('error', 'Keranjang kosong!');
        }

        // Periksa ketersediaan stok sebelum checkout
        $stockCheck = $this->checkStockAvailability($items);
        if (!$stockCheck['available']) {
            return redirect()->route('user.cart')->with('error', $stockCheck['message']);
        }

        // Hitung total termasuk topping price jika ada
        $total = 0;
        foreach ($items as $item) {
            $line = ($item->produk->harga ?? 0) * $item->jumlah;
            if (!empty($item->toppings) && is_array($item->toppings)) {
                $toppingSum = \App\Models\Topping::whereIn('id', $item->toppings)->sum('price');
                $line += ($toppingSum * $item->jumlah);
            }
            $total += $line;
        }

        // Simpan ke transactions dengan sistem pembayaran baru
        $transactionData = [
            'user_id' => $user->id,
            'metode_pembayaran' => $this->getPaymentMethodLabel($request),
            'total' => $total,
            'status' => 'paid', // Langsung paid karena tidak ada konfirmasi admin
            'customer_name' => $request->nama_customer,
        ];


        
        // Tambahan data untuk cash payment  
        if ($request->metode_pembayaran === 'cash') {
            $transactionData['cash_amount'] = $request->cash_amount;
            $transactionData['change_amount'] = $request->cash_amount - $total;
        }

        $transaction = Transaction::create($transactionData);

        // Simpan ke transaction_items (termasuk toppings JSON)
        foreach ($items as $item) {
            TransactionItem::create([
                'user_id' => auth()->id(),
                'transaction_id' => $transaction->id,
                'produk_id' => $item->produk_id,
                'nama_barang' => $item->produk ? $item->produk->nama : null,
                'jumlah' => $item->jumlah,
                'harga' => $item->produk->harga,
                'toppings' => $item->toppings ?? null,
            ]);
        }

        // Kurangi stok produk (simple stock system)
        $this->reduceProductStock($items);

        // Hapus cart
        $user->cart()->delete();

        // Redirect ke struk digital
        return redirect()->route('user.transaction.receipt', $transaction->id)
                        ->with('success', 'Pembayaran berhasil! Berikut struk digital Anda.');
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

    public function receipt($id)
    {
        $transaction = Transaction::with('items.produk')
                                 ->where('id', $id)
                                 ->where('user_id', Auth::id())
                                 ->firstOrFail();
        
        return view('user.receipt', compact('transaction'));
    }

    public function downloadReceipt($id)
    {
        // Check if PDF invoice feature is enabled
        if (!config('fitur.pdf_invoice')) {
            return redirect()->route('user.transaction.receipt', $id)
                           ->with('error', 'Fitur download PDF tidak tersedia.');
        }

        $transaction = Transaction::with('items.produk')
                                 ->where('id', $id)
                                 ->where('user_id', Auth::id())
                                 ->firstOrFail();
        
        $pdf = \PDF::loadView('user.receipt-pdf', compact('transaction'));
        return $pdf->download('struk-' . $transaction->id . '.pdf');
    }

    /**
     * Kurangi stok produk berdasarkan pembelian (Simple Stock System)
     */
    private function reduceProductStock($cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->produk;
            $quantityOrdered = $cartItem->jumlah;
            
            if (!$product) continue;
            
            // Kurangi stok produk langsung
            $product->decrement('stok', $quantityOrdered);
            
            \Log::info('Product stock reduced', [
                'product_id' => $product->id,
                'product_name' => $product->nama,
                'quantity_ordered' => $quantityOrdered,
                'remaining_stock' => $product->fresh()->stok
            ]);
        }
    }

    /**
     * Periksa ketersediaan stok produk untuk semua item di cart (Simple Stock System)
     */
    private function checkStockAvailability($cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->produk;
            $quantityOrdered = $cartItem->jumlah;
            
            if (!$product) continue;
            
            // Periksa stok produk langsung (simple stock system)
            if ($product->stok < $quantityOrdered) {
                return [
                    'message' => "Stok {$product->nama} tidak mencukupi. Tersedia {$product->stok}, diminta {$quantityOrdered}."
                ];
            }
        }
        
        return ['available' => true, 'message' => ''];
    }

    /**
     * Get payment method label for display
     */
    private function getPaymentMethodLabel(Request $request)
    {
        switch ($request->metode_pembayaran) {
            case 'qris':
                return 'QRIS DANA';
            case 'cash':
                return 'Bayar Tunai';
            default:
                return 'Unknown Payment Method';
        }
    }
}
