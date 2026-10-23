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
     * @var int|string
     */
    public $service_time_out = 15;

    /**
     * @var bool|null
     */
    public $need_decrypt = null;

    /**
     * @var bool
     */
    public $log_enabled = true;

    /**
     * Format output log: 'json' (baris JSON) atau 'text' (Monolog style)
     * @var string
     */
    public $log_format = 'json';

    /**
     * Ekstensi file log ('json' atau 'log')
     * @var string
     */
    public $log_file_extension = 'json';

    /**
     * @var string|null
     */
    public $log_dir = null;

    /**
     * @var string|null
     */
    public $log_file = null;

    /**
     * @var string
     */
    public $log_file_prefix = 'bpjs-bridging-';

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
     *      'need_decrypt' => true, // optional (default: auto detect via service_name)
     *      'log_enabled' => true,
     *      'log_format' => 'json', // 'json' or 'text'
     *      'log_file_extension' => 'json', // 'json' or 'log'
     *      'log_dir' => '/path/to/log',
     *      'log_file' => 'custom.json', // optional
     *      'log_file_prefix' => 'bpjs-bridging-', // optional
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
        if (empty($response) || !is_string($response)) {
            return $response;
        }

        if ($this->need_decrypt !== null) {
            $shouldDecrypt = (bool) $this->need_decrypt;
        } else {
            $prefix = strtok(strtolower((string) $this->service_name), '-_');
            $shouldDecrypt = in_array($prefix, ['vclaim', 'antreanrs', 'ihs', 'wsihs', 'apotek'], true);
        }

        if ($shouldDecrypt) {
            $responseVar = json_decode($response);
            if (!is_object($responseVar)) {
                return $response;
            }

            if (isset($responseVar->response) && is_string($responseVar->response) && trim($responseVar->response) !== '') {
                $decrypted = $this->stringDecrypt($this->decrypt_key, $responseVar->response);
                if ($decrypted !== false && $decrypted !== null) {
                    $decompressed = $this->decompress($decrypted);
                    if ($decompressed !== false && $decompressed !== null) {
                        $jsonDecoded = json_decode($decompressed, true);
                        $responseVar->response = ($jsonDecoded !== null) ? $jsonDecoded : $decompressed;
                    }
                }
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

        $response = null;
        $error = null;
        $startTime = microtime(true);
        $timeout = (!empty($this->service_time_out) && (int) $this->service_time_out > 0) ? (int) $this->service_time_out : 15;

        try {
            $response = $this->client->request('GET', $url, ['timeout' => $timeout, 'headers' => $this->headers])->getBody()->getContents();
            $response = $this->decryptResponse($response);
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $response = $e->getResponse() ? (string) $e->getResponse()->getBody() : $this->timeoutResponse();
            $error = $e->getMessage();
        } finally {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            $this->writeBpjsBridgeLog('get', $url, null, $response, $durationMs, $error);
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

        $response = null;
        $error = null;
        $startTime = microtime(true);
        $timeout = (!empty($this->service_time_out) && (int) $this->service_time_out > 0) ? (int) $this->service_time_out : 15;

        try {
            $response = $this->client->request('POST', $url, ['timeout' => $timeout, 'headers' => $this->headers, 'json' => $data])->getBody()->getContents();
            $response = $this->decryptResponse($response);
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $response = $e->getResponse() ? (string) $e->getResponse()->getBody() : $this->timeoutResponse();
            $error = $e->getMessage();
        } finally {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            $this->writeBpjsBridgeLog('post', $url, $data, $response, $durationMs, $error);
        }

        return $response;
    }

    public function put($feature, $data = [])
    {
        $this->setTimestamp()->setSignature()->setHeaders();

        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';

        $response = null;
        $error = null;
        $startTime = microtime(true);
        $timeout = (!empty($this->service_time_out) && (int) $this->service_time_out > 0) ? (int) $this->service_time_out : 15;

        try {
            $response = $this->client->request('PUT', $url, ['timeout' => $timeout, 'headers' => $this->headers, 'json' => $data])->getBody()->getContents();
            $response = $this->decryptResponse($response);
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $response = $e->getResponse() ? (string) $e->getResponse()->getBody() : $this->timeoutResponse();
            $error = $e->getMessage();
        } finally {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            $this->writeBpjsBridgeLog('put', $url, $data, $response, $durationMs, $error);
        }

        return $response;
    }

    public function delete($feature, $data = [])
    {
        $this->setTimestamp()->setSignature()->setHeaders();

        $url = $this->base_url . '/' . $this->service_name . '/' . $feature;
        $this->headers['Content-Type'] = 'Application/x-www-form-urlencoded';

        $response = null;
        $error = null;
        $startTime = microtime(true);
        $timeout = (!empty($this->service_time_out) && (int) $this->service_time_out > 0) ? (int) $this->service_time_out : 15;

        try {
            $response = $this->client->request('DELETE', $url, ['timeout' => $timeout, 'headers' => $this->headers, 'json' => $data])->getBody()->getContents();
            $response = $this->decryptResponse($response);
        } catch (ClientException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (RequestException $e) {
            $response = $this->timeoutResponse();
            $error = $e->getMessage();
        } catch (\Exception $e) {
            $response = $e->getResponse() ? (string) $e->getResponse()->getBody() : $this->timeoutResponse();
            $error = $e->getMessage();
        } finally {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            $this->writeBpjsBridgeLog('delete', $url, $data, $response, $durationMs, $error);
        }

        return $response;
    }

    protected function writeBpjsBridgeLog($functionName, $url, $requestData = null, $response = null, $durationMs = null, $error = null)
    {
        if (!$this->log_enabled) {
            return false;
        }

        $now = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));

        $logDir = $this->log_dir;
        if (!empty($this->log_file) && (strpos($this->log_file, '/') === 0 || preg_match('/^[A-Za-z]:[\\\\\/]/', $this->log_file))) {
            $logFile = $this->log_file;
            $logDir = dirname($logFile);
        } else {
            if (empty($logDir)) {
                $logDir = getenv('BPJS_LOG_DIR') ?: (sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bpjs-log');
            }
            $logDir = rtrim($logDir, '/\\');

            if (!empty($this->log_file)) {
                $logFile = $logDir . DIRECTORY_SEPARATOR . ltrim($this->log_file, '/\\');
            } else {
                $prefix = !empty($this->log_file_prefix) ? $this->log_file_prefix : 'bpjs-bridging-';
                $ext = !empty($this->log_file_extension) ? ltrim($this->log_file_extension, '.') : 'json';
                $logFile = $logDir . DIRECTORY_SEPARATOR . $prefix . $now->format('Y-m-d') . '.' . $ext;
            }
        }

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        if (!is_dir($logDir) || !is_writable($logDir)) {
            return false;
        }

        $forwarded = $this->getServerHeader('X-Forwarded-For');
        $realIp = $this->getServerHeader('X-Real-IP');
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        $ips = $this->extractClientIps($forwarded, $remoteAddr);

        $decodedResponse = is_string($response) ? json_decode($response, true) : $response;

        $payload = [
            'timestamp' => $now->format('Y-m-d H:i:s'),
            'function' => $functionName,
            'url' => $url,
            'duration_ms' => $durationMs,
            'error' => $error,
            'request' => $requestData,
            'response' => ($decodedResponse !== null) ? $decodedResponse : $response,
            'ip' => count($ips) > 0 ? $ips[0] : $remoteAddr,
            'ips' => $ips,
            'forwarded' => $forwarded,
            'real_ip' => $realIp,
        ];

        if ($this->log_format === 'text') {
            $line = '[' . $now->format('Y-m-d H:i:s') . '] bpjs.INFO: ' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
        } else {
            $line = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
        }

        return @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX) !== false;
    }

    protected function getServerHeader($headerName)
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));

        if (isset($_SERVER[$serverKey])) {
            return $_SERVER[$serverKey];
        }

        return null;
    }

    protected function extractClientIps($forwarded, $remoteAddr)
    {
        $ips = [];

        if (!empty($forwarded)) {
            $forwardedIps = explode(',', $forwarded);

            foreach ($forwardedIps as $ip) {
                $ip = trim($ip);

                if ($ip !== '') {
                    $ips[] = $ip;
                }
            }
        }

        if (!empty($remoteAddr) && !in_array($remoteAddr, $ips)) {
            $ips[] = $remoteAddr;
        }

        return $ips;
    }
}
