<?php

namespace Pw\Bpjs\Vclaim;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class LembarPengajuanKlaim extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertLPK($data = [])
    {
        $response = $this->post('LPK/insert', $data);
        return json_decode($response, true);
    }

    public function updateLPK($data = [])
    {
        $response = $this->put('LPK/update', $data);
        return json_decode($response, true);
    }

    public function deleteLPK($data = [])
    {
        $response = $this->delete('LPK/delete', $data);
        return json_decode($response, true);
    }
    
    public function cariLPK($tglMasuk, $jnsPelayanan)
    {
        $response = $this->get('LPK/TglMasuk/'.$tglMasuk.'/JnsPelayanan/'.$jnsPelayanan);
        return json_decode($response, true);
    }
}