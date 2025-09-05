<?php

namespace Pw\Bpjs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use LZCompressor\LZString;

class BpjsIntegration
{
    /**
     * Guzzle HTTP Client object
     * @var \GuzzleHttp\Client
     */
    public $client;

    /**
     * Request headers
     * @var array
     */
    public $headers;

    /**
     * X-cons-id header value
     * @var int
     */
    public $cons_id;

    /**
     * X-Timestamp header value
     * @var string
     */
    public $timestamp;

    /**
     * X-Signature header value
     * @var string
     */
    public $signature;

    /**
     * @var string
     */
    public $secret_key;

    /**
     * @var string
     */
    public $user_key;

    /**
     * @var string
     */
    public $base_url;

    /**
     * @var string
     */
    public $service_name;

    /**
     * @var string
     */
    public $service_time_out;

    /**
     * @var string
     */
    private $decrypt_key;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false,
            // 'timeout' => 1,
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
     *      'user_key' => '1234567890',
     *      'base_url' => 'https://xxxxxxxxxx.xx.xx',
     *      'service_name' => 'xxxxxx-xxxx-xxx',
     *      'service_time_out' => 6,
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
            'user_key' => $this->user_key,
        ];
        return $this;
    }

    public function setSignature()
    {
        $data = $this->cons_id . '&' . $this->timestamp;
        $signature = hash_hmac('sha256', $data, $this->secret_key, true);
        $encodedSignature = base64_encode($signature);
        $this->signature = $encodedSignature;

        //decrypt_key
        $this->decrypt_key = $this->cons_id . $this->secret_key . $this->timestamp;
        return $this;
    }

    public function setTimestamp()
    {
        date_default_timezone_set('UTC');
        $this->timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        date_default_timezone_set('Asia/Jakarta');
        return $this;
    }

    protected function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';

        // hash
        $key_hash = hex2bin(hash('sha256', $key));

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);

        return $output;
    }

    protected function stringEncrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';

        // hash
        $key_hash = hex2bin(hash('sha256', $key));

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $output = openssl_encrypt($string, $encrypt_method, $key_hash, 0, $iv);

        return $output;
    }

    protected function decompress($string)
    {
        return LZString::decompressFromEncodedURIComponent($string);
    }

    protected function decryptResponse($response)
    {
        if (strpos($this->service_name, 'vclaim') === 0 || strpos($this->service_name, 'antreanrs') === 0 || strpos($this->service_name, 'ihs') === 0 || strpos($this->service_name, 'apotek') === 0) {
          $responseVar = json_decode($response);
          if (isset($responseVar->response)) {
              $responseVar->response = json_decode($this->decompress($this->stringDecrypt($this->decrypt_key, $responseVar->response)), true);
          }

          return json_encode($responseVar);
        }
        return $response;
    }

    public function encryptGzip($data)
    {
        $compressed = gzcompress(json_encode($data), 9);
        $output = $this->stringEncrypt($this->decrypt_key, $compressed);

        return $output;
    }

    public function timeoutResponse()
    {
        $output = [
            'metaData' => [
                'code' => '201',
                'message' => 'Koneksi ke server BPJS bermasalah. Silahkan Coba Lagi Setelah Beberapa Saat.',
            ],
            'response' => null,
        ];
        return json_encode($output);
    }

    public function get($feature)
    {
        $this->setTimestamp()->setSignature()->setHeaders();

        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';

        try {
            $response = $this->client->request('GET', $url, ['timeout' => (int) $this->service_time_out, 'headers' => $this->headers])->getBody()->getContents();
            $response = $this->decryptResponse($response);
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
        $this->setTimestamp()->setSignature()->setHeaders();

        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';
        if ($header != null) {
            $this->headers['Content-Type'] = $header;
        }
        try {
            $response = $this->client->request('POST', $url, ['timeout' => (int) $this->service_time_out, 'headers' => $this->headers, 'json' => $data])->getBody()->getContents();
            $response = $this->decryptResponse($response);
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
        $this->setTimestamp()->setSignature()->setHeaders();

        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';
        try {
            $response = $this->client->request('PUT', $url, ['timeout' => (int) $this->service_time_out, 'headers' => $this->headers, 'json' => $data])->getBody()->getContents();
            $response = $this->decryptResponse($response);
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
        $this->setTimestamp()->setSignature()->setHeaders();

        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';
        try {
            $response = $this->client->request('DELETE', $url, ['timeout' => (int) $this->service_time_out, 'headers' => $this->headers, 'json' => $data])->getBody()->getContents();
            $response = $this->decryptResponse($response);
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
