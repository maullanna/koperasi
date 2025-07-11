<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
   
   <style>
    header.navbar {
        background-color: #004366 !important; /* Biru tua */
        color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Sidebar baru yang lebih kontras */
    #sidebar {
        background-color:rgb(226, 226, 226) !important; /* Abu terang */
        border-right: 1px solid #dee2e6;
        box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        color: #212529;
        transition: all 0.5s cubic-bezier(0.25, 0.1, 0.25, 1); /* Transisi yang lebih smooth */
    }

    @media (max-width: 992px) {
        #sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            z-index: 1050;
            overflow-y: auto;
            box-shadow: 0 0 0 rgba(0,0,0,0); /* Bayangan awal tidak terlihat */
        }
        
        #sidebar.show {
            left: 0;
            box-shadow: 5px 0 15px rgba(0,0,0,0.3); /* Bayangan muncul saat sidebar ditampilkan */
        }
        
        .sidebar-overlay {
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s cubic-bezier(0.25, 0.1, 0.25, 1), visibility 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
    }

    #sidebar .nav-link {
        color: #343a40 !important; /* Abu gelap */
        padding: 10px 15px;
        margin: 5px 10px;
        border-radius: 5px;
        transition: all 0.2s ease;
    }

    #sidebar .nav-link:hover,
    #sidebar .nav-link.active {
        background-color: #003366 !important; /* Biru tua untuk aktif/hover */
        color: #ffffff !important;
        transform: translateX(4px);
    }

    #sidebar .sidebar-heading {
        color: #6c757d !important; /* Abu heading */
        text-transform: uppercase;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 10px 15px;
        border-top: 1px solid #dee2e6;
    }

    #sidebar .text-center.mb-4 {
        padding: 20px 0;
        border-bottom: 1px solid #dee2e6;
        background-color: #ffffff;
    }

    #sidebar img {
        max-width: 100px;
    }

    .main-content {
        background-color: #ffffff;
        transition: margin-left 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
    }
    
    .sidebar-close {
        position: absolute;
        top: 10px;
        right: 10px;
        background: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        cursor: pointer;
        transition: transform 0.3s ease;
        z-index: 1060;
    }
    
    .sidebar-close:hover {
        transform: rotate(90deg);
    }
</style>

</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse text-white">
            <div class="sidebar-close d-lg-none" style="background: white; color: black; font-size: 20px; cursor: pointer; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); opacity: 0.3;">
                <i class="fa fa-times"></i>
            </div>

            <div class="position-sticky pt-3">
                <div class="text-center mb-2"></div>
                <ul class="nav flex-column">

                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?= $this->uri->segment(1) == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                    </li>

                    <?php if(has_owner_access()): ?>
                    <!-- Master Data -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Master Data</span>
                    </h6>
                    <!-- Karyawan -->
                    <li class="nav-item">
                        <a class="nav-link <?= $this->uri->segment(1) == 'karyawan' ? 'active' : '' ?>" href="<?= base_url('karyawan') ?>">
                            <i class="fa fa-users"></i> Karyawan
                        </a>
                    </li>
                    <!-- Jenis Pekerjaan -->
                    <li class="nav-item">
                        <a class="nav-link <?= $this->uri->segment(1) == 'pekerjaan' ? 'active' : '' ?>" href="<?= base_url('pekerjaan') ?>">
                            <i class="fa fa-tasks"></i> Jenis Pekerjaan
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Transaksi -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Transaksi</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <?php 
                            $role = $this->session->userdata('role');
                            if ($role == 'admin' || $role == 'owner'):
                        ?>
                        <!-- Pendapatan -->
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'pendapatan' ? 'active' : '' ?>" href="<?= base_url('pendapatan') ?>">
                                <i class="fa fa-money"></i> Pendapatan
                            </a>
                        </li>
                        <!-- Kasbon -->
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'kasbon' ? 'active' : '' ?>" href="<?= base_url('kasbon') ?>">
                                <i class="fa fa-credit-card"></i> Kasbon
                            </a>
                        </li>
                        <!-- Absensi -->
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'absensi' ? 'active' : '' ?>" href="<?= base_url('absensi') ?>">
                                <i class="fa fa-calendar"></i> Absensi
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(has_owner_access()): ?>
                        <!-- Tabungan -->
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'tabungan' ? 'active' : '' ?>" href="<?= base_url('tabungan') ?>">
                                <i class="fa fa-bank"></i> Tabungan
                            </a>
                        </li>
                        <!-- Slip Gaji -->
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'slipgaji' ? 'active' : '' ?>" href="<?= base_url('slipgaji') ?>">
                                <i class="fa fa-file-text"></i> Slip Gaji
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <?php if(has_owner_access()): ?>
                    <!-- Laporan -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Laporan</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'laporan_slip_gaji' ? 'active' : '' ?>" href="<?= base_url('laporan_slip_gaji') ?>">
                                <i class="fa fa-file-text"></i> Laporan Slip Gaji
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'laporan_pendapatan' ? 'active' : '' ?>" href="<?= base_url('laporan_pendapatan') ?>">
                                <i class="fa fa-file-text"></i> Laporan Pendapatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'laporan_tabungan' ? 'active' : '' ?>" href="<?= base_url('laporan_tabungan') ?>">
                                <i class="fa fa-file-text"></i> Laporan Tabungan
                            </a>
                        </li>
                    </ul>

                    <!-- Pengaturan -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Pengaturan</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'pengaturan' ? 'active' : '' ?>" href="<?= base_url('pengaturan') ?>">
                                <i class="fa fa-cog"></i> Pengaturan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(1) == 'backup' ? 'active' : '' ?>" href="<?= base_url('backup') ?>">
                                <i class="fa fa-database"></i> Backup
                            </a>
                        </li>
                    <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                                <i class="fa fa-sign-out"></i> Logout
                            </a>
                        </li>
                    </ul>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">

<!-- Overlay untuk sidebar mobile -->
<div class="sidebar-overlay"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sidebarToggle').click(function(e) {
            e.preventDefault();
            $('.sidebar-overlay').css('display', 'block');
            setTimeout(function() {
                $('#sidebar').addClass('show');
                setTimeout(function() {
                    $('.sidebar-overlay').addClass('show');
                }, 50);
            }, 10);
        });

        $('.sidebar-overlay').click(function() {
            $('.sidebar-overlay').removeClass('show');
            setTimeout(function() {
                $('#sidebar').removeClass('show');
                setTimeout(function() {
                    if (!$('#sidebar').hasClass('show')) {
                        $('.sidebar-overlay').css('display', 'none');
                    }
                }, 500);
            }, 100);
        });

        $('.sidebar-close').click(function() {
            $('.sidebar-overlay').removeClass('show');
            setTimeout(function() {
                $('#sidebar').removeClass('show');
                setTimeout(function() {
                    if (!$('#sidebar').hasClass('show')) {
                        $('.sidebar-overlay').css('display', 'none');
                    }
                }, 500);
            }, 100);
        });

        $(window).resize(function() {
            if ($(window).width() >= 992) {
                $('.sidebar-overlay').removeClass('show');
                $('#sidebar').removeClass('show');
                setTimeout(function() {
                    if (!$('#sidebar').hasClass('show')) {
                        $('.sidebar-overlay').css('display', 'none');
                    }
                }, 500);
            }
        });
    });
</script>
</body>

</html>
