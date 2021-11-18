<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\OTPController;
use Inertia\Inertia;
use App\Http\Controllers\SMSController;
use Exception;

class PhoneNumberVerifyController extends Controller
{
    public function create(Request $request)
    {
        if ($request->user()->userPhoneVerified()) {
            redirect(RouteServiceProvider::HOME);
        } else {
            $otp = new OTPController();
            [$fullHash, $phoneNumber, $otp, $maskedPhoneNumber] = $otp->ProcessOTPRequest($request->user());
            if ($this->sendOTP($phoneNumber, $otp)) {
                return Inertia::render('Auth/VerifyPhone')->with([
                    'loginHash' => $fullHash,
                    'phoneNumber' => $maskedPhoneNumber,
                ]);
            } else {
                throw new Exception("Error sending SMS", 1);
                
            }
        };
    }

    public function verify(Request $request)
    {
        $otp = new OTPController();
        $otp_verification = $otp->VerifyOTPRequest($request);
        if (!$otp_verification['result']) {
            return back()->withErrors(['msg', 'Invalid Code Please Try Again!']);
        }

        if ($request->user()->userPhoneVerified()) {
            return redirect()->route('home');
        }

        $request->user()->phoneVerifiedAt();

        return redirect(RouteServiceProvider::HOME);
    }

    protected function sendOTP($phoneNumber, $otp)
    {
        $sms = new SMSController();
        $result = $sms->sendSMS($phoneNumber, $otp);
        error_log($result);
        return $result;
    }
}
