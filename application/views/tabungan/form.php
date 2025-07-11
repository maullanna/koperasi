<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
</style>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Tabungan</h1>
</div>

<?php if(validation_errors()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= validation_errors() ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <form action="<?= base_url('tabungan/tambah') ?>" method="post">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= set_value('tanggal', date('Y-m-d')) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_karyawan" class="form-label">Karyawan</label>
                                <input type="text" class="form-control form-control-sm" id="search_karyawan" placeholder="Cari berdasarkan NIP atau nama...">
                                <input type="hidden" name="id_karyawan" id="id_karyawan" required>
                                <div id="karyawan_list" class="position-absolute bg-white border rounded shadow-sm" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto; width: 95%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-control" id="jenis" name="jenis" required>
                                    <option value="setor">Setor</option>
                                    <option value="tarik">Tarik</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('tabungan') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
        if (!$(e.target).closest('.mb-3').length) {
            $('#karyawan_list').hide();
        }
    });
});
</script>