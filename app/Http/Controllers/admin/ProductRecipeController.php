<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductRecipe;
use App\Models\Produk;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductRecipe::with(['product', 'inventory']);

        // Filter berdasarkan produk
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // Filter berdasarkan kategori bahan
        if ($request->kategori_bahan) {
            $query->whereHas('inventory', function($q) use ($request) {
                $q->where('kategori_bahan', $request->kategori_bahan);
            });
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($subQ) use ($request) {
                    $subQ->where('nama', 'like', '%' . $request->search . '%');
                })->orWhereHas('inventory', function($subQ) use ($request) {
                    $subQ->where('nama_bahan', 'like', '%' . $request->search . '%');
                });
            });
        }

        $recipes = $query->paginate(15);
        $products = Produk::with('recipes')->get();
        $kategoriBahan = Inventory::distinct('kategori_bahan')->pluck('kategori_bahan');

        return view('admin.recipe.index', compact('recipes', 'products', 'kategoriBahan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Produk::all();
        $inventories = Inventory::where('status', '!=', 'habis')->get();
        
        return view('admin.recipe.create', compact('products', 'inventories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'inventory_id' => 'required|integer', 
            'quantity_needed' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500'
        ], [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.integer' => 'Produk tidak valid',
            'inventory_id.required' => 'Bahan baku harus dipilih',
            'inventory_id.integer' => 'Bahan baku tidak valid',
            'quantity_needed.required' => 'Jumlah kebutuhan harus diisi',
            'quantity_needed.numeric' => 'Jumlah kebutuhan harus berupa angka',
            'quantity_needed.min' => 'Jumlah kebutuhan minimal 0.01',
            'unit.required' => 'Satuan harus diisi',
            'unit.max' => 'Satuan maksimal 50 karakter'
        ]);

        // Manual validation untuk product dan inventory exists
        if (!Produk::find($request->product_id)) {
            return back()->withErrors(['product_id' => 'Produk tidak ditemukan'])->withInput();
        }

        if (!Inventory::find($request->inventory_id)) {
            return back()->withErrors(['inventory_id' => 'Bahan baku tidak ditemukan'])->withInput();
        }

        // Check if recipe already exists
        $existing = ProductRecipe::where('product_id', $request->product_id)
                                ->where('inventory_id', $request->inventory_id)
                                ->first();

        if ($existing) {
            return back()->withErrors(['inventory_id' => 'Resep untuk kombinasi produk dan bahan ini sudah ada'])->withInput();
        }

        try {
            // Create recipe with explicit data
            ProductRecipe::create([
                'product_id' => $request->product_id,
                'inventory_id' => $request->inventory_id,
                'quantity_needed' => $request->quantity_needed,
                'unit' => $request->unit,
                'notes' => $request->notes
            ]);

            return redirect()->route('admin.recipe.index')->with('success', 'Resep berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Produk::with(['recipes.inventory'])->findOrFail($id);
        
        return view('admin.recipe.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $recipe = ProductRecipe::with(['product', 'inventory'])->findOrFail($id);
        $products = Produk::all();
        $inventories = Inventory::where('status', '!=', 'habis')->get();
        
        return view('admin.recipe.edit', compact('recipe', 'products', 'inventories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $recipe = ProductRecipe::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'inventory_id' => 'required|exists:inventories,id',
            'quantity_needed' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500'
        ], [
            'product_id.required' => 'Produk harus dipilih',
            'product_id.exists' => 'Produk tidak valid',
            'inventory_id.required' => 'Bahan baku harus dipilih',
            'inventory_id.exists' => 'Bahan baku tidak valid',
            'quantity_needed.required' => 'Jumlah kebutuhan harus diisi',
            'quantity_needed.numeric' => 'Jumlah kebutuhan harus berupa angka',
            'quantity_needed.min' => 'Jumlah kebutuhan minimal 0.01',
            'unit.required' => 'Satuan harus diisi',
            'unit.max' => 'Satuan maksimal 50 karakter'
        ]);

        // Check if recipe already exists (excluding current)
        $existing = ProductRecipe::where('product_id', $request->product_id)
                                ->where('inventory_id', $request->inventory_id)
                                ->where('id', '!=', $id)
                                ->first();

        if ($existing) {
            return back()->withErrors(['inventory_id' => 'Resep untuk kombinasi produk dan bahan ini sudah ada'])->withInput();
        }

        $recipe->update($request->all());

        return redirect()->route('admin.recipe.index')->with('success', 'Resep berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $recipe = ProductRecipe::findOrFail($id);
        $recipe->delete();

        return redirect()->route('admin.recipe.index')->with('success', 'Resep berhasil dihapus');
    }

    /**
     * Bulk add recipes untuk produk
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produks,id',
            'recipes' => 'required|array|min:1',
            'recipes.*.inventory_id' => 'required|exists:inventories,id',
            'recipes.*.quantity_needed' => 'required|numeric|min:0.01',
            'recipes.*.unit' => 'required|string|max:50',
            'recipes.*.notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->recipes as $recipeData) {
                // Check if recipe already exists
                $existing = ProductRecipe::where('product_id', $request->product_id)
                                        ->where('inventory_id', $recipeData['inventory_id'])
                                        ->first();

                if (!$existing) {
                    ProductRecipe::create([
                        'product_id' => $request->product_id,
                        'inventory_id' => $recipeData['inventory_id'],
                        'quantity_needed' => $recipeData['quantity_needed'],
                        'unit' => $recipeData['unit'],
                        'notes' => $recipeData['notes'] ?? null
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.recipe.index')->with('success', 'Resep berhasil ditambahkan secara bulk');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
