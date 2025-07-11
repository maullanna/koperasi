<div class="container mt-5">
    <h2 class="mb-4">Profile</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?= $karyawan->nama_karyawan ?></h5>
            <p class="card-text"><strong>NIK:</strong> <?= $karyawan->nik ?></p>
            <p class="card-text"><strong>Phone Number:</strong> <?= $karyawan->no_hp ?></p>
            <p class="card-text"><strong>Address:</strong> <?= $karyawan->alamat ?></p>
            <p class="card-text"><strong>Role:</strong> <?= ucfirst($karyawan->role) ?></p>
            <!-- Add more user details as needed -->
            <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
</div>