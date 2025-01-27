<?php

namespace App\Http\Controllers;

use App\Models\WorkType;
use App\Models\Device;
use Illuminate\Http\Request;

class WorkTypeController extends Controller
{
    public function index()
    {
        $workTypes = WorkType::with('device')->get();
        return view('work-types.index', compact('workTypes'));
    }

    public function create()
    {
        $devices = Device::all();
        return view('work-types.create', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:work_types',
            'device_id' => 'required|exists:devices,id',
        ]);

        WorkType::create($request->all());

        return redirect()->route('work-types.index')->with('success', 'Тип роботи успішно створено.');
    }

    public function edit(WorkType $workType)
    {
        $devices = Device::all();
        return view('work-types.edit', compact('workType', 'devices'));
    }

    public function update(Request $request, WorkType $workType)
    {
        $request->validate([
            'name' => 'required|string|unique:work_types,name,' . $workType->id,
            'device_id' => 'required|exists:devices,id',
        ]);

        $workType->update($request->all());

        return redirect()->route('work-types.index')->with('success', 'Тип роботи успішно оновлено.');
    }

    public function destroy(WorkType $workType)
    {
        $workType->delete();

        return redirect()->route('work-types.index')->with('success', 'Тип роботи успішно видалено.');
    }
}
