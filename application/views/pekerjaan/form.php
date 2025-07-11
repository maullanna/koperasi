<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Pekerjaan</h1>
</div>

<?php if(validation_errors()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= validation_errors() ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <form action="<?= base_url('pekerjaan/tambah') ?>" method="post">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama_pekerjaan" class="form-label">Nama Pekerjaan</label>
                        <input type="text" class="form-control" id="nama_pekerjaan" name="nama_pekerjaan" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_koperasi" class="form-label">Harga Koperasi</label>
                        <input type="text" class="form-control" id="harga_koperasi" name="harga_koperasi" placeholder="Contoh: 50000" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_karyawan" class="form-label">Harga Karyawan</label>
                        <input type="text" class="form-control" id="harga_karyawan" name="harga_karyawan" placeholder="Contoh: 50000" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('pekerjaan') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatRupiah = (angka, prefix) => {
        const numberString = angka.replace(/[^,\d]/g, '').toString();
        const split = numberString.split(',');
        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    };

    const inputHargaKoperasi = document.getElementById('harga_koperasi');
    const inputHargaKaryawan = document.getElementById('harga_karyawan');

    inputHargaKoperasi.addEventListener('keyup', function(e) {
        inputHargaKoperasi.value = formatRupiah(this.value, 'Rp ');
    });

    inputHargaKaryawan.addEventListener('keyup', function(e) {
        inputHargaKaryawan.value = formatRupiah(this.value, 'Rp ');
    });
});
</script>