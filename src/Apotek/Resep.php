<?php

namespace Pw\Bpjs\Apotek;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Resep extends BpjsIntegration
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Simpan Resep
   */
  public function simpanResep($data = [])
  {
    $response = $this->post("sjpresep/v3/insert", $data);
    return json_decode($response, true);
  }

  /**
   * Hapus Resep
   */
  public function hapusResep($data = [])
  {
    $response = $this->delete("hapusresep", $data);
    return json_decode($response, true);
  }

  /**
   * Daftar Resep
   */
  public function daftarResep($data = [])
  {
    $response = $this->post("daftarresep", $data);
    return json_decode($response, true);
  }
}
