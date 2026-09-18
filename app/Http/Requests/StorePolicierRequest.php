<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePolicierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'matricule'              => 'required|string|max:20|unique:policiers',
            'nom'                    => 'required|string|max:100',
            'prenoms'                => 'required|string|max:150',
            'sexe'                   => 'required|in:M,F',
            'grade_id'               => 'required|exists:grades,id',
            'service_id'             => 'nullable|exists:services,id',
            'localite_id'            => 'nullable|exists:localites,id',
            'statut'                 => 'required|in:actif,suspendu,retraite,decede,radie,disponibilite,demission,hors_cadre,detachement,stagiaire',
            'date_naissance'         => 'nullable|date',
            'date_prise_service'     => 'nullable|date',
            'proprietaire_logement'  => 'boolean',
        ];
    }
}
