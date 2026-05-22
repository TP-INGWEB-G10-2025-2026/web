<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\ReturnLoanRequest;
use App\Http\Requests\Loan\StoreLoanRequest;
use App\Http\Resources\LoanCollection;
use App\Http\Resources\LoanResource;
use App\Models\Material;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function __construct(private readonly LoanService $loanService) {}

    /** GET /api/v1/loans */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'     => ['sometimes', 'uuid'],
            'material_id' => ['sometimes', 'uuid'],
            'overdue'     => ['sometimes', 'in:true,false,1,0'],
            'ongoing'     => ['sometimes', 'in:true,false,1,0'],
            'returned'    => ['sometimes', 'in:true,false,1,0'],
            'from'        => ['sometimes', 'date'],
            'to'          => ['sometimes', 'date', 'after_or_equal:from'],
        ]);

        $loans = $this->loanService->list($request->only([
            'user_id',
            'material_id',
            'overdue',
            'ongoing',
            'returned',
            'from',
            'to',
        ]));

        return response()->json(new LoanCollection($loans));
    }

    /** GET /api/v1/loans/{id} */
    public function show(string $id): JsonResponse
    {
        $loan = $this->loanService->findOrFail($id);

        return response()->json(['data' => new LoanResource($loan)]);
    }

    /** POST /api/v1/loans */
    public function store(StoreLoanRequest $request): JsonResponse
    {
        $loan = $this->loanService->create($request->validated());

        return response()->json([
            'message' => 'Prêt enregistré avec succès.',
            'data'    => new LoanResource($loan),
        ], 201);
    }

    /** PATCH /api/v1/loans/{id}/return */
    public function return(ReturnLoanRequest $request, string $id): JsonResponse
    {
        $loan = $this->loanService->findOrFail($id);
        $loan = $this->loanService->processReturn($loan, $request->validated());

        return response()->json([
            'message' => 'Retour enregistré avec succès.',
            'data'    => new LoanResource($loan),
        ]);
    }

    /** GET /api/v1/materials/{id}/loans */
    public function materialHistory(Request $request, string $id): JsonResponse
    {
        // Ensure material exists
        Material::findOrFail($id);

        $loans = $this->loanService->list(
            array_merge(
                $request->only(['overdue', 'ongoing', 'returned', 'from', 'to']),
                ['material_id' => $id]
            )
        );

        return response()->json(new LoanCollection($loans));
    }

    /** GET /api/v1/teachers/{id}/loans */
    public function teacherHistory(Request $request, string $id): JsonResponse
    {
        // Ensure user exists
        User::findOrFail($id);

        $loans = $this->loanService->list(
            array_merge(
                $request->only(['overdue', 'ongoing', 'returned', 'from', 'to']),
                ['user_id' => $id]
            )
        );

        return response()->json(new LoanCollection($loans));
    }

    /**
     * Block any attempt to update a loan record directly.
     * Loans are immutable — only processReturn() is allowed.
     */
    public function update(): JsonResponse
    {
        return response()->json([
            'message' => 'Les enregistrements de prêt sont immuables. Utilisez PATCH /{id}/return pour enregistrer un retour.',
        ], 405);
    }

    /**
     * Block any attempt to delete a loan record.
     * Loans are immutable historical records.
     */
    public function destroy(): JsonResponse
    {
        return response()->json([
            'message' => 'Les enregistrements de prêt ne peuvent pas être supprimés. Ils constituent un historique immuable.',
        ], 405);
    }
}
