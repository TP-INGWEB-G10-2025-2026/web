<?php
namespace App\Http\Requests;

use App\Enums\MaterialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMaterialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|uuid|exists:categories,id',
            'status' => ['sometimes', new Enum(MaterialStatus::class)],
            'description' => 'nullable|string',
        ];
    }
}

?>