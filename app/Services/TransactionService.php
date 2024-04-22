<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;

class TransactionService
{
    public function create($data)
    {
        return Transaction::create($data);
    }

    public function getTransactionByRefNo($refNo, $type)
    {
        return Transaction::where('ref_no', $refNo)->where('type', $type)->first();
    }

    public function getTransactionByWalletId($wallet_no, $userId)
    {
        $wallet_id = Wallet::where('wallet_no', $wallet_no)->where('user_id', $userId)->first()->id;
        return Transaction::where('wallet_id', $wallet_id)->paginate(10);
    }

    public function checkUserAndWalletMatch($wallet_no, $userId)
    {
        return Wallet::where('wallet_no', $wallet_no)->where('user_id', $userId)->exists();
    }


}
