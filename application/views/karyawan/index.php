<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
    /* Card View for Mobile */
    @media (max-width: 767.98px) {
        .table-responsive {
            display: none;
        }
        
        .card-view {
            display: block;
        }
        
        .tabungan-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
        }
        
        .tabungan-card .card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .tabungan-card .card-label {
            font-weight: bold;
            color: #6c757d;
            flex: 1;
        }
        
        .tabungan-card .card-value {
            text-align: right;
            flex: 1;
        }
        
        .tabungan-card .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
        
        .tabungan-card .actions {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
    }

    @media (min-width: 768px) {
        .card-view {
            display: none;
        }
    }

    /* Misc Styles */
    .alert-floating {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1046;
        min-width: 300px;
    }
    .import-container {
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .import-success {
            color: #155724;
            background-color: #d4edda;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .import-duplicate {
            color: #856404;
            background-color: #fff3cd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .import-error {
            color: #721c24;
            background-color: #f8d7da;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .import-container h5 {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .import-container ul {
            margin-bottom: 10px;
            padding-left: 20px;
        }
        
        .import-container li {
            margin-bottom: 5px;
        }
        
        .import-container hr {
            margin: 15px 0;
            opacity: 0.2;
        }
        
        .import-container strong {
            color: #0056b3;
        }
</style>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Karyawan</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="dropdown me-2">
            <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="dropdownExcel" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownExcel">
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fa fa-upload me-2"></i> Import Data
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('karyawan/export') ?>">
                        <i class="fa fa-download me-2"></i> Export Data
                    </a>
                </li>
            </ul>
        </div>
        <button type="button" class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#contohModal">
            <i class="fa fa-question-circle"></i> Contoh Format Excel
        </button>
        <a href="<?= base_url('karyawan/tambah') ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Tambah Karyawan
        </a>
    </div>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('import_message')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('import_message') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(function() {
            $('.alert-info').fadeOut('slow');
        }, 30000); // 30 detik untuk pesan import
    </script>
   
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Pekerja</th>
                <th>Username</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Role</th>
                <th class="text-dark text-center" style="width: 200px;">Aksi</th>              </tr>
        </thead>
        <tbody>
            <?php foreach($karyawan as $key => $row): ?>
            <tr>
                <td><?= $key + 1 ?></td>
                <td><?= $row->nip ?></td>
                <td><?= $row->nama_karyawan ?></td>
                <td><?= $row->username ?></td>
                <td><?= $row->no_hp ?></td>
                <td><?= strlen($row->alamat) > 30 ? substr($row->alamat, 0, 30) . '...' : $row->alamat ?></td>
                <td><?= ucfirst($row->role) ?></td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="<?= base_url('karyawan/lihat/'.$row->id) ?>" 
                           class="btn" 
                           data-bs-toggle="tooltip" 
                           title="Lihat Detail">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="<?= base_url('karyawan/edit/'.$row->id) ?>" 
                           class="btn" 
                           data-bs-toggle="tooltip" 
                           title="Edit Data">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="#" 
                           onclick="return confirmDelete('<?= $row->id ?>')" 
                           class="btn" 
                           data-bs-toggle="tooltip" 
                           title="Hapus Data">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($karyawan)): ?>
            <tr>
                <td colspan="7" class="text-center">Tidak ada data</td>  <!-- Changed colspan to 7 -->
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('karyawan/import') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <strong>Catatan:</strong>
                            <ul class="mb-0">
                                <li>File yang diupload harus berformat .xls atau .xlsx</li>
                                <li>Data harus memiliki kolom: NIK, Nama Karyawan, Username, Password, No HP, Alamat, dan Role</li>
                                <li>Role yang tersedia: admin, karyawan</li>
                                <li>Gunakan template yang sesuai untuk menghindari kesalahan format</li>
                            </ul>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Contoh Format -->
<div class="modal fade" id="contohModal" tabindex="-1" aria-labelledby="contohModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contoh Format Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h5><i class="fa fa-info-circle"></i> Petunjuk Import:</h5>
                    <ol>
                        <li>Format file harus .xlsx atau .xls</li>
                        <li>Pastikan nama kolom pada baris pertama sesuai dengan contoh:
                            <ul>
                                <li>NIK</li>
                                <li>Nama Karyawan</li>
                                <li>Username</li>
                                <li>Password</li>
                                <li>No HP</li>
                                <li>Alamat</li>
                                <li>Role</li>
                            </ul>
                        </li>
                        <li>Urutan kolom harus sesuai dengan contoh</li>
                        <li>NIK harus unik dan berupa angka</li>
                        <li>Role hanya bisa diisi dengan:
                            <ul>
                                <li>admin</li>
                                <li>karyawan</li>
                                <li>Gunakan huruf kecil semua</li>
                            </ul>
                        </li>
                        <li>Password minimal 6 karakter</li>
                        <li>No HP diawali dengan format 08 atau +62</li>
                        <li>Pastikan tidak ada baris kosong di antara data</li>
                        <li>Pastikan tidak ada spasi berlebih pada setiap sel</li>
                    </ol>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>123456</td>
                                <td>John Doe</td>
                                <td>johndoe</td>
                                <td>pass123</td>
                                <td>081234567890</td>
                                <td>Jl. Contoh No. 123</td>
                                <td>karyawan</td>
                            </tr>
                            <tr>
                                <td>789012</td>
                                <td>Jane Smith</td>
                                <td>janesmith</td>
                                <td>pass456</td>
                                <td>089876543210</td>
                                <td>Jl. Sample No. 456</td>
                                <td>admin</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data karyawan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('karyawan/hapus/') ?>' + id;
        }
    });
    return false;
}

// Tampilkan SweetAlert untuk pesan sukses
<?php if($this->session->flashdata('success')): ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= $this->session->flashdata('success') ?>',
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
<?php endif; ?>
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inisialisasi dropdowns
    var dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(function(dropdown) {
        new bootstrap.Dropdown(dropdown);
    });
});
</script>
</body>
</html>
