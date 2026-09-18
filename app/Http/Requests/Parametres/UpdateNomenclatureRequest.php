<?php

namespace App\Http\Requests\Parametres;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNomenclatureRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Le code n'est jamais modifiable (préserve l'intégrité des données métier).
        return [
            'libelle'       => ['required', 'string', 'max:160'],
            'couleur_badge' => ['nullable', 'string', 'max:120'],
            'couleur_dot'   => ['nullable', 'string', 'max:60'],
            'ordre'         => ['nullable', 'integer', 'min:0'],
            'actif'         => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
        ];
    }
}
