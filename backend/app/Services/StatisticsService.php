<?php
// app/Services/StatisticsService.php

namespace App\Services;

use App\Enums\MaterialStatus;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Material;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function usageStats(array $filters = []): array
    {
        $year = $filters['year'] ?? now()->year;

        return [
            'summary'             => $this->summary(),
            'top_materials'       => $this->topMaterials(10),
            'category_usage'      => $this->categoryUsageRate(),
            'monthly_loans'       => $this->monthlyLoans($year),
            'currently_on_loan'   => $this->currentlyOnLoan(),
            'overdue_loans'       => $this->overdueLoans(),
            'reservation_summary' => $this->reservationSummary(),
        ];
    }

    public function summary(): array
    {
        return [
            'total_loans'          => Loan::count(),
            'active_loans'         => Loan::ongoing()->count(),
            'overdue_loans'        => Loan::overdue()->count(),
            'returned_loans'       => Loan::returned()->count(),
            'total_materials'      => Material::count(),
            'available_materials'  => Material::where('status', MaterialStatus::Available->value)->count(),
            'in_use_materials'     => Material::where('status', MaterialStatus::InUse->value)->count(),
            'broken_materials'     => Material::where('status', MaterialStatus::Broken->value)->count(),
            'total_categories'     => Category::count(),
            'total_reservations'   => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];
    }

    /**
     * Top N most-loaned materials — fully Eloquent, SQLite compatible.
     */
    public function topMaterials(int $limit = 10): Collection
    {
        $today = today();

        return Material::query()
            ->with('category:id,name')
            ->withCount('loans')
            ->get()
            ->sortByDesc('loans_count')
            ->take($limit)
            ->map(function (Material $material) use ($today) {
                $loans    = $material->loans()->get(['loan_date', 'actual_return_date', 'expected_return_date']);
                $returned = $loans->whereNotNull('actual_return_date');
                $overdue  = $loans->whereNull('actual_return_date')
                                  ->filter(fn($l) => Carbon::parse($l->expected_return_date)->lt($today));

                $avgDays = $returned->avg(function ($l) {
                    return Carbon::parse($l->loan_date)
                        ->diffInDays(Carbon::parse($l->actual_return_date));
                });

                return [
                    'id'             => $material->id,
                    'name'           => $material->name,
                    'status'         => $material->status->value,
                    'category_name'  => $material->category?->name,
                    'total_loans'    => $loans->count(),
                    'overdue_count'  => $overdue->count(),
                    'returned_count' => $returned->count(),
                    'avg_loan_days'  => $avgDays ? round($avgDays, 1) : 0,
                ];
            })
            ->values();
    }

    /**
     * Usage rate per category — Eloquent, SQLite compatible.
     */
    public function categoryUsageRate(): Collection
    {
        return Category::query()
            ->withCount('materials')
            ->with(['materials' => fn($q) => $q->select('id', 'category_id', 'status')])
            ->get()
            ->map(function (Category $category) {
                $total     = $category->materials_count;
                $inUse     = $category->materials->where('status', MaterialStatus::InUse->value)->count();
                $available = $category->materials->where('status', MaterialStatus::Available->value)->count();
                $broken    = $category->materials->where('status', MaterialStatus::Broken->value)->count();
                $loans     = Loan::whereIn('material_id', $category->materials->pluck('id'))->count();

                return [
                    'id'                 => $category->id,
                    'name'               => $category->name,
                    'total_materials'    => $total,
                    'total_loans'        => $loans,
                    'in_use_count'       => $inUse,
                    'available_count'    => $available,
                    'broken_count'       => $broken,
                    'usage_rate_percent' => $total > 0 ? round($inUse / $total * 100, 1) : 0,
                ];
            })
            ->sortByDesc('total_loans')
            ->values();
    }

    /**
     * Loans per month for a given year — Eloquent + PHP, SQLite compatible.
     */
    public function monthlyLoans(int $year): Collection
    {
        $loans = Loan::query()
            ->whereYear('loan_date', $year)
            ->select('loan_date', 'actual_return_date', 'expected_return_date')
            ->get();

        $today   = today();
        $grouped = $loans->groupBy(fn($l) => Carbon::parse($l->loan_date)->month);

        return collect(range(1, 12))->map(function ($month) use ($grouped, $year, $today) {
            $monthLoans = $grouped->get($month, collect());

            return [
                'month'      => $month,
                'month_name' => Carbon::create($year, $month, 1)->translatedFormat('F'),
                'year'       => $year,
                'total'      => $monthLoans->count(),
                'returned'   => $monthLoans->whereNotNull('actual_return_date')->count(),
                'overdue'    => $monthLoans
                    ->whereNull('actual_return_date')
                    ->filter(fn($l) => Carbon::parse($l->expected_return_date)->lt($today))
                    ->count(),
            ];
        });
    }

    /**
     * Materials currently on active (non-overdue) loan.
     */
    public function currentlyOnLoan(): Collection
    {
        return Loan::query()
            ->with(['user:id,name,email', 'material:id,name,status,category_id', 'material.category:id,name'])
            ->ongoing()
            ->select('id', 'user_id', 'material_id', 'loan_date', 'expected_return_date', 'notes')
            ->orderBy('expected_return_date')
            ->get()
            ->map(fn($loan) => [
                'loan_id'              => $loan->id,
                'material'             => $loan->material?->name,
                'category'             => $loan->material?->category?->name,
                'user'                 => $loan->user?->name,
                'user_email'           => $loan->user?->email,
                'loan_date'            => $loan->loan_date?->format('Y-m-d'),
                'expected_return_date' => $loan->expected_return_date?->format('Y-m-d'),
                'days_remaining'       => max(0, (int) now()->diffInDays($loan->expected_return_date, false)),
            ]);
    }

    /**
     * All overdue loans with details.
     */
    public function overdueLoans(): Collection
    {
        return Loan::query()
            ->with(['user:id,name,email,phone', 'material:id,name,category_id', 'material.category:id,name'])
            ->overdue()
            ->select('id', 'user_id', 'material_id', 'loan_date', 'expected_return_date', 'notes')
            ->orderBy('expected_return_date')
            ->get()
            ->map(fn($loan) => [
                'loan_id'              => $loan->id,
                'material'             => $loan->material?->name,
                'category'             => $loan->material?->category?->name,
                'user'                 => $loan->user?->name,
                'user_email'           => $loan->user?->email,
                'user_phone'           => $loan->user?->phone,
                'loan_date'            => $loan->loan_date?->format('Y-m-d'),
                'expected_return_date' => $loan->expected_return_date?->format('Y-m-d'),
                'overdue_days'         => $loan->overdueDays(),
            ]);
    }

    /**
     * Reservation status breakdown.
     */
    public function reservationSummary(): array
    {
        $counts = Reservation::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'pending'   => (int) $counts->get('pending',   0),
            'validated' => (int) $counts->get('validated', 0),
            'rejected'  => (int) $counts->get('rejected',  0),
            'cancelled' => (int) $counts->get('cancelled', 0),
            'total'     => (int) $counts->sum(),
        ];
    }
}
