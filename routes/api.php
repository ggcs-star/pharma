<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Users\AuthController;
use App\Http\Controllers\Api\Users\ProductController;
use App\Http\Controllers\Api\Users\CategoryController;
use App\Http\Controllers\Api\Users\CartController;
use App\Http\Controllers\Api\Users\OrderController;
use App\Http\Controllers\Api\Users\AddressController;
use App\Http\Controllers\Api\Users\PrescriptionController;
use App\Http\Controllers\Api\Users\v1\DeviceVerificationController;

/*
|--------------------------------------------------------------------------
| TEST API
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json([
        'status' => true,
        'message' => 'API WORKING',
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (PUBLIC)
|--------------------------------------------------------------------------
*/

Route::prefix('user')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    Route::post('/register', [
        AuthController::class,
        'register'
    ])->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | VERIFY EMAIL OTP
    |--------------------------------------------------------------------------
    */

    Route::post('/verify-email-otp', [
        AuthController::class,
        'verifyEmailOtp'
    ])->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | RESEND EMAIL OTP
    |--------------------------------------------------------------------------
    */

    Route::post('/resend-email-otp', [
        AuthController::class,
        'resendEmailOtp'
    ])->middleware('throttle:3,1');

    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::post('/forgot-password', [
        AuthController::class,
        'forgotPassword'
    ])->middleware('throttle:3,1');

    /*
    |--------------------------------------------------------------------------
    | VERIFY RESET OTP
    |--------------------------------------------------------------------------
    */

    Route::post('/verify-reset-otp', [
        AuthController::class,
        'verifyResetOtp'
    ])->middleware('throttle:5,1');

    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::post('/reset-password', [
        AuthController::class,
        'resetPassword'
    ])->middleware('throttle:3,1');
});

/*
|--------------------------------------------------------------------------
| PUBLIC PRODUCT APIs
|--------------------------------------------------------------------------
*/

Route::get('/products', [
    ProductController::class,
    'index'
]);

Route::get('/product/{id}', [
    ProductController::class,
    'show'
]);

Route::get('/products/category/{slug}', [
    ProductController::class,
    'byCategory'
]);

Route::get('/popular-categories', [
    ProductController::class,
    'popularCategories'
]);

Route::get('/brands', [
    ProductController::class,
    'brands'
]);

Route::get('/brands/{brand}', [
    ProductController::class,
    'brandProducts'
]);

Route::get('/home-sections', [
    ProductController::class,
    'homeSections'
]);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED + DEVICE IDENTIFICATION
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    'device.identification'
])->group(function () {

    Route::get('/auth/check', [
        AuthController::class,
        'checkAuth'
    ]);


    /*
    |--------------------------------------------------------------------------
    | DEVICE VERIFICATION
    |--------------------------------------------------------------------------
    */

    Route::prefix('device')->group(function () {

        Route::post('/send-otp', [
            DeviceVerificationController::class,
            'sendOtp'
        ]);

        Route::post('/verify-otp', [
            DeviceVerificationController::class,
            'verifyOtp'
        ]);

        Route::get('/status', [
            DeviceVerificationController::class,
            'status'
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | TRUSTED DEVICE PROTECTED APIs
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'require.trusted.device'
    ])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */

        Route::prefix('cart')->group(function () {

            Route::post('/add', [
                CartController::class,
                'add'
            ]);

            Route::get('/', [
                CartController::class,
                'index'
            ]);

            Route::post('/update', [
                CartController::class,
                'update'
            ]);

            Route::delete('/remove/{id}', [
                CartController::class,
                'remove'
            ]);

            Route::delete('/clear', [
                CartController::class,
                'clear'
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        Route::prefix('orders')->group(function () {

            Route::post('/place', [
                OrderController::class,
                'place'
            ]);

            Route::get('/', [
                OrderController::class,
                'index'
            ]);

            Route::get('/{id}', [
                OrderController::class,
                'show'
            ]);

            Route::post('/cancel/{id}', [
                OrderController::class,
                'cancel'
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | PRESCRIPTIONS
        |--------------------------------------------------------------------------
        */

        Route::post('/prescription/upload', [
            PrescriptionController::class,
            'upload'
        ]);

        Route::get('/prescription/check', [
            PrescriptionController::class,
            'check'
        ]);

        Route::get('/prescription/my', [
            PrescriptionController::class,
            'myPrescriptions'
        ]);

        Route::delete('/prescription/{id}', [
            PrescriptionController::class,
            'delete'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADDRESSES
        |--------------------------------------------------------------------------
        */

        Route::prefix('addresses')->group(function () {

            Route::get('/', [
                AddressController::class,
                'index'
            ]);

            Route::post('/', [
                AddressController::class,
                'store'
            ]);

            Route::put('/{id}', [
                AddressController::class,
                'update'
            ]);

            Route::delete('/{id}', [
                AddressController::class,
                'destroy'
            ]);

            Route::post('/{id}/default', [
                AddressController::class,
                'setDefault'
            ]);

            Route::get('/default', [
                AddressController::class,
                'default'
            ]);
        });
    });
});
