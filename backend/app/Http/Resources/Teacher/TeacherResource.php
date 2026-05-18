<?php


namespace App\Http\Resources\Teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'role'       => $this->role->value,
            'is_blocked' => $this->is_blocked,
            'avatar'     => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
