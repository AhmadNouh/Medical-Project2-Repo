<?php

namespace App\Services;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordService {

public function sendResetOtp(string $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false; 
        }

        $code = rand(100000, 999999);

        DB::table('password_reset_otps')->where('email', $email)->delete();
        
        DB::table('password_reset_otps')->insert([
            'email' => $email,
            'code' => Hash::make($code) , 
            'expires_at' => now()->addMinutes(15) 
        ]);

        try {
            Mail::to($email)->send(new ResetPasswordOtpMail($code));
        } catch (\Exception $e) {
            return false;
        }
        
        return $code;
    }

   
    public function verifyOtp(string $email, string $code): bool
    {
        $otpRecord = DB::table('password_reset_otps')
            ->where('email', $email)
            ->first();

        if (!$otpRecord) {
            return false; 
        }

        if (now()->isAfter($otpRecord->expires_at)) {
            return false; 
        }

        if(!Hash::check($code , $otpRecord->code))
            return false;

        return true;
    }


    public function resetPassword(array $data): bool
    {
       
        if (!$this->verifyOtp($data['email'], $data['code'])) {
            return false;
        }

        $user = User::where('email', $data['email'])->first();
        $user->update([
            'password' => Hash::make($data['password'])
        ]);

        DB::table('password_reset_otps')->where('email', $data['email'])->delete();

        return true;
    }
}