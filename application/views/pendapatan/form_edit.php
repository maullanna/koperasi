
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pendapatan</title>
    
    <!-- jQuery (must be loaded first) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Select2 JS (after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="post" action="<?= base_url('pendapatan/edit/' . $pendapatan->id) ?>">
            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= $pendapatan->tanggal ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Pekerja</label>
                <select name="id_karyawan" class="form-select" required>
                    <?php foreach ($karyawan as $k) : ?>
                        <option value="<?= $k->id ?>" <?= $k->id == $pendapatan->id_karyawan ? 'selected' : '' ?>>
                            <?= $k->nip ?> | <?= $k->nama_karyawan ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Detail Pekerjaan</label>
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
                            <?php foreach ($details as $detail) : ?>
                                <tr>
                                    <td>
                                        <select class="form-select pekerjaan" name="id_pekerjaan[]" required>
                                            <option value="">Pilih Pekerjaan</option>
                                            <?php foreach ($pekerjaan as $p) : ?>
                                                <option value="<?= $p->id ?>" 
                                                    data-harga-karyawan="<?= $p->harga_karyawan ?>"
                                                    data-harga-koperasi="<?= $p->harga_koperasi ?>"
                                                    <?= $p->id == $detail->id_pekerjaan ? 'selected' : '' ?>>
                                                    <?= $p->nama_pekerjaan ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" class="harga-koperasi" name="harga_koperasi[]" value="<?= $detail->harga_koperasi ?>">
                                        <input type="hidden" class="harga-karyawan" name="harga_karyawan[]" value="<?= $detail->harga_karyawan ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control banyak" name="banyak[]" value="<?= $detail->banyak ?>" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control total" name="total[]" value="<?= $detail->total ?>" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove" onclick="removeRow(this)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end">Total Pendapatan</td>
                                <td>
                                    <input type="number" class="form-control" id="total_pendapatan" name="total_pendapatan" value="<?= $pendapatan->total_pendapatan ?>" readonly>
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

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="<?= base_url('pendapatan') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<!-- LIBRARIES -->
<!-- jQuery -->


<!-- SCRIPT UTAMA -->
<script>
$(document).ready(function() {
    // Fungsi untuk menghitung total
    function hitungTotal(row) {
        var hargaKaryawan = parseFloat(row.find('.harga-karyawan').val()) || 0;
        var banyak = parseFloat(row.find('.banyak').val()) || 0;
        var total = hargaKaryawan * banyak;
        row.find('.total').val(total);
        
        hitungTotalPendapatan();
    }

    // Fungsi untuk menghitung total pendapatan
    function hitungTotalPendapatan() {
        var totalPendapatan = 0;
        $('.total').each(function() {
            totalPendapatan += parseFloat($(this).val()) || 0;
        });
        $('#total_pendapatan').val(totalPendapatan);
    }

    // Event handler untuk perubahan pekerjaan
    $(document).on('change', '.pekerjaan', function() {
        var row = $(this).closest('tr');
        var selectedOption = $(this).find('option:selected');
        
        row.find('.harga-karyawan').val(selectedOption.data('harga-karyawan'));
        row.find('.harga-koperasi').val(selectedOption.data('harga-koperasi'));
        
        hitungTotal(row);
    });

    // Event handler untuk perubahan banyak
    $(document).on('change', '.banyak', function() {
        hitungTotal($(this).closest('tr'));
    });

    // Fungsi untuk menambah baris
    window.addRow = function() {
        var newRow = `
            <tr>
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
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove" onclick="removeRow(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#detail-pekerjaan tbody').append(newRow);
    };

    // Fungsi untuk menghapus baris
    window.removeRow = function(button) {
        var totalRows = $('#detail-pekerjaan tbody tr').length;
        if (totalRows > 1) {
            $(button).closest('tr').remove();
            hitungTotalPendapatan();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Minimal harus ada satu pekerjaan!',
                confirmButtonColor: '#d33'
            });
        }
    };
});
</script>

</body>
</html>
<!-- Card Form -->
