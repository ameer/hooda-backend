<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    public function verifyOTP($phoneNumber, $fullHash, $input_otp)
    {
        $result = false;
        $msg = "کد وارد شده صحیح نمی‌باشد";
        // Seperate Hash value and expires from the hash returned from the user
        $exploded_hash = explode('.', $fullHash);
        $hashValue = $exploded_hash[0];
        $expires = $exploded_hash[1];
        $now = Carbon::now()->timestamp;
        // Calculate new hash with the same key and the same algorithm
        $data  = "$phoneNumber.$input_otp.$expires";
        $newCalculatedHash = hash_hmac('sha256', $data, config('app.key'));

        // Match the hashes
        if (md5($newCalculatedHash) === md5($hashValue)) {

            // Check if expiry time has passed
            if ($now > intval($expires)) {
                $msg = "کد وارد شده منقضی شده است.";
            } else {
                $result = true;
                $msg = "کد وارد شده صحیح است.";
            }
        }
        return [$result, $msg];
    }

    public function verifySignUpHash($phoneNumber, $fullHash)
    {
        $result = false;
        $msg = "در روند ثبت‌نام اشتباهی رخ داده است.";
        // Seperate Hash value and expires from the hash returned from the user
        $exploded_hash = explode('.', $fullHash);
        $hashValue = $exploded_hash[0];
        $expires = $exploded_hash[1];
        $now = Carbon::now()->timestamp;
        // Calculate new hash with the same key and the same algorithm
        $data  = "$phoneNumber.$expires";
        $newCalculatedHash = hash_hmac('sha256', $data, config('app.key'));

        // Match the hashes
        if (md5($newCalculatedHash) === md5($hashValue)) {

            // Check if expiry time has passed
            if ($now > intval($expires)) {
                $msg = "ثبت‌نام منقضی شده است.";
            } else {
                $result = true;
                $msg = "ثبت‌نام معتبر است..";
            }
        }
        return [$result, $msg];
    }

    public function ProcessOTPRequest($phoneNumber)
    {
        [$fullHash, $otp] = $this->GenerateHash($phoneNumber);
        $masked_phone_number = substr_replace($phoneNumber, '***', 4, 3);
        return [$fullHash, $phoneNumber, $otp, $masked_phone_number];
    }

    protected function GenerateHash($phoneNumber)
    {
        $otp = $this->OTPGenerator($phoneNumber);
        $ttl = config('app.otp_expire_minutes') * 60; //? Minutes in miliseconds
        $expires  = Carbon::now()->timestamp + $ttl;
        $data = "$phoneNumber.$otp.$expires";
        $hash = hash_hmac('sha256', $data, config('app.key'));
        $fullHash = "$hash.$expires";
        return [$fullHash, $otp];
    }

    public function generateSignUpHash($phoneNumber)
    {
        $ttl = config('app.otp_expire_minutes') * 60; //? Minutes in miliseconds
        $expires  = Carbon::now()->timestamp + $ttl;
        $data = "$phoneNumber.$expires";
        $hash = hash_hmac('sha256', $data, config('app.key'));
        $signUpHash = "$hash.$expires";
        return $signUpHash;
    }

    protected function OTPGenerator()
    {
        mt_srand(crc32(microtime()));
        $number = strval(mt_rand(100000000, 999999999));
        return substr($number, 0, config('app.otp_length'));
    }
}
