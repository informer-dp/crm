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

    public function search(Request $request)
    {
        return Counterparty::with('contact')
            ->whereHas('contact', function ($q) use ($request) {
                $q->where('phone', 'like', "%{$request->q}%")
                ->orWhere('name', 'like', "%{$request->q}%");
            })
            ->limit(20)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'text' => "{$c->contact->name} ({$c->contact->phone})"
            ]);
    }

}
