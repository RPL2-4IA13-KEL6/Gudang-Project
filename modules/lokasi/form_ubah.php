<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
  // mengecek data GET "id_lokasi"
  if (isset($_GET['id'])) {
    // ambil data GET dari tombol ubah
    $id_lokasi = $_GET['id'];

    // sql statement untuk menampilkan data dari tabel "tbl_lokasi" berdasarkan "id_lokasi"
    $query = mysqli_query($mysqli, "SELECT * FROM tbl_lokasi WHERE id_lokasi='$id_lokasi'")
      or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    $data = mysqli_fetch_assoc($query);
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Lokasi</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=lokasi" class="text-white"> Lokasi</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Ubah</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <!-- judul form -->
        <div class="card-title">Ubah Data Lokasi</div>
      </div>
      <!-- form ubah data -->
      <form action="modules/lokasi/proses_ubah.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <input type="hidden" name="id_lokasi" value="<?php echo $data['id_lokasi']; ?>">

          <div class="form-group">
            <label>lokasi <span class="text-danger">*</span></label>
            <input type="text" name="nama_lokasi" class="form-control col-lg-5" autocomplete="off" value="<?php echo $data['nama_lokasi']; ?>" required>
            <div class="invalid-feedback">Lokasi tidak boleh kosong.</div>
          </div>
        </div>
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali ke halaman data lokasi -->
          <a href="?module=lokasi" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>
<?php } ?>