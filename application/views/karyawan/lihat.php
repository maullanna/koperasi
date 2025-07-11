<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Karyawan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('karyawan') ?>" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Informasi Karyawan</h5>
        <div class="row">
            <div class="col-md-6">
                <p><strong>NIP:</strong> <?= $karyawan->nip ?></p>
                <p><strong>Nama:</strong> <?= $karyawan->nama_karyawan ?></p>
                <p><strong>Username:</strong> <?= $karyawan->username ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>No HP:</strong> <?= $karyawan->no_hp ?: '-' ?></p>
                <p><strong>Alamat:</strong> <?= $karyawan->alamat ?></p>
                <p><strong>Role:</strong> <?= ucfirst($karyawan->role) ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-success">
                        Aktif
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?= base_url('karyawan/edit/'.$karyawan->id) ?>" class="btn btn-warning">
        <i class="fa fa-edit"></i> Edit Data
    </a>
</div>