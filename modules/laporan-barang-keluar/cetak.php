<?php
session_start();      // mengaktifkan session

// include autoloader untuk load dompdf, libraries, dan helper functions
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
  $tanggal_awal  = $_GET['tanggal_awal'];
  $tanggal_akhir = $_GET['tanggal_akhir'];

  // gunakan dompdf class
  $dompdf = new Dompdf();
  // setting options
  $options = $dompdf->getOptions();
  $options->setIsRemoteEnabled(true); // aktifkan akses file untuk bisa mengakses file gambar dan CSS
  $options->setChroot('C:\xampp\htdocs\gudang'); // tentukan path direktori aplikasi
  $dompdf->setOptions($options);

  // halaman HTML yang akan diubah ke PDF
  $html = '<!DOCTYPE html>
          <html>
          <head>
              <title>Laporan Data Barang Keluar</title>
            <link href="../../assets/css/laporan.css" rel="stylesheet">
            <style>
              .table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
              }
              th, td {
                border: 1px solid #000;
                padding: 8px;
              }
              th {
                background-color: #6861ce;
                color: #fff;
                text-align: center;
              }
              .text-center {
                text-align: center;
              }
              .text-right {
                text-align: right;
              }
            </style>
          </head>
          <body class="text-dark">
            <div class="text-center">
              <h2>LAPORAN DATA BARANG KELUAR</h2>
              <span>Tanggal ' . $tanggal_awal . ' s.d. ' . $tanggal_akhir . '</span>
            </div>
            <hr>
            <div class="mt-4">
              <table class="table" width="100%" cellspacing="0">
                <thead>
		              <tr>
                    <th>No.</th>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Jenis Barang</th>
                    <th>Jumlah Keluar</th>
                    <th>Satuan</th>
                    <th>Vendor</th> <!-- Kolom Vendor -->
                    <th>Lokasi</th> <!-- Kolom Lokasi -->
		              </tr>
		            </thead>
						    <tbody>';
  // ubah format tanggal menjadi Tahun-Bulan-Hari (Y-m-d)
  $tanggal_awal  = date('Y-m-d', strtotime($tanggal_awal));
  $tanggal_akhir = date('Y-m-d', strtotime($tanggal_akhir));
  // variabel untuk nomor urut tabel 
  $no = 1;
  // sql statement untuk menampilkan data dari tabel "tbl_barang_keluar", tabel "tbl_barang", dan tabel "tbl_satuan" berdasarkan "tanggal"
  $query = mysqli_query($mysqli, "SELECT a.id_transaksi, a.tanggal, a.barang, a.jumlah, b.nama_barang, c.nama_satuan, b.id_vendor, b.id_lokasi, d.nama_vendor, e.nama_lokasi, j.nama_jenis
                                  FROM tbl_barang_keluar as a INNER JOIN tbl_barang as b INNER JOIN tbl_satuan as c INNER JOIN tbl_vendor as d INNER JOIN tbl_lokasi as e INNER JOIN tbl_jenis as j
                                  ON a.barang=b.id_barang AND b.satuan=c.id_satuan AND b.id_vendor=d.id_vendor AND b.id_lokasi=e.id_lokasi AND b.jenis=j.id_jenis 
                                  WHERE a.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY a.id_transaksi ASC")
                                  or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
  // ambil data hasil query
  while ($data = mysqli_fetch_assoc($query)) {
    // tampilkan data
    $html .= '		<tr>
                    <td class="text-center">' . $no++ . '</td>
                    <td class="text-center">' . $data['id_transaksi'] . '</td>
                    <td class="text-center">' . date('d-m-Y', strtotime($data['tanggal'])) . '</td>
                    <td>' . $data['barang'] . ' - ' . $data['nama_barang'] . '</td>
                    <td>' . $data['nama_jenis'] . '</td>
                    <td class="text-right">' . number_format($data['jumlah'], 0, '', '.') . '</td>
                    <td>' . $data['nama_satuan'] . '</td>
                    <td>' . $data['nama_vendor'] . '</td> <!-- Kolom Vendor -->
                    <td>' . $data['nama_lokasi'] . '</td> <!-- Kolom Lokasi -->
                  </tr>';
  }
  $html .= '		</tbody>
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
  // menampilkan file PDF yang dihasilkan ke browser dan berikan nama file "Laporan Data Barang Keluar.pdf"
  $dompdf->stream('Laporan Data Barang Keluar.pdf', array('Attachment' => 0));
}
?>
