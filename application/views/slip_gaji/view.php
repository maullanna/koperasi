<div class="container py-5">
    <div class="card shadow rounded-4">
        <div class="card-body">
            <!-- Header Slip Gaji -->
            <div class="text-center mb-4">
                <h3 class="fw-bold mb-1">SLIP GAJI KARYAWAN</h3>
                <h4 class="fw-bold mb-3"><?= $slip_gaji->nama_karyawan ?></h4>
                <div class="text-muted">NIP: <?= $slip_gaji->nip ?> | Periode: <?= date('d/m/Y', strtotime($slip_gaji->tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($slip_gaji->tanggal_akhir)) ?></div>
            </div>
            <hr class="border-2">

            <div class="row g-4">
                <!-- Pendapatan Section - Kolom Kiri -->
                <div class="col-md-6">
                    <h5 class="fw-semibold bg-primary text-white p-2 rounded">RINCIAN PENDAPATAN</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-primary">
                                <tr class="text-center">
                                    <th class="align-middle" width="10%">No</th>
                                    <th class="align-middle" width="50%">Jenis Pekerjaan</th>
                                    <th class="align-middle" width="20%">Banyak</th>
                                    <th class="align-middle" width="20%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pendapatan_details)): 
                                    // Buat array untuk menampung data yang sudah digabung
                                    $grouped_details = [];
                                    
                                    // Gabungkan data berdasarkan nama pekerjaan
                                    foreach ($pendapatan_details as $detail) {
                                        $key = $detail->nama_pekerjaan;
                                        if (!isset($grouped_details[$key])) {
                                            $grouped_details[$key] = [
                                                'nama_pekerjaan' => $detail->nama_pekerjaan,
                                                'banyak' => 0,
                                                'total' => 0
                                            ];
                                        }
                                        $grouped_details[$key]['banyak'] += $detail->banyak;
                                        $grouped_details[$key]['total'] += $detail->total;
                                    }
                                    
                                    $no = 1;
                                    foreach ($grouped_details as $detail): 
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= $detail['nama_pekerjaan'] ?></td>
                                        <td class="text-center"><?= number_format($detail['banyak'], 0, ',', '.') ?></td>
                                        <td class="text-end">Rp <?= number_format($detail['total'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data yang tersedia pada tabel ini</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">Total Pendapatan:</td>
                                    <td class="text-end">Rp <?= number_format($slip_gaji->total_pendapatan, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Uang Makan:</td>
                                    <td class="text-end">Rp <?= number_format($slip_gaji->uang_makan, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                <td colspan="3" class="text-end">BPJS (3%):</td>
                                <td class="text-end text-success"> + Rp <?= number_format($slip_gaji->pemasukan_bpjs, 0, ',', '.') ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end">Total:</td>
                                    <td class="text-end">Rp <?= number_format($slip_gaji->total_pendapatan + $slip_gaji->uang_makan + $slip_gaji->pemasukan_bpjs, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Potongan Section - Kolom Kanan -->
                <div class="col-md-6">
                    <h5 class="fw-semibold bg-danger text-white p-2 rounded">RINCIAN POTONGAN</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-danger">
                                <tr>
                                    <th width="60%">Jenis Potongan</th>
                                    <th width="40%" class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total Kasbon</td>
                                    <td class="text-end">Rp <?= number_format($slip_gaji->total_kasbon, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>Total Tabungan</td>
                                    <td class="text-end">Rp <?= number_format($slip_gaji->total_tabungan, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-bold">
                            <tr>
                          
                       </tr>
                            </tfoot>
                        </table>

                        <!-- Gaji Bersih -->
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h5 class="fw-bold mb-0">GAJI BERSIH</h5>
                                    </div>
                                    <div class="col-6 text-end">
                                        <h4 class="text-success fw-bold mb-0">Rp <?= number_format($slip_gaji->gaji_bersih, 0, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($slip_gaji->catatan)): ?>
                <div class="alert alert-info mt-4">
                    <strong>Catatan:</strong> <?= $slip_gaji->catatan ?>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-3 mt-4">
                <a href="<?= base_url('slipgaji') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                <a href="<?= base_url('slipgaji/print/' . $slip_gaji->id) ?>" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak</a>
            </div>
        </div>
    </div>
</div>


