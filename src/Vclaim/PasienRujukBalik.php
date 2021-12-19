<?php

namespace Pw\Bpjs\Vclaim;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class PasienRujukBalik extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    /* Pembuatan Rujuk Balik (PRB) ---------------------------------------------- */
    public function insertRujukan($data = [])
    {
        $response = $this->post('PRB/insert', $data);
        return json_decode($response, true);
    }

    public function updateRujukan($data = [])
    {
        $response = $this->put('PRB/Update', $data);
        return json_decode($response, true);
    }

    public function deleteRujukan($data = [])
    {
        $response = $this->delete('PRB/Delete', $data);
        return json_decode($response, true);
    }
    
    /* Pencarian Data PRB ------------------------------------------------------- */
    public function cariByNoSrb($noSrb, $noSep)
    {
        $response = $this->get('prb/'.$noSrb.'/nosep/'.$noSep);
        return json_decode($response, true);
    }
    
    public function cariByTglSrb($tglMulai, $tglAkhir)
    {
        $response = $this->get('prb/tglMulai/'.$tglMulai.'/tglAkhir/'.$tglAkhir);
        return json_decode($response, true);
    }
}