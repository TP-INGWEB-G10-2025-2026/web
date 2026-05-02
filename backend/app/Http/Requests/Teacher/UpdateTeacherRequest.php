<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $teacherId = $this->route('id');

        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => [
                'sometimes', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($teacherId),
            ],
            'password' => ['sometimes', 'string', 'min:8'],
            'phone'    => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email'  => "L'adresse email n'est pas valide.",
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }
}
