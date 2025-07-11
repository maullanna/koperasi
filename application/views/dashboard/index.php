<!-- Font Awesome 5.15.4 CDN (Free version) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        .border-owner {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-admin {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-karyawan {
            border-left: 0.25rem solid #f6c23e !important;
        }
    </style>
</head>
<body>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <?php if($this->session->userdata('role') == 'admin' || $this->session->userdata('role') == 'owner'): ?>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2 <?= $this->session->userdata('role') == 'owner' ? 'border-owner' : 'border-admin' ?>">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Karyawan Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_karyawan ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2 <?= $this->session->userdata('role') == 'karyawan' ? 'border-karyawan' : ($this->session->userdata('role') == 'owner' ? 'border-owner' : 'border-admin') ?>">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <?= ($this->session->userdata('role') == 'karyawan') ? 'Total Pendapatan Bulan Ini' : 'Total Pendapatan Hari Ini' ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-money fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2 <?= $this->session->userdata('role') == 'karyawan' ? 'border-karyawan' : ($this->session->userdata('role') == 'owner' ? 'border-owner' : 'border-admin') ?>">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <?= ($this->session->userdata('role') == 'karyawan') ? 'Total Kasbon Anda' : 'Total Kasbon' ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_kasbon, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2 <?= $this->session->userdata('role') == 'karyawan' ? 'border-karyawan' : ($this->session->userdata('role') == 'owner' ? 'border-owner' : 'border-admin') ?>">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <?= ($this->session->userdata('role') == 'karyawan') ? 'Total Tabungan Anda' : 'Total Tabungan' ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($total_tabungan, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
