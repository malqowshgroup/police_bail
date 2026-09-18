<?php

namespace App\Http\Requests;

use App\Enums\StatutContrat;
use App\Models\ContratBail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBordereauRequest extends FormRequest
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
            $bordereau = $this->route('bordereau');

            foreach ((array) $this->contrats as $id) {
                $contrat = ContratBail::find($id);
                if (! $contrat) {
                    continue;
                }
                // Accepté s'il est « en attente » ET (libre OU déjà sur CE bordereau).
                $libre = ! $contrat->bordereau_id || (int) $contrat->bordereau_id === (int) $bordereau?->id;
                if ($contrat->statut !== StatutContrat::EnAttente->value || ! $libre) {
                    $validator->errors()->add('contrats', "Le contrat {$contrat->numero_contrat} n'est plus disponible.");
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
