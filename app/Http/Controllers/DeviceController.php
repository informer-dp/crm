<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Показує список усіх пристроїв.
     */
    public function index()
    {
        $devices = Device::all();
        return view('devices.index', compact('devices'));
    }

    /**
     * Показує форму для створення нового пристрою.
     */
    public function create()
    {
        return view('devices.create');
    }

    /**
     * Зберігає новий пристрій у базу даних.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:devices,name|max:255',
            'description' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
        ]);
    
        Device::create($request->all());
    
        return redirect()->route('devices.index')->with('success', 'Пристрій успішно доданий.');
    }

    /**
     * Показує форму редагування наявного пристрою.
     */
    public function edit($id)
    {
        $device = Device::findOrFail($id);
        return view('devices.edit', compact('device'));
    }

    /**
     * Оновлює дані пристрою в базі.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'brand_id' => 'nullable|exists:brands,id',
        ]);

        $device = Device::findOrFail($id);
        $device->update($request->only(['name', 'description']));

        return redirect()->route('devices.index')->with('success', 'Дані пристрою успішно оновлено.');
    }

    /**
     * Видаляє пристрій із бази.
     */
    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Пристрій успішно видалено.');
    }
}
