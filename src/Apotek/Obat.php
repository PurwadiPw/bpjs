<?php

namespace Pw\Bpjs\Apotek;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Obat extends BpjsIntegration
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Simpan Obat Non Racikan
   */
  public function insertNonRacikan($data = [])
  {
    $response = $this->post("obatnonracikan/v3/insert", $data);
    return json_decode($response, true);
  }

  /**
   * Simpan Obat Racikan
   */
  public function insertRacikan($data = [])
  {
    $response = $this->post("obatracikan/v3/insert", $data);
    return json_decode($response, true);
  }
}
