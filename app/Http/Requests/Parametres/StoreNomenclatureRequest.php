<?php

namespace App\Http\Requests\Parametres;

use App\Models\Nomenclature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNomenclatureRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => str($this->code)->snake()->lower()->toString()]);
        }
    }

    public function rules(): array
    {
        return [
            'categorie' => ['required', Rule::in(array_keys(Nomenclature::CATEGORIES))],
            'code'      => [
                'required', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/',
                Rule::unique('nomenclatures', 'code')->where('categorie', $this->categorie),
            ],
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
            'code.regex'   => 'Le code ne doit contenir que des minuscules, chiffres et tirets bas.',
            'code.unique'  => 'Ce code existe déjà dans cette catégorie.',
            'libelle.required' => 'Le libellé est obligatoire.',
        ];
    }
}
