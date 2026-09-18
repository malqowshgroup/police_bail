<?php

namespace App\Http\Requests\Parametres;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocaliteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('localite')?->id;

        return [
            'code'    => ['nullable', 'string', 'max:20', Rule::unique('localites', 'code')->ignore($id)],
            'libelle' => ['required', 'string', 'max:120'],
            'actif'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé de la localité est obligatoire.',
            'code.unique'      => 'Ce code de localité est déjà utilisé.',
        ];
    }
}
