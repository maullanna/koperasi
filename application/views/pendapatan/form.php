<style>
    .cursor-pointer {
    cursor: pointer;
}
.hover-bg-light:hover {
    background-color: #f8f9fa;
}
</style>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Pendapatan</h1>
</div>

<?php if(validation_errors()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= validation_errors() ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <form action="<?= base_url('pendapatan/tambah') ?>" method="post" id="form-pendapatan">
            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="tanggal" name="tanggal" value="<?= set_value('tanggal', date('Y-m-d')) ?>" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label for="id_karyawan" class="form-label">Pekerja</label>
                                <input type="text" class="form-control form-control-sm" id="search_karyawan" placeholder="Cari nama karyawan...">
                                <input type="hidden" name="id_karyawan" id="id_karyawan" required>
                                <div id="karyawan_list" class="position-absolute bg-white border rounded shadow-sm" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto; width: 95%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detail-pekerjaan">
                            <thead class="table-dark">
                                <tr>
                                    <th>Pekerjaan</th>
                                    <th>Banyak</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="row-0">
                                    <td>
                                        <select class="form-select pekerjaan" name="id_pekerjaan[]" required>
                                            <option value="">Pilih Pekerjaan</option>
                                            <?php foreach($pekerjaan as $p): ?>
                                                <option value="<?= $p->id ?>" 
                                                    data-harga-karyawan="<?= $p->harga_karyawan ?>"
                                                    data-harga-koperasi="<?= $p->harga_koperasi ?>">
                                                    <?= $p->nama_pekerjaan ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" class="harga-koperasi" name="harga_koperasi[]" value="">
                                        <input type="hidden" class="harga-karyawan" name="harga_karyawan[]" value="">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control banyak" name="banyak[]" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control total" name="total[]" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-remove" onclick="removeRow(this)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end">Total Pendapatan</td>
                                    <td>
                                        <input type="number" class="form-control" id="total_pendapatan" name="total_pendapatan" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success" onclick="addRow()">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('pendapatan') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function addRow() {
    var rowCount = $('#detail-pekerjaan tbody tr').length;
    var newRow = $('#row-0').clone();
    newRow.attr('id', 'row-' + rowCount);
    newRow.find('input').val('');
    newRow.find('select').val('');
    $('#detail-pekerjaan tbody').append(newRow);
}

function removeRow(btn) {
    if($('#detail-pekerjaan tbody tr').length > 1) {
        $(btn).closest('tr').remove();
        calculateTotal();
    }
}

$(document).on('change', '.pekerjaan', function() {
    var row = $(this).closest('tr');
    var selected = $(this).find('option:selected');
    var hargaKaryawan = parseFloat(selected.data('harga-karyawan')) || 0;
    var hargaKoperasi = parseFloat(selected.data('harga-koperasi')) || 0;
    row.find('.harga-koperasi').val(hargaKoperasi);
    row.find('.harga-karyawan').val(hargaKaryawan);
    calculateRowTotal(row, hargaKaryawan);
});

$(document).on('change', '.banyak', function() {
    var row = $(this).closest('tr');
    var hargaKaryawan = parseFloat(row.find('.pekerjaan option:selected').data('harga-karyawan')) || 0;
    calculateRowTotal(row, hargaKaryawan);
});

function calculateRowTotal(row, harga) {
    var banyak = parseFloat(row.find('.banyak').val()) || 0;
    var total = banyak * harga;
    row.find('.total').val(total.toFixed(2));
    calculateTotal();
}

function calculateTotal() {
    var total = 0;
    $('.total').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#total_pendapatan').val(total.toFixed(2));
}
</script>

<!-- Tambahkan script berikut di bagian bawah file -->
<script>
$(document).ready(function() {
    var karyawanData = <?= json_encode($karyawan) ?>;
    
    $('#search_karyawan').on('input', function() {
        var searchText = $(this).val().toLowerCase();
        var matches = karyawanData.filter(function(k) {
            return k.nama_karyawan.toLowerCase().includes(searchText) || 
                   k.nip.toLowerCase().includes(searchText);
        });
        
        var listHtml = '';
        if (searchText.length > 0 && matches.length > 0) {
            matches.forEach(function(k) {
                listHtml += '<div class="p-2 karyawan-item cursor-pointer hover-bg-light" data-id="' + k.id + '" data-nama="' + k.nama_karyawan + '">' +
                           '<span class="text-primary">' + k.nip + '</span> | ' + k.nama_karyawan + '</div>';
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
    
    $(document).click(function(e) {
        if (!$(e.target).closest('.mb-2').length) {
            $('#karyawan_list').hide();
        }
    });
});
</script>

