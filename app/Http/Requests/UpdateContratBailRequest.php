<?php

namespace App\Http\Requests;

use App\Enums\StatutContrat;
use App\Models\ContratBail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateContratBailRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'policier_id'         => ['required', 'exists:policiers,id'],
            'logement_civil_id'   => ['required', 'exists:logements_civils,id'],
            'bordereau_id'        => ['nullable', 'exists:bordereaux,id'],
            'date_debut'          => ['required', 'date'],
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'preavis_mois'        => ['nullable', 'integer', 'min:0', 'max:24'],
            'avec_arrieres'       => ['boolean'],
            'nb_mois_arrieres'    => ['nullable', 'integer', 'min:0', 'max:120'],
            'date_debut_arrieres' => ['nullable', 'date', 'required_if:avec_arrieres,1'],
            'observations'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $contratId = $this->route('contrat')?->id;

            if ($this->policier_id && $this->contratEnCoursExiste('policier_id', $this->policier_id, $contratId)) {
                $validator->errors()->add('policier_id', 'Ce policier possède déjà un autre contrat de bail en cours.');
            }

            if ($this->logement_civil_id && $this->contratEnCoursExiste('logement_civil_id', $this->logement_civil_id, $contratId)) {
                $validator->errors()->add('logement_civil_id', 'Ce logement est déjà rattaché à un autre contrat de bail en cours.');
            }
        });
    }

    private function contratEnCoursExiste(string $colonne, int $valeur, ?int $exclureId): bool
    {
        return ContratBail::where($colonne, $valeur)
            ->when($exclureId, fn($q) => $q->where('id', '!=', $exclureId))
            ->whereIn('statut', [
                StatutContrat::EnAttente->value,
                StatutContrat::Actif->value,
                StatutContrat::Suspendu->value,
                StatutContrat::EnResiliation->value,
            ])
            ->exists();
    }

    public function messages(): array
    {
        return [
            'policier_id.required'       => 'Veuillez sélectionner un policier.',
            'logement_civil_id.required' => 'Veuillez sélectionner un logement.',
            'date_debut.required'        => 'La date de début est obligatoire.',
            'date_fin.after'             => 'La date de fin doit être postérieure à la date de début.',
            'date_debut_arrieres.required_if' => 'Veuillez préciser la date de début des arriérés.',
        ];
    }
}
