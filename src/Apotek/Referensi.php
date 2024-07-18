<?php

namespace Pw\Bpjs\Apotek;

use Pw\Bpjs\BpjsIntegration;
use GuzzleHttp\Exception\ClientException;

class Referensi extends BpjsIntegration
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Daftar Obat DPHO
   */
  public function dpho()
  {
    $response = $this->get("referensi/dpho");
    return json_decode($response, true);
  }

  /**
   * Daftar Poli
   *
   * @param string $keyword kode atau nama poli
   */
  public function poli($keyword)
  {
    $response = $this->get("referensi/poli/{$keyword}");
    return json_decode($response, true);
  }

  /**
   * Pencarian data fasilitas kesehatan
   *
   * @param int $jnsFaskes Jenis Faskes (1. Faskes = 1, 2. Faskes = 2/RS)
   * @param string $namaFaskes Nama Faskes
   */
  public function faskes($jnsFaskes, $namaFaskes)
  {
    $response = $this->get("referensi/ppk/{$jnsFaskes}/{$namaFaskes}");
    return json_decode($response, true);
  }

  /**
   * Pencarian Setting Apotek
   *
   * @param string $kdApotek Kode Apotek
   */
  public function setting($kdApotek)
  {
    $response = $this->get("referensi/settingppk/read/{$kdApotek}");
    return json_decode($response, true);
  }

  /**
   * Pencarian data spesialistik
   */
  public function spesialistik()
  {
    $response = $this->get("referensi/spesialistik");
    return json_decode($response, true);
  }

  /**
   * Pencarian obat
   *
   * @param string $kdJnsObat
   * @param string $tglResep
   * @param string $filter
   */
  public function obat($kdJnsObat, $tglResep, $filter)
  {
    $response = $this->get("referensi/obat/{$kdJnsObat}/{$tglResep}/{$filter}");
    return json_decode($response, true);
  }
}
