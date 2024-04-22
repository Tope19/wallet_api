<?php

namespace App\Services;

use App\Exceptions\PaystackException;
// use Unicodeveloper\Paystack\Paystack;
use Unicodeveloper\Paystack\Facades\Paystack;

// use Paystack;

class PaystackService
{
    public function initializePayment($amount, $email)
    {
        $url = "https://api.paystack.co/transaction/initialize";
        $secretKey = env('PAYSTACK_SECRET_KEY');

        $fields = [
          'email' => $email,
          'amount' => $amount * 100,
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $secretKey",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = json_decode(curl_exec($ch), true);
        return $result;

    }

    public function verifyPayment($reference)
    {
       try{
            $url = "https://api.paystack.co/transaction/verify/$reference";
            $secretKey = env('PAYSTACK_SECRET_KEY');
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $secretKey",
                    "Cache-Control: no-cache",
                ),
            ));

            $response = json_decode(curl_exec($curl), true);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                throw new PaystackException("cURL Error #:" . $err);
            }

            return $response;

        } catch (\Exception $e) {
            report_error($e);
            throw new PaystackException($e->getMessage());
        }
    }


}
