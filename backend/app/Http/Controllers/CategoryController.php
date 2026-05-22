<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    // ─────────────────────────────────────────────────────────────
    // GET /api/v1/categories
    // Liste toutes les catégories avec le nombre de matériels liés
    // ─────────────────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->list();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/v1/categories/{id}
    // Détails d'une catégorie avec ses matériels associés
    // ─────────────────────────────────────────────────────────────

    public function show(string $id): JsonResponse
    {
        $category = $this->categoryService->find($id);

        return response()->json(
            new CategoryResource($category)
        );
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/v1/categories
    // Créer une nouvelle catégorie
    // ─────────────────────────────────────────────────────────────

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'message'  => 'Catégorie créée avec succès.',
            'category' => new CategoryResource($category),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────
    // PUT /api/v1/categories/{id}
    // Modifier une catégorie
    // ─────────────────────────────────────────────────────────────

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $category = $this->categoryService->find($id);
        $updated  = $this->categoryService->update($category, $request->validated());

        return response()->json([
            'message'  => 'Catégorie mise à jour avec succès.',
            'category' => new CategoryResource($updated),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /api/v1/categories/{id}
    // Supprimer une catégorie (interdit si matériels liés → 422)
    // ─────────────────────────────────────────────────────────────

    public function destroy(string $id): JsonResponse
    {
        $category = $this->categoryService->find($id);
        $this->categoryService->delete($category);

        return response()->json([
            'message' => 'Catégorie supprimée avec succès.',
        ]);
    }
}
