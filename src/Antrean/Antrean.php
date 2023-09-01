<?php

namespace Pw\Bpjs\Antrean;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Antrean extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function refPoli()
    {
        $response = $this->get('ref/poli');
        return json_decode($response, true);
    }
    
    public function refDokter()
    {
        $response = $this->get('ref/dokter');
        return json_decode($response, true);
    }
    
    public function refJadwalDokter($kodePoli, $tgl)
    {
        $response = $this->get('jadwaldokter/kodepoli/'.$kodePoli.'/tanggal/'.$tgl);
        return json_decode($response, true);
    }

    public function updateJadwalDokter($data = [])
    {
    	  $header = 'application/json';
        $response = $this->post('jadwaldokter/updatejadwaldokter', $data, $header);
        return json_decode($response, true);
    }

    public function tambahAntrean($data = [])
    {
    	  $header = 'application/json';
        $response = $this->post('antrean/add', $data, $header);
        return json_decode($response, true);
    }

    public function updateWaktuAntrean($data = [])
    {
    	  $header = 'application/json';
        $response = $this->post('antrean/updatewaktu', $data, $header);
        return json_decode($response, true);
    }

    public function batalAntrean($data = [])
    {
    	  $header = 'application/json';
        $response = $this->post('antrean/batal', $data, $header);
        return json_decode($response, true);
    }

    public function listTaskAntrean($data = [])
    {
    	  $header = 'application/json';
        $response = $this->post('antrean/getlisttask', $data, $header);
        return json_decode($response, true);
    }
    
    public function dashboardPerTgl($tgl, $wkt)
    {
        $response = $this->get('dashboard/waktutunggu/tanggal/'.$tgl.'/waktu/'.$wkt);
        return json_decode($response, true);
    }
    
    public function dashboardPerBln($bulan, $tahun, $waktu)
    {
        $response = $this->get('dashboard/waktutunggu/bulan/'.$bulan.'/tahun/'.$tahun.'/'.$waktu);
        return json_decode($response, true);
    }
    
    public function antreanPerTgl($tgl)
    {
        $response = $this->get('antrean/pendaftaran/tanggal/'.$tgl);
        return json_decode($response, true);
    }
    
    public function antreanPerKodeBooking($kodebooking)
    {
        $response = $this->get('antrean/pendaftaran/kodebooking/'.$kodebooking);
        return json_decode($response, true);
    }
}
