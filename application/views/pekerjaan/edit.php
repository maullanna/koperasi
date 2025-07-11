<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Pekerjaan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('pekerjaan') ?>" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('pekerjaan/edit/'.$pekerjaan->id) ?>" method="post">
    <div class="mb-3">
        <label for="nama_pekerjaan" class="form-label">Nama Pekerjaan</label>
        <input type="text" class="form-control" id="nama_pekerjaan" name="nama_pekerjaan" value="<?= $pekerjaan->nama_pekerjaan ?>" required>
    </div>
    <div class="mb-3">
        <label for="harga_koperasi" class="form-label">Harga Koperasi</label>
        <input type="number" class="form-control" id="harga_koperasi" name="harga_koperasi" value="<?= $pekerjaan->harga_koperasi ?>" required>
    </div>
    <div class="mb-3">
        <label for="harga_karyawan" class="form-label">Harga Karyawan</label>
        <input type="number" class="form-control" id="harga_karyawan" name="harga_karyawan" value="<?= $pekerjaan->harga_karyawan ?>" required>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status" required>
            <option value="aktif" <?= $pekerjaan->status == 'aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="nonaktif" <?= $pekerjaan->status == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
</form>