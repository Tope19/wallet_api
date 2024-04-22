<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public function list()
    {
        return Currency::where('status', 'active')->get();
    }
}
