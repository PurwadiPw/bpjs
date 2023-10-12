<?php

namespace Pw\Bpjs\Vclaim;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class iCare extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getICareHistory($data = [])
    {
        $response = $this->post('api/rs/validate', $data, 'application/json');
        return json_decode($response, true);
    }
    
}
