<?php

namespace App\Services;

use App\Enums\Role;
use App\Mail\WelcomeTeacherMail;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class TeacherService
{


    public function list(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->where('role', Role::Teacher)
            ->when($filters['name']       ?? null, fn($q, $v) => $q->where('name',  'like', "%{$v}%"))
            ->when($filters['email']      ?? null, fn($q, $v) => $q->where('email', 'like', "%{$v}%"))
            ->when(isset($filters['is_blocked']), fn($q) => $q->where('is_blocked', filter_var($filters['is_blocked'], FILTER_VALIDATE_BOOLEAN)))
            ->latest()
            ->paginate(15);
    }

    public function findOrFail(string $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        $plain = $data['password'];

        $teacher = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($plain),
            'phone'    => $data['phone'] ?? null,
            'role'     => Role::Teacher,
        ]);

        // Send welcome email via SendGrid
        // Mail::to($teacher->email)->queue(new WelcomeTeacherMail($teacher, $plain));

        // Send welcome SMS via Twilio (only if phone provided)
        // if ($teacher->phone) {
        //     $this->twilioService->sendSms(
        //         to     : $teacher->phone,
        //         message: "Bienvenue {$teacher->name} ! Votre compte a été créé. Email: {$teacher->email} | Mot de passe: {$plain}",
        //     );
        // }

        return $teacher;
    }

    public function update(User $teacher, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $teacher->update(array_filter($data, fn($v) => $v !== null));

        return $teacher->fresh();
    }

    public function delete(User $teacher): void
    {
        $this->guardAdmin($teacher);
        $teacher->delete();
    }

    public function block(User $teacher): User
    {
        $this->guardAdmin($teacher);
        $teacher->update(['is_blocked' => true]);

        // if ($teacher->phone) {
        //     $this->twilioService->sendSms(
        //         to     : $teacher->phone,
        //         message: "Votre compte a été bloqué. Contactez un administrateur.",
        //     );
        // }

        return $teacher->fresh();
    }

    public function unblock(User $teacher): User
    {
        $this->guardAdmin($teacher);
        $teacher->update(['is_blocked' => false]);

        // if ($teacher->phone) {
        //     $this->twilioService->sendSms(
        //         to     : $teacher->phone,
        //         message: "Votre compte a été débloqué. Vous pouvez vous connecter.",
        //     );
        // }

        return $teacher->fresh();
    }

    private function guardAdmin(User $user): void
    {
        if ($user->isAdmin()) {
            throw ValidationException::withMessages([
                'role' => ['Impossible d\'effectuer cette action sur un compte administrateur.'],
            ]);
        }
    }
}
