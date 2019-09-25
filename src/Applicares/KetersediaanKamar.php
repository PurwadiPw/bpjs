<?php

namespace Bpjs\Vclaim\Applicares;

use Bpjs\Vclaim\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class KetersediaanKamar extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function refKelas()
    {
        $response = $this->get('ref/kelas');
        return json_decode($response, true);
    }

    public function bedGet($kodePpk, $start, $limit)
    {
        $response = $this->get('bed/read/'.$kodePpk.'/'.$start.'/'.$limit);
        return json_decode($response, true);
    }

    public function bedCreate($kodePpk, $data = [])
    {
    	$header = 'application/json';
        $response = $this->post('bed/create/'.$kodePpk, $data, $header);
        return json_decode($response, true);
    }

    public function bedUpdate($kodePpk, $data = [])
    {
        $header = 'application/json';
        $response = $this->post('bed/update/'.$kodePpk, $data, $header);
        return json_decode($response, true);
    }

    public function bedDelete($kodePpk, $data = [])
    {
        $header = 'application/json';
        $response = $this->post('bed/delete/'.$kodePpk, $data, $header);
        return json_decode($response, true);
    }
}