<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('Pengaturan_model');
        $this->load->helper(['money', 'access']); // Tambahkan helper money dan access
    }

    public function index() {
        // Tambahkan pengecekan akses admin
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('dashboard');
        }

        $data['title'] = 'Pengaturan';
        $data['kategori'] = 'pengaturan'; // Tambahkan kategori
        
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengaturan/index', $data);
        $this->load->view('templates/footer');
    }

    public function update() {
        if (!has_admin_access()) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki akses']);
            exit;
        }
    
        $pemasukan_bpjs = clean_money_input($this->input->post('pemasukan_bpjs'));
        $uang_makan = clean_money_input($this->input->post('uang_makan'));
    
        // Validasi pemasukan BPJS
        if (!is_numeric($pemasukan_bpjs) || $pemasukan_bpjs < 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Pemasukan BPJS harus berupa angka lebih dari 0'
            ]);
            exit;
        }
    
        // Validasi uang makan
        $error_uang_makan = validate_money_input($uang_makan, 'Uang Makan');
        if (!empty($error_uang_makan)) {
            echo json_encode([
                'status' => 'error',
                'message' => $error_uang_makan
            ]);
            exit;
        }
    
        $data = [
            'nama_koperasi' => $this->input->post('nama_koperasi'),
            'pemasukan_bpjs' => $pemasukan_bpjs,
            'uang_makan' => $uang_makan
        ];
    
        $result = $this->Pengaturan_model->update($data);
    
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Pengaturan berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui pengaturan']);
        }
        exit;
    }
    
}
