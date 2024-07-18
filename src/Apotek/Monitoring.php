<?php

namespace Pw\Bpjs\Apotek;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Monitoring extends BpjsIntegration
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Data Klaim
   *
   * @param integer $bulan
   * @param integer $tahun
   * @param integer (0. Semua 1. Obat PRB 2. Obat Kronis Blm Stabil 3. Obat Kemoterapi)
   * @param integer (1. Belum diverifikasi 2. Sudah Verifikasi)
   */
  public function dataKlaim($bulan, $tahun, $jenisObat, $status)
  {
    $response = $this->get("monitoring/klaim/{$bulan}/{$tahun}/{$jenisObat}/{$status}");
    return json_decode($response, true);
  }
}
