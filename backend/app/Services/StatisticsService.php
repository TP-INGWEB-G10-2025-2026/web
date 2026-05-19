<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    // ─────────────────────────────────────────────────────────────
    // Statistiques globales d'utilisation
    // ─────────────────────────────────────────────────────────────

    public function usageStats(?string $period = null, ?int $year = null): array
    {
        return [
            'top_materials'      => $this->topMaterials(),
            'usage_by_category'  => $this->usageByCategory(),
            'loans_per_month'    => $this->loansPerMonth($year),
            'active_loans'       => $this->activeLoans(),
            'overdue_loans'      => $this->overdueLoans(),
            'summary'            => $this->summary(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Top 10 matériels les plus utilisés
    // GET /api/v1/statistics/materials/top
    // ─────────────────────────────────────────────────────────────

    public function topMaterials(int $limit = 10): array
    {
        return Loan::select('material_id', DB::raw('COUNT(*) as total_loans'))
            ->with('material:id,name,status')
            ->groupBy('material_id')
            ->orderByDesc('total_loans')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'material_id'   => $row->material_id,
                'material_name' => $row->material?->name,
                'status'        => $row->material?->status,
                'total_loans'   => $row->total_loans,
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────
    // Taux d'utilisation par catégorie
    // ─────────────────────────────────────────────────────────────

    public function usageByCategory(): array
    {
        return DB::table('loans')
            ->join('materials', 'loans.material_id', '=', 'materials.id')
            ->join('categories', 'materials.category_id', '=', 'categories.id')
            ->select('categories.name as category', DB::raw('COUNT(loans.id) as total_loans'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_loans')
            ->get()
            ->map(fn ($row) => [
                'category'    => $row->category,
                'total_loans' => $row->total_loans,
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────
    // Nombre de prêts par mois sur les 12 derniers mois
    // GET /api/v1/statistics/usage?period=monthly&year=
    // ─────────────────────────────────────────────────────────────

    public function loansPerMonth(?int $year = null): array
    {
        $year = $year ?? now()->year;

        return Loan::select(
                DB::raw('MONTH(loan_date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('loan_date', $year)
            ->groupBy(DB::raw('MONTH(loan_date)'))
            ->orderBy(DB::raw('MONTH(loan_date)'))
            ->get()
            ->map(fn ($row) => [
                'month' => $row->month,
                'label' => now()->month($row->month)->translatedFormat('F'),
                'total' => $row->total,
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────
    // Matériels actuellement en prêt
    // ─────────────────────────────────────────────────────────────

    public function activeLoans(): array
    {
        return Loan::where('status', 'active')
            ->with(['material:id,name', 'user:id,name,email'])
            ->get()
            ->map(fn ($loan) => [
                'loan_id'              => $loan->id,
                'material'             => $loan->material?->name,
                'user'                 => $loan->user?->name,
                'loan_date'            => $loan->loan_date?->toDateString(),
                'expected_return_date' => $loan->expected_return_date?->toDateString(),
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────
    // Prêts en retard (expected_return_date dépassée)
    // ─────────────────────────────────────────────────────────────

    public function overdueLoans(): array
    {
        return Loan::where('status', 'active')
            ->where('expected_return_date', '<', now()->toDateString())
            ->with(['material:id,name', 'user:id,name,email'])
            ->get()
            ->map(fn ($loan) => [
                'loan_id'              => $loan->id,
                'material'             => $loan->material?->name,
                'user'                 => $loan->user?->name,
                'email'                => $loan->user?->email,
                'expected_return_date' => $loan->expected_return_date?->toDateString(),
                'days_overdue'         => now()->diffInDays($loan->expected_return_date),
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────
    // Résumé général (KPI Cards)
    // ─────────────────────────────────────────────────────────────

    public function summary(): array
    {
        $totalMaterials     = Material::count();
        $availableMaterials = Material::where('status', 'available')->count();
        $activeLoans        = Loan::where('status', 'active')->count();
        $overdueLoans       = Loan::where('status', 'active')
                                  ->where('expected_return_date', '<', now()->toDateString())
                                  ->count();
        $damagedMaterials   = Material::where('status', 'broken')->count();
        $totalLoans         = Loan::count();

        return [
            'total_materials'     => $totalMaterials,
            'available_materials' => $availableMaterials,
            'active_loans'        => $activeLoans,
            'overdue_loans'       => $overdueLoans,
            'damaged_materials'   => $damagedMaterials,
            'total_loans'         => $totalLoans,
        ];
    }
}
