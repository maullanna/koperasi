<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Data Karyawan</h1>
</div>

<form method="post" action="<?= base_url('karyawan/update/'.$karyawan->id) ?>" id="formEdit">
    <div class="mb-3">
        <label for="nip" class="form-label">NIp</label>
        <input type="text" class="form-control" id="nip" name="nip" value="<?= $karyawan->nip ?>" required>
    </div>
    <div class="mb-3">
        <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
        <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan" value="<?= $karyawan->nama_karyawan ?>" required>
    </div>
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?= $karyawan->username ?>" required>
    </div>
    <div class="mb-3">
        <label for="no_hp" class="form-label">No HP</label>
        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= $karyawan->no_hp ?>">
    </div>
    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <textarea class="form-control" id="alamat" name="alamat"><?= $karyawan->alamat ?></textarea>
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-select" id="role" name="role" required>
            <option value="karyawan" <?= $karyawan->role == 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
            <option value="admin" <?= $karyawan->role == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="owner" <?= $karyawan->role == 'owner' ? 'selected' : '' ?>>Owner</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('karyawan') ?>" class="btn btn-secondary">Kembali</a>
</form>

<!-- Script untuk SweetAlert -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tampilkan SweetAlert untuk error validasi
    <?php if(validation_errors()): ?>
        Swal.fire({
            title: 'Error Validasi!',
            html: '<?= str_replace("\n", "<br>", validation_errors()) ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>

    // Tampilkan SweetAlert untuk error umum
    <?php if(isset($error)): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?= $error ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>

    // Konfirmasi sebelum submit form
    const formEdit = document.getElementById('formEdit');
    formEdit.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menyimpan perubahan data karyawan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                formEdit.submit();
            }
        });
    });
});

// Tampilkan SweetAlert untuk pesan sukses
<?php if($this->session->flashdata('success')): ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= $this->session->flashdata('success') ?>',
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
<?php endif; ?>
</script>