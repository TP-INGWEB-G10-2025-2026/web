<?php


namespace App\Http\Resources\Material;

use Illuminate\Http\Request;
use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'status'      => $this->status->value,
            'status_label'=> $this->status->label(),
            'description' => $this->description,
            'category_id' => $this->category_id,
            'category'    => new CategoryResource($this->whenLoaded('category')),
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
