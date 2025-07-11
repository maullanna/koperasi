<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Slip Gaji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            @page {
                size: A4;
                margin: 20mm;
            }
            body {
                background: white;
            }
            th {
                background-color: #f2f2f2 !important;
            }
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            width: 40%;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card shadow rounded-4">
        <div class="card-body">
            <!-- Header Slip Gaji -->
            <div class="text-center mb-4 p-4 bg-light border rounded shadow-sm">
    <?php 
    $CI =& get_instance();
    $CI->load->model('Pengaturan_model');
    $pengaturan = $CI->Pengaturan_model->get_pengaturan();
    ?>
    <h2 class="fw-bold text-primary mb-2"><?= $pengaturan->nama_koperasi ?></h2>
    <h4 class="fw-semibold text-dark mb-1">SLIP GAJI KARYAWAN</h4>
    <h5 class="fw-bold text-secondary mb-2"><?= $slip_gaji->nama_karyawan ?></h5>
    <div class="text-muted small">
        NIP: <strong><?= $slip_gaji->nip ?></strong> | 
        Periode: <strong><?= date('F Y', strtotime($slip_gaji->bulan)) ?></strong>
    </div>
</div>

<div class="mb-4 p-4 bg-white border rounded shadow-sm">
    <div class="mb-3">
        <h5 class="text-primary fw-bold">Informasi Karyawan</h5>
        <p class="mb-1"><strong>Nama:</strong> <?= $slip_gaji->nama_karyawan ?></p>
        <p class="mb-1"><strong>NIP:</strong> <?= $slip_gaji->nip ?></p>
        <p class="mb-1"><strong>Periode:</strong> <?= date('d/m/Y', strtotime($slip_gaji->tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($slip_gaji->tanggal_akhir)) ?></p>
    </div>

    <h5 class="text-success fw-bold mt-4">Pendapatan</h5>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-success">
            <tr>
                <th>Pekerjaan</th>
                <th class="text-center">Banyak</th>
                <th class="text-end">Harga Karyawan</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detail_pekerjaan as $detail): ?>
                <tr>
                    <td><?= $detail->nama_pekerjaan ?></td>
                    <td class="text-center"><?= $detail->total_banyak ?></td>
                    <td class="text-end">Rp <?= number_format($detail->total_pendapatan / $detail->total_banyak, 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format($detail->total_pendapatan, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="fw-bold text-end">Total Pendapatan: <span class="text-success">Rp <?= number_format($slip_gaji->total_pendapatan, 0, ',', '.') ?></span></p>

    <hr>

    <h5 class="text-danger fw-bold mt-4">Potongan</h5>
    <table class="table table-bordered table-sm align-middle">
        <tbody>
            <tr>
                <th>Total Kasbon</th>
                <td class="text-end">Rp <?= number_format($slip_gaji->total_kasbon, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Total Tabungan</th>
                <td class="text-end">Rp <?= number_format($slip_gaji->total_tabungan, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <hr>

    <h5 class="fw-bold text-end text-primary mt-4">Gaji Bersih: <span class="text-success">Rp <?= number_format($slip_gaji->gaji_bersih, 0, ',', '.') ?></span></h5>

    <?php if (!empty($slip_gaji->catatan)): ?>
        <p class="mt-3"><strong>Catatan:</strong> <?= $slip_gaji->catatan ?></p>
    <?php endif; ?>

    <div class="row text-center mt-5">
        <div class="col">
            <p>Yang Menerima,</p>
            <br><br>
            <p><strong>(<?= $slip_gaji->nama_karyawan ?>)</strong></p>
        </div>
        <div class="col">
            <p>HRD,</p>
            <br><br>
            <p><strong>(__________________)</strong></p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <button class="btn btn-primary me-2" onclick="window.print()"><i class="fa fa-print me-1"></i> Print</button>
        <a href="<?= base_url('slipgaji') ?>" class="btn btn-secondary"><i class="fa fa-arrow-left me-1"></i> Kembali</a>
    </div>
</div>
