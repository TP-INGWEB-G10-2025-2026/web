<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    // ─── Lister toutes les catégories ────────────────────────────

    public function list(): Collection
    {
        return Category::withMaterialCount()
            ->orderBy('name')
            ->get();
    }

    // ─── Trouver une catégorie ────────────────────────────────────

    public function find(string $id): Category
    {
        // Charge aussi les matériels associés
        return Category::withCount('materials')
            ->with('materials')
            ->findOrFail($id);
    }

    // ─── Créer une catégorie ──────────────────────────────────────

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    // ─── Modifier une catégorie ───────────────────────────────────

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    // ─── Supprimer une catégorie ──────────────────────────────────
    // Interdit si des matériels sont liés → 422

    public function delete(Category $category): void
    {
        if ($category->materials()->exists()) {
            abort(422, 'Impossible de supprimer cette catégorie : des matériels y sont associés.');
        }

        $category->delete();
    }
}
