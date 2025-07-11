<style>
.karyawan-item:hover {
    background-color: #f8f9fa;
}
.karyawan-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.karyawan-item:hover,
.karyawan-item.active {
    background-color: #f8f9fa;
}

#karyawan_list {
    z-index: 1050;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>
<!-- Pastikan Bootstrap dan Bootstrap Icons sudah di-load -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h3 text-primary"><i class="fa fa-line-chart"></i> Rekap Laporan Pendapatan</h1>
        <!-- Ganti bagian ini -->
        <a href="<?= base_url('laporan_pendapatan/export_excel') ?>" class="btn btn-sm btn-success shadow-sm" id="exportBtn">
            <i class="fa fa-file-excel-o me-1"></i> Export Excel
        </a>
        
        <!-- Tambahkan script ini di bagian JavaScript -->
        <script>
        $(document).ready(function() {
            $('#exportBtn').on('click', function(e) {
                e.preventDefault();
                var tanggal_awal = $('#tanggal_awal').val();
                var tanggal_akhir = $('#tanggal_akhir').val();
                var id_karyawan = $('#id_karyawan').val();
                var id_pekerjaan = $('#id_pekerjaan').val();
        
                var url = $(this).attr('href');
                url += '?tanggal_awal=' + tanggal_awal;
                url += '&tanggal_akhir=' + tanggal_akhir;
                if(id_karyawan) url += '&id_karyawan=' + id_karyawan;
                if(id_pekerjaan) url += '&id_pekerjaan=' + id_pekerjaan;
        
                window.location.href = url;
            });
        });
        </script>
    </div>

    <!-- Tambahkan search container di sini -->
    <div class="search-container d-md-none mb-3">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fa fa-search"></i>
            </span>
            <input type="text" class="form-control" id="searchMobile" placeholder="Cari data pendapatan...">
        </div>
    </div>

    <div class="card shadow rounded mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="fa fa-filter me-1 text-secondary"></i> Filter Laporan
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url('laporan_pendapatan') ?>" class="row g-3" id="filterForm">
                <div class="col-md-2">
                    <label for="tanggal_awal" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" 
                           value="<?= set_value('tanggal_awal', isset($tanggal_awal) ? $tanggal_awal : date('Y-m-01')) ?>" required>
                </div>
                <div class="col-md-2">
                    <label for="tanggal_akhir" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" 
                           value="<?= set_value('tanggal_akhir', isset($tanggal_akhir) ? $tanggal_akhir : date('Y-m-t')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="search_karyawan" class="form-label">Cari Karyawan</label>
                    <div class="position-relative">
                        <input type="text" class="form-control" id="search_karyawan" 
                               placeholder="Cari Nama Pekerja"
                               value="<?= isset($nama_karyawan) ? $nama_karyawan : '' ?>">
                        <input type="hidden" name="id_karyawan" id="id_karyawan" 
                               value="<?= isset($id_karyawan) ? $id_karyawan : '' ?>">
                        <div id="karyawan_list" class="position-absolute bg-white border rounded shadow-sm" 
                              style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto; width: 100%;"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="id_pekerjaan" class="form-label">Jenis Pekerjaan</label>
                    <select name="id_pekerjaan" id="id_pekerjaan" class="form-select">
                        <option value="">Semua Pekerjaan</option>
                        <?php foreach($jenis_pekerjaan as $pekerjaan): ?>
                            <option value="<?= $pekerjaan->id ?>" <?= isset($id_pekerjaan) && $id_pekerjaan == $pekerjaan->id ? 'selected' : '' ?>>
                                <?= $pekerjaan->nama_pekerjaan ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm" id="btnTampilkan">
                        <i class="fa fa-search me-1"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnReset">
                        <i class="fa fa-refresh me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow rounded">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fa fa-table me-1"></i> Data Pendapatan
        </div>
        <div class="card-body">
            <?php if (!empty($pendapatan)): ?>
                <div class="table-responsive">
                    <table id="DataTables_Table_0" class="table table-striped table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Pekerjaan</th>
                                <th>Banyak</th>
                                <th>Harga Koperasi</th>
                                <th>Total Koperasi</th>
                                <th>Harga Karyawan</th>
                                <th>Total Karyawan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            $total_koperasi = 0;
                            $total_karyawan = 0;
                            foreach ($pendapatan as $row): 
                                $total_koperasi += $row->total_koperasi;
                                $total_karyawan += $row->total_karyawan;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                               
                                    <td><?= $row->nama_karyawan ?></td>
                                    <td><?= $row->nama_pekerjaan ?></td>
                                    <td class="text-center"><?= $row->banyak ?></td>
                                    <td class="text-end">Rp <?= number_format($row->harga_koperasi, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($row->total_koperasi, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($row->harga_karyawan_master, 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($row->total_karyawan, 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= strtolower($row->status) == 'lunas' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($row->status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('laporan_pendapatan/view/' . $row->id) ?>" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="6" class="text-end">Total Keseluruhan:</td>
                                <td class="text-end">Rp <?= number_format($total_koperasi, 0, ',', '.') ?></td>
                                <td class="text-end">-</td>
                                <td class="text-end">Rp <?= number_format($total_karyawan, 0, ',', '.') ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
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

<!-- Tambahkan script untuk autocomplete -->
<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(document).ready(function() {
    $('#search_karyawan').on('keyup', function() {
        var searchTerm = $(this).val();
        if(searchTerm.length > 0) {
            $.ajax({
                url: '<?= base_url("laporan_pendapatan/search_karyawan") ?>',
                type: 'GET',
                data: {term: searchTerm},
                success: function(data) {
                    var results = JSON.parse(data);
                    var html = '';
                    results.forEach(function(karyawan) {
                        html += '<div class="p-2 karyawan-item" data-id="' + karyawan.id + '" data-nama="' + karyawan.nama_karyawan + '">';
                        html += karyawan.nama_karyawan + ' (' + karyawan.nip + ')';
                        html += '</div>';
                    });
                    $('#karyawan_list').html(html).show();
                }
            });
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
        $('#filterForm').submit(); // Submit form ketika karyawan dipilih
    });

    // Tambahkan tombol Tampilkan
    // Hapus bagian script ini karena menimpa tombol yang sudah ada:
    /*
    $('.col-md-2.d-flex.align-items-end').html('\
        <button type="submit" class="btn btn-primary w-100" id="btnTampilkan">\
            <i class="fa fa-search me-1"></i> Tampilkan\
        </button>\
    ');
    */

    // Validasi tanggal sebelum submit
    $('#filterForm').on('submit', function(e) {
        var tanggal_awal = new Date($('#tanggal_awal').val());
        var tanggal_akhir = new Date($('#tanggal_akhir').val());
        
        if (tanggal_akhir < tanggal_awal) {
            e.preventDefault();
            alert('Tanggal akhir tidak boleh lebih kecil dari tanggal awal');
            return false;
        }
    });
});
// Tambahkan di bagian script
$('#btnReset').on('click', function() {
    // Reset semua input
    $('#id_karyawan').val('');
    $('#search_karyawan').val('');
    $('#id_pekerjaan').val('');
    $('#tanggal_awal').val('<?= date('Y-m-01') ?>');
    $('#tanggal_akhir').val('<?= date('Y-m-t') ?>');
    
    // Submit form
    $('#filterForm').submit();
});
</script>



