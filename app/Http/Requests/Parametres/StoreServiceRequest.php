<?php

namespace App\Http\Requests\Parametres;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'code'     => ['nullable', 'string', 'max:20', Rule::unique('services', 'code')],
            'libelle'  => ['required', 'string', 'max:160'],
            'localite' => ['nullable', 'string', 'max:120'],
            'actif'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé du service est obligatoire.',
            'code.unique'      => 'Ce code de service est déjà utilisé.',
        ];
    }
}
