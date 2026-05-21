<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'materials_count' => $this->whenCounted('materials'),
            'materials' => MaterialResource::collection($this->whenLoaded('materials')),
            'created_at' => $this->created_at,
        ];
    }
}

?>