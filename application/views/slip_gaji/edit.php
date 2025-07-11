<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Slip Gaji</h2>
        <a href="<?= base_url('slipgaji') ?>" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    
    <form action="<?= base_url('slipgaji/edit/' . $slip_gaji->id) ?>" method="post">
        <div class="row">
            <!-- Earnings Section -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_karyawan">Nama Karyawan</label>
                    <select name="id_karyawan" id="id_karyawan" class="form-control" required>
                        <option value="">Pilih Karyawan</option>
                        <?php foreach ($karyawan as $k): ?>
                        <option value="<?= $k->id ?>" <?= $k->id == $slip_gaji->id_karyawan ? 'selected' : '' ?>><?= $k->nama_karyawan ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mt-3">
                    <label for="bulan">Tanggal</label>
                    <input type="date" name="bulan" id="bulan" class="form-control" value="<?= $slip_gaji->bulan ?>" required>
                </div>
                <div class="form-group mt-3">
                    <label for="total_pendapatan">Total Pendapatan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="total_pendapatan" id="total_pendapatan" class="form-control text-end" value="<?= number_format($slip_gaji->total_pendapatan, 0, ',', '.') ?>" required>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="uang_makan">Uang Makan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="uang_makan" id="uang_makan" class="form-control text-end" value="<?= number_format($slip_gaji->uang_makan, 0, ',', '.') ?>" required>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="pemasukan_bpjs">Pemasukan BPJS (3%)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="pemasukan_bpjs" id="pemasukan_bpjs" class="form-control text-end" value="<?= number_format($slip_gaji->pemasukan_bpjs, 0, ',', '.') ?>" readonly>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="gaji_bersih">Gaji Bersih</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="gaji_bersih" id="gaji_bersih" class="form-control text-end" value="<?= number_format($slip_gaji->gaji_bersih, 0, ',', '.') ?>" readonly>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="catatan">Catatan</label>
                    <textarea name="catatan" id="catatan" class="form-control"><?= $slip_gaji->catatan ?></textarea>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Update Slip Gaji</button>
            <a href="<?= base_url('slipgaji') ?>" class="btn btn-secondary ms-2">Batal</a>
        </div>
    </form>
</div>

<script>
// Fungsi untuk format angka ke format Rupiah
function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Fungsi untuk parse format Rupiah ke angka
function parseRupiah(rupiah) {
    return parseInt(rupiah.replace(/[^\d]/g, "")) || 0;
}

// Fungsi untuk menghitung BPJS dan Gaji Bersih
function hitungBPJSdanGajiBersih() {
    const totalPendapatan = parseRupiah(document.getElementById('total_pendapatan').value);
    const uangMakan = parseRupiah(document.getElementById('uang_makan').value);
    const totalKasbon = parseRupiah(document.getElementById('total_kasbon').value) || 0;
    const totalTabungan = parseRupiah(document.getElementById('total_tabungan').value) || 0;
    
    // Hitung BPJS (3% dari total pendapatan)
    const pemasukan_bpjs = Math.round(totalPendapatan * 0.03);
    
    // Hitung Gaji Bersih
    const gajiBersih = totalPendapatan + uangMakan + pemasukan_bpjs - totalKasbon - totalTabungan;
    
    // Update nilai di form
    document.getElementById('pemasukan_bpjs').value = formatRupiah(pemasukan_bpjs);
    document.getElementById('gaji_bersih').value = formatRupiah(gajiBersih);
}

// Event listener untuk input yang mempengaruhi perhitungan
document.getElementById('total_pendapatan').addEventListener('input', function(e) {
    let value = parseRupiah(this.value);
    this.value = formatRupiah(value);
    hitungBPJSdanGajiBersih();
});

document.getElementById('uang_makan').addEventListener('input', function(e) {
    let value = parseRupiah(this.value);
    this.value = formatRupiah(value);
    hitungBPJSdanGajiBersih();
});

document.getElementById('total_kasbon').addEventListener('input', function(e) {
    let value = parseRupiah(this.value);
    this.value = formatRupiah(value);
    hitungBPJSdanGajiBersih();
});

document.getElementById('total_tabungan').addEventListener('input', function(e) {
    let value = parseRupiah(this.value);
    this.value = formatRupiah(value);
    hitungBPJSdanGajiBersih();
});

// Inisialisasi format dan perhitungan saat halaman dimuat
window.addEventListener('load', function() {
    hitungBPJSdanGajiBersih();
});
</script>

