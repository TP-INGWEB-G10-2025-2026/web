<?php


namespace App\Services;

use App\Enums\MaterialStatus;
use App\Enums\ReturnStatus;
use App\Mail\DamagedMaterialMail;
use App\Models\Loan;
use App\Models\Material;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function list(array $filters): LengthAwarePaginator
    {
        return Loan::query()
            ->with(['user', 'material.category', 'reservation'])
            // Filters
            ->when($filters['user_id']     ?? null, fn($q, $v) => $q->forUser($v))
            ->when($filters['material_id'] ?? null, fn($q, $v) => $q->forMaterial($v))
            ->when($filters['from']        ?? null, fn($q, $v) => $q->where('loan_date', '>=', $v))
            ->when($filters['to']          ?? null, fn($q, $v) => $q->where('loan_date', '<=', $v))
            // Status scopes — mutually exclusive, priority order
            ->when(
                filter_var($filters['overdue']  ?? false, FILTER_VALIDATE_BOOLEAN),
                fn($q) => $q->overdue()
            )
            ->when(
                filter_var($filters['ongoing']  ?? false, FILTER_VALIDATE_BOOLEAN),
                fn($q) => $q->ongoing()
            )
            ->when(
                filter_var($filters['returned'] ?? false, FILTER_VALIDATE_BOOLEAN),
                fn($q) => $q->returned()
            )
            ->latest('loan_date')
            ->paginate(15);
    }

    public function findOrFail(string $id): Loan
    {
        return Loan::with(['user', 'material.category', 'reservation'])->findOrFail($id);
    }

    public function create(array $data): Loan
    {
        $material = Material::findOrFail($data['material_id']);

        if ($material->status !== MaterialStatus::Available) {
            throw ValidationException::withMessages([
                'material_id' => [
                    "Ce matériel n'est pas disponible (statut actuel : {$material->status->label()})."
                ],
            ]);
        }

        $loan = Loan::create([
            'reservation_id'       => $data['reservation_id'] ?? null,
            'user_id'              => $data['user_id'],
            'material_id'          => $data['material_id'],
            'loan_date'            => $data['loan_date'],
            'expected_return_date' => $data['expected_return_date'],
            'notes'                => $data['notes'] ?? null,
        ]);

        $material->update(['status' => MaterialStatus::InUse]);

        return $loan->load(['user', 'material.category', 'reservation']);
    }

    public function processReturn(Loan $loan, array $data): Loan
    {
        if ($loan->isReturned()) {
            throw ValidationException::withMessages([
                'loan' => ['Ce prêt a déjà été retourné.'],
            ]);
        }

        $returnStatus = ReturnStatus::from($data['return_status']);

        $loan->update([
            'actual_return_date' => today()->format('Y-m-d'),
            'return_status'      => $returnStatus->value,
            'notes'              => $data['notes'] ?? $loan->notes,
        ]);

        $newMaterialStatus = match($returnStatus) {
            ReturnStatus::Good    => MaterialStatus::Available,
            ReturnStatus::Damaged => MaterialStatus::Broken,
            ReturnStatus::Lost    => MaterialStatus::Broken,
        };

        $loan->material->update(['status' => $newMaterialStatus]);

        if ($returnStatus !== ReturnStatus::Good) {
            $this->alertAdmin($loan->load(['user', 'material']));
        }

        return $loan->fresh(['user', 'material.category', 'reservation']);
    }

    private function alertAdmin(Loan $loan): void
    {
        $adminEmail = config('services.admin.email');

        if (! $adminEmail) {
            Log::warning('LoanService: ADMIN_EMAIL not configured.');
            return;
        }

        try {
            Mail::to($adminEmail)->queue(new DamagedMaterialMail($loan));
        } catch (\Exception $e) {
            Log::error('LoanService damage alert failed: ' . $e->getMessage());
        }
    }
}
