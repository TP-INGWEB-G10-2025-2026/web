<?php


namespace App\Http\Requests\Loan;

use App\Enums\ReturnStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReturnLoanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'return_status' => ['required', new Enum(ReturnStatus::class)],
            'notes'         => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_status.required' => 'L\'état de retour est obligatoire.',
        ];
    }
}
