<?php

namespace App\Http\Controllers\API\V1\Wallet;

use App\Services\PaystackService;
use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Http\Resources\WalletResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Wallet\WalletFormRequest;
use App\Http\Requests\Wallet\DebitWalletRequest;
use App\Http\Requests\Wallet\CreditWalletRequest;

class WalletController extends Controller
{
    protected $walletService;
    protected $transactionService;
    protected $paystackService;
    private const DEBIT_TRANSACTION = 'debit';
    private const CREDIT_TRANSACTION = 'credit';
    private const DEFAULT_BALANCE = 0.00;
    private const ACTIVE_STATUS = 'active';
    private const INACTIVE_STATUS = 'inactive';
    private const PENDING_STATUS = 'pending';
    private const COMPLETED_STATUS = 'completed';
    private const FAILED_STATUS = 'failed';

    public function __construct(WalletService $walletService, TransactionService $transactionService, PaystackService $paystackService)
    {
        $this->walletService = $walletService;
        $this->transactionService = $transactionService;
        $this->paystackService = $paystackService;
    }

    public function getUserWallets(Request $request)
    {
        $data = $this->walletService->list($request->user()->id);
        return response()->json([
            'data' => WalletResource::collection($data),
            'message' => 'Wallets retrieved successfully'
        ], Response::HTTP_OK);
    }

    public function createWallet(WalletFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['wallet_no'] = uniqueNumber(15);
            $data['user_id'] = $request->user()->id;
            $data['balance'] = self::DEFAULT_BALANCE;
            $data['status'] = self::ACTIVE_STATUS;

            // check if the same currency_id exists
            if ($this->walletService->exists($data['currency_id'])) {
                return response()->json([
                    'error' => 'Wallet creation failed',
                    'message' => 'Wallet already exists for the currency, please try another currency'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Create the wallet
            $wallet = $this->walletService->create($data);

            // Commit the transaction if successful
            DB::commit();

            return response()->json([
                'data' => WalletResource::make($wallet),
                'message' => 'Wallet created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollback();
            report_error($e);
            // Handle the exception,
            return response()->json([
                'error' => 'Wallet creation failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function creditWallet(CreditWalletRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $userId = $request->user()->id;
            $data = $request->validated();
            $amount = $data['amount'];
            $wallet_no = $data['wallet_no'];

            $trf_no = getRandomToken(15);

            $existingCreditTransaction = $this->transactionService->getTransactionByRefNo($trf_no, self::CREDIT_TRANSACTION);
            if ($existingCreditTransaction) {
                logger()->info("Duplicate credit request for transaction_ref_no: $trf_no. Skipping crediting.");
                return response()->json([
                    'message' => 'Credit already processed for this transaction.',
                ], Response::HTTP_OK);
            }

            $wallet = $this->walletService->getWalletByUserIdAndWalletNo($userId, $wallet_no);
            if($wallet){
                $paystack = $this->paystackService->initializePayment($amount, $request->user()->email);
            }

            return response()->json([
                'data' => $paystack,
                'message' => 'Payment initialized successfully'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollback();
            report_error($e);
            return response()->json([
                'error' => 'Wallet credit failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyCreditedWallet(Request $request)
    {
        try {
            $reference = $request->get('reference');
            $wallet_no = $request->get('wallet_no');
            $userId = $request->user()->id;

            $paystack = $this->paystackService->verifyPayment($reference);

            if ($paystack['data']['status'] === 'success') {
                $wallet = $this->walletService->getWalletByUserIdAndWalletNo($userId, $wallet_no);
                $amount = $paystack['data']['amount'] / 100;
                self::fundWallet($wallet, $amount, $reference);
                return response()->json([
                    'data' => WalletResource::make($wallet),
                    'message' => 'Wallet credited successfully'
                ], Response::HTTP_CREATED);

            } else {
                return response()->json([
                    'error' => 'Wallet credit failed',
                    'message' => 'Payment failed'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            DB::rollback();
            report_error($e);
            return response()->json([
                'error' => 'Wallet credit failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    function fundWallet($wallet, $amount, $trf_no)
    {
        $wallet->increment('balance', $amount);

        $wallet->transactions()->create([
            'amount' => $amount,
            'type' => self::CREDIT_TRANSACTION,
            'ref_no' => $trf_no,
            'description' => 'Wallet credit',
            'status' => self::COMPLETED_STATUS
        ]);

        DB::commit();

    }

    public function debitWallet(DebitWalletRequest $request)
    {
        try {
            DB::beginTransaction();
            $userId = $request->user()->id;
            $data = $request->validated();
            $amount = $data['amount'];
            $wallet_no = $data['wallet_no'];

            $trf_no = getRandomToken(15);

            $existingCreditTransaction = $this->transactionService->getTransactionByRefNo($trf_no, self::DEBIT_TRANSACTION);
            if ($existingCreditTransaction) {
                logger()->info("Duplicate debit request for transaction_ref_no: $trf_no. Skipping debiting.");
                return response()->json([
                    'message' => 'Debit already processed for this transaction.',
                ], Response::HTTP_OK);
            }

            $wallet = $this->walletService->getWalletByUserIdAndWalletNo($userId, $wallet_no);
            if ($wallet->balance < $amount) {
                return response()->json([
                    'error' => 'Wallet debit failed',
                    'message' => 'Insufficient balance'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $wallet->decrement('balance', $amount);

            $wallet->transactions()->create([
                'amount' => $amount,
                'type' => self::DEBIT_TRANSACTION,
                'ref_no' => $trf_no,
                'description' => 'Wallet debit',
                'status' => self::COMPLETED_STATUS
            ]);

            DB::commit();
            return response()->json([
                'data' => WalletResource::make($wallet),
                'message' => 'Wallet debited successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
            report_error($e);
            return response()->json([
                'error' => 'Wallet debit failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
