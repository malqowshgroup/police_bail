<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLogementCivilRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reference'        => ['required', 'string', 'max:50', 'unique:logements_civils,reference,'.$this->logement_civil->id],
            'localite_id'      => ['required', 'exists:localites,id'],
            'proprietaire_id'  => ['required', 'exists:proprietaires,id'],
            'quartier'         => ['required', 'string', 'max:100'],
            'ilot'             => ['nullable', 'string', 'max:20'],
            'lot'              => ['nullable', 'string', 'max:20'],
            'adresse_complete' => ['nullable', 'string', 'max:500'],
            'actif'            => ['boolean'],
        ];
    }
}
