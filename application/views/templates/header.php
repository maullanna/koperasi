<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pengaturan) && isset($pengaturan->nama_koperasi) ? $pengaturan->nama_koperasi : 'Koperasi' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> <!-- Ensure Roboto is loaded -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 CSS -->
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Tambahan CSS untuk sidebar mobile */
        @media (max-width: 992px) {
            #sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                width: 250px;
                height: 100vh;
                z-index: 1050;
                transition: all 0.5s cubic-bezier(0.25, 0.1, 0.25, 1); /* Transisi yang lebih smooth */
                overflow-y: auto;
                padding-top: 50px; /* Ruang untuk tombol close */
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
            
            .main-content {
                width: 100%;
                margin-left: 0;
                transition: margin-left 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
            }
            
            .sidebar-close {
                position: absolute;
                top: 10px;
                right: 10px;
                background: #fff;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                z-index: 1060;
                transition: transform 0.3s ease;
            }
            
            .sidebar-close:hover {
                transform: rotate(90deg);
            }
        }
        
        /* Styling untuk profile icon */
        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #003366;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-icon:hover {
            background-color: #004a80;
            transform: scale(1.05);
        }
        
        .profile-menu {
            position: absolute;
            top: 60px;
            right: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            width: 200px;
            z-index: 1060;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .profile-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .profile-menu-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .profile-menu-item {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        
        .profile-menu-item:hover {
            background-color: #f8f9fa;
        }
        
        .profile-menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .profile-menu-item.danger {
            color: #dc3545;
        }
        
        .profile-menu-item.danger:hover {
            background-color: #fff5f5;
        }
        
        /* Styling untuk navbar mobile */
        @media (max-width: 992px) {
            .navbar-mobile-icons {
                display: flex;
                align-items: center;
            }
            
            .navbar-toggler {
                display: none; /* Sembunyikan toggle asli */
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <!-- Di dalam navbar -->
        <button id="sidebarToggle" class="btn btn-link d-lg-none text-white me-2">
            <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand" href="<?= base_url() ?>">
            <?= isset($pengaturan) && isset($pengaturan->nama_koperasi) ? $pengaturan->nama_koperasi : 'Koperasi' ?>
        </a>
        
        <!-- Hapus tombol toggle dan ganti dengan div untuk ikon mobile -->
        <div class="navbar-mobile-icons d-flex d-lg-none">
            <a href="<?= base_url('profile') ?>" class="profile-icon me-2">
                <i class="fa fa-user"></i>
            </a>
        </div>
        
        <!-- Sembunyikan tombol toggle asli -->
        <button class="navbar-toggler d-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item position-relative d-none d-lg-flex align-items-center ms-2">
    <div class="d-flex align-items-center" id="profileIcon" style="cursor:pointer;">
        <div class="profile-icon me-2">
            <i class="fa fa-user"></i>
        </div>
        <span class="text-white fw-semibold">
    <?= $this->session->userdata('nama') ?>
</span>

    </div>
    <div class="profile-menu" id="profileMenu">
        <div class="profile-menu-header">
            <strong><?= $this->session->userdata('nama') ?></strong>
        </div>
        <a href="<?= base_url('profile') ?>" class="profile-menu-item">
            <i class="fa fa-user"></i> Profil Saya
        </a>
        <a href="<?= base_url('auth/logout') ?>" class="profile-menu-item danger">
            <i class="fa fa-sign-out"></i> Logout
        </a>
    </div>
</li>

            </ul>
        </div>
    </div>
</nav>

<!-- Hapus Profile menu untuk mobile karena tidak diperlukan lagi -->

<!-- Overlay untuk sidebar mobile -->
<div class="sidebar-overlay"></div>

<script>
$(document).ready(function() {
    // Toggle sidebar saat tombol diklik
    $('#sidebarToggle').click(function(e) {
        e.preventDefault();
        $('#sidebar').addClass('show');
        // Gunakan setTimeout untuk memberikan efek bertahap
        setTimeout(function() {
            $('.sidebar-overlay').addClass('show');
        }, 50);
    });
    
    // Sembunyikan sidebar saat mengklik overlay
    $('.sidebar-overlay').click(function() {
        $('.sidebar-overlay').removeClass('show');
        // Gunakan setTimeout untuk memberikan efek bertahap
        setTimeout(function() {
            $('#sidebar').removeClass('show');
        }, 100);
    });
    
    // Sembunyikan sidebar saat tombol close diklik
    $(document).on('click', '.sidebar-close', function() {
        $('.sidebar-overlay').removeClass('show');
        // Gunakan setTimeout untuk memberikan efek bertahap
        setTimeout(function() {
            $('#sidebar').removeClass('show');
        }, 100);
    });
    
    // Sembunyikan sidebar saat ukuran layar berubah ke desktop
    $(window).resize(function() {
        if ($(window).width() >= 992) {
            $('.sidebar-overlay').removeClass('show');
            $('#sidebar').removeClass('show');
        }
    });
    
    // Toggle profile menu saat icon profile diklik (desktop)
    $('#profileIcon').click(function(e) {
        e.stopPropagation();
        $('#profileMenu').toggleClass('show');
    });
    
    // Sembunyikan profile menu saat mengklik di luar menu
    $(document).click(function(e) {
        if (!$(e.target).closest('#profileIcon, #profileMenu').length) {
            $('#profileMenu').removeClass('show');
        }
    });
});
</script>
</body>
</html>
