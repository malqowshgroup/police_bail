<?php

namespace App\Http\Requests;

use App\Enums\StatutContrat;
use App\Models\ContratBail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBordereauRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'annee'        => ['required', 'integer', 'min:2020', 'max:2100'],
            'observations' => ['nullable', 'string', 'max:1000'],
            'contrats'     => ['nullable', 'array'],
            'contrats.*'   => ['integer', 'exists:contrats_bail,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ((array) $this->contrats as $id) {
                $contrat = ContratBail::find($id);
                if (! $contrat) {
                    continue;
                }
                if ($contrat->statut !== StatutContrat::EnAttente->value || $contrat->bordereau_id) {
                    $validator->errors()->add('contrats', "Le contrat {$contrat->numero_contrat} n'est plus disponible (déjà rattaché ou non « en attente »).");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'annee.required' => "L'année est obligatoire.",
        ];
    }
}
