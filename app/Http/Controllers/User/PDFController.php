<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use PDF;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function cetakStruk($id)
    {
        $transaksi = Transaction::with('items.produk', 'user')->findOrFail($id);

        if ($transaksi->user_id !== Auth::id()) {
            abort(403); // keamanan: user hanya bisa cetak struk miliknya sendiri
        }

        $pdf = PDF::loadView('user.struk', compact('transaksi'));
        return $pdf->download('struk-pembelian-' . $id . '.pdf');
    }
}
