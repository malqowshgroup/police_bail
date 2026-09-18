<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProprietaireRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type_personne'           => ['required', 'in:physique,morale'],
            'nom'                     => ['required', 'string', 'max:100'],
            'prenoms'                 => ['nullable', 'string', 'max:150'],
            'raison_sociale'          => ['required_if:type_personne,morale', 'nullable', 'string', 'max:200'],
            'num_piece_identite'      => ['required', 'string', 'max:50', 'unique:proprietaires,num_piece_identite'],
            'type_piece'              => ['required', 'in:cni,passeport,sejour'],
            'telephone'               => ['required', 'string', 'max:30'],
            'telephone2'              => ['nullable', 'string', 'max:30'],
            'email'                   => ['nullable', 'email', 'max:150'],
            'adresse_postale'         => ['nullable', 'string', 'max:255'],
            'num_compte_contribuable' => ['nullable', 'string', 'max:50'],
            'localite_id'             => ['nullable', 'exists:localites,id'],
            'actif'                   => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['actif' => $this->boolean('actif')]);
    }
}
