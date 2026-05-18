<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService) {}

    /**
     * GET /api/v1/profile
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * PUT /api/v1/profile
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->update(
            user  : $request->user(),
            data  : $request->validated(),
        );

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user'    => new UserResource($user),
        ]);
    }
}
