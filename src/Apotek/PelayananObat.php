<?php

namespace Pw\Bpjs\Apotek;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class PelayananObat extends BpjsIntegration
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Hapus Pelayanan Obat
   */
  public function hapusPelayananObat($data = [])
  {
    $response = $this->delete("pelayanan/obat/hapus/", $data);
    return json_decode($response, true);
  }

  /**
   * Daftar Pelayanan Obat
   *
   * @param string $noSep
   */
  public function daftarPelayananObat($noSep)
  {
    $response = $this->get("obat/daftar/{$noSep}");
    return json_decode($response, true);
  }

  /**
   * Riwayat Pelayanan Obat
   *
   * @param string $tglAwal YYYY-MM-DD
   * @param string $tglAkhir YYYY-MM-DD
   * @param string $noKartu
   */
  public function riwayatPelayananObat($tglAwal, $tglAkhir, $noKartu)
  {
    $response = $this->get("riwayatobat/{$tglAwal}/{$tglAkhir}/{$noKartu}");
    return json_decode($response, true);
  }
}
