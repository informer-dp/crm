<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->paginate(20);
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
       $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:contacts,phone',
            'email' => 'nullable|email',
        ]);


        Contact::create($request->all());

        return redirect()->route('contacts.index')
            ->with('success', 'Контакт створено');
    }
    public function checkPhone(Request $request)
    {
            return response()->json([
                'exists' => Contact::where('phone', $request->phone)->exists()
            ]);
    }
}