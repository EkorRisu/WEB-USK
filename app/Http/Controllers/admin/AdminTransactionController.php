<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class AdminTransactionController extends Controller
{
    // AdminTransactionController.php

    // AdminTransactionController.php

    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }

        $transactions = Transaction::with('user')->get();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function konfirmasi($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses hanya untuk admin.');
        }

        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status' => 'dikirim']);

        return back()->with('success', 'Transaksi telah dikonfirmasi.');
    }
}
