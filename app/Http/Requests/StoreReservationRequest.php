<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // réservation publique
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s()-]{6,20}$/'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'cin' => ['nullable', 'string', 'max:20'],
            'project_type' => ['required', 'in:evenement,chantier,stand,autre'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'delivery_required' => ['required', 'in:0,1'],
            'pack_id' => ['nullable', 'exists:packs,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
            'customer_message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Veuillez indiquer votre nom.',
            'customer_phone.required' => 'Le numéro de téléphone est obligatoire.',
            'customer_phone.regex' => 'Le numéro de téléphone n\'est pas valide.',
            'customer_email.email' => 'L\'adresse email n\'est pas valide.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou une date future.',
            'end_date.after_or_equal' => 'La date de fin doit être après (ou égale à) la date de début.',
            'location.required' => 'Veuillez indiquer le lieu de l\'installation.',
        ];
    }
}
