<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name|max:255',
        ]);

        Brand::create($request->only('name'));

        return redirect()->route('brands.index')
                         ->with('success', 'Бренд успішно додано.');
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brand->id . '|max:255',
        ]);

        $brand->update($request->only('name'));

        return redirect()->route('brands.index')
                         ->with('success', 'Бренд оновлено.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('brands.index')
                         ->with('success', 'Бренд видалено.');
    }
}
