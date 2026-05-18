<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'                     => ['sometimes', 'string', 'max:255'],
            'email'                    => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'phone'                    => ['sometimes', 'nullable', 'string'],
           'current_password'         => ['required_with:new_password', 'string'],
            'new_password'             => ['sometimes', 'nullable', 'min:8', 'confirmed'],
            'new_password_confirmation'=> ['required_with:new_password', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'                    => 'Cette adresse email est déjà utilisée.',
            'current_password.required_with'  => 'Le mot de passe actuel est requis pour changer le mot de passe.',
            'new_password.confirmed'          => 'La confirmation du nouveau mot de passe ne correspond pas.',
            'new_password.min'                => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
         ];
    }
}
