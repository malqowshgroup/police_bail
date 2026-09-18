<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogementCivilRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reference'        => ['required', 'string', 'max:50', 'unique:logements_civils'],
            'localite_id'      => ['required', 'exists:localites,id'],
            'proprietaire_id'  => ['required', 'exists:proprietaires,id'],
            'quartier'         => ['required', 'string', 'max:100'],
            'ilot'             => ['nullable', 'string', 'max:20'],
            'lot'              => ['nullable', 'string', 'max:20'],
            'adresse_complete' => ['nullable', 'string', 'max:500'],
            'actif'            => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference.unique'       => 'Cette référence est déjà utilisée.',
            'proprietaire_id.required' => 'Veuillez sélectionner un propriétaire.',
            'localite_id.required'   => 'Veuillez sélectionner une localité.',
            'quartier.required'      => 'Le quartier est obligatoire.',
        ];
    }
}
