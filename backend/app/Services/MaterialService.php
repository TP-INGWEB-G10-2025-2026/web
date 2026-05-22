<?php


namespace App\Services;

use App\Enums\MaterialStatus;
use App\Models\Material;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class MaterialService
{
    public function list(array $filters): LengthAwarePaginator
    {
        return Material::query()
            ->with('category')
            ->when($filters['name']        ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('category_id', $v))
            ->when($filters['status']      ?? null, fn($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15);
    }

    public function findOrFail(string $id): Material
    {
        return Material::with('category')->findOrFail($id);
    }

    public function create(array $data): Material
    {
        $material = Material::create($data);
        return $material->load('category');
    }

    public function update(Material $material, array $data): Material
    {
        $material->update($data);
        return $material->fresh('category');
    }

    public function updateStatus(Material $material, string $status): Material
    {
        $material->update(['status' => $status]);
        return $material->fresh('category');
    }

    public function delete(Material $material): void
    {
        if ($material->status === MaterialStatus::InUse) {
            throw ValidationException::withMessages([
                'material' => ['Impossible de supprimer un matériel actuellement en utilisation.'],
            ]);
        }

        $material->delete();
    }
}
