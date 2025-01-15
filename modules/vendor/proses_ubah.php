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
    $id_vendor   = mysqli_real_escape_string($mysqli, $_POST['id_vendor']);
    $nama_vendor = mysqli_real_escape_string($mysqli, trim($_POST['nama_vendor']));

    // mengecek "nama_vendor" untuk mencegah data duplikat
    // sql statement untuk menampilkan data "nama_vendor" dari tabel "tbl_vendor" berdasarkan input "nama_vendor"
    $query = mysqli_query($mysqli, "SELECT nama_vendor FROM tbl_vendor WHERE nama_vendor='$nama_vendor' AND id_vendor!='$id_vendor'")
      or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil jumlah baris data hasil query
    $rows = mysqli_num_rows($query);

    // cek hasil query
    // jika "nama_vendor" sudah ada di tabel "tbl_vendor"
    if ($rows <> 0) {
      // alihkan ke halaman vendor dan tampilkan pesan gagal ubah data
      header("location: ../../main.php?module=vendor&pesan=4&vendor=$nama_vendor");
    }
    // jika "nama_vendor" belum ada di tabel "tbl_vendor"
    else {
      // sql statement untuk update data di tabel "tbl_vendor" berdasarkan "id_vendor"
      $update = mysqli_query($mysqli, "UPDATE tbl_vendor
                                       SET nama_vendor='$nama_vendor'
                                       WHERE id_vendor='$id_vendor'")
        or die('Ada kesalahan pada query update : ' . mysqli_error($mysqli));
      // cek query
      // jika proses update berhasil
      if ($update) {
        // alihkan ke halaman vendor dan tampilkan pesan berhasil ubah data
        header('location: ../../main.php?module=vendor&pesan=2');
      }
    }
  }
}
