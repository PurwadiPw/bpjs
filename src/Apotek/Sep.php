<?php

namespace Pw\Bpjs\Apotek;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Sep extends BpjsIntegration
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Data No Kunjungan/SEP
   *
   * @param string $noSep
   */
  public function sep($noSep)
  {
    $response = $this->get("sep/{$noSep}");
    return json_decode($response, true);
  }
}
