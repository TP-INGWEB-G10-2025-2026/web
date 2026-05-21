<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $service) {}

    public function index()
    {
        return CategoryResource::collection(Category::withMaterialCount()->get());
    }

    public function show(string $id)
    {
        return new CategoryResource(Category::with('materials')->findOrFail($id));
    }

    public function store(StoreCategoryRequest $request)
    {
        return new CategoryResource(Category::create($request->validated()));
    }

    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $this->service->delete($category);
        return response()->json(['message' => 'Catégorie supprimée']);
    }
}

?>