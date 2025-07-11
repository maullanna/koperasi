
<!-- Pastikan Bootstrap dan Bootstrap Icons sudah di-load -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="h3 text-primary"><i class="fa fa-book"></i> Rekap Laporan Tabungan</h1>
        <a href="<?= base_url('laporan_tabungan/export_pdf?' . http_build_query($_GET)) ?>" class="btn btn-sm btn-danger shadow-sm">
            <i class="fa fa-file-pdf-o me-1"></i> Export PDF
        </a>
    </div>
    <div class="card shadow rounded mb-4">
        <div class="card-header bg-light fw-semibold">
            <i class="fa fa-filter me-1 text-secondary"></i> Filter Laporan
        </div>
<div class="card-body">
    <form method="post" action="<?= base_url('laporan_tabungan') ?>" class="row g-3">
        <!-- Date Range Filter -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="tanggal_mulai" class="form-label fw-semibold">
                    <i class="fa fa-calendar me-1"></i> Tanggal Mulai
                </label>
                <input type="date" 
                       id="tanggal_mulai"
                       name="tanggal_mulai" 
                       class="form-control" 
                       value="<?= isset($tanggal_mulai) ? $tanggal_mulai : date('Y-m-d', strtotime('-1 month')) ?>"
                       required>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="tanggal_selesai" class="form-label fw-semibold">
                    <i class="fa fa-calendar me-1"></i> Tanggal Selesai
                </label>
                <input type="date" 
                       id="tanggal_selesai"
                       name="tanggal_selesai" 
                       class="form-control" 
                       value="<?= isset($tanggal_selesai) ? $tanggal_selesai : date('Y-m-d') ?>"
                       required>
            </div>
        </div>

        <!-- Search Input -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="searchInput" class="form-label fw-semibold">
                    <i class="fa fa-search me-1"></i> Pencarian
                </label>
                <div class="input-group">
                    <input type="text" 
                           id="searchInput"
                           class="form-control" 
                           placeholder="Cari data tabungan..."
                           aria-label="Search savings data">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
    </div>

    <div class="card shadow rounded">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fa fa-table me-1"></i> Data Tabungan
        </div>
        <div class="card-body">
            <?php if (!empty($tabungan)): ?>
                <!-- Tambahkan search container di sini -->
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Karyawan</th>
                                <th>Jenis</th>
                                <th class="text-end">Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach($tabungan as $t): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($t->tanggal)) ?></td>
                                <td><?= $t->nama_karyawan ?></td>
                                <td>
                                    <span class="badge bg-<?= $t->jenis == 'setor' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($t->jenis) ?>
                                    </span>
                                </td>
                                <td class="text-end"><?= format_money($t->jumlah) ?></td>
                                <td><?= $t->keterangan ?: '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total Setor:</th>
                                <th class="text-end"><?= format_money($total_setor) ?></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Total Tarik:</th>
                                <th class="text-end"><?= format_money($total_tarik) ?></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Saldo:</th>
                                <th class="text-end"><?= format_money($total_setor - $total_tarik) ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center p-5">
                    <i class="bi bi-folder-x" style="font-size: 5rem; color: #adb5bd;"></i>
                    <h5 class="mt-4 text-muted">Data tabungan untuk periode ini tidak ditemukan.</h5>
                    <p class="text-secondary">Silakan coba pilih filter lain atau cek kembali inputan Anda.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tambahkan di bagian bawah file sebelum closing tag -->
<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function() {
    var karyawanList = <?= json_encode($karyawan_list) ?>;
    var isSearching = false;
    
    $('#search_karyawan').on('input focus', function() {
        isSearching = true;
        updateKaryawanList();
    });

    // Tambahkan fungsi pencarian untuk tabel
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    function updateKaryawanList() {
        var searchText = $('#search_karyawan').val().toLowerCase();
        var results = karyawanList.filter(function(karyawan) {
            return karyawan.nama_karyawan.toLowerCase().includes(searchText) || 
                   karyawan.nip.toLowerCase().includes(searchText);
        });
        
        var listHtml = '';
        if (searchText && results.length > 0) {
            results.forEach(function(karyawan) {
                listHtml += '<div class="p-2 border-bottom karyawan-item" style="cursor:pointer;" ' +
                           'data-id="' + karyawan.id + '" ' +
                           'data-nama="' + karyawan.nama_karyawan + '" ' +
                           'data-nip="' + karyawan.nip + '">' +
                           karyawan.nip + ' | ' + karyawan.nama_karyawan +
                           '</div>';
            });
            $('#karyawan_list').html(listHtml).show();
        } else {
            $('#karyawan_list').hide();
        }
    }

    $(document).on('click', '.karyawan-item', function() {
        var id = $(this).data('id');
        var nip = $(this).data('nip');
        var nama = $(this).data('nama');
        $('#id_karyawan').val(id);
        $('#search_karyawan').val(nip + ' | ' + nama);
        $('#karyawan_list').hide();
        isSearching = false;
    });

    // Hapus event hover pada card
    // $('.card').hover(...) dihapus

    // Tambahkan event untuk menutup list saat klik di luar area pencarian
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length && 
            !$(e.target).hasClass('karyawan-item')) {
            $('#karyawan_list').hide();
            isSearching = false;
        }
    });

    // Set nilai awal jika ada
    if ($('#id_karyawan').val()) {
        var selectedKaryawan = karyawanList.find(function(k) {
            return k.id == $('#id_karyawan').val();
        });
        if (selectedKaryawan) {
            $('#search_karyawan').val(selectedKaryawan.nip + ' | ' + selectedKaryawan.nama_karyawan);
        }
    }
});
</script>

<script>
$(document).ready(function() {
    // Simpan data tabel asli saat halaman dimuat
    let originalTableData = {
        desktop: $('.table tbody').html(),
        mobile: $('.card-view').html()
    };

    // Fungsi untuk filter tabel desktop
    function filterDesktopTable(searchTerm) {
        if (!searchTerm) {
            $('.table tbody').html(originalTableData.desktop);
            return;
        }

        $('.table tbody tr').each(function() {
            const nama = $(this).find('td:eq(2)').text().toLowerCase();
            if (nama.includes(searchTerm.toLowerCase())) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Fungsi untuk filter tampilan mobile
    function filterMobileView(searchTerm) {
        if (!searchTerm) {
            $('.card-view').html(originalTableData.mobile);
            return;
        }

        $('.tabungan-card').each(function() {
            const nama = $(this).find('.card-value').eq(1).text().toLowerCase();
            if (nama.includes(searchTerm.toLowerCase())) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Event handler untuk input pencarian
    $('#searchMobile').on('input', function() {
        const searchTerm = $(this).val().trim();
        filterDesktopTable(searchTerm);
        filterMobileView(searchTerm);
    });
});
</script>