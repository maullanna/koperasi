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

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <h2 class="mb-4">Daftar Slip Gaji</h2>

    <!-- Add Generate Button -->
    <?php if($this->session->userdata('role') != 'karyawan'): ?>
        <a href="<?= base_url('slipgaji/generate') ?>" class="btn deep-blue mb-3">Generate Slip Gaji</a>
    <?php endif; ?>

    <!-- Tampilan tabel untuk desktop -->
    <div class="table-responsive d-none d-md-block">
        
        <table class="table table-bordered" id="slip-gaji-table">
            <thead>
                <tr>
                    <th class="bg-light text-dark">Nama Karyawan</th>
                    <th class="bg-light text-dark">Periode</th>
                    <th class="bg-light text-dark">Gaji Bersih</th>
                    <th class="text-dark text-center" style="width: 200px;">Aksi</th>      
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slip_gaji_list as $slip): ?>
                <tr>
                    <td><?= $slip->nama_karyawan ?></td>
                    <td><?= '01-' . date('m-Y', strtotime($slip->bulan)) ?> s/d <?= date('t-m-Y', strtotime($slip->bulan)) ?></td>
                    <td>Rp <?= number_format($slip->gaji_bersih, 2) ?></td>
                    <td class="text-center">
                        <!-- View, Edit, Delete, Cetak Buttons -->
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="<?= base_url('slipgaji/view/' . $slip->id) ?>" class="btn deep-blue"><i class="fa fa-eye"></i></a>
                            <?php if($this->session->userdata('role') != 'karyawan'): ?>
                                <a href="<?= base_url('slipgaji/edit/' . $slip->id) ?>" class="btn deep-blue"><i class="fa fa-edit"></i></a>
                                <a href="<?= base_url('slipgaji/delete/' . $slip->id) ?>" class="btn deep-blue" onclick="return confirm('Apakah Anda yakin ingin menghapus slip gaji ini?');"><i class="fa fa-trash"></i></a>
                            <?php endif; ?>
                            <a href="<?= base_url('slipgaji/print/' . $slip->id) ?>" class="btn deep-blue"><i class="fa fa-print"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tampilan card untuk mobile -->
    <div class="d-md-none">
        <?php if(!empty($slip_gaji)): ?>
            <?php foreach($slip_gaji as $s): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Periode: <?= '01-' . date('m-Y', strtotime($s->bulan)) ?> s/d <?= date('t-m-Y', strtotime($s->bulan)) ?></h6>
                        <h5 class="card-title"><?= $s->nama_karyawan ?></h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Pendapatan:</span>
                            <strong>Rp <?= number_format($s->total_pendapatan, 0, ',', '.') ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Uang Makan:</span>
                            <span>Rp <?= number_format($s->uang_makan, 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Kasbon:</span>
                            <span class="text-danger">- Rp <?= number_format($s->total_kasbon, 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Tabungan:</span>
                            <span>Rp <?= number_format($s->total_tabungan, 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                       <span>Pemasukan BPJS:</span>
                       <span class="text-success">+ Rp <?= number_format($s->pemasukan_bpjs, 0, ',', '.') ?></span>
                   </div>

                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Gaji Bersih:</span>
                            <span class="fw-bold text-success">Rp <?= number_format($s->gaji_bersih, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Tidak ada data slip gaji</div>
        <?php endif; ?>
    </div>
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
</script>


