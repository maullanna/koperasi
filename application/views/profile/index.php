<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3 text-primary"><i class="fa fa-user-circle"></i> Profil Saya</h1>
    </div>
    
    <?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <h4 class="card-title"><?= $user->nama_karyawan ?></h4>
                    <p class="card-text text-muted"><?= ucfirst($user->role) ?></p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit Profil</a>
                        <a href="<?= base_url('profile/change_password') ?>" class="btn btn-secondary"><i class="fa fa-key"></i> Ubah Password</a>
                        <!-- Tombol Logout hanya untuk mobile -->
                        <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger d-md-none mt-2"><i class="fa fa-sign-out"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-info-circle"></i> Informasi Profil</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Username:</strong>
                <div><?= $user->username ?></div>
            </div>
            <div class="mb-3">
                <strong>NIP:</strong>
                <div><?= $user->nip ?></div>
            </div>
            <div class="mb-3">
                <strong>Nama Karyawan:</strong>
                <div><?= $user->nama_karyawan ?></div>
            </div>
            <div class="mb-3">
                <strong>Nomor HP:</strong>
                <div><?= !empty($user->no_telepon) ? $user->no_telepon : '-' ?></div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<style>
/* Mengubah warna hover tombol */
.btn-primary:hover {
    background-color: #0056b3 !important;
    border-color: #0056b3 !important;
}

.btn-secondary:hover {
    background-color: #0056b3 !important;
    border-color: #0056b3 !important;
}

.btn-danger:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
}
</style>
