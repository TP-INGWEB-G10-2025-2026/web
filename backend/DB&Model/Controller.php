<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\MaterialCollection;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Services\MaterialService;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct(private MaterialService $service) {}

    public function index(Request $request): MaterialCollection
    {
        $query = Material::with('category');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return new MaterialCollection($query->paginate(15));
    }

    public function show(string $id): MaterialResource
    {
        return new MaterialResource(Material::with('category')->findOrFail($id));
    }

    public function store(StoreMaterialRequest $request): MaterialResource
    {
        $material = Material::create($request->validated());
        return new MaterialResource($material->load('category'));
    }

    public function update(UpdateMaterialRequest $request, string $id): MaterialResource
    {
        $material = Material::findOrFail($id);
        $material->update($request->validated());
        return new MaterialResource($material->load('category'));
    }

    public function destroy(string $id)
    {
        $material = Material::findOrFail($id);
        $this->service->delete($material);
        return response()->json(['message' => 'Matériel supprimé']);
    }

    public function updateStatus(UpdateStatusRequest $request, string $id): MaterialResource
    {
        $material = Material::findOrFail($id);
        $material->update(['status' => $request->status]);
        return new MaterialResource($material->load('category'));
    }
}

?>