<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required'         => 'La date de début est obligatoire.',
            'start_date.after_or_equal'   => 'La date de début doit être aujourd\'hui ou dans le futur.',
            'end_date.required'           => 'La date de fin est obligatoire.',
            'end_date.after'              => 'La date de fin doit être après la date de début.',
        ];
    }
}
