<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Slip Gaji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN SLIP GAJI KARYAWAN</h2>
        <p>Periode: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Periode</th>
                <th>Total Pendapatan</th>
                <th>Uang Makan</th>
                <th>Total Kasbon</th>
                <th>Total Tabungan</th>
                <th>Potongan BPJS</th>
                <th>Gaji Bersih</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($slip_gaji as $slip): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $slip->nama_karyawan ?></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($slip->bulan)) ?></td>
                <td class="text-right">Rp <?= isset($slip->total_pendapatan) ? number_format($slip->total_pendapatan, 0, ',', '.') : '0' ?></td>
                <td class="text-right">Rp <?= isset($slip->uang_makan) ? number_format($slip->uang_makan, 0, ',', '.') : '0' ?></td>
                <td class="text-right">Rp <?= isset($slip->total_kasbon) ? number_format($slip->total_kasbon, 0, ',', '.') : '0' ?></td>
                <td class="text-right">Rp <?= isset($slip->total_tabungan) ? number_format($slip->total_tabungan, 0, ',', '.') : '0' ?></td>
                <td class="text-right">Rp <?= isset($slip->potongan_bpjs) ? number_format($slip->potongan_bpjs, 0, ',', '.') : '0' ?></td>
                <td class="text-right">Rp <?= number_format($slip->gaji_bersih, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="8" class="text-right">Total Gaji Bersih:</td>
                <td class="text-right">Rp <?= number_format($total_gaji, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        <br><br><br>
        <p>(_________________________)</p>
        <p>Pimpinan</p>
    </div>
</body>
</html>