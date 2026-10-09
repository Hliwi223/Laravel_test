<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_type' => ['required', 'in:evenement,chantier,stand,autre'],
            'battery_chemistry' => ['nullable', 'in:lithium,plomb'],
            'battery_voltage' => ['nullable', 'in:12,24,48'],
            'appliances' => ['required', 'array', 'min:1'],
            'appliances.*.appliance_id' => ['nullable', 'integer'],
            'appliances.*.name' => ['required', 'string', 'max:255'],
            'appliances.*.power_w' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'appliances.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'appliances.*.duration_hours' => ['required', 'numeric', 'min:0.1', 'max:24'],
            'appliances.*.usage_period' => ['required', 'in:jour,nuit,jour_nuit'],
            'appliances.*.has_startup_surge' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'appliances.required' => 'Veuillez ajouter au moins un appareil.',
            'appliances.min' => 'Veuillez ajouter au moins un appareil.',
            'appliances.*.power_w.required' => 'La puissance (W) est obligatoire pour chaque appareil.',
            'appliances.*.power_w.min' => 'La puissance doit être supérieure à 0.',
            'appliances.*.quantity.min' => 'La quantité doit être supérieure à 0.',
            'appliances.*.duration_hours.min' => 'La durée doit être supérieure à 0.',
        ];
    }
}
