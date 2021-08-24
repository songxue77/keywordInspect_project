<?php

namespace App\Libraries;

class RestApi
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;
    protected $customerId;

    function __construct($baseUrl, $apiKey, $secretKey, $customerId)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->customerId = $customerId;
    }

    protected function generateSignature($timestamp, $method, $path)
    {
        $sign = $timestamp.'.'.$method.'.'.$path;
        $signature = hash_hmac('sha256', $sign, $this->secretKey, true);

        return base64_encode($signature);
    }

    protected function getTimestamp()
    {
        return round(microtime(true) * 1000);
    }

    protected function getHeader($method, $uri)
    {
        $timestamp = $this->getTimestamp();
        $header = [
            'Content-Type: application/json; charset=UTF-8',
            'X-Timestamp: '.$timestamp,
            'X-API-KEY: '.$this->apiKey,
            'X-Customer: '.$this->customerId,
            'X-Signature: '.$this->generateSignature($timestamp, $method, $uri),
        ];

        return $header;
    }

    protected function buildHttpQuery($query)
    {
        if (!empty ($query)) {
            $query_array = [];
            foreach ($query as $key => $key_value) {
                $query_array [] = urlencode($key).'='.urlencode($key_value);
            }

            return implode('&', $query_array);
        }

        return '';
    }

    protected function parseResponse($response)
    {
        $return = [];

        if (!empty($response)) {
            $result = explode("\r\n\r\n", $response, 2);
            if (count($result) < 2) {
                echo 'invalid response body! it has no HEADER and BODY!\n';

                return $return;
            }

            if (isset($result[1])) {
                $body = $result[1];
                $jsonbody = json_decode($body, true);

                $return = [
                    'code' => '0000',
                    'data' => $jsonbody,
                    'message' => 'success'
                ];
            }
        }

        return $return;
    }

    public function get($uri, $query = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl.$uri.(empty($query) ? '' : '?'.$this->buildHttpQuery($query)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeader('GET', $uri));

        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if (empty ($code)) {
            $response = [
                'code' => '9001',
                'message' => 'No HTTP code was returned',
                'data' => []
            ];
        } elseif (!empty ($error)) {
            $response = [
                'code' => '9002',
                'messgae' => $error,
                'data' => []
            ];
        } else {
            $response = $this->parseResponse($output);
        }

        return $response;
    }

    public function post($uri, $data, $query = [])
    {
        $data_string = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl.$uri.(empty($query) ? '' : '?'.$this->buildHttpQuery($query)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeader('POST', $uri));

        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if (empty ($code)) {
            $response = [
                'code' => '9001',
                'message' => 'No HTTP code was returned',
                'data' => []
            ];
        } elseif (!empty ($error)) {
            $response = [
                'code' => '9002',
                'messgae' => $error,
                'data' => []
            ];
        } else {
            $response = $this->parseResponse($output);
        }

        return $response;
    }
}
