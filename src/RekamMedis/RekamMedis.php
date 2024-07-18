<?php

namespace Pw\Bpjs\RekamMedis;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class RekamMedis extends BpjsIntegration
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Simpan Medical Record
   */
  public function insertMedicalRecord($data = [])
  {
    $data['dataMR'] = $this->encryptGzip($data['dataMR']);

    $response = $this->post("eclaim/rekammedis/insert", $data);
    return json_decode($response, true);
  }
}
