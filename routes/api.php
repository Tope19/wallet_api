<?php

use App\Http\Controllers\API\V1\Authentication\LoginController;
use App\Http\Controllers\API\V1\Authentication\RegisterController;
use App\Http\Controllers\API\V1\Currency\CurrencyController;
use App\Http\Controllers\API\V1\Transaction\TransactionController;
use App\Http\Controllers\API\V1\Wallet\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'v1', 'namespace' => 'API\V1'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Authentication'], function () {
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('login', [LoginController::class, 'login'])->name('login');
        Route::post( 'logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'currency', 'namespace' => 'Currency'], function () {
            Route::get('list', [CurrencyController::class, 'list']);
        });

        Route::group(['prefix' => 'wallet', 'namespace' => 'Wallet'], function () {
            Route::get('list', [WalletController::class, 'getUserWallets']);
            Route::post('create', [WalletController::class, 'createWallet']);
            Route::post('credit', [WalletController::class, 'creditWallet']);
            Route::post('/credit/verify', [WalletController::class, 'verifyCreditedWallet']);
            Route::post('debit', [WalletController::class, 'debitWallet']);
        });

        Route::group(['prefix' => 'transactions', 'namespace' => 'Transaction'], function () {
            Route::post('list', [TransactionController::class, 'balance']);
        });
    });
});
