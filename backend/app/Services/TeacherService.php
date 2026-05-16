<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TeacherService
{
    // ─────────────────────────────────────────────────────────────
    // Lister les enseignants
    // Filtres : ?name= &email= &is_blocked=true|false
    // ─────────────────────────────────────────────────────────────

    public function list(array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->where('role', 'teacher')
            ->when(
                $filters['name'] ?? null,
                fn ($q, $v) => $q->where('name', 'like', "%{$v}%")
            )
            ->when(
                $filters['email'] ?? null,
                fn ($q, $v) => $q->where('email', 'like', "%{$v}%")
            )
            ->when(
                array_key_exists('is_blocked', $filters),
                fn ($q) => $q->where(
                    'is_blocked',
                    filter_var($filters['is_blocked'], FILTER_VALIDATE_BOOLEAN)
                )
            )
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    // ─────────────────────────────────────────────────────────────
    // Trouver un enseignant — 404 automatique si introuvable
    // ─────────────────────────────────────────────────────────────

    public function find(string $id): User
    {
        return User::where('role', 'teacher')->findOrFail($id);
    }

    // ─────────────────────────────────────────────────────────────
    // Créer un enseignant
    // Hash::make explicite (le modèle n'a pas le cast 'hashed')
    // ─────────────────────────────────────────────────────────────

    public function create(array $data): User
    {
        $plainPassword = $data['password'];

        $teacher = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($plainPassword),  // Hash explicite
            'phone'      => $data['phone'] ?? null,
            'role'       => 'teacher',
            'is_blocked' => false,
        ]);

        $this->sendWelcomeEmail($teacher, $plainPassword);

        return $teacher;
    }

    // ─────────────────────────────────────────────────────────────
    // Modifier un enseignant
    // ─────────────────────────────────────────────────────────────

    public function update(User $teacher, array $data): User
    {
        // Hash explicite si le mot de passe est fourni
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $teacher->update($data);

        return $teacher->fresh();
    }

    // ─────────────────────────────────────────────────────────────
    // Supprimer — Soft Delete (deleted_at renseigné)
    // ─────────────────────────────────────────────────────────────

    public function delete(User $teacher): void
    {
        $this->guardAdmin($teacher, 'supprimer');
        $teacher->delete();
    }

    // ─────────────────────────────────────────────────────────────
    // Bloquer — is_blocked = true + révocation tokens
    // ─────────────────────────────────────────────────────────────

    public function block(User $teacher): User
    {
        $this->guardAdmin($teacher, 'bloquer');

        $teacher->update(['is_blocked' => true]);
        $teacher->tokens()->delete();  // Déconnexion forcée

        return $teacher->fresh();
    }

    // ─────────────────────────────────────────────────────────────
    // Débloquer — is_blocked = false
    // ─────────────────────────────────────────────────────────────

    public function unblock(User $teacher): User
    {
        $this->guardAdmin($teacher, 'débloquer');

        $teacher->update(['is_blocked' => false]);

        return $teacher->fresh();
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers privés
    // ─────────────────────────────────────────────────────────────

    private function guardAdmin(User $user, string $action): void
    {
        if ($user->isAdmin()) {
            abort(403, "Impossible de {$action} un compte administrateur.");
        }
    }

    private function sendWelcomeEmail(User $teacher, string $plainPassword): void
    {
        try {
            Mail::raw(
                "Bonjour {$teacher->name},\n\n"
                . "Votre compte enseignant a été créé sur la plateforme GMP\n"
                . "(Gestion du Matériel Pédagogique — Université de Ngaoundéré).\n\n"
                . "Vos identifiants :\n"
                . "  Email        : {$teacher->email}\n"
                . "  Mot de passe : {$plainPassword}\n\n"
                . "⚠️  Changez votre mot de passe dès votre première connexion.\n\n"
                . "Cordialement,\nL'administration — Département MI",
                fn ($m) => $m
                    ->to($teacher->email)
                    ->subject('[GMP] Création de votre compte enseignant')
            );
        } catch (\Throwable $e) {
            Log::error("Email bienvenue [{$teacher->email}] : " . $e->getMessage());
        }
    }
}
