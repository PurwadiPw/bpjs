<?php

namespace Pw\Bpjs\Vclaim;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Peserta extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByNoKartu($noKartu, $tglPelayananSEP)
    {
        $response = $this->get('Peserta/nokartu/'.$noKartu.'/tglSEP/'.$tglPelayananSEP);
        return json_decode($response, true);
    }

    public function getByNIK($nik, $tglPelayananSEP)
    {
        $response = $this->get('Peserta/nik/'.$nik.'/tglSEP/'.$tglPelayananSEP);
        return json_decode($response, true);
    }
}