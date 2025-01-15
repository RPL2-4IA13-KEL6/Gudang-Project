<?php
// mencegah direct access file PHP agar file PHP tidak bisa diakses secara langsung dari browser dan hanya dapat dijalankan ketika di include oleh file lain
// jika file diakses secara langsung
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  // alihkan ke halaman error 404
  header('location: 404.html');
}
// jika file di include oleh file lain, tampilkan isi file
else {
  // mengecek data GET "id_vendor"
  if (isset($_GET['id'])) {
    // ambil data GET dari tombol ubah
    $id_vendor = $_GET['id'];

    // sql statement untuk menampilkan data dari tabel "tbl_vendor" berdasarkan "id_vendor"
    $query = mysqli_query($mysqli, "SELECT * FROM tbl_vendor WHERE id_vendor='$id_vendor'")
      or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
    // ambil data hasil query
    $data = mysqli_fetch_assoc($query);
  }
?>
  <div class="panel-header bg-secondary-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-clone mr-2"></i> Vendor</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=vendor" class="text-white"> Vendor</a></li>
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
        <div class="card-title">Ubah Data Vendor</div>
      </div>
      <!-- form ubah data -->
      <form action="modules/vendor/proses_ubah.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <input type="hidden" name="id_vendor" value="<?php echo $data['id_vendor']; ?>">

          <div class="form-group">
            <label>vendor <span class="text-danger">*</span></label>
            <input type="text" name="nama_vendor" class="form-control col-lg-5" autocomplete="off" value="<?php echo $data['nama_vendor']; ?>" required>
            <div class="invalid-feedback">Vendor tidak boleh kosong.</div>
          </div>
        </div>
        <div class="card-action">
          <!-- tombol simpan data -->
          <input type="submit" name="simpan" value="Simpan" class="btn btn-secondary btn-round pl-4 pr-4 mr-2">
          <!-- tombol kembali ke halaman data vendor -->
          <a href="?module=vendor" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>
<?php } ?>