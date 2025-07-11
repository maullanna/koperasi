<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pengaturan</h5>
                </div>
                <div class="card-body">
                    <form id="pengaturanForm"> <!-- Form for updating settings -->
                        <div class="mb-3">
                            <label for="nama_koperasi" class="form-label">Nama Koperasi</label>
                            <input type="text" class="form-control" id="nama_koperasi" name="nama_koperasi" 
                                   placeholder="Masukkan nama koperasi"
                                   value="<?= set_value('nama_koperasi', isset($pengaturan->nama_koperasi) ? $pengaturan->nama_koperasi : '') ?>" required>
                        </div>

                        <div class="mb-3">
                      <label for="pemasukan_bpjs" class="form-label">Pemasukan BPJS (Rp)</label>
                      <input type="text" class="form-control" id="pemasukan_bpjs" name="pemasukan_bpjs" 
                             placeholder="Masukkan angka saja (contoh: 3 untuk 3%)"
                             inputmode="numeric" pattern="[0-9]*" value="<?= format_rupiah($pengaturan->pemasukan_bpjs ?? 0) ?>" required>
                  </div>

                        <div class="mb-3">
                            <label for="uang_makan" class="form-label">Uang Makan per Hari (Rp)</label>
                            <input type="text" class="form-control" id="uang_makan" name="uang_makan" 
                                   placeholder="Masukkan angka saja (contoh: 10000)"
                                   inputmode="numeric" pattern="[0-9]*" value="<?= format_rupiah($pengaturan->uang_makan) ?>" required>
                            </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation and submission with SweetAlert
    document.getElementById('pengaturanForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const namaKoperasi = document.getElementById('nama_koperasi').value;
        const pemasukanBpjs = document.getElementById('pemasukan_bpjs').value.replace(/[^0-9]/g, ''); // hanya angka
        const uangMakan = document.getElementById('uang_makan').value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        if (!namaKoperasi || !pemasukanBpjs || !uangMakan) {
    Swal.fire({
        title: 'Error!',
        text: 'Semua field harus diisi!',
        icon: 'error',
        confirmButtonText: 'OK'
    });
    return;
}


        
        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Kirim data dengan AJAX
        const formData = new FormData(this);
        formData.set('uang_makan', uangMakan);
        formData.set('pemasukan_bpjs', pemasukanBpjs);
        
        fetch('<?= base_url('pengaturan/update') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan data',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
</script>
