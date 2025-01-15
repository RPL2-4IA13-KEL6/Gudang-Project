<?php
session_start();      // mengaktifkan session

// panggil file "autoload.inc.php" untuk load dompdf, libraries, dan helper functions
require_once("../../assets/js/plugin/dompdf/autoload.inc.php");
// mereferensikan Dompdf namespace
use Dompdf\Dompdf;

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk cetak
else {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";
  // panggil file "fungsi_tanggal_indo.php" untuk membuat format tanggal indonesia
  require_once "../../helper/fungsi_tanggal_indo.php";

  // ambil data GET dari tombol cetak
  $stok = $_GET['stok'];

  // variabel untuk nomor urut tabel 
  $no = 1;

  // gunakan dompdf class
  $dompdf = new Dompdf();
  // setting options
  $options = $dompdf->getOptions();
  $options->setIsRemoteEnabled(true); // aktifkan akses file untuk bisa mengakses file gambar dan CSS
  $options->setChroot('C:\xampp\htdocs\gudang'); // tentukan path direktori aplikasi
  $dompdf->setOptions($options);

  // mengecek filter data stok
  // jika filter data stok "Seluruh" dipilih, tampilkan laporan stok seluruh barang
  if ($stok == 'Seluruh') {
    // halaman HTML yang akan diubah ke PDF
    $html = '<!DOCTYPE html>
            <html>
            <head>
              <title>Laporan Stok Seluruh Barang</title>
              <link rel="stylesheet" href="../../assets/css/laporan.css">
              <style>
                body { font-size: 60%; }
                .table {
                  font-size: 0.8em;
                  padding: 5px;
                }
              </style>
            </head>
            <body class="text-dark">
              <div class="text-center mb-4">
                <h1>LAPORAN STOK SELURUH BARANG</h1>
              </div>
              <hr>
              <div class="mt-4">
                <table class="table table-bordered" width="100%" cellspacing="0">
                  <thead class="bg-secondary text-white text-center">
                    <tr>
                      <th width="5%">No.</th>
                      <th width="10%">ID Barang</th>
                      <th width="20%">Nama Barang</th>
                      <th width="10%">Jenis Barang</th>
                      <th width="10%">Stok</th>
                      <th width="10%">Satuan</th>
                      <th width="15%">Vendor</th>
                      <th width="20%">Lokasi</th>
                    </tr>
                  </thead>
                  <tbody class="text-dark">';
    // sql statement untuk menampilkan data dari tabel "tbl_barang", tabel "tbl_jenis", dan tabel "tbl_satuan"
    $query = mysqli_query($mysqli, "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, a.id_vendor, a.id_lokasi, b.nama_jenis, c.nama_satuan, d.nama_vendor, e.nama_lokasi
                                            FROM tbl_barang as a INNER JOIN tbl_jenis as b INNER JOIN tbl_satuan as c INNER JOIN tbl_vendor as d INNER JOIN tbl_lokasi as e 
                                            ON a.jenis=b.id_jenis AND a.satuan=c.id_satuan AND a.id_vendor=d.id_vendor AND a.id_lokasi=e.id_lokasi 
                                            ORDER BY a.id_barang ASC")
                                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    while ($data = mysqli_fetch_assoc($query)) {
      // tampilkan data
      $html .= '    <tr>
                      <td class="text-center">' . $no++ . '</td>
                      <td class="text-center">' . $data['id_barang'] . '</td>
                      <td>' . $data['nama_barang'] . '</td>
                      <td>' . $data['nama_jenis'] . '</td>';
      // mengecek data "stok"
      // jika data stok minim, tampilkan data dengan warna background
      if ($data['stok'] <= $data['stok_minimum']) {
        $html .= '      <td class="text-right">
                          <span class="badge badge-warning">' . $data['stok'] . '</span>
                        </td>';
      }
      // jika data stok tidak minim, tampilkan data tanpa warna background
      else {
        $html .= '      <td class="text-right">' . $data['stok'] . '</td>';
      }
      $html .= '        <td>' . $data['nama_satuan'] . '</td>
                        <td>' . $data['nama_vendor'] . '</td>
                        <td>' . $data['nama_lokasi'] . '</td>
                    </tr>';
    }
    $html .= '      </tbody>
                </table>
              </div>
              <div class="text-right mt-5">............, ' . tanggal_indo(date('Y-m-d')) . '</div>
            </body>
            </html>';

    // load html
    $dompdf->loadHtml($html);
    // mengatur ukuran dan orientasi kertas
    $dompdf->setPaper('A4', 'landscape');
    // mengubah dari HTML menjadi PDF
    $dompdf->render();
    // menampilkan file PDF yang dihasilkan ke browser dan berikan nama file "Laporan Stok Seluruh Barang.pdf"
    $dompdf->stream('Laporan Stok Seluruh Barang.pdf', array('Attachment' => 0));
  }
  // jika filter data stok "Minimum" dipilih, tampilkan laporan stok barang yang mencapai batas minimum
  else {
    // halaman HTML yang akan diubah ke PDF
    $html = '<!DOCTYPE html>
            <html>
            <head>
              <title>Laporan Stok Barang Minimum</title>
              <link rel="stylesheet" href="../../assets/css/laporan.css">
              <style>
                body { font-size: 60%; }
                .table {
                  font-size: 0.8em;
                  padding: 5px;
                }
              </style>
            </head>
            <body class="text-dark">
              <div class="text-center mb-4">
                <h1>LAPORAN STOK BARANG YANG MENCAPAI BATAS MINIMUM</h1>
              </div>
              <hr>
              <div class="mt-4">
                <table class="table table-bordered" width="100%" cellspacing="0">
                  <thead class="bg-secondary text-white text-center">
                    <tr>
                      <th width="5%">No.</th>
                      <th width="10%">ID Barang</th>
                      <th width="20%">Nama Barang</th>
                      <th width="10%">Jenis Barang</th>
                      <th width="10%">Stok</th>
                      <th width="10%">Satuan</th>
                      <th width="15%">Vendor</th>
                      <th width="20%">Lokasi</th>
                    </tr>
                  </thead>
                  <tbody class="text-dark">';
    // sql statement untuk menampilkan data dari tabel "tbl_barang", tabel "tbl_jenis", dan tabel "tbl_satuan" berdasarkan "stok"
    $query = mysqli_query($mysqli, "SELECT a.id_barang, a.nama_barang, a.jenis, a.stok_minimum, a.stok, a.satuan, a.id_vendor, a.id_lokasi, b.nama_jenis, c.nama_satuan, d.nama_vendor, e.nama_lokasi
                                            FROM tbl_barang as a INNER JOIN tbl_jenis as b INNER JOIN tbl_satuan as c INNER JOIN tbl_vendor as d INNER JOIN tbl_lokasi as e 
                                            ON a.jenis=b.id_jenis AND a.satuan=c.id_satuan AND a.id_vendor=d.id_vendor AND a.id_lokasi=e.id_lokasi 
                                            WHERE a.stok<=a.stok_minimum ORDER BY a.id_barang ASC")
                                            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    while ($data = mysqli_fetch_assoc($query)) {
      // tampilkan data
      $html .= '    <tr>
                      <td class="text-center">' . $no++ . '</td>
                      <td class="text-center">' . $data['id_barang'] . '</td>
                      <td>' . $data['nama_barang'] . '</td>
                      <td>' . $data['nama_jenis'] . '</td>
                      <td class="text-right">' . $data['stok'] . '</td>
                      <td>' . $data['nama_satuan'] . '</td>
                      <td>' . $data['nama_vendor'] . '</td>
                      <td>' . $data['nama_lokasi'] . '</td>
                    </tr>';
    }
    $html .= '      </tbody>
                </table>
              </div>
              <div class="text-right mt-5">............, ' . tanggal_indo(date('Y-m-d')) . '</div>
            </body>
            </html>';

    // load html
    $dompdf->loadHtml($html);
    // mengatur ukuran dan orientasi kertas
    $dompdf->setPaper('A4', 'landscape');
    // mengubah dari HTML menjadi PDF
    $dompdf->render();
    // menampilkan file PDF yang dihasilkan ke browser dan berikan nama file "Laporan Stok Barang Minimum.pdf"
    $dompdf->stream('Laporan Stok Barang Minimum.pdf', array('Attachment' => 0));
  }
}
?>
