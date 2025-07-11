<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data BPJS</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('bpjs/tambah') ?>" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Tambah BPJS
        </a>
    </div>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Aksi</th>  <!-- Added action column -->
            </tr>
        </thead>
        <tbody>
            <?php foreach($bpjs as $key => $row): ?>
            <tr>
                <td><?= $key + 1 ?></td>
                <td><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                <td><?= $row->nama_karyawan ?></td>
                <td>Rp <?= number_format($row->jumlah, 0, ',', '.') ?></td>
                <td><?= $row->keterangan ?></td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="<?= base_url('bpjs/lihat/'.$row->id) ?>" 
                           class="btn btn-info" 
                           data-bs-toggle="tooltip" 
                           title="Lihat Detail">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="<?= base_url('bpjs/edit/'.$row->id) ?>" 
                           class="btn btn-warning" 
                           data-bs-toggle="tooltip" 
                           title="Edit Data">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="<?= base_url('bpjs/hapus/'.$row->id) ?>" 
                           class="btn btn-danger" 
                           data-bs-toggle="tooltip" 
                           title="Hapus Data"
                           onclick="return confirm('Yakin ingin menghapus data ini?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($bpjs)): ?>
            <tr>
                <td colspan="6" class="text-center">Tidak ada data</td>  <!-- Changed colspan to 6 -->
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>