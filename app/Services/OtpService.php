<?php

namespace App\Services;

use App\Models\Otp;

class OtpService
{
    public function generate(string $email, string $type): Otp
    {
        Otp::where('email',$email)->where('type',$type)->delete();

        return Otp::create([
            'email'=>$email,
            'code'=>random_int(100000,999999),
            'type'=>$type,
            'expires_at'=>now()->addMinutes(10)
        ]);
    }

    public function verify(string $email, string $code, string $type): ?Otp
    {
        return Otp::where('email',$email)
            ->where('code',$code)
            ->where('type',$type)
            ->where('expires_at','>',now())
            ->first();
    }
}
