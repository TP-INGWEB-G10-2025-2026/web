<?php


namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id'              => ['required', 'uuid', 'exists:users,id'],
            'material_id'          => ['required', 'uuid', 'exists:materials,id'],
            'reservation_id'       => ['sometimes', 'nullable', 'uuid', 'exists:reservations,id'],
            'loan_date'            => ['required', 'date'],
            'expected_return_date' => ['required', 'date', 'after:loan_date'],
            'notes'                => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'              => 'L\'utilisateur est obligatoire.',
            'user_id.exists'                => 'L\'utilisateur sélectionné n\'existe pas.',
            'material_id.required'          => 'Le matériel est obligatoire.',
            'material_id.exists'            => 'Le matériel sélectionné n\'existe pas.',
            'loan_date.required'            => 'La date de prêt est obligatoire.',
            'expected_return_date.required' => 'La date de retour prévue est obligatoire.',
            'expected_return_date.after'    => 'La date de retour doit être après la date de prêt.',
        ];
    }
}
