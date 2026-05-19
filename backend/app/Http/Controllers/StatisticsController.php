<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly StatisticsService $statisticsService
    ) {}

    // ─────────────────────────────────────────────────────────────
    // GET /api/v1/statistics/usage
    // GET /api/v1/statistics/usage?period=monthly&year=2026
    // Statistiques globales d'utilisation
    // ─────────────────────────────────────────────────────────────

    public function usageStats(Request $request): JsonResponse
    {
        $period = $request->query('period');
        $year   = $request->query('year') ? (int) $request->query('year') : null;

        $stats = $this->statisticsService->usageStats($period, $year);

        return response()->json([
            'data' => $stats,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/v1/statistics/materials/top
    // Top 10 matériels les plus utilisés
    // ─────────────────────────────────────────────────────────────

    public function topMaterials(): JsonResponse
    {
        $top = $this->statisticsService->topMaterials();

        return response()->json([
            'data' => $top,
        ]);
    }
}
