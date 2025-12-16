<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->byKategori($request->kategori);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('nama_bahan', 'like', '%' . $request->search . '%');
        }

        $inventories = $query->orderBy('nama_bahan')->paginate(15);
        
        return view('admin.inventory.index', compact('inventories'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stok_tersedia' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'harga_per_satuan' => 'required|numeric|min:0',
            'kategori_bahan' => 'required|in:biji_kopi,susu,sirup,topping,kemasan,lainnya',
            'tanggal_kadaluarsa' => 'nullable|date|after:today',
            'supplier' => 'nullable|string|max:255'
        ]);

        Inventory::create($request->all());

        return redirect()->route('admin.inventory.index')->with('success', 'Bahan berhasil ditambahkan!');
    }

    public function show(Inventory $inventory)
    {
        return view('admin.inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stok_tersedia' => 'required|numeric|min:0',
            'stok_minimum' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'harga_per_satuan' => 'required|numeric|min:0',
            'kategori_bahan' => 'required|in:biji_kopi,susu,sirup,topping,kemasan,lainnya',
            'tanggal_kadaluarsa' => 'nullable|date|after:today',
            'supplier' => 'nullable|string|max:255'
        ]);

        $inventory->update($request->all());

        return redirect()->route('admin.inventory.index')->with('success', 'Data bahan berhasil diperbarui!');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('admin.inventory.index')->with('success', 'Bahan berhasil dihapus!');
    }

    // Method tambahan untuk adjust stok
    public function adjustStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'adjustment' => 'required|numeric',
            'type' => 'required|in:add,subtract',
            'keterangan' => 'nullable|string'
        ]);

        $newStock = $inventory->stok_tersedia;
        
        if ($request->type === 'add') {
            $newStock += $request->adjustment;
        } else {
            $newStock -= $request->adjustment;
            $newStock = max(0, $newStock); // Tidak boleh negatif
        }

        $inventory->update(['stok_tersedia' => $newStock]);

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disesuaikan',
            'new_stock' => $newStock
        ]);
    }

    // Method untuk cek bahan yang menipis
    public function lowStock()
    {
        $lowStockItems = Inventory::menipis()->get();
        
        return response()->json([
            'count' => $lowStockItems->count(),
            'items' => $lowStockItems
        ]);
    }
}
