<!DOCTYPE html>
<html>
<head>
    <title>Laporan Tabungan</title>
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
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN TABUNGAN</h2>
        <p>Periode: <?= date('F Y', strtotime($tabungan[0]->tanggal)) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Karyawan</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($tabungan as $t): 
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($t->tanggal)) ?></td>
                <td><?= $t->nama_karyawan ?></td>
                <td><?= ucfirst($t->jenis) ?></td>
                <td class="text-right"><?= format_money($t->jumlah) ?></td>
                <td><?= $t->keterangan ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Setor:</td>
                <td class="text-right"><?= format_money($total_setor) ?></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Tarik:</td>
                <td class="text-right"><?= format_money($total_tarik) ?></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td colspan="4" class="text-right">Saldo:</td>
                <td class="text-right"><?= format_money($total_setor - $total_tarik) ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>