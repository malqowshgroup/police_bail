<?php

namespace App\Http\Requests;

use App\Enums\TypeDocument;
use App\Models\DocumentLien;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fichier'       => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,tiff,tif', 'max:10240'],
            'type_document' => ['required', Rule::in(array_column(TypeDocument::cases(), 'value'))],
            'observations'  => ['nullable', 'string', 'max:1000'],
            'entite_type'   => ['nullable', Rule::in(array_keys(DocumentLien::ENTITES))],
            'entite_id'     => ['nullable', 'integer', 'required_with:entite_type'],
        ];
    }

    public function messages(): array
    {
        return [
            'fichier.required'     => 'Veuillez sélectionner un fichier.',
            'fichier.mimes'        => 'Format accepté : PDF, JPG, PNG ou TIFF.',
            'fichier.max'          => 'Le fichier ne doit pas dépasser 10 Mo.',
            'type_document.required' => 'Veuillez choisir un type de document.',
            'entite_id.required_with' => 'Veuillez préciser l\'élément à rattacher.',
        ];
    }
}
