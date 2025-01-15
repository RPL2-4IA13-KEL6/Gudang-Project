<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk update
else {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // mengecek data hasil submit dari form
  if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $id_lokasi   = mysqli_real_escape_string($mysqli, $_POST['id_lokasi']);
    $nama_lokasi = mysqli_real_escape_string($mysqli, trim($_POST['nama_lokasi']));

    // mengecek "nama_lokasi" untuk mencegah data duplikat
    // sql statement untuk menampilkan data "nama_lokasi" dari tabel "tbl_lokasi" berdasarkan input "nama_lokasi"
    $query = mysqli_query($mysqli, "SELECT nama_lokasi FROM tbl_lokasi WHERE nama_lokasi='$nama_lokasi' AND id_lokasi!='$id_lokasi'")
      or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil jumlah baris data hasil query
    $rows = mysqli_num_rows($query);

    // cek hasil query
    // jika "nama_lokasi" sudah ada di tabel "tbl_lokasi"
    if ($rows <> 0) {
      // alihkan ke halaman lokasi dan tampilkan pesan gagal ubah data
      header("location: ../../main.php?module=lokasi&pesan=4&lokasi=$nama_lokasi");
    }
    // jika "nama_lokasi" belum ada di tabel "tbl_lokasi"
    else {
      // sql statement untuk update data di tabel "tbl_lokasi" berdasarkan "id_lokasi"
      $update = mysqli_query($mysqli, "UPDATE tbl_lokasi
                                       SET nama_lokasi='$nama_lokasi'
                                       WHERE id_lokasi='$id_lokasi'")
        or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
      // cek query
      // jika proses update berhasil
      if ($update) {
        // alihkan ke halaman lokasi dan tampilkan pesan berhasil ubah data
        header('location: ../../main.php?module=lokasi&pesan=2');
      }
    }
  }
}
