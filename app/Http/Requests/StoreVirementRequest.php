<?php

namespace App\Http\Requests;

use App\Enums\StatutReglement;
use App\Models\Reglement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVirementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'proprietaire_id' => ['required', 'exists:proprietaires,id'],
            'banque'          => ['nullable', 'string', 'max:120'],
            'date_virement'   => ['nullable', 'date'],
            'observations'    => ['nullable', 'string', 'max:1000'],
            'reglements'      => ['required', 'array', 'min:1'],
            'reglements.*'    => ['integer', 'exists:reglements,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ((array) $this->reglements as $id) {
                $reglement = Reglement::with('contratBail.logementCivil')->find($id);
                if (! $reglement) {
                    continue;
                }

                $proprietaireOk = (int) $reglement->contratBail?->logementCivil?->proprietaire_id === (int) $this->proprietaire_id;
                $disponible = $reglement->statut === StatutReglement::EnAttenteVirement->value && ! $reglement->virement_id;

                if (! $proprietaireOk || ! $disponible) {
                    $validator->errors()->add('reglements', "Le règlement #{$reglement->id} n'est plus disponible pour ce propriétaire.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'proprietaire_id.required' => 'Veuillez sélectionner un propriétaire bailleur.',
            'reglements.required'      => 'Sélectionnez au moins un règlement à virer.',
            'reglements.min'           => 'Sélectionnez au moins un règlement à virer.',
        ];
    }
}
