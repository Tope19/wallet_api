<?php

namespace App\Services;

use App\Models\Wallet;

class WalletService
{
    public function list($id)
    {
        return Wallet::where('user_id', $id)->get();
    }

    public function create($data)
    {
        return Wallet::create($data);
    }

    public function exists($currency_id)
    {
        return Wallet::where('currency_id', $currency_id)->exists();
    }

    public function getTransactionByRefNo($refNo, $type)
    {
        return Wallet::where('ref_no', $refNo)->where('type', $type)->first();
    }

    public function getWalletByUserIdAndWalletNo($id, $walletNo)
    {
        return Wallet::where('user_id', $id)->where('wallet_no', $walletNo)->firstOrFail();
    }

}
