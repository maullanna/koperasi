<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Data BPJS</h1>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" action="<?= base_url('bpjs/update/'.$bpjs->id) ?>">
    <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $bpjs->tanggal ?>" required>
    </div>
    <div class="mb-3">
        <label for="id_karyawan" class="form-label">Karyawan</label>
        <select class="form-select" id="id_karyawan" name="id_karyawan" required>
            <?php foreach($karyawan_list as $karyawan): ?>
                <option value="<?= $karyawan->id ?>" <?= $karyawan->id == $bpjs->id_karyawan ? 'selected' : '' ?>>
                    <?= $karyawan->nama_karyawan ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah</label>
        <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= $bpjs->jumlah ?>" required>
    </div>
    <div class="mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea class="form-control" id="keterangan" name="keterangan"><?= $bpjs->keterangan ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('bpjs') ?>" class="btn btn-secondary">Kembali</a>
</form>