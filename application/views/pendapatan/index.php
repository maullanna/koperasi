<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/pendapatan.css')?>">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>



<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Pendapatan</h1>
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
                    <a class="dropdown-item" href="<?= base_url('pendapatan/export') ?>">
                        <i class="fa fa-download me-2"></i> Export Data
                    </a>
                </li>
            </ul>
        </div>
        <button type="button" class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#contohModal">
            <i class="fa fa-question-circle"></i> Contoh Format Excel
        </button>
        <a href="<?= base_url('pendapatan/tambah') ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Tambah Pendapatan
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('pendapatan/import') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Pendapatan</h5>
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
                        <li>Urutan kolom harus sesuai dengan contoh</li>
                        <li>Format tanggal: YYYY-MM-DD</li>
                        <li>Nama Karyawan harus ada di database</li>
                        <li>Total Pendapatan harus berupa angka</li>
                        <li>Selesai</li>
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
        <h5 class="alert-heading"><i class="fa fa-check-circle"></i> Berhasil!</h5>
        <hr>
        <p class="mb-0"><?= $this->session->flashdata('success') ?></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(function() {
            $('.alert-success').fadeOut('fast');
        }, 3000); // 3 detik untuk pesan sukses
    </script>
<?php endif; ?>

<?php if($this->session->flashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Perhatian!</h5>
        <hr>
        <p class="mb-0"><?= $this->session->flashdata('warning') ?></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(function() {
            $('.alert-warning').fadeOut('slow');
        }, 12000); // 12 detik untuk pesan warning
    </script>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fa fa-times-circle"></i> Gagal!</h5>
        <hr>
        <div class="error-details">
            <?= $this->session->flashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        setTimeout(function() {
            $('.alert-danger').fadeOut('slow');
        }, 30000); // 15 detik untuk pesan error
    </script>
<?php endif; ?>


<div class="table-responsive">
    <table class="table table-striped table-hover" id="dataTable">
        <thead class="table-secondary">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Pekerja</th>
                <th>Total Pendapatan</th>
                <th>Status</th>
                <?php if(has_admin_access()): ?>
                    <th class="text-dark text-center" style="width: 200px;">Aksi</th>                  <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($pendapatan)): ?>
                <?php $no = 1; foreach($pendapatan as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($p->tanggal)) ?></td>
                        <td><?= $p->nama_karyawan ?></td>
                        <td>Rp <?= number_format($p->total_pendapatan, 0, ',', '.') ?></td>
                        <td>
                            <span class="badge bg-<?= $p->status == 'selesai' ? 'success' : 'warning' ?>">
                                <?= ucfirst($p->status) ?>
                            </span>
                        </td>
                        <?php if(has_admin_access()): ?>
                            <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= base_url('pendapatan/detail/'.$p->id) ?>" class="btn">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="<?= base_url('pendapatan/edit/'.$p->id) ?>" class="btn">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="<?= base_url('pendapatan/hapus/'.$p->id) ?>" class="btn btn-delete">
                                    <i class="fa fa-trash"></i>
                                </a>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
              
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
        <input type="text" class="form-control" id="searchMobile" placeholder="Cari data pendapatan...">
    </div>
</div>
<!--Tampilan card untuk mobile -->
<div class="card-view d-md-none">
    <?php if(!empty($pendapatan)): ?>
        <?php foreach($pendapatan as $p): ?>
            <div class="tabungan-card">
                <div class="card-row">
                    <div class="card-label">Tanggal</div>
                    <div class="card-value"><?= date('d/m/Y', strtotime($p->tanggal)) ?></div>
                </div>
                <div class="card-row">
                    <div class="card-label">Karyawan</div>
                    <div class="card-value"><?= $p->nama_karyawan ?></div>
                </div>
                <div class="card-row">
                    <div class="card-label">Total Pendapatan</div>
                    <div class="card-value">Rp <?= number_format($p->total_pendapatan, 0, ',', '.') ?></div>
                </div>
                <div class="card-row">
                    <div class="card-label">Status</div>
                    <div class="card-value">
                        <span class="badge bg-<?= $p->status == 'selesai' ? 'success' : 'warning' ?>">
                            <?= ucfirst($p->status) ?>
                        </span>
                    </div>
                </div>
                <?php if(has_admin_access()): ?>
                <div class="actions">
                    <a href="<?= base_url('pendapatan/detail/'.$p->id) ?>" class="btn">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="<?= base_url('pendapatan/edit/'.$p->id) ?>" class="btn">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="<?= base_url('pendapatan/hapus/'.$p->id) ?>" class="btn btn-delete">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Tidak ada data pendapatan</div>
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
            text: "Data pendapatan akan dihapus permanen!",
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


