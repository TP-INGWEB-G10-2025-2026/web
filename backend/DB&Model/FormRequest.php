<?php
namespace App\Http\Requests;

use App\Enums\MaterialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|uuid|exists:categories,id',
            'status' => ['required', new Enum(MaterialStatus::class)],
            'description' => 'nullable|string',
        ];
    }
}
?>