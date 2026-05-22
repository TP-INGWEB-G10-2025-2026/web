<?php


namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::withMaterialCount()->orderBy('name')->get();
    }

    public function findOrFail(string $id): Category
    {
        return Category::withCount('materials')
            ->with('materials')
            ->findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create(['name' => $data['name']]);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update(['name' => $data['name']]);
        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        if ($category->materials()->count() > 0) {
            throw ValidationException::withMessages([
                'category' => [
                    "Impossible de supprimer cette catégorie : {$category->materials()->count()} matériel(s) y sont associés. Réaffectez-les d'abord."
                ],
            ]);
        }

        $category->delete();
    }
}
