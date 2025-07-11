<?php if($this->session->flashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $this->session->flashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="container mt-5">
  <h2 class="mb-4">Generate Slip Gaji</h2>
  <form action="<?= base_url('slipgaji/generate') ?>" method="post">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="id_karyawan">Nama Karyawan</label>
          <select name="id_karyawan" id="id_karyawan" class="form-control" required>
            <option value="">Pilih Karyawan</option>
            <?php foreach ($karyawan as $k): ?>
              <option value="<?= $k->id ?>"><?= $k->nama_karyawan ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mt-3">
          <label>Periode</label>
          <div class="row">
            <div class="col-md-6">
              <label for="tanggal_awal" class="form-label">Dari Tanggal</label>
              <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="tanggal_akhir" class="form-label">Sampai Tanggal</label>
              <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="form-group mt-3">
          <label for="uang_makan">Uang Makan (otomatis sesuai kehadiran)</label>
          <input type="number" name="uang_makan" id="uang_makan" class="form-control" readonly required>
        </div>

        <div class="form-group mt-3">
          <label for="pemasukan_bpjs">Pemasukan BPJS (<?= $pengaturan->pemasukan_bpjs ?>% dari pendapatan)</label>
          <input type="number" name="pemasukan_bpjs" id="pemasukan_bpjs" class="form-control" readonly>
        </div>

        <div class="form-group mt-3">
          <label for="total_pendapatan">Total Pendapatan</label>
          <input type="number" name="total_pendapatan" id="total_pendapatan" class="form-control" readonly>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group mt-3">
          <label for="total_kasbon">Total Kasbon</label>
          <input type="number" name="total_kasbon" id="total_kasbon" class="form-control" readonly>
        </div>

        <div class="form-group mt-3">
          <label for="total_tabungan">Total Tabungan</label>
          <input type="number" name="total_tabungan" id="total_tabungan" class="form-control" readonly>
        </div>

        <div class="form-group mt-3">
          <label for="gaji_bersih">Gaji Bersih</label>
          <input type="number" name="gaji_bersih" id="gaji_bersih" class="form-control" readonly>
        </div>

        <div class="form-group mt-3">
          <label for="catatan">Catatan</label>
          <textarea name="catatan" id="catatan" class="form-control"></textarea>
        </div>
      </div>
    </div>

    <button type="submit" class="btn deep-blue mt-4">Buat Slip Gaji</button>
  </form>
</div>

<script>
const baseUrl = '<?= base_url() ?>';

document.getElementById('id_karyawan').addEventListener('change', fetchFinancialData);
document.getElementById('tanggal_awal').addEventListener('change', fetchFinancialData);
document.getElementById('tanggal_akhir').addEventListener('change', fetchFinancialData);

function fetchFinancialData() {
  const id = document.getElementById('id_karyawan').value;
  const tglAwal = document.getElementById('tanggal_awal').value;
  const tglAkhir = document.getElementById('tanggal_akhir').value;

  if (!id || !tglAwal || !tglAkhir) return;

  $.ajax({
    url: baseUrl + 'slipgaji/get_financial_data',
    type: 'POST',
    dataType: 'json',
    data: {
      id_karyawan: id,
      tanggal_awal: tglAwal,
      tanggal_akhir: tglAkhir
    },
    success: function(response) {
      if (response.error) {
        alert('Error: ' + response.error);
        return;
      }

      document.getElementById('total_pendapatan').value = response.total_pendapatan || 0;
      document.getElementById('uang_makan').value = response.total_uang_makan || 0;
      document.getElementById('total_kasbon').value = response.total_kasbon || 0;
      document.getElementById('total_tabungan').value = response.total_tabungan || 0;
      document.getElementById('pemasukan_bpjs').value = response.pemasukan_bpjs || 0;

      calculateNetSalary();
    },
    error: function(xhr, status, error) {
      console.error('Error:', xhr.responseText);
      let errorMessage = 'Terjadi kesalahan';
      try {
        const response = JSON.parse(xhr.responseText);
        if (response.error) errorMessage = response.error;
      } catch (e) {
        errorMessage += ': ' + error;
      }
      alert(errorMessage);
    }
  });
}

function calculateNetSalary() {
  const pendapatan = parseFloat(document.getElementById('total_pendapatan').value) || 0;
  const makan = parseFloat(document.getElementById('uang_makan').value) || 0;
  const bpjs = parseFloat(document.getElementById('pemasukan_bpjs').value) || 0;
  const kasbon = parseFloat(document.getElementById('total_kasbon').value) || 0;
  const tabungan = parseFloat(document.getElementById('total_tabungan').value) || 0;

  const net = (pendapatan + makan + bpjs) - (kasbon + tabungan);
  document.getElementById('gaji_bersih').value = net.toFixed(2);
}
</script>
