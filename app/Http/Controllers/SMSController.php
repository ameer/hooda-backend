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
        $this->webServiceURL = config('app.KAVENEGAR_WEB_SERVICE_URL') . config('app.KAVENEGAR_API_KEY') . '/verify/lookup.json';
        $this->template = config('app.KAVENEGAR_VERIFY_TEMPLATE');
    }

    public static function sendSMSUsingSOAP($phoneNumber, $otp)
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        $sms_client = new SoapClient('http://payamak-service.ir/SendService.svc?wsdl', array('encoding' => 'UTF-8'));
        $parameters['userName'] = "mt.09396799420";
        $parameters['password'] = "mom#523";
        $parameters['fromNumber'] = "50005708637509";
        $parameters['toNumbers'] = array($phoneNumber);
        $parameters['messageContent'] = "کد ورود به اپلیکیشن هودا:\n $otp";
        $parameters['isFlash'] = false;

        $result = $sms_client->SendSMS(array('userName' => "mt.09396799420", 'password' => "mom#523", 'fromNumber' => "50005708637509", 'toNumbers' => array($phoneNumber), 'messageContent' => "کد ورود به اپلیکیشن هودا:\n $otp", false));
        error_log($result->SendSMSResult);
        if ($result->SendSMSResult == 0) {
            return array('status' => 'success', 'result' => $result);
        } else {
            return array('status' => 'error', 'result' => $result);
        }
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
            return array('status' => 'success', 'result' => $response);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return array('status' => 'error', 'result' => $e->getMessage());
        }
    }
}
