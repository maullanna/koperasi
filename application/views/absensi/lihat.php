<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Absensi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('absensi') ?>" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Informasi Absensi</h5>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($absensi->tanggal)) ?></p>
                <p><strong>Karyawan:</strong> <?= $absensi->nama_karyawan ?></p>
                <p><strong>Status:</strong> <?= ucfirst($absensi->status) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Keterangan:</strong> <?= $absensi->keterangan ?: '-' ?></p>
                <p><strong>Total Kehadiran:</strong> <?= $absensi->total_kehadiran ?> hari</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?= base_url('absensi/edit/'.$absensi->id) ?>" class="btn btn-warning">
        <i class="fa fa-edit"></i> Edit Data
    </a>
</div>