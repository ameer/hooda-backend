<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use SoapClient;

class SMSController extends Controller
{
    function __construct()
    {
        $this->curl = curl_init();
        $this->webServiceURL = env('WEB_SERVICE_URL') . env('KAVENEGAR_API_KEY');
        $this->template = env('KAVENEGAR_VERIFY_TEMPLATE');
    }

    public function sendSMS($phoneNumber, $otp)
    {
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => "$this->webServiceURL/verify/lookup.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('receptor' => $phoneNumber, 'token' => $otp, 'template' => $this->template),
        ));
        try {
            $response = curl_exec($this->curl);
            curl_close($this->curl);
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
