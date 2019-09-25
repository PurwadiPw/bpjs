<?php

namespace Bpjs\Vclaim\v1;

use Bpjs\Vclaim\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Rujukan extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRujukan($data = [])
    {
        $response = $this->post('Rujukan/insert', $data);
        return json_decode($response, true);
    }

    public function updateRujukan($data = [])
    {
        $response = $this->put('Rujukan/update', $data);
        return json_decode($response, true);
    }

    public function deleteRujukan($data = [])
    {
        $response = $this->delete('Rujukan/delete', $data);
        return json_decode($response, true);
    }
    
    public function cariByNoRujukan($searchBy, $keyword)
    {
        if ($searchBy == 'RS') {
            $urlSearch = 'Rujukan/RS/'.$keyword;
        } else {
            $urlSearch = 'Rujukan/'.$keyword;
        }
        $response = $this->get($urlSearch);
        return json_decode($response, true);
    }
    
    public function cariByNoKartu($searchBy, $keyword, $multi = false)
    {
        $record = $multi ? 'List/' : '';

        if ($searchBy == 'RS') {
            $urlSearch = 'Rujukan/RS/Peserta/'.$keyword;
        } else {
            $urlSearch = 'Rujukan/'.$record.'Peserta/'.$keyword;
        }
        $response = $this->get($urlSearch);
        return json_decode($response, true);
    }
    
    public function cariByTglRujukan($searchBy, $keyword)
    {
        if ($searchBy == 'RS') {
            $urlSearch = 'Rujukan/RS/TglRujukan/'.$keyword;
        } else {
            $urlSearch = 'Rujukan/List/Peserta/'.$keyword;
        }
        $response = $this->get($urlSearch);
        return json_decode($response, true);
    }
}