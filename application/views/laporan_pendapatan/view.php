<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 text-primary"><i class="bi bi-file-earmark-text me-2"></i>Detail Pendapatan</h1>
        <div>
            <a href="<?= base_url('pendapatan/edit/' . $pendapatan->id) ?>" class="btn btn-warning shadow-sm me-2">
                <i class="fa fa-edit me-1"></i> Edit Data
            </a>
            <a href="<?= base_url('laporan_pendapatan/export_detail/' . $pendapatan->id) ?>" class="btn btn-danger shadow-sm">
                <i class="fa fa-file-pdf-o me-1"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Card Informasi Utama -->
    <div class="card shadow-sm mb-4 border-primary border-opacity-25">
        <div class="card-header bg-primary bg-opacity-10">
            <h5 class="card-title mb-0 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Utama</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <i class="bi bi-calendar-event me-2 text-primary fs-4"></i>
                        <div>
                            <small class="text-muted d-block">Tanggal</small>
                            <strong class="text-dark"><?= date('d/m/Y', strtotime($pendapatan->tanggal)) ?></strong>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <i class="bi bi-person-badge me-2 text-info fs-4"></i>
                        <div>
                            <small class="text-muted d-block">NIP</small>
                            <strong class="text-dark"><?= $pendapatan->nip ?></strong>
                        </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="bi bi-person me-2 text-success fs-4"></i>
                        <div>
                            <small class="text-muted d-block">Nama Karyawan</small>
                            <strong class="text-dark"><?= $pendapatan->nama_karyawan ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <i class="bi bi-check-circle me-2 text-warning fs-4"></i>
                        <div>
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-<?= strtolower($pendapatan->status) == 'lunas' ? 'success' : 'warning' ?> px-3 py-2">
                                <?= ucfirst($pendapatan->status) ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="bi bi-wallet2 me-2 text-danger fs-4"></i>
                        <div>
                            <small class="text-muted d-block">Total Pendapatan</small>
                            <strong class="text-success fs-5">Rp <?= number_format($pendapatan->total_karyawan, 0, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Detail Pekerjaan -->
    <div class="card shadow-sm border-primary border-opacity-25">
        <div class="card-header bg-primary bg-opacity-10">
            <h5 class="card-title mb-0 text-primary"><i class="bi bi-list-check me-2"></i>Rincian Pekerjaan</h5>
        </div>
        <div class="card-body p-0">
            <?php if(!empty($pendapatan_detail)): ?>
                <?php foreach($pendapatan_detail as $key => $detail): ?>
                    <div class="p-3 border-bottom hover-bg-light <?= $key % 2 == 0 ? 'bg-light bg-opacity-50' : '' ?>">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-3 mb-2 mb-md-0">
                                <small class="text-primary d-block">Jenis Pekerjaan</small>
                                <strong class="text-dark"><?= $detail->nama_pekerjaan ?></strong>
                            </div>
                            <div class="col-6 col-md-2">
                                <small class="text-info d-block">Banyak</small>
                                <strong class="text-dark"><?= $detail->banyak ?></strong>
                            </div>
                            <div class="col-6 col-md-2">
                                <small class="text-success d-block">Harga Koperasi</small>
                                <strong class="text-dark">Rp <?= number_format($detail->harga_koperasi, 0, ',', '.') ?></strong>
                            </div>
                            <div class="col-6 col-md-2">
                                <small class="text-warning d-block">Total Koperasi</small>
                                <strong class="text-dark">Rp <?= number_format($detail->total_koperasi, 0, ',', '.') ?></strong>
                            </div>
                            <div class="col-6 col-md-2">
                                <small class="text-danger d-block">Harga Karyawan</small>
                                <strong class="text-dark">Rp <?= number_format($detail->harga_karyawan, 0, ',', '.') ?></strong>
                            </div>
                            <div class="col-6 col-md-1">
                                <small class="text-primary d-block">Total</small>
                                <strong class="text-success">Rp <?= number_format($detail->total_karyawan, 0, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 text-primary mb-3 d-block"></i>
                    <p class="mb-0">Tidak ada data detail pekerjaan</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= base_url('laporan_pendapatan') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<style>
.hover-bg-light:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
    transition: all 0.3s ease;
}
</style>

<!-- Tambahkan link Bootstrap Icons jika belum ada -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
