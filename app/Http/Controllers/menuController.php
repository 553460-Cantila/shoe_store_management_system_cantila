<?php

namespace App\Http\Controllers;

use App\Models\ShoeProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class menuController extends Controller
{
    public function index()
    {
        $menus = ShoeProduct::orderBy('name')->paginate(10);
        return view('menu.menu', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shoe_products',
            'category' => 'required|string|max:100',
            'size' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        ShoeProduct::create($validated);

        return redirect()->route('menus.index')
            ->with('success', 'Shoe product added successfully.');
    }

    public function update(Request $request, ShoeProduct $menu)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('shoe_products')->ignore($menu->id)],
            'category' => 'required|string|max:100',
            'size' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $menu->update($validated);

        return redirect()->route('menus.index')
            ->with('success', 'Shoe product updated successfully.');
    }

    public function destroy(ShoeProduct $menu)
    {
        if ($menu->orders()->exists()) {
            return back()->with('error', 'Cannot delete a shoe product that has existing orders.');
        }
        $menu->delete();
        return redirect()->route('menus.index')
            ->with('success', 'Shoe product deleted successfully.');
    }
}