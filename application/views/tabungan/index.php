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
    <h1 class="h2">Data Tabungan</h1>
    <?php if(has_admin_access()): ?>
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
                    <a class="dropdown-item" href="<?= base_url('tabungan/export') ?>">
                        <i class="fa fa-download me-2"></i> Export Data
                    </a>
                </li>
            </ul>
        </div>
        <button type="button" class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#contohModal">
            <i class="fa fa-question-circle"></i> Contoh Format Excel
        </button>
        <a href="<?= base_url('tabungan/tambah') ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Tambah Tabungan
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('tabungan/import') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Tabungan</h5>
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
                <h5 class="modal-title">Contoh Format Excel Tabungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h5><i class="fa fa-info-circle"></i> Petunjuk Import:</h5>
                    <ol>
                        <li>Format file harus .xlsx atau .xls</li>
                        <li>Data dimulai dari baris ke-2 (baris pertama untuk header)</li>
                        <li>Format tanggal: DD/MM/YYYY (contoh: 23/04/2025)</li>
                        <li>Jenis hanya boleh: Setoran atau Penarikan</li>
                        <li>Jumlah harus angka tanpa format (tanpa Rp dan pemisah ribuan)</li>
                    </ol>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>23/04/2025</td>
                                <td>Budi Santoso</td>
                                <td>Setoran</td>
                                <td>500000</td>
                                <td>Tabungan Bulanan</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>24/04/2025</td>
                                <td>Ani Wati</td>
                                <td>Penarikan</td>
                                <td>200000</td>
                                <td>Kebutuhan Mendadak</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-warning mt-3">
                    <h6><i class="fa fa-exclamation-triangle"></i> Contoh Kesalahan:</h6>
                    <ul>
                        <li>❌ Tanggal: 2025-04-23 (format salah)</li>
                        <li>❌ Jenis: Simpan (tidak valid)</li>
                        <li>❌ Jumlah: Rp 500.000 (format salah)</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Tampilan tabel untuk desktop -->
<div class="table-responsive d-none d-md-block">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <?php if(has_admin_access()): ?>
                    <th class="text-dark text-center" style="width: 200px;">Aksi</th>                  <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tabungan as $key => $row): ?>
            <tr>
                <td><?= $key + 1 ?></td>
                <td><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                <td><?= $row->nama_karyawan ?></td>
                <!-- For desktop view -->
                <td>
                    <span class="badge <?= trim($row->jenis) === 'setor' ? 'bg-success' : 'bg-danger' ?>">
                        <?= ucfirst($row->jenis) ?>
                    </span>
                </td>
                <td class="text-end"><?= format_rupiah($row->jumlah) ?></td>
                <td><?= $row->keterangan ?: '-' ?></td>
                <?php if(has_admin_access()): ?>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="<?= base_url('tabungan/lihat/'.$row->id) ?>" class="btn" data-bs-toggle="tooltip" title="Lihat Detail">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="<?= base_url('tabungan/edit/'.$row->id) ?>" class="btn" data-bs-toggle="tooltip" title="Edit Data">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="<?= base_url('tabungan/hapus/'.$row->id) ?>" class="btn" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" data-bs-toggle="tooltip" title="Hapus Data">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($tabungan)): ?>
           
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Search bar untuk mobile -->
<div class="search-container d-md-none">
    <div class="input-group">
        <span class="input-group-text">
            <i class="fa fa-search"></i>
        </span>
        <input type="text" class="form-control" id="searchMobile" placeholder="Cari data absensi...">
    </div>
</div>

<!-- Tampilan card untuk mobile -->
<div class="card-view d-md-none">
    <?php if(!empty($tabungan)): ?>
        <?php foreach($tabungan as $t): ?>
            <div class="tabungan-card">
                <div class="card-row">
                    <div class="card-label">Tanggal</div>
                    <div class="card-value"><?= date('d/m/Y', strtotime($t->tanggal)) ?></div>
                </div>
                <div class="card-row">
                    <div class="card-label">Karyawan</div>
                    <div class="card-value"><?= $t->nama_karyawan ?></div>
                </div>
                <div class="card-row">
                    <div class="card-label">Jenis</div>
                    <div class="card-value">
                        <span class="badge <?= $t->jenis === 'setor' ? 'bg-success' : 'bg-danger' ?>">
                            <?= ucfirst($t->jenis) ?>
                        </span>
                    </div>
                </div>
                <div class="card-row">
                    <div class="card-label">Jumlah</div>
                    <div class="card-value"><?= format_rupiah($t->jumlah) ?></div>
                </div>
                <div class="card-row">
                    <div class="card-label">Keterangan</div>
                    <div class="card-value"><?= $t->keterangan ?: '-' ?></div>
                </div>
                <?php if(has_admin_access()): ?>
                <div class="actions">
                    <a href="<?= base_url('tabungan/edit/'.$t->id) ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="<?= base_url('tabungan/hapus/'.$t->id) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Tidak ada data tabungan</div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi dropdown
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function(element) {
        return new bootstrap.Dropdown(element);
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
            text: "Data tabungan akan dihapus permanen!",
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


    // Validasi untuk tombol hapus
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data tabungan yang dihapus tidak bisa dikembalikan!",
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

    // Validasi untuk form
    $('#formTabungan').on('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah data tabungan sudah benar?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

</script>
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
</script>

