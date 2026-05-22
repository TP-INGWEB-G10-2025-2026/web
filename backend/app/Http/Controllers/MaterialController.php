<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
use App\Http\Requests\Material\UpdateStatusRequest;
use App\Http\Resources\Material\MaterialCollection;
use App\Http\Resources\Material\MaterialResource;
use App\Services\MaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct(private readonly MaterialService $materialService) {}

    /** GET /api/v1/materials */
    public function index(Request $request): JsonResponse
    {
        $materials = $this->materialService->list(
            $request->only(['name', 'category_id', 'status'])
        );

        return response()->json(new MaterialCollection($materials));
    }

    /** GET /api/v1/materials/{id} */
    public function show(string $id): JsonResponse
    {
        $material = $this->materialService->findOrFail($id);

        return response()->json(['data' => new MaterialResource($material)]);
    }

    /** POST /api/v1/materials */
    public function store(StoreMaterialRequest $request): JsonResponse
    {
        $material = $this->materialService->create($request->validated());

        return response()->json([
            'message' => 'Matériel créé avec succès.',
            'data'    => new MaterialResource($material),
        ], 201);
    }

    /** PUT /api/v1/materials/{id} */
    public function update(UpdateMaterialRequest $request, string $id): JsonResponse
    {
        $material = $this->materialService->findOrFail($id);
        $material = $this->materialService->update($material, $request->validated());

        return response()->json([
            'message' => 'Matériel mis à jour avec succès.',
            'data'    => new MaterialResource($material),
        ]);
    }

    /** DELETE /api/v1/materials/{id} */
    public function destroy(string $id): JsonResponse
    {
        $material = $this->materialService->findOrFail($id);
        $this->materialService->delete($material);

        return response()->json(['message' => 'Matériel supprimé avec succès.']);
    }

    /** PATCH /api/v1/materials/{id}/status */
    public function updateStatus(UpdateStatusRequest $request, string $id): JsonResponse
    {
        $material = $this->materialService->findOrFail($id);
        $material = $this->materialService->updateStatus($material, $request->validated('status'));

        return response()->json([
            'message' => 'Statut mis à jour avec succès.',
            'data'    => new MaterialResource($material),
        ]);
    }
}
