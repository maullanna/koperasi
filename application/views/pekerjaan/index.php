<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<style>
@media (max-width: 767.98px) {
    .search-container {
        max-width: 88%;
        margin: 1rem auto;
    }
    .search-container .input-group-text i {
        color: white;
    }
    .search-container .input-group-text {
        background-color: #0d6efd; /* atau warna gelap lain */
    }

    .search-container .input-group {
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .search-container .form-control {
        border: none;
        border-left: 1px solidrgb(209, 211, 214);
    }

    .search-container .input-group-text {
        background-color: #004366;
        border: none;
    }
}

    /* Card View for Mobile */
    @media (max-width: 767.98px) {
        .table-responsive {
            display: none;
            padding: 5px;
        }
        
        .card-view {
            display: block;
        }
        
        .tabungan-card {
    border: 1px solid #dee2e6;
    border-radius: 0.75rem;
    margin-top: 1rem;
    margin-bottom: 1rem;
    padding: 1.5rem;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;

    /* Tambahan agar tidak terlalu lebar */
    max-width: 90%;
    margin-left: auto;
    margin-right: auto;
}
        
        .tabungan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .tabungan-card .card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.85rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .tabungan-card .card-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .tabungan-card .card-label {
            font-weight: 600;
            color: #495057;
            flex: 1;
            font-size: 0.95rem;
        }
        
        .tabungan-card .card-value {
            text-align: right;
            flex: 1;
            color: #212529;
            font-size: 0.95rem;
        }
        
        .tabungan-card .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.85em;
            border-radius: 50rem;
            font-weight: 500;
        }
        
        .tabungan-card .actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.08);
        }
        
        .tabungan-card .actions .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .tabungan-card .actions .btn:hover {
            transform: translateY(-1px);
        }
        
        /* Status badges */
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        /* Custom scrollbar for card view */
        .card-view::-webkit-scrollbar {
            width: 6px;
        }
        
        .card-view::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .card-view::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .card-view::-webkit-scrollbar-thumb:hover {
            background: #555;
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
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
</style>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Pekerjaan</h1>
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
                    <a class="dropdown-item" href="<?= base_url('pekerjaan/export') ?>">
                        <i class="fa fa-download me-2"></i> Export Data
                    </a>
                </li>
            </ul>
        </div>
        <button type="button" class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#contohModal">
            <i class="fa fa-question-circle"></i> Contoh Format Excel
        </button>
        <a href="<?= base_url('pekerjaan/tambah') ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Tambah Pekerjaan
        </a>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('pekerjaan/import') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Pekerjaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Excel</label>
                        <input type="file" name="excel_file" class="form-control" required accept=".xlsx,.xls">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                                <li>No</li>
                                <li>Nama Pekerja</li>
                                <li>Harga Koperasi</li>
                                <li>Harga Karyawan</li>
                                <li>Status</li>
                            </ul>
                        </li>
                        <li>Urutan kolom harus sesuai dengan contoh</li>
                        <li>Harga bisa diisi dengan format:
                            <ul>
                                <li>Angka biasa: 50000</li>
                                <li>Dengan pemisah ribuan: 50.000</li>
                                <li>Dengan format mata uang: Rp 50.000</li>
                            </ul>
                        </li>
                        <li>Status hanya bisa diisi dengan:
                            <ul>
                                <li>aktif</li>
                                <li>tidak aktif</li>
                                <li>Jika dikosongkan akan otomatis diisi 'aktif'</li>
                            </ul>
                        </li>
                        <li>Pastikan tidak ada baris kosong di antara data</li>
                        <li>Pastikan tidak ada spasi berlebih pada setiap sel</li>
                    </ol>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <?php foreach ($contoh_excel[0] as $header): ?>
                                    <th><?= $header ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i < count($contoh_excel); $i++): ?>
                                <tr>
                                    <?php foreach ($contoh_excel[$i] as $cell): ?>
                                        <td><?= $cell ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endfor; ?>
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

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(function() {
            $('.alert-success').fadeOut('fast');
        }, 3000); // Timeout 3 detik untuk pesan sukses
    </script>
<?php endif; ?>

<?php if($this->session->flashdata('info')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('info') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(function() {
            $('.alert-info').fadeOut('fast');
        }, 3000); // Timeout 3 detik untuk pesan info
    </script>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(function() {
            $('.alert-danger').fadeOut('slow');
        }, 7000); // Timeout 7 detik untuk pesan error
    </script>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pekerjaan</th>
                <th>Harga Koperasi</th>
                <th>Harga Karyawan</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $nomor = 1;
            foreach($pekerjaan as $row): 
            ?>
            <tr>
                <td><?= $nomor++ ?></td>
                <td><?= $row->nama_pekerjaan ?></td>
                <td>Rp <?= number_format($row->harga_koperasi, 0, ',', '.') ?></td>
                <td>Rp <?= number_format($row->harga_karyawan, 0, ',', '.') ?></td>
                <td>
                    <span class="badge bg-success">
                        <?= ucfirst($row->status) ?>
                    </span>
                </td> 
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="<?= base_url('pekerjaan/lihat/'.$row->id) ?>" class="btn"><i class="fa fa-eye"></i></a>
                        <a href="<?= base_url('pekerjaan/edit/'.$row->id) ?>" class="btn"><i class="fa fa-edit"></i></a>
                        <a href="<?= base_url('pekerjaan/hapus/'.$row->id) ?>" class="btn btn-delete"><i class="fa fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pekerjaan)): ?>
            <tr>
                <td colspan="6" class="text-center">Tidak ada data</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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


    // Tampilkan SweetAlert untuk pesan sukses
    <?php if($this->session->flashdata('success')): ?>
        Swal.fire({
            title: 'Berhasil!',
            text: '<?= $this->session->flashdata('success') ?>',
            icon: 'success',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    <?php endif; ?>

    // Tampilkan SweetAlert untuk pesan error
    <?php if($this->session->flashdata('error')): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?= $this->session->flashdata('error') ?>',
            icon: 'error',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    <?php endif; ?>

    // Konfirmasi hapus data
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>

  