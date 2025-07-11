
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 text-primary"><i class="bi bi-file-text me-2"></i>Detail Pendapatan</h1>
</div>

<!-- Card Informasi Utama -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body bg-white rounded-3">
        <div class="row mb-3">
            <div class="col-12 col-md-6">
                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3 border-start border-primary border-3">
                    <i class="bi bi-calendar-event me-3 text-primary fs-4"></i>
                    <div>
                        <small class="text-muted d-block">Tanggal</small>
                        <strong class="fs-6"><?= date('d/m/Y', strtotime($pendapatan->tanggal)) ?></strong>
                    </div>
                </div>
                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-success border-3">
                    <i class="bi bi-person me-3 text-success fs-4"></i>
                    <div>
                        <small class="text-muted d-block">Karyawan</small>
                        <strong class="fs-6"><?= $pendapatan->nama_karyawan ?></strong>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3 border-start border-warning border-3">
                    <i class="bi bi-wallet2 me-3 text-warning fs-4"></i>
                    <div>
                        <small class="text-muted d-block">Total Pendapatan</small>
                        <strong class="text-success fs-5">Rp <?= number_format($pendapatan->total_pendapatan, 0, ',', '.') ?></strong>
                    </div>
                </div>
                <div class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-info border-3">
                    <i class="bi bi-check-circle me-3 text-info fs-4"></i>
                    <div>
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-<?= $pendapatan->status == 'selesai' ? 'success' : 'warning' ?> px-3 py-2">
                            <?= ucfirst($pendapatan->status) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card Detail Pekerjaan -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
            <i class="bi bi-list-check me-2"></i>Rincian Pekerjaan
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if(!empty($details)): ?>
            <?php foreach($details as $key => $detail): ?>
                <div class="p-3 border-bottom transition-all <?= $key % 2 == 0 ? 'bg-light bg-opacity-50' : 'bg-white' ?>">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <small class="text-primary d-block">Jenis Pekerjaan</small>
                            <strong><?= $detail->nama_pekerjaan ?></strong>
                        </div>
                        <div class="col-6 col-md-2">
                            <small class="text-info d-block">Banyak</small>
                            <strong><?= $detail->banyak ?></strong>
                        </div>
                        <div class="col-6 col-md-2">
                            <small class="text-success d-block">Harga Koperasi</small>
                            <strong>Rp <?= number_format($detail->harga_koperasi, 0, ',', '.') ?></strong>
                        </div>
                        <div class="col-6 col-md-2">
                            <small class="text-warning d-block">Harga Karyawan</small>
                            <strong>Rp <?= number_format($detail->harga_karyawan, 0, ',', '.') ?></strong>
                        </div>
                        <div class="col-6 col-md-2 text-end">
                            <small class="text-danger d-block">Total</small>
                            <strong class="text-success">Rp <?= number_format($detail->total, 0, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="p-5 text-center">
                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                <p class="text-muted mb-0">Tidak ada data detail pekerjaan</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <a href="<?= base_url('pendapatan') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<style>
.transition-all {
    transition: all 0.3s ease;
}
.transition-all:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
}
</style>

<!-- Tambahkan link Bootstrap Icons jika belum ada -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
