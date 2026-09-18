<?php

namespace App\Http\Requests\Parametres;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'libelle'   => ['required', 'string', 'max:120'],
            'taux_bail' => ['required', 'numeric', 'min:0'],
            'ordre'     => ['nullable', 'integer', 'min:0'],
            'actif'     => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required'   => 'Le libellé du grade est obligatoire.',
            'taux_bail.required' => 'Le taux de bail est obligatoire.',
        ];
    }
}
