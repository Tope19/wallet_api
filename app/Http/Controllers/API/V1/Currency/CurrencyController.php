<?php

namespace App\Http\Controllers\API\V1\Currency;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function list()
    {
        $data = $this->currencyService->list();
        return response()->json([
            'data' => $data,
            'message' => 'Currencies registered successfully'
        ], Response::HTTP_OK);
    }
}
