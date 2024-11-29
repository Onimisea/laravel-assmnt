<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
    Route::patch('/me/name', [UserController::class, 'updateName'])->middleware('auth:sanctum');
    Route::patch('/me/pin', [UserController::class, 'changePin'])->middleware('auth:sanctum');

    Route::get('/', [UserController::class, 'allUsers'])->middleware('auth:sanctum');
    Route::get('/{id}', [UserController::class, 'getUser'])->middleware('auth:sanctum');
    Route::delete('/{id}', [UserController::class, 'deleteUser']);
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('wallets')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [WalletController::class, 'create']);
    Route::get('/', [WalletController::class, 'index']);
    Route::get('/me', [WalletController::class, 'show']);
    Route::patch('/fund', [WalletController::class, 'fundWallet']);
    Route::patch('/withdraw', [WalletController::class, 'withdrawFromWallet']);
});

Route::prefix('purchases')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [PurchaseController::class, 'index']);
    Route::post('/', [PurchaseController::class, 'store']);
    Route::post('/buy/{id}/{bill_id}', [PurchaseController::class, 'buy']);
    Route::get('/{id}', [PurchaseController::class, 'show']);
    Route::patch('/{id}', [PurchaseController::class, 'update']);
    Route::delete('/{id}', [PurchaseController::class, 'destroy']);
});

Route::prefix('bills')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [BillController::class, 'index']);
    Route::post('/', [BillController::class, 'store']);
    Route::get('/{id}', [BillController::class, 'show']);
    Route::patch('/{id}', [BillController::class, 'update']);
    Route::delete('/{id}', [BillController::class, 'destroy']);
});


Route::prefix('transactions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TransactionsController::class, 'index']);
    Route::get('/{id}', [TransactionsController::class, 'show']);
});
