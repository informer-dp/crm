<?php

namespace App\Http\Controllers;

use App\Models\CounterpartyGroup;
use Illuminate\Http\Request;

class CounterpartyGroupController extends Controller
{
    public function index()
    {
        $groups = CounterpartyGroup::latest()->paginate(20);
        return view('counterparty_groups.index', compact('groups'));
    }

    public function create()
    {
        return view('counterparty_groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        CounterpartyGroup::create($request->all());

        return redirect()->route('counterparty_groups.index')
            ->with('success', 'Групу створено');
    }
}
