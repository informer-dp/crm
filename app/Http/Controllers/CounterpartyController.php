<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Counterparty;
use Illuminate\Http\Request;

class CounterpartyController extends Controller
{
    public function index()
    {
        $counterparties = Counterparty::with('contact')->paginate(20);
        return view('counterparties.index', compact('counterparties'));
    }

    public function create()
    {
        $contacts = Contact::orderBy('name')->get();
        return view('counterparties.create', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'type' => 'required'
        ]);

        Counterparty::create($request->all());

        return redirect()->route('counterparties.index')
            ->with('success', 'Контрагент створений');
    }
}
