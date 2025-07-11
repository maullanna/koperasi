<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Backup Data</h1>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Backup Database</h5>
                    <p class="card-text">Backup seluruh data aplikasi termasuk data karyawan, pendapatan, slip gaji, dan lainnya.</p>
                    <a href="<?= base_url('backup/database') ?>" class="btn btn-primary">
                        <i class="fa fa-database"></i> Backup Database
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Backup Source Code</h5>
                    <p class="card-text">Backup seluruh kode sumber aplikasi.</p>
                    <a href="<?= base_url('backup/source_code') ?>" class="btn btn-primary">
                        <i class="fa fa-code"></i> Backup Source Code
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Riwayat Backup</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama File</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Ukuran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $backup_files = array();
                        
                        // Get database backups
                        if(is_dir(FCPATH . 'backup/database')) {
                            $db_files = glob(FCPATH . 'backup/database/*.zip');
                            foreach($db_files as $file) {
                                $backup_files[] = array(
                                    'name' => basename($file),
                                    'type' => 'Database',
                                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                                    'size' => filesize($file),
                                    'path' => $file
                                );
                            }
                        }
                        
                        // Get source code backups
                        if(is_dir(FCPATH . 'backup/source_code')) {
                            $code_files = glob(FCPATH . 'backup/source_code/*.zip');
                            foreach($code_files as $file) {
                                $backup_files[] = array(
                                    'name' => basename($file),
                                    'type' => 'Source Code',
                                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                                    'size' => filesize($file),
                                    'path' => $file
                                );
                            }
                        }
                        
                        // Sort by date descending
                        usort($backup_files, function($a, $b) {
                            return strtotime($b['date']) - strtotime($a['date']);
                        });
                        
                        if(!empty($backup_files)):
                            foreach($backup_files as $key => $file):
                        ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $file['name'] ?></td>
                                <td><?= $file['type'] ?></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($file['date'])) ?></td>
                                <td><?= number_format($file['size'] / 1024, 2) ?> KB</td>
                                <td>
                                    <a href="<?= base_url('backup/download/' . base64_encode($file['path'])) ?>" class="btn btn-sm btn-primary">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada file backup</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>