<?php
// mencegah direct access file PHP
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header('location: 404.html');
}
else { ?>
  <!-- menampilkan pesan kesalahan -->
  <div id="pesan"></div>

  <div class="panel-header bg-danger-gradient">
    <div class="page-inner py-4">
      <div class="page-header text-white">
        <!-- judul halaman -->
        <h4 class="page-title text-white"><i class="fas fa-exclamation-triangle mr-2"></i> Barang Rusak</h4>
        <!-- breadcrumbs -->
        <ul class="breadcrumbs">
          <li class="nav-home"><a href="?module=dashboard"><i class="flaticon-home text-white"></i></a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a href="?module=barang_rusak" class="text-white">Barang Rusak</a></li>
          <li class="separator"><i class="flaticon-right-arrow"></i></li>
          <li class="nav-item"><a>Entri</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-inner mt--5">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Entri Data Barang Rusak</div>
      </div>
      <form action="modules/barang-rusak/proses_entri.php" method="post" class="needs-validation" novalidate>
        <div class="card-body">
          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <?php
                // membuat "id_transaksi"
                $query = mysqli_query($mysqli, "SELECT RIGHT(id_transaksi,7) as nomor FROM tbl_barang_rusak ORDER BY id_transaksi DESC LIMIT 1")
                                                or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                $rows = mysqli_num_rows($query);

                if ($rows <> 0) {
                  $data = mysqli_fetch_assoc($query);
                  $nomor_urut = $data['nomor'] + 1;
                } else {
                  $nomor_urut = 1;
                }

                $id_transaksi = "TR-" . str_pad($nomor_urut, 7, "0", STR_PAD_LEFT);
                ?>
                <label>ID Transaksi <span class="text-danger">*</span></label>
                <input type="text" name="id_transaksi" class="form-control" value="<?php echo $id_transaksi; ?>" readonly>
              </div>
            </div>

            <div class="col-md-5 ml-auto">
              <div class="form-group">
                <label>Tanggal <span class="text-danger">*</span></label>
                <input type="text" name="tanggal" class="form-control date-picker" autocomplete="off" value="<?php echo date("d-m-Y"); ?>" required>
                <div class="invalid-feedback">Tanggal tidak boleh kosong.</div>
              </div>
            </div>
          </div>

          <hr class="mt-3 mb-4">

          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <label>Barang <span class="text-danger">*</span></label>
                <select id="data_barang" name="barang" class="form-control chosen-select" autocomplete="off" required>
                  <option selected disabled value="">-- Pilih --</option>
                  <?php
                  $query_barang = mysqli_query($mysqli, "SELECT id_barang, nama_barang FROM tbl_barang ORDER BY id_barang ASC")
                                                         or die('Ada kesalahan pada query tampil data : ' . mysqli_error($mysqli));
                  while ($data_barang = mysqli_fetch_assoc($query_barang)) {
                    echo "<option value='$data_barang[id_barang]'>$data_barang[id_barang] - $data_barang[nama_barang]</option>";
                  }
                  ?>
                </select>
                <div class="invalid-feedback">Barang tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Stok <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="text" id="data_stok" name="stok" class="form-control" readonly>
                  <div id="data_satuan" class="input-group-append"></div>
                </div>
              </div>
            </div>

            <div class="col-md-5 ml-auto">
              <div class="form-group">
                <label>Jumlah Rusak <span class="text-danger">*</span></label>
                <input type="text" id="jumlah" name="jumlah" class="form-control" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" required>
                <div class="invalid-feedback">Jumlah rusak tidak boleh kosong.</div>
              </div>

              <div class="form-group">
                <label>Sisa Stok <span class="text-danger">*</span></label>
                <input type="text" id="sisa" name="sisa" class="form-control" readonly>
              </div>
            </div>
          </div>
        </div>
        <div class="card-action">
          <input type="submit" name="simpan" value="Simpan" class="btn btn-danger btn-round pl-4 pr-4 mr-2">
          <a href="?module=barang_rusak" class="btn btn-default btn-round pl-4 pr-4">Batal</a>
        </div>
      </form>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#data_barang').change(function() {
        var id_barang = $('#data_barang').val();

        $.ajax({
          type: "GET", 
          url: "modules/barang-rusak/get_barang.php", 
          data: {id_barang: id_barang}, 
          dataType: "JSON", 
          success: function(result) {
            $('#data_stok').val(result.stok);
            $('#data_satuan').html('<span class="input-group-text">' + result.nama_satuan + '</span>');
            $('#jumlah').focus();
          }
        });
      });

      $('#jumlah').keyup(function() {
        var stok = $('#data_stok').val();
        var jumlah = $('#jumlah').val();

        if (stok == "") {
          $('#pesan').html('<div class="alert alert-info">Silahkan isi data barang terlebih dahulu.</div>');
          $('#jumlah').val('');
          var sisa_stok = "";
        } else if (jumlah == "") {
          var sisa_stok = "";
        } else if (jumlah == 0) {
          $('#pesan').html('<div class="alert alert-warning">Jumlah rusak tidak boleh 0 (nol).</div>');
          $('#jumlah').val('');
          var sisa_stok = "";
        } else if (eval(jumlah) > eval(stok)) {
          $('#pesan').html('<div class="alert alert-warning">Stok tidak mencukupi, kurangi jumlah rusak.</div>');
          $('#jumlah').val('');
          var sisa_stok = "";
        } else {
          var sisa_stok = eval(stok) - eval(jumlah);
        }

        $('#sisa').val(sisa_stok);
      });
    });
  </script>
<?php } ?>
