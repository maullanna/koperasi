<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    
    // Konstruktor untuk memuat model dan memeriksa sesi login
    public function __construct() {
        parent::__construct();

        // Cek session login
        if(!$this->session->userdata('logged_in')) {
            redirect('auth'); // Jika tidak ada sesi login, arahkan ke halaman login
        }

      
        $this->load->model('Karyawan_model'); 
        $this->load->model('Pendapatan_model');  // Untuk mengambil data pendapatan
        $this->load->model('Kasbon_model');  // Untuk mengambil data kasbon
        $this->load->model('Tabungan_model');  // Untuk mengambil data tabungan
        $this->load->model('Pengaturan_model');  // Pastikan Pengaturan_model juga dimuat
    }

    // Method utama untuk menampilkan halaman dashboard
    public function index() {
        $data['title'] = 'Dashboard';
        
        // Cek role user
        $role = $this->session->userdata('role');
        $id_karyawan = $this->session->userdata('id_karyawan'); // Ubah dari $user_id ke $id_karyawan
        
        if ($role == 'admin' || $role == 'owner') {
            // Data untuk admin dan owner (full akses)
            $data['total_karyawan'] = $this->db->where('status', 'aktif')->count_all_results('karyawan');
            $data['total_pendapatan'] = $this->Pendapatan_model->get_all_total();
            $data['total_kasbon'] = $this->Kasbon_model->get_total_outstanding();
            $data['total_tabungan'] = $this->Tabungan_model->get_total_all();
        } else {
            // Data untuk karyawan (hanya data sendiri)
            $data['total_karyawan'] = 1; // Hanya dirinya sendiri
            $data['total_pendapatan'] = $this->Pendapatan_model->get_total_month_by_karyawan($id_karyawan); // Menggunakan method untuk total bulanan
            $data['total_kasbon'] = $this->Kasbon_model->get_total_by_karyawan($id_karyawan);
            $data['total_tabungan'] = $this->Tabungan_model->get_total_by_karyawan($id_karyawan);
        }

        // Mengambil data pengaturan
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();

        // Mengambil data pengguna berdasarkan username yang ada di sesi
        $data['user'] = $this->Karyawan_model->get_by_username($this->session->userdata('username'));

        // Memuat view untuk header, sidebar, dashboard, dan footer
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer');
    }
}
