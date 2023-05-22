<?php

namespace Pw\Bpjs\Vclaim;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Rujukan extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRujukan($data = [])
    {
        $response = $this->post('Rujukan/2.0/insert', $data);
        return json_decode($response, true);
    }

    public function insertRujukanKhusus($data = [])
    {
        $response = $this->post('Rujukan/Khusus/insert', $data);
        return json_decode($response, true);
    }

    public function updateRujukan($data = [])
    {
        $response = $this->put('Rujukan/2.0/Update', $data);
        return json_decode($response, true);
    }

    public function deleteRujukan($data = [])
    {
        $response = $this->delete('Rujukan/delete', $data);
        return json_decode($response, true);
    }

    public function deleteRujukanKhusus($data = [])
    {
        $response = $this->delete('Rujukan/Khusus/delete', $data);
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
    
    public function listRujukanKhusus($bulan, $tahun)
    {
        $response = $this->get('Rujukan/Khusus/List/Bulan/'.$bulan.'/Tahun/'.$tahun);
        return json_decode($response, true);
    }
    
    public function listSpesialistikRujukan($kodePpk, $tgl)
    {
        $response = $this->get('Rujukan/ListSpesialistik/PPKRujukan/'.$kodePpk.'/TglRujukan/'.$tgl);
        return json_decode($response, true);
    }
    
    public function listSarana($kodePpk)
    {
        $response = $this->get('Rujukan/ListSarana/PPKRujukan/'.$kodePpk);
        return json_decode($response, true);
    }
    
    public function jumlahSepRujukan($jenisRujukan, $noRujukan)
    {
        $response = $this->get('Rujukan/JumlahSEP/'.$jenisRujukan.'/'.$noRujukan);
        return json_decode($response, true);
    }
}
