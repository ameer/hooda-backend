<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\SMSController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class PasswordResetOTPController extends Controller
{
    public function handlePasswordResetRequest(Request $request)
    {
        $user = User::where(['phone' => $request->phone])->firstOrFail();
        $request->validate([
            'phone' => 'required|string|max:11'
        ]);
        $request->validate([
            'phone' => 'required|string|max:11'
        ]);
        $otp = new OTPController();
        [$fullHash, $phoneNumber, $otp, $maskedPhoneNumber] = $otp->ProcessOTPRequest($request->phone);
        $smsResult = $this->sendOTP($phoneNumber, $otp);
        if ($smsResult['status'] == 'success') {
            return response()->json([
                'loginHash' => $fullHash,
                'phoneNumber' => $maskedPhoneNumber
            ]);
        } else {
            throw new Exception("Error sending SMS", 1);
        }
    }
    public function verifyOTP(Request $request)
    {
        $otp_length = config('otp_length', 4);
        $request->validate([
            'otp' => 'required|string|max:' . $otp_length,
            'phone' => 'required|string|max:11',
            'loginHash' => 'required|string'
        ]);
        $phoneNumber = $request->phone;
        $fullHash = $request->loginHash;
        $input_otp = $request->otp;
        $otp_class = new OTPController();
        [$result, $msg] = $otp_class->verifyOTP($phoneNumber, $fullHash, $input_otp);
        if ($result) {
            $otp = new OTPController();
            $signUpHash = $otp->generateSignupHash($request->phone);
            return response()->json([
                'success' => true,
                'message' => $msg,
                'hash' => $signUpHash,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => [
                    'message' => $msg
                ]
            ], 422);
        }
    }
    public function resetUserPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:11',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'hash' => 'required|string'
        ]);
        $otp_class = new OTPController();
        [$result, $msg] = $otp_class->verifySignupHash($request->phone, $request->hash);
        if (!$result) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'message' => $msg
                ]
            ], 422);
        }
        $user = User::where(['phone' => $request->phone])->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            if ($user->tokens()->count() > 0) {
                $user->tokens()->delete();
            }
            $token = $user->createToken('default-token');
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'access_token' => $token->plainTextToken,
                'user' => $user
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }
    protected function sendOTP($phoneNumber, $otp)
    {
        $sms = new SMSController();
        $result = $sms->sendSMS($phoneNumber, $otp);
        return $result;
    }
}
