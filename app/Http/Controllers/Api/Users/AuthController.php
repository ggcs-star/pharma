<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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

    public function register(Request $request, OtpService $otpService)
    {

        Log::info('MOBILE HIT REGISTER', [
            'url' => request()->fullUrl(),
            'ip' => request()->ip(),
            'data' => request()->all()
        ]);
        Log::info('RAW INPUT', [
            'raw' => file_get_contents('php://input'),
            'all' => request()->all(),
            'headers' => request()->headers->all()
        ]);

        $data = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'confirmed',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).+$/'
                ],
            ],
            [
                'password.regex' =>
                    'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special symbol.',
            ]
        );

        try {
            DB::beginTransaction();

            $email = $this->normalizeEmail($data['email']);

            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('user');

            $this->sendEmailOtp($email, $otpService);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. OTP has been sent to your email.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'otp_sent_to' => $email
                ]
            ], 201);

        } catch (Throwable $e) {

            DB::rollBack();

            Log::error('Register API Error', [
                'message' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again later.'
            ], 500);
        }
    }



    public function verifyEmailOtp(Request $request, OtpService $otpService)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        try {
            $email = $this->normalizeEmail($data['email']);

            $otp = $otpService->verify($email, $data['otp'], 'email_verification');

            if (!$otp) {
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

            if (!$user->email_verified_at) {
                $user->update([
                    'email_verified_at' => now()
                ]);
            }

            $otp->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully',
                'data' => [
                    'email_verified' => true,
                    'email_verified_at' => $user->email_verified_at,
                ]
            ]);

        } catch (Throwable $e) {

            DB::rollBack();

            Log::error('Verify OTP API Error', [
                'message' => $e->getMessage(),
                'email' => $data['email'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed. Please try again.'
            ], 500);
        }
    }



    public function login(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $this->normalizeEmail($request->email);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'You are not registered'
            ], 404);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $request->password])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        if (!$user->email_verified_at) {

            $this->sendEmailOtp($email, $otpService);

            return response()->json([
                'status' => false,
                'message' => 'Please verify your email. OTP has been sent again.',
                'data' => [
                    'email' => $email,
                    'otp_sent' => true
                ]
            ], 403);
        }

        $token = $user->createToken(
            $user->hasRole('user') ? 'user-token' : 'admin-token'
        )->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ]
        ]);
    }
public function verifyResetOtp(Request $request, OtpService $otpService)
{
    $data = $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string'
    ]);

    try {

        $email = $this->normalizeEmail($data['email']);

        $otp = $otpService->verify($email, $data['otp'], 'password_reset');

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);

    } catch (Throwable $e) {

        Log::error('Verify Reset OTP Error', [
            'message' => $e->getMessage(),
            'email' => $data['email'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'OTP verification failed'
        ], 500);
    }
}
public function resetPassword(Request $request, OtpService $otpService)
{
    $data = $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string',
        'password' => [
            'required',
            'confirmed',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).+$/'
        ],
    ], [
        'password.regex' =>
            'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special symbol.',
    ]);

    try {

        $email = $this->normalizeEmail($data['email']);

        $otp = $otpService->verify($email, $data['otp'], 'password_reset');

        if (!$otp) {
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
            'password' => Hash::make($data['password'])
        ]);

        // 🔥 Important for wallet / MLM apps
        // Revoke all active tokens after password change
$user->tokens()->where('name', 'user-token')->delete();

        $otp->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);

    } catch (Throwable $e) {

        DB::rollBack();

        Log::error('Reset Password API Error', [
            'message' => $e->getMessage(),
            'email' => $data['email'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Password reset failed. Try again.'
        ], 500);
    }
}

    public function resendEmailOtp(Request $request, OtpService $otpService)
    {
        $data = $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $email = strtolower(trim($data['email']));

            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if ($user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already verified'
                ], 422);
            }

            $otp = $otpService->generate($email, 'email_verification');

            Mail::to($email)->send(new OtpMail($otp->code));

            return response()->json([
                'success' => true,
                'message' => 'OTP has been resent successfully',
                'data' => [
                    'email' => $email,
                    'expires_in' => 600
                ]
            ]);

        } catch (Throwable $e) {

            Log::error('Resend OTP API Error', [
                'message' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to resend OTP. Please try again later.'
            ], 500);
        }
    }

private function sendResetOtp(string $email, OtpService $otpService): void
{
    Log::info('Password Reset OTP triggered for: ' . $email);

    $otp = $otpService->generate($email, 'password_reset');

    Mail::to($email)->send(new OtpMail($otp->code));
}
public function forgotPassword(Request $request, OtpService $otpService)
{
    $data = $request->validate([
        'email' => 'required|email'
    ]);

    try {

        $email = $this->normalizeEmail($data['email']);
$user = User::where('email', $email)->first();

if ($user) {
    $this->sendResetOtp($email, $otpService);
}

return response()->json([
    'success' => true,
    'message' => 'If the email exists, a reset OTP has been sent.',
    'data' => [
        'expires_in' => 600
    ]
]);
    

    } catch (Throwable $e) {

        Log::error('Forgot Password API Error', [
            'message' => $e->getMessage(),
            'email' => $data['email'] ?? null,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Unable to process request. Try again later.'
        ], 500);
    }
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
