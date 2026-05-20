<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // GET /api/materials - Récupère tous les materials avec leur catégorie
    public function index()
    {
        return Material::with('category')->latest()->get();
    }

    // POST /api/materials - Crée un nouveau material
    public function store(Request $request)
    {
        // Valide les données envoyées
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Crée le material en DB
        $material = Material::create($validated);
        
        // Retourne le material créé avec un code 201
        return response()->json($material, 201);
    }

    // GET /api/materials/{id} - Récupère un material précis
    public function show(Material $material)
    {
        return $material->load('category');
    }

    // PUT/PATCH /api/materials/{id} - Met à jour un material
    public function update(Request $request, Material $material)
    {
        $material->update($request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
        ]));

        return response()->json($material);
    }

    // DELETE /api/materials/{id} - Supprime un material
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->json(['message' => 'Material deleted']);
    }
}