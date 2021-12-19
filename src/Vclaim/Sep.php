<?php

namespace Pw\Bpjs\Vclaim;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Sep extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    /* Pembuatan SEP ------------------------------------------------------------ */
    public function insertSEP($data = [])
    {
        $response = $this->post('SEP/2.0/insert', $data);
        return json_decode($response, true);
    }

    public function updateSEP($data = [])
    {
        $response = $this->put('SEP/2.0/update', $data);
        return json_decode($response, true);
    }

    public function deleteSEP($data = [])
    {
        $response = $this->delete('SEP/2.0/delete', $data);
        return json_decode($response, true);
    }
    
    public function cariSEP($keyword)
    {
        $response = $this->get('SEP/'.$keyword);
        return json_decode($response, true);
    }
    
    /* Potensi Suplesi Jasa Raharja --------------------------------------------- */
    public function suplesiJasaRaharja($noKartu, $tglPelayanan)
    {
        $response = $this->get('sep/JasaRaharja/Suplesi/'.$noKartu.'/tglPelayanan/'.$tglPelayanan);
        return json_decode($response, true);
    }
    
    public function dataIndukKecelakaan($noKartu)
    {
        $response = $this->get('sep/KllInduk/List/'.$noKartu);
        return json_decode($response, true);
    }

    /* Approval Penjaminan SEP -------------------------------------------------- */
    public function pengajuanPenjaminanSep($data = [])
    {
        $response = $this->post('Sep/pengajuanSEP', $data);
        return json_decode($response, true);
    }

    public function approvalPenjaminanSep($data = [])
    {
        $response = $this->post('Sep/aprovalSEP', $data);
        return json_decode($response, true);
    }

    /* Update Tgl Pulang SEP ---------------------------------------------------- */
    public function updateTglPlg($data = [])
    {
        $response = $this->put('Sep/2.0/updtglplg', $data);
        return json_decode($response, true);
    }
    
    /* Integrasi SEP dan Inacbg ------------------------------------------------- */
    public function inacbgSEP($keyword)
    {
        $response = $this->get('sep/cbg/'.$keyword);
        return json_decode($response, true);
    }
    
    /* SEP Internal ------------------------------------------------------------- */
    public function dataSEPInternal($noSep)
    {
        $response = $this->get('SEP/Internal/'.$noSep);
        return json_decode($response, true);
    }

    public function deleteSEPInternal($data = [])
    {
        $response = $this->delete('SEP/Internal/delete', $data);
        return json_decode($response, true);
    }

    /* Finger Print ------------------------------------------------------------- */
    public function getFingerPrint($noKartu, $tglPelayanan)
    {
        $response = $this->get('SEP/FingerPrint/Peserta/'.$noKartu.'/TglPelayanan/'.$tglPelayanan);
        return json_decode($response, true);
    }

    public function getFingerPrintList($tglPelayanan)
    {
        $response = $this->get('SEP/FingerPrint/List/Peserta/TglPelayanan/'.$tglPelayanan);
        return json_decode($response, true);
    }
}