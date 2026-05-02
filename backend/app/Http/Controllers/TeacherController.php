<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Http\Resources\Teacher\TeacherCollection;
use App\Http\Resources\Teacher\TeacherResource;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(
        private readonly TeacherService $teacherService
    ) {}

    // ──────────────────────────────────────────────────────────────
    // GET /api/v1/teachers
    // Filtres optionnels : ?name= &email= &is_blocked=true|false
    // ──────────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $teachers = $this->teacherService->list(
            $request->only(['name', 'email', 'is_blocked'])
        );

        return response()->json(new TeacherCollection($teachers));
    }

    // ──────────────────────────────────────────────────────────────
    // GET /api/v1/teachers/{id}
    // ──────────────────────────────────────────────────────────────

    public function show(string $id): JsonResponse
    {
        $teacher = $this->teacherService->find($id);

        return response()->json(new TeacherResource($teacher));
    }

    // ──────────────────────────────────────────────────────────────
    // POST /api/v1/teachers
    // Body : { name, email, password, phone? }
    // ──────────────────────────────────────────────────────────────

    public function store(StoreTeacherRequest $request): JsonResponse
    {
        $teacher = $this->teacherService->create($request->validated());

        return response()->json([
            'message' => 'Enseignant créé avec succès.',
            'teacher' => new TeacherResource($teacher),
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────
    // PUT /api/v1/teachers/{id}
    // Body : { name?, email?, password?, phone? }
    // ──────────────────────────────────────────────────────────────

    public function update(UpdateTeacherRequest $request, string $id): JsonResponse
    {
        $teacher = $this->teacherService->find($id);
        $updated = $this->teacherService->update($teacher, $request->validated());

        return response()->json([
            'message' => 'Enseignant mis à jour avec succès.',
            'teacher' => new TeacherResource($updated),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // DELETE /api/v1/teachers/{id}
    // Soft delete — deleted_at renseigné, données conservées en base
    // ──────────────────────────────────────────────────────────────

    public function destroy(string $id): JsonResponse
    {
        $teacher = $this->teacherService->find($id);
        $this->teacherService->delete($teacher);

        return response()->json([
            'message' => 'Enseignant supprimé avec succès.',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // PATCH /api/v1/teachers/{id}/block
    // ──────────────────────────────────────────────────────────────

    public function block(string $id): JsonResponse
    {
        $teacher = $this->teacherService->find($id);
        $updated = $this->teacherService->block($teacher);

        return response()->json([
            'message' => 'Enseignant bloqué avec succès.',
            'teacher' => new TeacherResource($updated),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // PATCH /api/v1/teachers/{id}/unblock
    // ──────────────────────────────────────────────────────────────

    public function unblock(string $id): JsonResponse
    {
        $teacher = $this->teacherService->find($id);
        $updated = $this->teacherService->unblock($teacher);

        return response()->json([
            'message' => 'Enseignant débloqué avec succès.',
            'teacher' => new TeacherResource($updated),
        ]);
    }
}
