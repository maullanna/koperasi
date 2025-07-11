<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Kasbon</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('kasbon') ?>" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Informasi Kasbon</h5>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($kasbon->tanggal)) ?></p>
                <p><strong>Karyawan:</strong> <?= $kasbon->nama_karyawan ?></p>
                <p><strong>Jenis:</strong> 
                    <span class="badge <?= $kasbon->jenis == 'belum lunas' ? 'bg-danger' : 'bg-success' ?>">
                        <?= $kasbon->jenis == 'belum lunas' ? 'Belum Lunas' : 'Lunas' ?>
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Jumlah:</strong> Rp <?= number_format($kasbon->jumlah, 0, ',', '.') ?></p>
                <p><strong>Keterangan:</strong> <?= $kasbon->keterangan ?: '-' ?></p>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?= base_url('kasbon/edit/'.$kasbon->id) ?>" class="btn btn-warning">
        <i class="fa fa-edit"></i> Edit Data
    </a>
</div>