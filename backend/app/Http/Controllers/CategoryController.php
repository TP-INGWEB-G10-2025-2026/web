<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    /** GET /api/v1/categories */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->list();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /** GET /api/v1/categories/{id} */
    public function show(string $id): JsonResponse
    {
        $category = $this->categoryService->findOrFail($id);

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    /** POST /api/v1/categories */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'message' => 'Catégorie créée avec succès.',
            'data'    => new CategoryResource($category),
        ], 201);
    }

    /** PUT /api/v1/categories/{id} */
    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category = $this->categoryService->update($category, $request->validated());

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès.',
            'data'    => new CategoryResource($category),
        ]);
    }

    /** DELETE /api/v1/categories/{id} */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $this->categoryService->delete($category);

        return response()->json([
            'message' => 'Catégorie supprimée avec succès.',
        ]);
    }
}
