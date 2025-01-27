<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    //
    public function index()
{
    $users = User::all(); // Отримання всіх користувачів
    return view('users.index', compact('users')); // Повернення у вигляді
}

    public function create()
{
    $roles = Role::all(); // Отримання всіх ролей
    return view('users.create', compact('roles'));
}
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|exists:roles,name', // Перевірка наявності ролі
    ]);

    // Створення користувача
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    // Призначення ролі
    $user->assignRole($request->role);

    return redirect()->route('users.create')->with('success', 'Користувача успішно створено!');
}
}
