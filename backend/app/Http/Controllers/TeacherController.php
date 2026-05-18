<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Http\Resources\Teacher\TeacherCollection;
use App\Http\Resources\Teacher\TeacherResource;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(private readonly TeacherService $teacherService) {}

    /** GET /api/v1/teachers */
    public function index(Request $request): JsonResponse
    {
        $teachers = $this->teacherService->list($request->only(['name', 'email', 'is_blocked']));

        return response()->json(new TeacherCollection($teachers));
    }

    /** GET /api/v1/teachers/{id} */
    public function show(string $id): JsonResponse
    {
        $teacher = $this->teacherService->findOrFail($id);

        return response()->json(['data' => new TeacherResource($teacher)]);
    }

    /** POST /api/v1/teachers */
    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $teacher = $this->teacherService->create($request->validated());

        return response()->json([
            'message' => 'Enseignant créé avec succès.',
            'data'    => new TeacherResource($teacher),
        ], 201);
    }

    /** PUT /api/v1/teachers/{id} */
    public function update(UpdateTeacherRequest $request, string $id): JsonResponse
    {
        $teacher = $this->teacherService->findOrFail($id);
        $teacher = $this->teacherService->update($teacher, $request->validated());

        return response()->json([
            'message' => 'Enseignant mis à jour avec succès.',
            'data'    => new TeacherResource($teacher),
        ]);
    }

    /** DELETE /api/v1/teachers/{id} */
    public function destroy(string $id): JsonResponse
    {
        $teacher = $this->teacherService->findOrFail($id);
        $this->teacherService->delete($teacher);

        return response()->json(['message' => 'Enseignant supprimé avec succès.']);
    }

    /** PATCH /api/v1/teachers/{id}/block */
    public function block(string $id): JsonResponse
    {
        $teacher = $this->teacherService->findOrFail($id);
        $teacher = $this->teacherService->block($teacher);

        return response()->json([
            'message' => 'Enseignant bloqué avec succès.',
            'data'    => new TeacherResource($teacher),
        ]);
    }

    /** PATCH /api/v1/teachers/{id}/unblock */
    public function unblock(string $id): JsonResponse
    {
        $teacher = $this->teacherService->findOrFail($id);
        $teacher = $this->teacherService->unblock($teacher);

        return response()->json([
            'message' => 'Enseignant débloqué avec succès.',
            'data'    => new TeacherResource($teacher),
        ]);
    }
}
