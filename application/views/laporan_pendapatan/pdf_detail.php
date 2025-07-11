<!DOCTYPE html>
<html>
<head>
    <title>Detail Laporan Pendapatan</title>
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
        .info-table {
            margin-bottom: 20px;
        }
        .info-table td {
            border: none;
            padding: 4px;
        }
    </style>
</head>
<body>
    <h1>Detail Laporan Pendapatan</h1>
    
    <table class="info-table">
        <tr>
            <td width="20%">Tanggal</td>
            <td width="5%">:</td>
            <td><?= date('d/m/Y', strtotime($pendapatan->tanggal)) ?></td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td><?= $pendapatan->nip ?></td>
        </tr>
        <tr>
            <td>Nama Karyawan</td>
            <td>:</td>
            <td><?= $pendapatan->nama_karyawan ?></td>
        </tr>
    </table>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Pekerjaan</th>
                <th>Banyak</th>
                <th>Harga Koperasi</th>
                <th>Total Koperasi</th>
                <th>Harga Karyawan</th>
                <th>Total Karyawan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_koperasi = 0;
            $total_karyawan = 0;
            foreach($pendapatan_detail as $detail): 
                $total_koperasi += $detail->total_koperasi;
                $total_karyawan += $detail->total_karyawan;
            ?>
            <tr>
                <td class="text-right"><?= $no++ ?></td>
                <td><?= $detail->nama_pekerjaan ?></td>
                <td class="text-right"><?= $detail->banyak ?></td>
                <td class="text-right">Rp <?= number_format($detail->harga_koperasi, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($detail->total_koperasi, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($detail->harga_karyawan, 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($detail->total_karyawan, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>Rp <?= number_format($total_koperasi, 0, ',', '.') ?></strong></td>
                <td class="text-right"></td>
                <td class="text-right"><strong>Rp <?= number_format($total_karyawan, 0, ',', '.') ?></strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>