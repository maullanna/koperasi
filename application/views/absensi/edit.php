<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Data Absensi</h1>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if(has_admin_access()): ?>
<form method="post" action="<?= base_url('absensi/update/'.$absensi->id) ?>">
    <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $absensi->tanggal ?>" required>
    </div>
    <div class="mb-3">
        <label for="id_karyawan" class="form-label">Karyawan</label>
        <select class="form-select" id="id_karyawan" name="id_karyawan" required>
            <?php foreach($karyawan_list as $karyawan): ?>
                <option value="<?= $karyawan->id ?>" <?= $karyawan->id == $absensi->id_karyawan ? 'selected' : '' ?>>
                    <?= $karyawan->nama_karyawan ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status" required>
            <option value="masuk" <?= $absensi->status == 'masuk' ? 'selected' : '' ?>>Masuk</option>
            <option value="izin" <?= $absensi->status == 'izin' ? 'selected' : '' ?>>Izin</option>
            <option value="sakit" <?= $absensi->status == 'sakit' ? 'selected' : '' ?>>Sakit</option>
            <option value="alpha" <?= $absensi->status == 'alpha' ? 'selected' : '' ?>>Alpha</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea class="form-control" id="keterangan" name="keterangan"><?= $absensi->keterangan ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('absensi') ?>" class="btn btn-secondary">Kembali</a>
</form>
<?php else: ?>
    <div class="alert alert-warning">
        Anda tidak memiliki akses untuk mengedit data ini.
        <a href="<?= base_url('absensi') ?>" class="btn btn-secondary mt-2">Kembali</a>
    </div>
<?php endif; ?>