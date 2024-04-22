<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // Fetch user and currency details for the wallet
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'wallet_no' => $this->wallet_no,
            'user' => new UserResource($this->user()->first()),
            'currency' => new CurrencyResource($this->currency()->first()),
            'balance' => (double)$this->balance,
            'status' => $this->status,
        ];
    }
}
