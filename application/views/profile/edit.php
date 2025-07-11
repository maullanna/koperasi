<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3 text-primary"><i class="fa fa-edit"></i> Edit Profil</h1>
        <a href="<?= base_url('profile') ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <?php if(validation_errors()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= validation_errors(); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-user-edit"></i> Form Edit Profil</h5>
        </div>
        <div class="card-body">
            <?= form_open('profile/edit'); ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= set_value('username', $user->username) ?>" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>