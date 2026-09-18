<?php

namespace App\Http\Requests;

use App\Enums\TypeReglement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReglementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type_reglement' => ['required', Rule::in(array_column(TypeReglement::cases(), 'value'))],
            'periode_mois'   => ['required', 'integer', 'min:1', 'max:12'],
            'periode_annee'  => ['required', 'integer', 'min:2020', 'max:2100'],
            'montant'        => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant est obligatoire.',
        ];
    }
}
