<?php

namespace App\Http\Requests\Parametres;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles'    => ['required', 'array', 'min:1'],
            'roles.*'  => [Rule::in(Role::where('guard_name', 'web')->pluck('name'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Le nom est obligatoire.',
            'email.required'    => "L'adresse e-mail est obligatoire.",
            'email.unique'      => 'Cette adresse e-mail est déjà utilisée.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'roles.required'    => 'Sélectionnez au moins un rôle.',
            'roles.min'         => 'Sélectionnez au moins un rôle.',
        ];
    }
}
