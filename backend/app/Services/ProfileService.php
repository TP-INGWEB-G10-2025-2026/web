<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    /**
     * Update user profile fields.
     *
     * @throws ValidationException
     */
    public function update(User $user, array $data): User
    {
        // Password change
        if (!empty($data['new_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Le mot de passe actuel est incorrect.'],
                ]);
            }
            $user->password = Hash::make($data['new_password']);
        }



        // Update fillable fields
        $user->fill(array_filter([
            'name'  => $data['name']  ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ], fn($v) => $v !== null));

        $user->save();

        return $user->fresh();
    }


}
