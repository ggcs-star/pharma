<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\Users\AuthController;
use App\Http\Controllers\Api\Users\ProductController;
use App\Http\Controllers\Api\Users\CategoryController;
use App\Http\Controllers\Api\Users\CartController;
use App\Http\Controllers\Api\Users\OrderController;
    use App\Http\Controllers\Api\Users\AddressController;
    use App\Http\Controllers\Api\Users\PrescriptionController;


Route::get('/test', function () {
    return "API WORKING";
});

/* ===============================
    AUTH ROUTES
=============================== */
Route::prefix('user')->group(function () {

    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::post('/verify-email-otp', [AuthController::class, 'verifyEmailOtp'])->middleware('throttle:5,1');
    Route::post('/resend-email-otp', [AuthController::class, 'resendEmailOtp'])->middleware('throttle:3,1');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
    Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOtp'])->middleware('throttle:5,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,1');
});


/* ===============================
    PUBLIC APIs (NO LOGIN)
=============================== */



// Product APIs
Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/category/{id}', [ProductController::class, 'byCategory']);


/* ===============================
    AUTHENTICATED APIs
=============================== */
Route::middleware('auth:sanctum')->group(function () {


    /* ---------- CART ---------- */
    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'add']);
        Route::get('/', [CartController::class, 'index']);
        Route::post('/update', [CartController::class, 'update']);
        Route::delete('/remove/{id}', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });

    /* ---------- CHECKOUT ---------- */
    Route::post('/checkout', [CheckoutController::class, 'checkout']);

    /* ---------- ORDER ---------- */
    Route::prefix('orders')->group(function () {
        Route::post('/place', [OrderController::class, 'place']);
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/cancel/{id}', [OrderController::class, 'cancel']);
    });

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/prescription/upload', [PrescriptionController::class, 'upload']);

    Route::get('/prescription/check', [PrescriptionController::class, 'check']);

    Route::get('/prescription/my', [PrescriptionController::class, 'myPrescriptions']);

    Route::delete('/prescription/{id}', [PrescriptionController::class, 'delete']);

});
/* ---------- ADDRESS ---------- */
Route::prefix('addresses')->group(function () {

    Route::get('/', [AddressController::class, 'index']);
    Route::post('/', [AddressController::class, 'store']);
    Route::put('/{id}', [AddressController::class, 'update']);
    Route::delete('/{id}', [AddressController::class, 'destroy']);

Route::post('/{id}/default', [AddressController::class, 'setDefault']);
Route::get('/default', [AddressController::class, 'default']);
});
    

});