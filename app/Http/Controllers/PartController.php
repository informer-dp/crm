<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index()
    {
        return view('parts.index', ['parts' => Part::all()]);
    }

    public function create()
    {
        return view('parts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Part::create($request->all());

        return redirect()->route('parts.index')->with('success', 'Запчастина додана успішно.');
    }
}
