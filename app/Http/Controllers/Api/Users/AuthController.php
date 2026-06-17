<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\OtpService;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\PersonalAccessToken;

use Throwable;

class AuthController extends Controller
{
    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function sendEmailOtp(string $email, OtpService $otpService): void
    {
        Log::info('OTP function triggered for: ' . $email);

        $otp = $otpService->generate($email, 'email_verification');

        Mail::to($email)->send(new OtpMail($otp->code));
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

   public function register(Request $request, OtpService $otpService)
{
$data = $request->validate([
'name' => 'required|string|max:255',
'email' => 'required|email|unique:users,email',
'device_id' => 'required|string',
'password' => [
'required',
'confirmed',
'min:8',
'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).+$/'
],
], [
'password.regex' =>
'Password must contain uppercase, lowercase, number and special symbol.',
]);

try {

    DB::beginTransaction();

    $email = $this->normalizeEmail($data['email']);

    $user = User::create([
        'name' => $data['name'],
        'email' => $email,
        'password' => Hash::make($data['password']),
    ]);

    $user->assignRole('user');

    UserDevice::create([

        'user_id' => $user->id,

        'device_id' => $data['device_id'],

        'fingerprint_hash' => hash(
            'sha256',
            $data['device_id']
        ),

        'device_name' => $request->userAgent(),

        'browser' => 'Unknown',

        'platform' => 'Unknown',

        'app_version' => '1.0',

        'last_ip_address' => $request->ip(),

        'first_ip_address' => $request->ip(),

        'user_agent' => $request->userAgent(),

        'trust_level' => UserDevice::TRUST_NEW,

        'is_verified' => false,

        'is_active' => true,

        'first_login_at' => now(),

        'last_active_at' => now(),
    ]);

    $this->sendEmailOtp($email, $otpService);

    DB::commit();

    SecurityLogger::log(
        $request,
        'AUTH',
        'user_registered',
        2,
        201,
        [
            'email' => $email,
            'device_id' => $data['device_id']
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Registration successful. OTP sent.'
    ], 201);

} catch (Throwable $e) {

    DB::rollBack();

    Log::error('REGISTER ERROR', [
        'message' => $e->getMessage()
    ]);

    SecurityLogger::log(
        $request,
        'AUTH',
        'registration_failed',
        8,
        500,
        [
            'error' => $e->getMessage()
        ]
    );

    return response()->json([
        'success' => false,
        'message' => 'Registration failed'
    ], 500);
}


}


    /*
    |--------------------------------------------------------------------------
    | VERIFY EMAIL OTP
    |--------------------------------------------------------------------------
    */

public function verifyEmailOtp(Request $request, OtpService $otpService)
{
$data = $request->validate([
'email' => 'required|email',
'otp' => 'required|string',
'device_id' => 'required|string',
]);


try {

    $email = $this->normalizeEmail($data['email']);

    $otp = $otpService->verify(
        $email,
        $data['otp'],
        'email_verification'
    );

    if (!$otp) {

        SecurityLogger::log(
            $request,
            'AUTH',
            'otp_verification_failed',
            7,
            422,
            [
                'email' => $email
            ]
        );

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP'
        ], 422);
    }

    DB::beginTransaction();

    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $user->update([
        'email_verified_at' => now()
    ]);

    UserDevice::where(
        'user_id',
        $user->id
    )
    ->where(
        'device_id',
        $data['device_id']
    )
    ->update([

        'is_verified' => true,

        'verified_at' => now(),

        'trust_level' =>
            UserDevice::TRUST_TRUSTED,

        'last_active_at' => now(),

        'failed_attempts' => 0,

        'failed_otp_attempts' => 0,
    ]);

    $otp->delete();

    DB::commit();

    SecurityLogger::log(
        $request,
        'AUTH',
        'email_verified',
        2,
        200,
        [
            'user_id' => $user->id,
            'device_id' => $data['device_id']
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Email and device verified successfully'
    ]);

} catch (Throwable $e) {

    DB::rollBack();

    Log::error('VERIFY OTP ERROR', [
        'message' => $e->getMessage()
    ]);

    return response()->json([
        'success' => false,
        'message' => 'OTP verification failed'
    ], 500);
}

}

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $this->normalizeEmail($request->email);

        $user = User::where('email', $email)->first();

        if (!$user) {

            SecurityLogger::log(
                $request,
                'AUTH',
                'login_failed_user_not_found',
                8,
                404,
                [
                    'email' => $email
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'You are not registered'
            ], 404);
        }

        if (!Auth::attempt([
            'email' => $email,
            'password' => $request->password
        ])) {

            SecurityLogger::log(
                $request,
                'AUTH',
                'login_failed_invalid_password',
                9,
                401,
                [
                    'user_id' => $user->id
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        if (!$user->email_verified_at) {

            $this->sendEmailOtp($email, $otpService);

            SecurityLogger::log(
                $request,
                'AUTH',
                'login_blocked_email_unverified',
                6,
                403,
                [
                    'user_id' => $user->id
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Please verify your email.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | DEVICE HANDLING
        |--------------------------------------------------------------------------
        */

        $deviceId =
            $request->device_id ??
            $request->header('X-Device-ID') ??
            $request->header('x-device-id') ??
            $request->server('HTTP_X_DEVICE_ID');

        $isTrustedDevice = false;
        $device = null;

        if ($deviceId) {

            $device = UserDevice::firstOrCreate(

                [
                    'user_id' => $user->id,
                    'device_id' => $deviceId
                ],

                [
                   'fingerprint_hash' => hash(
    'sha256',
    $deviceId
),

                    'device_name' => $request->userAgent(),

                    'browser' => 'Chrome',

                    'platform' => 'Windows',

                    'app_version' => '1.0',

                    'last_ip_address' => $request->ip(),

                    'user_agent' => $request->userAgent(),

                    'trust_level' => 'NEW',

                    'failed_attempts' => 0,

                    'last_active_at' => now()
                ]
            );

            $device->update([
                'last_ip_address' => $request->ip(),
                'last_active_at' => now(),
                'user_agent' => $request->userAgent()
            ]);

   /*
|--------------------------------------------------------------------------
| REFRESH DEVICE
|--------------------------------------------------------------------------
*/

$device = $device->fresh();

/*
|--------------------------------------------------------------------------
| TRUST CHECK
|--------------------------------------------------------------------------
*/

$isTrustedDevice =
    $device->trust_level ===
    UserDevice::TRUST_TRUSTED;

/*
|--------------------------------------------------------------------------
| DEBUG
|--------------------------------------------------------------------------
*/

Log::info('LOGIN TRUST CHECK', [

    'device_id' =>
        $device->device_id,

    'trust_level' =>
        $device->trust_level,

    'trusted_constant' =>
        UserDevice::TRUST_TRUSTED,

    'is_trusted' =>
        $isTrustedDevice
]);

            $request->attributes->set(
                'current_device',
                $device
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TOKEN
        |--------------------------------------------------------------------------
        */

       $user->tokens()->delete();

$token = $user->createToken(
    $user->hasRole('user')
        ? 'user-token'
        : 'admin-token'
)->plainTextToken;

        /*
        |--------------------------------------------------------------------------
        | SECURITY LOG
        |--------------------------------------------------------------------------
        */

        SecurityLogger::log(
            $request,
            'AUTH',
            'login_success',
            2,
            200,
            [
                'user_id' => $user->id,
                'device_id' => $deviceId,
                'trusted_device' => $isTrustedDevice
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'status' => true,
            'message' => 'Login successful',

            'token' => $token,

            'trusted_device' => $isTrustedDevice,

            'device_verification_required' => !$isTrustedDevice,

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        SecurityLogger::log(
            $request,
            'AUTH',
            'logout',
            1,
            200
        );

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
public function checkAuth(Request $request)
{
    $bearerToken = $request->bearerToken();

    $token = PersonalAccessToken::findToken(
        $bearerToken
    );

    return response()->json([
        'bearer_token' => $bearerToken,
        'token_found' => $token ? true : false,
        'token_id' => $token?->id,
        'user_id' => $token?->tokenable_id,
    ]);
}
}