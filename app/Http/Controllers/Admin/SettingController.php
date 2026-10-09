<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const KEYS = [
        'whatsapp_number' => 'Numéro WhatsApp (ex: 21612345678)',
        'default_system_efficiency' => 'Rendement système (0-1)',
        'default_energy_margin' => 'Marge énergie (0-1)',
        'default_solar_hours' => 'Heures de soleil / jour',
        'default_inverter_factor' => 'Facteur onduleur',
        'default_lithium_dod' => 'DoD lithium (0-1)',
        'default_lead_dod' => 'DoD plomb (0-1)',
        'default_motor_surge_factor' => 'Facteur pic moteur',
        'default_delivery_fee' => 'Frais de livraison par défaut (TND)',
    ];

    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();

        return view('admin.settings.index', [
            'labels' => self::KEYS,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'default_system_efficiency' => ['nullable', 'numeric', 'min:0.1', 'max:1'],
            'default_energy_margin' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'default_solar_hours' => ['nullable', 'numeric', 'min:1', 'max:12'],
            'default_inverter_factor' => ['nullable', 'numeric', 'min:1', 'max:3'],
            'default_lithium_dod' => ['nullable', 'numeric', 'min:0.1', 'max:1'],
            'default_lead_dod' => ['nullable', 'numeric', 'min:0.1', 'max:1'],
            'default_motor_surge_factor' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'default_delivery_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return back()->with('success', 'Paramètres enregistrés.');
    }
}
