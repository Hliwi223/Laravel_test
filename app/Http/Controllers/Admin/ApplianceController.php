<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appliance;
use App\Models\Setting;
use Illuminate\Http\Request;

class ApplianceController extends Controller
{
    public function index()
    {
        return view('admin.appliances.index', [
            'appliances' => Appliance::orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_power_w' => ['required', 'integer', 'min:1'],
            'default_duration_hours' => ['required', 'numeric', 'min:0.1', 'max:24'],
            'has_startup_surge' => ['nullable', 'boolean'],
            'startup_factor' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);
        $data['has_startup_surge'] = $request->boolean('has_startup_surge');
        Appliance::create($data);

        return back()->with('success', 'Appareil ajouté.');
    }

    public function update(Request $request, Appliance $appliance)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_power_w' => ['required', 'integer', 'min:1'],
            'default_duration_hours' => ['required', 'numeric', 'min:0.1', 'max:24'],
            'has_startup_surge' => ['nullable', 'boolean'],
            'startup_factor' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);
        $data['has_startup_surge'] = $request->boolean('has_startup_surge');
        $appliance->update($data);

        return back()->with('success', 'Appareil mis à jour.');
    }

    public function destroy(Appliance $appliance)
    {
        $appliance->delete();

        return back()->with('success', 'Appareil supprimé.');
    }
}
