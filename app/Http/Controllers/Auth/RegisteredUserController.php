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
use Exception;
use Illuminate\Support\Facades\Date;
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
            'phone' => 'required|string|max:11|unique:users'
        ]);
        $otp = new OTPController();
        [$fullHash, $phoneNumber, $otp, $maskedPhoneNumber] = $otp->ProcessOTPRequest($request->phone);
        if ($this->sendOTP($phoneNumber, $otp)) {
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

    public function verifyOTP(Request $request)
    {
        $otp_length = config('otp_length', 4);
        $request->validate([
            'otp' => 'required|string|max:' . $otp_length,
            'phone' => 'required|string|max:11|unique:users',
            'loginHash' => 'required|string'
        ]);
        $phoneNumber = $request->phone;
        $fullHash = $request->loginHash;
        $input_otp = $request->otp;
        $otp_class = new OTPController();
        [$result, $msg] = $otp_class->verifyOTP($phoneNumber, $fullHash, $input_otp);
        if ($result) {
            $user = User::create([
                'phone' => $phoneNumber
            ]);
            event(new Registered($user));
            Auth::login($user);
            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => [
                    'message' => $msg
                ]
            ])->setStatusCode(401);
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
        // $sms = new SMSController();
        // $result = $sms->sendSMS($phoneNumber, $otp);
        // return $result;
        error_log($otp);
        return true;
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
            'fullname' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = Auth::user();
        if ($user->password == null) { // if user is not registered
            $user->fullname = $request->fullname;
            $user->city = $request->city;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            $token = $request->user()->createToken('default-token');
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'access_token' => $token->plainTextToken
            ]);
        } else { // Duplicate register request. redirect to home
            return response()->json([
                'success' => false,
                'errors' => [
                    'message' => 'You have already registered.'
                ]
            ])->setStatusCode(401);
        }
    }
    public function showUserData(Request $request)
    {
        return $request->user();
    }
}
