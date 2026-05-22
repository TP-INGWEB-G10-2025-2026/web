<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct(private readonly StatisticsService $statisticsService) {}

    /**
     * GET /api/v1/statistics/usage
     * GET /api/v1/statistics/usage?period=monthly&year=2025
     */
    public function usageStats(Request $request): JsonResponse
    {
        $request->validate([
            'year'   => ['sometimes', 'integer', 'min:2020', 'max:' . (now()->year + 1)],
            'period' => ['sometimes', 'string', 'in:monthly,yearly'],
        ]);

        $stats = $this->statisticsService->usageStats($request->only(['year', 'period']));

        return response()->json([
            'data'      => $stats,
            'generated' => now()->toISOString(),
            'period'    => [
                'year' => $request->integer('year', now()->year),
            ],
        ]);
    }

    /**
     * GET /api/v1/statistics/materials/top
     */
    public function topMaterials(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $limit = $request->integer('limit', 10);
        $data  = $this->statisticsService->topMaterials($limit);

        return response()->json([
            'data'  => $data,
            'meta'  => ['limit' => $limit, 'count' => $data->count()],
        ]);
    }

    /**
     * GET /api/v1/statistics/usage?period=monthly (monthly breakdown only)
     */
    public function monthlyLoans(Request $request): JsonResponse
    {
        $request->validate([
            'year' => ['sometimes', 'integer', 'min:2020', 'max:' . (now()->year + 1)],
        ]);

        $year = $request->integer('year', now()->year);
        $data = $this->statisticsService->monthlyLoans($year);

        return response()->json([
            'data' => $data,
            'meta' => ['year' => $year],
        ]);
    }

    /**
     * GET /api/v1/statistics/overdue
     */
    public function overdueLoans(): JsonResponse
    {
        $data = $this->statisticsService->overdueLoans();

        return response()->json([
            'data'  => $data,
            'meta'  => ['count' => $data->count()],
        ]);
    }

    /**
     * GET /api/v1/statistics/summary
     */
    public function summary(): JsonResponse
    {
        return response()->json([
            'data' => $this->statisticsService->summary(),
        ]);
    }
}
