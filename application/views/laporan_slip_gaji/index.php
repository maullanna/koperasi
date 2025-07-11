<?php if($this->session->userdata('role') != 'karyawan'): ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h3 text-primary"><i class="fa fa-file-text-o"></i> Rekap Laporan Slip Gaji</h1>
        <div>
            <a href="<?= base_url('laporan_slip_gaji/export_excel?' . http_build_query($_GET)) ?>" class="btn btn-sm btn-success shadow-sm me-2">
                <i class="fa fa-file-excel-o me-1"></i> Export Excel
            </a>
            <a href="<?= base_url('laporan_slip_gaji/export_pdf?' . http_build_query($_GET)) ?>" class="btn btn-sm btn-danger shadow-sm">
                <i class="fa fa-file-pdf-o me-1"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="card shadow rounded mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="fa fa-filter me-1 text-secondary"></i> Filter Laporan
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('laporan_slip_gaji') ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="tanggal_awal" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" 
                           value="<?= $tanggal_awal ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="tanggal_akhir" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" 
                           value="<?= $tanggal_akhir ?>" required>
                </div>
              
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
<?php endif;?>
                

    <div class="card shadow rounded">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fa fa-table me-1"></i> <?= $this->session->userdata('role') != 'karyawan' ? 'Data Slip Gaji' : 'Slip Gaji Saya' ?>
        </div>
        <div class="card-body">
            <?php if (!empty($slip_gaji)): ?>
                <div class="table-responsive">
                <table id="DataTables_Table_0" class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Pekerja</th>
                                <th>Periode</th>
                                <th>Total Pendapatan</th>
                                <th>Uang Makan</th>
                                <th>Total Kasbon</th>
                                <th>Total Tabungan</th>
                                <th>Pemasukan BPJS</th>
                                <th>Gaji Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($slip_gaji as $slip): ?>
                            <?php
                            // Hitung ulang gaji bersih tanpa potongan BPJS
                            $total_pendapatan = $slip->total_pendapatan + $slip->uang_makan + $slip->pemasukan_bpjs;
                            $total_potongan = $slip->total_kasbon + $slip->total_tabungan;
                            $gaji_bersih = $total_pendapatan - $total_potongan;
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $slip->nama_karyawan ?></td>
                                <td><?= date('d/m/Y', strtotime($slip->bulan)) ?></td>
                                <td class="text-end">Rp <?= number_format(isset($slip->total_pendapatan) ? $slip->total_pendapatan : 0, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format(isset($slip->uang_makan) ? $slip->uang_makan : 0, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format(isset($slip->total_kasbon) ? $slip->total_kasbon : 0, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format(isset($slip->total_tabungan) ? $slip->total_tabungan : 0, 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format(isset($slip->pemasukan_bpjs) ? $slip->pemasukan_bpjs : 0, 0, ',', '.') ?></td>
                                <td class="text-end text-success fw-bold">Rp <?= number_format($gaji_bersih, 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <!-- Tampilkan ikon dan pesan jika data kosong -->
                <div class="text-center p-5">
                    <i class="bi bi-folder-x" style="font-size: 5rem; color: #adb5bd;"></i>
                    <h5 class="mt-4 text-muted">Data pendapatan untuk periode ini tidak ditemukan.</h5>
                    <p class="text-secondary">Silakan coba pilih filter lain atau cek kembali inputan Anda.</p>
                </div>
            <?php endif; ?>
                </div>  
                </div>
</div>

<script>
$(document).ready(function() {
    var karyawanList = <?= json_encode($karyawan_list) ?>;
    
    $('#search_karyawan').on('input', function() {
        var searchText = $(this).val().toLowerCase();
        var results = karyawanList.filter(function(karyawan) {
            return karyawan.nama_karyawan.toLowerCase().includes(searchText);
        });
        
        var listHtml = '';
        if (searchText && results.length > 0) {
            results.forEach(function(karyawan) {
                listHtml += '<div class="p-2 border-bottom karyawan-item" style="cursor:pointer;" ' +
                           'data-id="' + karyawan.id + '" ' +
                           'data-nama="' + karyawan.nama_karyawan + '">' +
                           karyawan.nama_karyawan +
                           '</div>';
            });
            $('#karyawan_list').html(listHtml).show();
        } else {
            $('#karyawan_list').hide();
        }
    });

    $(document).on('click', '.karyawan-item', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        $('#id_karyawan').val(id);
        $('#search_karyawan').val(nama);
        $('#karyawan_list').hide();
    });

    // Sembunyikan daftar saat klik di luar
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.col-md-4').length) {
            $('#karyawan_list').hide();
        }
    });

    // Set nilai awal jika ada
    if ($('#id_karyawan').val()) {
        var selectedKaryawan = karyawanList.find(function(k) {
            return k.id == $('#id_karyawan').val();
        });
        if (selectedKaryawan) {
            $('#search_karyawan').val(selectedKaryawan.nama_karyawan);
        }
    }
});
</script>


                