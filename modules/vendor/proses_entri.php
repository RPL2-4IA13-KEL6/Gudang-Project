<?php
session_start();      // mengaktifkan session

// pengecekan session login user 
// jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
  // alihkan ke halaman login dan tampilkan pesan peringatan login
  header('location: ../../login.php?pesan=2');
}
// jika user sudah login, maka jalankan perintah untuk insert
else {
  // panggil file "database.php" untuk koneksi ke database
  require_once "../../config/database.php";

  // mengecek data hasil submit dari form
  if (isset($_POST['simpan'])) {
    // ambil data hasil submit dari form
    $nama_vendor = mysqli_real_escape_string($mysqli, trim($_POST['nama_vendor']));

    // mengecek "nama_vendor" untuk mencegah data duplikat
    // sql statement untuk menampilkan data "nama_vendor" dari tabel "tbl_vendor" berdasarkan input "nama_vendor"
    $query = mysqli_query($mysqli, "SELECT nama_vendor FROM tbl_vendor WHERE nama_vendor='$nama_vendor'")
      or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil jumlah baris data hasil query
    $rows = mysqli_num_rows($query);

    // cek hasil query
    // jika "nama_vendor" sudah ada di tabel "tbl_vendor"
    if ($rows <> 0) {
      // alihkan ke halaman vendor dan tampilkan pesan gagal simpan data
      header("location: ../../main.php?module=vendor&pesan=4&vendor=$nama_vendor");
    }
    // jika "nama_vendor" belum ada di tabel "tbl_vendor"
    else {
      // sql statement untuk insert data ke tabel "tbl_vendor"
      $insert = mysqli_query($mysqli, "INSERT INTO tbl_vendor(nama_vendor) 
                                       VALUES('$nama_vendor')")
        or die('Ada kesalahan pada query insert : ' . mysqli_error($mysqli));
      // cek query
      // jika proses insert berhasil
      if ($insert) {
        // alihkan ke halaman vendor dan tampilkan pesan berhasil simpan data
        header('location: ../../main.php?module=vendor&pesan=1');
      }
    }
  }
}
