<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CreditPaymentController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Authentication Routes... as guest
Route::middleware("guest")->group(function () {
   Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/operations/credits', function () {
        return response()->json([
            'operations'=> \App\Enums\OperationEnum::listOfCredits()
        ]);
    });

    Route::post( '/image/fill', [ImageController::class, 'fill']);
    Route::post( '/image/restore', [ImageController::class, 'restore']);
    Route::post( '/image/recolour', [ImageController::class, 'recolour']);
    Route::post( '/image/remove', [ImageController::class, 'remove']);

    Route::get('/image/latest-operations', [ImageController::class, 'getLatestOperations']);
    Route::get('/image/operation/{id}', [ImageController::class, 'getOperation']);
    Route::delete('/image/operation/{id}', [ImageController::class, 'deleteOperation']);

    Route::post('/payment/create-payment-intent', 
    [CreditPaymentController::class, 'createPaymentIntent']);
    Route::post('/payment/handle-payment-success', 
    [CreditPaymentController::class, 'handlePaymentSuccess']);
});


