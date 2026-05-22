<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        // Ignore l'unicité pour la catégorie en cours de modification
        $categoryId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Une catégorie avec ce nom existe déjà.',
            'name.max'    => 'Le nom ne peut pas dépasser 255 caractères.',
        ];
    }
}
