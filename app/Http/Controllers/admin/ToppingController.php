<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use Illuminate\Http\Request;

class ToppingController extends Controller
{
    public function index()
    {
        $toppings = Topping::orderBy('created_at', 'desc')->paginate(12);
        return view('admin.topping.index', compact('toppings'));
    }

    public function create()
    {
        return view('admin.topping.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        $data = $request->only(['name', 'price']);

        Topping::create($data);

        return redirect()->route('admin.topping.index')
            ->with('success', 'Topping berhasil ditambahkan!');
    }

    public function show(Topping $topping)
    {
        return view('admin.topping.show', compact('topping'));
    }

    public function edit(Topping $topping)
    {
        return view('admin.topping.edit', compact('topping'));
    }

    public function update(Request $request, Topping $topping)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        $data = $request->only(['name', 'price']);

        $topping->update($data);

        return redirect()->route('admin.topping.index')
            ->with('success', 'Topping berhasil diupdate!');
    }

    public function destroy(Topping $topping)
    {
        $topping->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topping berhasil dihapus!'
        ]);
    }

    // API endpoint for getting toppings (for frontend)
    public function getToppings()
    {
        $toppings = Topping::select('id', 'name', 'price')
            ->orderBy('name')
            ->get();

        return response()->json($toppings);
    }
}
