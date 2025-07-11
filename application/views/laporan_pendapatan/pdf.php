<!DOCTYPE html>
<html>
<head>
    <title>Rekap Laporan Pendapatan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Rekap Laporan Pendapatan</h1>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Karyawan</th>
                <th>Jenis Pekerjaan</th>
                <th>Banyak</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_all = 0;
            foreach($pendapatan as $p): 
                $total = $p->banyak * $p->harga_karyawan;
                $total_all += $total;
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($p->tanggal)) ?></td>
                <td><?= $p->nama_karyawan ?></td>
                <td><?= $p->nama_pekerjaan ?></td>
                <td class="text-right"><?= $p->banyak ?></td>
                <td class="text-right">Rp <?= number_format($p->harga_karyawan, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($total, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>Total Keseluruhan:</strong></td>
                <td class="text-right"><strong>Rp <?= number_format($total_all, 0, ',', '.') ?></strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>