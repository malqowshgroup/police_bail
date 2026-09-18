<?php

namespace App\Http\Requests;

use App\Enums\StatutReglement;
use App\Models\Reglement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateVirementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'banque'        => ['nullable', 'string', 'max:120'],
            'date_virement' => ['nullable', 'date'],
            'observations'  => ['nullable', 'string', 'max:1000'],
            'reglements'    => ['required', 'array', 'min:1'],
            'reglements.*'  => ['integer', 'exists:reglements,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $virement = $this->route('virement');

            foreach ((array) $this->reglements as $id) {
                $reglement = Reglement::with('contratBail.logementCivil')->find($id);
                if (! $reglement) {
                    continue;
                }

                // Doit appartenir au bailleur du virement.
                $proprietaireOk = (int) $reglement->contratBail?->logementCivil?->proprietaire_id === (int) $virement?->proprietaire_id;
                // Disponible : en attente sans virement, OU déjà sur CE virement.
                $libre = (! $reglement->virement_id || (int) $reglement->virement_id === (int) $virement?->id);
                $disponible = $reglement->statut === StatutReglement::EnAttenteVirement->value && $libre;

                if (! $proprietaireOk || ! $disponible) {
                    $validator->errors()->add('reglements', "Le règlement #{$reglement->id} n'est plus disponible.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'reglements.required' => 'Sélectionnez au moins un règlement.',
            'reglements.min'      => 'Sélectionnez au moins un règlement.',
        ];
    }
}
