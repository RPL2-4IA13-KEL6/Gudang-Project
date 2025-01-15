<?php
session_start(); // Mengaktifkan session

// Pengecekan session login user
// Jika user belum login
if (empty($_SESSION['username']) && empty($_SESSION['password'])) {
    // Alihkan ke halaman login dan tampilkan pesan peringatan login
    header('location: ../../login.php?pesan=2');
} else {
    // Panggil file "database.php" untuk koneksi ke database
    require_once "../../config/database.php";

    // Mengecek data GET "id_vendor"
    if (isset($_GET['id'])) {
        // Ambil data GET dari tombol hapus
        $id_vendor = mysqli_real_escape_string($mysqli, $_GET['id']);

        // SQL statement untuk mengecek apakah vendor sudah digunakan di tabel "tbl_barang"
        $query = mysqli_query($mysqli, "SELECT id_vendor FROM tbl_barang WHERE id_vendor='$id_vendor'")
            or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));

        // Ambil jumlah baris data hasil query
        $rows = mysqli_num_rows($query);

        // Cek hasil query
        // Jika data vendor sudah ada di tabel "tbl_barang"
        if ($rows <> 0) {
            // Alihkan ke halaman vendor dan tampilkan pesan gagal hapus data
            header('location: ../../main.php?module=vendor&pesan=5');
        } else {
            // SQL statement untuk delete data dari tabel "tbl_vendor" berdasarkan "id_vendor"
            $delete = mysqli_query($mysqli, "DELETE FROM tbl_vendor WHERE id_vendor='$id_vendor'")
                or die('Ada kesalahan pada query delete : ' . mysqli_error($mysqli));

            // Cek query
            // Jika proses delete berhasil
            if ($delete) {
                // Alihkan ke halaman vendor dan tampilkan pesan berhasil hapus data
                header('location: ../../main.php?module=vendor&pesan=3');
            }
        }
    }
}
?>
