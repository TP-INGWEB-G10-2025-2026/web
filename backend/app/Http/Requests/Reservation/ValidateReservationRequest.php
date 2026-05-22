<?php


namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class ValidateReservationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'material_id' => ['required', 'uuid', 'exists:materials,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'material_id.required' => 'Le matériel est obligatoire pour valider une réservation.',
            'material_id.exists'   => 'Le matériel sélectionné n\'existe pas.',
        ];
    }
}
