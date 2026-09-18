<?php

namespace App\Http\Requests;

use App\Enums\StatutContrat;
use App\Enums\StatutReglement;
use App\Enums\TypeReglement;
use App\Models\ContratBail;
use App\Models\Reglement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReglementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'contrat_bail_id' => ['required', 'exists:contrats_bail,id'],
            'type_reglement'  => ['required', Rule::in(array_column(TypeReglement::cases(), 'value'))],
            'periode_mois'    => ['required', 'integer', 'min:1', 'max:12'],
            'periode_annee'   => ['required', 'integer', 'min:2020', 'max:2100'],
            'montant'         => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $contrat = ContratBail::find($this->contrat_bail_id);

            if ($contrat && $contrat->statut !== StatutContrat::Actif->value) {
                $validator->errors()->add('contrat_bail_id', 'Seul un contrat actif peut faire l\'objet d\'un règlement.');
            }

            // Anti-doublon : un seul loyer mensuel par contrat et par période.
            if ($this->type_reglement === TypeReglement::LoyerMensuel->value) {
                $existe = Reglement::where('contrat_bail_id', $this->contrat_bail_id)
                    ->where('type_reglement', TypeReglement::LoyerMensuel->value)
                    ->where('periode_mois', $this->periode_mois)
                    ->where('periode_annee', $this->periode_annee)
                    ->where('statut', '!=', StatutReglement::Annule->value)
                    ->exists();

                if ($existe) {
                    $validator->errors()->add('periode_mois', 'Un loyer mensuel existe déjà pour ce contrat sur cette période.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'contrat_bail_id.required' => 'Veuillez sélectionner un contrat.',
            'montant.required'         => 'Le montant est obligatoire.',
        ];
    }
}
