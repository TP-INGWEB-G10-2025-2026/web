<?php


namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $teacherId = $this->route('id');

        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($teacherId)],
            'password' => ['sometimes', 'string', 'min:8'],
            'phone'    => ['sometimes', 'nullable', 'string'],
        ];
    }
}
