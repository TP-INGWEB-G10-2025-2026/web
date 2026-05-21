<?php
namespace App\Http\Requests;

use App\Enums\MaterialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(MaterialStatus::class)],
        ];
    }
}

?>