<?php

namespace Pw\Bpjs\Vclaim;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class RencanaKontrol extends BpjsIntegration
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertRencanaKontrol($data = [])
    {
        $response = $this->post('RencanaKontrol/insert', $data);
        return json_decode($response, true);
    }

    public function updateRencanaKontrol($data = [])
    {
        $response = $this->put('RencanaKontrol/Update', $data);
        return json_decode($response, true);
    }

    public function deleteRencanaKontrol($data = [])
    {
        $response = $this->delete('RencanaKontrol/Delete', $data);
        return json_decode($response, true);
    }

    public function insertSpri($data = [])
    {
        $response = $this->post('RencanaKontrol/InsertSPRI', $data);
        return json_decode($response, true);
    }

    public function updateSpri($data = [])
    {
        $response = $this->post('RencanaKontrol/UpdateSPRI', $data);
        return json_decode($response, true);
    }

    public function cariSep($noSep)
    {
        $response = $this->get('RencanaKontrol/nosep/'.$noSep);
        return json_decode($response, true);
    }

    public function cariSuratKontrol($noSurat)
    {
        $response = $this->get('RencanaKontrol/noSuratKontrol/'.$noSurat);
        return json_decode($response, true);
    }

    public function dataSuratKontrol($noKartu)
    {
		$bln = date("m"); 
		$thn = date("Y");
		$formatFilter = '1';
		
        $response = $this->get('RencanaKontrol/ListRencanaKontrol/Bulan/'.$bln.'/Tahun/'.$thn.'/Nokartu/'.$noKartu.'/filter/'.$formatFilter);
        return json_decode($response, true);
    }

    public function dataSpesialistik($jnsKontrol, $nomor, $tglRencanaKontrol)
    {
        $response = $this->get('RencanaKontrol/ListSpesialistik/JnsKontrol/'.$jnsKontrol.'/nomor/'.$nomor.'/TglRencanaKontrol/'.$tglRencanaKontrol);
        return json_decode($response, true);
    }

    public function dataDokter($jnsKontrol, $kdPoli, $tglRencanaKontrol)
    {
        $response = $this->get('RencanaKontrol/JadwalPraktekDokter/JnsKontrol/'.$jnsKontrol.'/KdPoli/'.$kdPoli.'/TglRencanaKontrol/'.$tglRencanaKontrol);
        return json_decode($response, true);
    }
}
