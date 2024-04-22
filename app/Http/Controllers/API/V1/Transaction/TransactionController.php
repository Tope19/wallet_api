<?php

namespace App\Http\Controllers\API\V1\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function balance(TransactionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $userId = $request->user()->id;
        $check = $this->transactionService->checkUserAndWalletMatch($validatedData['wallet_no'], $userId);
        if (!$check) {
            return response()->json([
                'error' => 'Wallet not found',
                'message' => 'Wallet not found for the user'
            ], Response::HTTP_NOT_FOUND);
        }
        $data = $this->transactionService->getTransactionByWalletId($validatedData['wallet_no'], $userId);
        return response()->json([
            'data' => $data,
            'message' => 'Transaction retrieved successfully'
        ], Response::HTTP_OK);
    }
}
