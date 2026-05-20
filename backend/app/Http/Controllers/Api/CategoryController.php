<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Liste toutes les catégories
     * IMPORTANT : withMaterialCount() vient du scope dans le modèle Category
     * Ça évite de faire N+1 requêtes pour compter les matériaux
     */
    public function index(): JsonResponse
    {
        $categories = Category::withMaterialCount()->get();
        return response()->json($categories);
    }

    /**
     * Crée une nouvelle catégorie
     * IMPORTANT : validation obligatoire pour éviter les doublons et données vides
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201); // 201 = Created
    }

    /**
     * Affiche une catégorie avec ses matériaux
     * IMPORTANT : Route Model Binding. Laravel récupère auto la catégorie via l'UUID dans l'URL
     * load('materials') charge la relation pour éviter une 2e requête après
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('materials');
        return response()->json($category);
    }

    /**
     * Met à jour une catégorie
     * IMPORTANT : unique:categories,name,$category->id ignore l'ID actuel pour la validation
     * Sinon ça bloque même si le nom ne change pas
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    /**
     * Supprime une catégorie
     * IMPORTANT : Soft Delete grâce à SoftDeletes dans le modèle
     * La ligne passe en deleted_at, elle n'est pas supprimée physiquement
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}