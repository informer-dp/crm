<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employees.index', ['employees' => Employee::all()]);
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Співробітник доданий успішно.');
    }
}
