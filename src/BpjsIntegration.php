<?php

namespace Bpjs\Vclaim;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class BpjsIntegration
{
    public $client;
    public $headers;

    // 1. X-cons-id
    public $cons_id;

    // 2. X-Timestamp
    public $timestamp;

    // 3. X-Signature
    public $signature;
    public $secret_key;

    // 4. Base URL & Service Name
    public $base_url;
    public $service_name;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false,
            'timeout' => 30,
            // 'connect_timeout' => 15,
            'http_errors' => false,
        ]);
    }

    /**
     * [initialize description]
     * @param array $config
     * [
     *      'cons_id' => '12345',
     *      'secret_key' => '1234567890',
     * ]
     */
    public function initialize($config = [])
    {
        foreach ($config as $configName => $configValue) {
            $this->$configName = $configValue;
        }

        $this->setTimestamp()->setSignature()->setHeaders();
        return $this;
    }

    public function setHeaders()
    {
        $this->headers = [
            'X-cons-id' => $this->cons_id,
            'X-Timestamp' => $this->timestamp,
            'X-Signature' => $this->signature,
        ];
        return $this;
    }

    public function setSignature()
    {
        $data = $this->cons_id . '&' . $this->timestamp;
        $signature = hash_hmac('sha256', $data, $this->secret_key, true);
        $encodedSignature = base64_encode($signature);
        $this->signature = $encodedSignature;
        return $this;
    }

    public function setTimestamp()
    {
        date_default_timezone_set('UTC');
        $this->timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        return $this;
    }

    public function timeoutResponse()
    {
        $output = [
            'metaData' => [
                'code' => '201',
                'message' => 'Koneksi ke server BPJS bermasalah. Harap hubungi IT RS.',
            ],
            'response' => null,
        ];
        return json_encode($output);
    }

    public function get($feature)
    {
        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';

        try {
            $response = $this->client->request('GET', $url, ['headers' => $this->headers])->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
        } catch (Exception $e) {
            $response = $e->getResponse()->getBody();
        }
        return $response;
    }

    public function post($feature, $data = [], $header = null)
    {
        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';
        if ($header != null) {
            $this->headers['Content-Type'] = $header;
        }
        try {
            $response = $this->client->request('POST', $url, ['headers' => $this->headers, 'json' => $data])->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
        } catch (Exception $e) {
            $response = $e->getResponse()->getBody();
        }
        return $response;
    }

    public function put($feature, $data = [])
    {
        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';
        try {
            $response = $this->client->request('PUT', $url, ['headers' => $this->headers, 'json' => $data])->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
        } catch (Exception $e) {
            $response = $e->getResponse()->getBody();
        }
        return $response;
    }

    public function delete($feature, $data = [])
    {
        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';
        try {
            $response = $this->client->request('DELETE', $url, ['headers' => $this->headers, 'json' => $data])->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
        } catch (Exception $e) {
            $response = $e->getResponse()->getBody();
        }
        return $response;
    }
}
