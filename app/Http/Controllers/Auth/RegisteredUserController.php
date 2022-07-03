<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\SMSController;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle a registration request for the application.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     * @return JsonResponse
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:11'
        ]);
        $user = User::where(['phone' => $request->phone])->first();
        if ($user && $user->password != null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number already exists'
            ], 409);
        } else {
            $otp = new OTPController();
            [$fullHash, $phoneNumber, $otp, $maskedPhoneNumber] = $otp->ProcessOTPRequest($request->phone);
            $smsResult = $this->sendOTP($phoneNumber, $otp);
            if ($smsResult['status'] == 'success') {
                if ($request->wantsJson()) {
                    return response()->json([
                        'loginHash' => $fullHash,
                        'phoneNumber' => $maskedPhoneNumber
                    ]);
                } else {
                    return Inertia::render('Auth/VerifyPhone')->with([
                        'loginHash' => $fullHash,
                        'phoneNumber' => $maskedPhoneNumber,
                    ]);
                }
            } else {
                throw new Exception("Error sending SMS", 1);
            }
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
            $user = User::updateOrCreate([
                'phone' => $phoneNumber
            ]);
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
    /** 
     * Send OTP to the user's phone number
     * 
     * @param string $phoneNumber
     * @param string $otp
     * 
     * @return bool
     */
    protected function sendOTP($phoneNumber, $otp)
    {
        $sms = new SMSController();
        $result = $sms->sendSMS($phoneNumber, $otp);
        return $result;
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:11',
            'fullname' => 'required|regex:/^[\x{0600}-\x{06ee}\s]+$/u|string|max:255',
            'city' => 'required|regex:/^[\x{0600}-\x{06ee}\s]+$/u|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
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
            if ($user->password != null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You already registered!'
                ], 409);
            } else {
                $user->fullname = $request->fullname;
                $user->city = $request->city;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->is_active = true;
                $user->save();
                $token = $user->createToken('default-token');
                return response()->json([
                    'success' => true,
                    'message' => 'User registered successfully',
                    'access_token' => $token->plainTextToken,
                    'user' => $user
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }
    public function showUserData(Request $request)
    {
        return $request->user();
    }

    public function register_admin(Request $request)
    {
        $request->validate([
            'fullname' => 'required|regex:/^[\x{0600}-\x{06ee}\s]+$/u|string|max:255',
            'city' => 'required|regex:/^[\x{0600}-\x{06ee}\s]+$/u|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::updateOrCreate([
            'phone' => $request->phone
        ]);
        $user->fullname = $request->fullname;
        $user->city = $request->city;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_active = true;
        $user->role = 2;
        $user->save();
        $token = $user->createToken('default-token');
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'access_token' => $token->plainTextToken,
            'user' => $user
        ]);
    }
}
