<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Pekerjaan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('pekerjaan') ?>" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title"><?= $pekerjaan->nama_pekerjaan ?></h5>
        <p class="card-text"><strong>Harga Koperasi:</strong> Rp <?= number_format($pekerjaan->harga_koperasi, 0, ',', '.') ?></p>
        <p class="card-text"><strong>Harga Karyawan:</strong> Rp <?= number_format($pekerjaan->harga_karyawan, 0, ',', '.') ?></p>
        <p class="card-text"><strong>Status:</strong> <?= ucfirst($pekerjaan->status) ?></p>
        <p class="card-text"><strong>Tanggal Buat:</strong> <?= date('d-m-Y H:i:s', strtotime($pekerjaan->tanggal_buat)) ?></p>
        <p class="card-text"><strong>Tanggal Update:</strong> <?= date('d-m-Y H:i:s', strtotime($pekerjaan->tanggal_update)) ?></p>
        <!-- Additional notes section -->
        <p class="card-text"><strong>Catatan:</strong> <?= !empty($pekerjaan->catatan) ? $pekerjaan->catatan : 'Tidak ada catatan.' ?></p>
    </div>
</div>