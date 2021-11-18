<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    public function VerifyOTPRequest(Request $request)
    {
        $phoneNumber = $request->user()->phone;
        $fullHash = $request->input('loginHash');
        $input_otp = $request->input('otp');
        $otp_verification = $this->verifyOTP($phoneNumber, $fullHash, $input_otp);
        return $otp_verification;
    }
    protected function verifyOTP($phoneNumber, $fullHash, $otp)
    {
        $result = false;
        $msg = "کد وارد شده صحیح نمی‌باشد";
        // Seperate Hash value and expires from the hash returned from the user
        $exploded_hash = explode('.', $fullHash);
        $hashValue = $exploded_hash[0];
        $expires = $exploded_hash[1];
        $now = Carbon::now()->timestamp;
        // Calculate new hash with the same key and the same algorithm
        $data  = "$phoneNumber.$otp.$expires";
        $newCalculatedHash = hash_hmac('sha256', $data, config('app.key'));
        // Match the hashes
        if (md5($newCalculatedHash) === md5($hashValue)) {
            // Check if expiry time has passed
            if ($now > intval($expires)) {
                $msg = "کد وارد شده منقضی شده است.";
            } else {
                $result = true;
            }
        }
        return ['result' => $result, 'msg' => $msg];
    }

    public function ProcessOTPRequest($user)
    {
        $phoneNumber = $user->phone;
        [$fullHash, $otp] = $this->GenerateHash($phoneNumber);
        $masked_phone_number = substr_replace($phoneNumber, '*********', 0, 9);
        return [$fullHash, $phoneNumber, $otp, $masked_phone_number];
    }

    protected function GenerateHash($phoneNumber)
    {
        $otp = $this->OTPGenerator($phoneNumber);
        $ttl = env('OTP_EXPIRE_MINUTES') * 60; //? Minutes in miliseconds
        $expires  = Carbon::now()->timestamp + $ttl;
        $data = "$phoneNumber.$otp.$expires";
        $hash = hash_hmac('sha256', $data, config('app.key'));
        $fullHash = "$hash.$expires";
        return [$fullHash, $otp];
    }

    protected function OTPGenerator()
    {
        mt_srand(crc32(microtime()));
        $number = strval(mt_rand(100000000, 999999999));
        return substr($number, 0, env('OTP_LENGTH'));
    }
}
