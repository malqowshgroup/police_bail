<?php

namespace App\Http\Requests;

use App\Enums\TypeDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type_document' => ['required', Rule::in(array_column(TypeDocument::cases(), 'value'))],
            'observations'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'type_document.required' => 'Veuillez choisir un type de document.',
        ];
    }
}
