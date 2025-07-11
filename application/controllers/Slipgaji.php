<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slipgaji extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model(['SlipGaji_model', 'Karyawan_model', 'Pendapatan_model']);
        $this->load->model(['Pengaturan_model', 'FinancialData_model']); // Add FinancialData_model
        $this->load->helper(['money', 'access']);
    }

    public function index() {
        $data['title'] = 'Daftar Slip Gaji';
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
    
        // Tambahkan pengecekan role di sinif
        if($this->session->userdata('role') == 'karyawan') {
            $id_karyawan = $this->session->userdata('id_karyawan');
            $data['slip_gaji_list'] = $this->SlipGaji_model->get_slip_gaji_by_karyawan($id_karyawan);
        } else {
            $data['slip_gaji_list'] = $this->SlipGaji_model->get_all_slip_gaji();
        }
    
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('slip_gaji/index', $data);
        $this->load->view('templates/footer');
    }
    

    public function view($id) {
        // Validasi ID
        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID Slip Gaji tidak valid');
            redirect('slipgaji');
        }

        $data['title'] = 'Detail Slip Gaji';
        $data['slip_gaji'] = $this->SlipGaji_model->get_slip_gaji_by_id($id);

        if (!$data['slip_gaji']) {
            $this->session->set_flashdata('error', 'Slip gaji tidak ditemukan');
            redirect('slipgaji');
        }

        // Ubah pemanggilan get_detail_by_period untuk menggunakan tanggal_awal dan tanggal_akhir
        $data['pendapatan_details'] = $this->Pendapatan_model->get_detail_by_period(
            $data['slip_gaji']->id_karyawan,
            $data['slip_gaji']->tanggal_awal,
            $data['slip_gaji']->tanggal_akhir
        );

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('slip_gaji/view', $data);
        $this->load->view('templates/footer');
    }
    

    public function generate() {
        // Cek akses admin
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('slipgaji');
        }

        if ($this->input->method() === 'post') {
            // Set rules validasi
            $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
            $this->form_validation->set_rules('tanggal_awal', 'Tanggal Awal', 'required');
            $this->form_validation->set_rules('tanggal_akhir', 'Tanggal Akhir', 'required');
            
            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('slipgaji/generate');
            }

            $id_karyawan = $this->input->post('id_karyawan');
            $tanggal_awal = $this->input->post('tanggal_awal');
            $tanggal_akhir = $this->input->post('tanggal_akhir');

            // Validasi rentang tanggal
            if (strtotime($tanggal_akhir) < strtotime($tanggal_awal)) {
                $this->session->set_flashdata('error', 'Tanggal akhir tidak boleh lebih awal dari tanggal awal');
                redirect('slipgaji/generate');
            }

            // Cek duplikasi slip gaji
            if ($this->SlipGaji_model->check_slip_exists($id_karyawan, $tanggal_awal, $tanggal_akhir)) {
                $this->session->set_flashdata('error', 'Slip gaji untuk karyawan dan periode ini sudah ada');
                redirect('slipgaji/generate');
            }

            // Ambil data finansial dari model FinancialData
            $financial_data = $this->FinancialData_model->get_data($id_karyawan, $tanggal_awal, $tanggal_akhir);
            
            // Hitung gaji bersih
            $gaji_bersih = ($financial_data['total_pendapatan'] + $financial_data['total_uang_makan'] + $financial_data['pemasukan_bpjs']) - 
                          ($financial_data['total_kasbon'] + $financial_data['total_tabungan']);
            
            $slip_data = [
                'id_karyawan' => $id_karyawan,
                'bulan' => $tanggal_akhir,
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'total_pendapatan' => $financial_data['total_pendapatan'],
                'uang_makan' => $financial_data['total_uang_makan'],
                'total_kasbon' => $financial_data['total_kasbon'],
                'total_tabungan' => $financial_data['total_tabungan'],
                'pemasukan_bpjs' => $financial_data['pemasukan_bpjs'],
                'gaji_bersih' => $gaji_bersih,
                'catatan' => $this->input->post('catatan'),
                'tanggal_cetak' => date('Y-m-d H:i:s')
            ];

            if ($this->SlipGaji_model->insert_slip_gaji($slip_data)) {
                $this->session->set_flashdata('success', 'Slip gaji berhasil dibuat');
            } else {
                $this->session->set_flashdata('error', 'Gagal membuat slip gaji');
            }
            
            redirect('slipgaji');
        }
        
        // Jika bukan POST, tampilkan form generate
        $data['title'] = 'Generate Slip Gaji';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        
        // Load Pengaturan model and get settings
        $this->load->model('Pengaturan_model');
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('slip_gaji/generate', $data);
        $this->load->view('templates/footer');
    }
    



    public function edit($id) {
        // Validasi akses dan ID
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('slipgaji');
        }

        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID Slip Gaji tidak valid');
            redirect('slipgaji');
        }

        $data['slip_gaji'] = $this->SlipGaji_model->get_slip_gaji_by_id($id);
        if (!$data['slip_gaji']) {
            $this->session->set_flashdata('error', 'Slip gaji tidak ditemukan');
            redirect('slipgaji');
        }

        // Set rules validasi
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('total_pendapatan', 'Total Pendapatan', 'required|numeric');
        $this->form_validation->set_rules('uang_makan', 'Uang Makan', 'required|numeric');
        $this->form_validation->set_rules('total_kasbon', 'Total Kasbon', 'required|numeric');
        $this->form_validation->set_rules('total_tabungan', 'Total Tabungan', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Slip Gaji';
            $data['kategori'] = 'transaksi';
            $data['karyawan'] = $this->Karyawan_model->get_all();
            
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('slip_gaji/edit', $data);
            $this->load->view('templates/footer');
        } else {
            // Bersihkan input uang
            $total_pendapatan = clean_money_input($this->input->post('total_pendapatan'));
            $total_kasbon = clean_money_input($this->input->post('total_kasbon'));
            $total_tabungan = clean_money_input($this->input->post('total_tabungan'));
            $pemasukan_bpjs = clean_money_input($this->input->post('pemasukan_bpjs'));  // Changed from potongan_bpjs
            $uang_makan = clean_money_input($this->input->post('uang_makan'));
            $gaji_bersih = clean_money_input($this->input->post('gaji_bersih'));
            
            // Validasi input uang
            $errors = [];
            if (!validate_money_input($total_pendapatan, 'Total Pendapatan')) $errors[] = 'Total Pendapatan tidak valid';
            if (!validate_money_input($total_kasbon, 'Total Kasbon')) $errors[] = 'Total Kasbon tidak valid';
            if (!validate_money_input($total_tabungan, 'Total Tabungan')) $errors[] = 'Total Tabungan tidak valid';
            if (!validate_money_input($pemasukan_bpjs, 'Pemasukan BPJS')) $errors[] = 'Pemasukan BPJS tidak valid';  // Changed text
            if (!validate_money_input($uang_makan, 'Uang Makan')) $errors[] = 'Uang Makan tidak valid';
            if (!validate_money_input($gaji_bersih, 'Gaji Bersih')) $errors[] = 'Gaji Bersih tidak valid';
            
            if (!empty($errors)) {
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('slipgaji/edit/' . $id);
            }
            
            // Hitung ulang BPJS dan gaji bersih
            $pemasukan_bpjs = $this->Pengaturan_model->get_pengaturan()->pemasukan_bpjs ?? 0;

            // Di method edit, ubah bagian perhitungan BPJS
            // Hitung ulang BPJS berdasarkan persentase
            $financial_data = $this->FinancialData_model->get_data($this->input->post('id_karyawan'), $data['slip_gaji']->tanggal_awal, $data['slip_gaji']->tanggal_akhir);
            $pemasukan_bpjs = $financial_data['pemasukan_bpjs'];

            // Hitung gaji bersih (perbaikan rumus)
            $gaji_bersih = ($total_pendapatan + $uang_makan + $pemasukan_bpjs) - ($total_kasbon + $total_tabungan);
            
            $slip_data = [
                'id_karyawan' => $this->input->post('id_karyawan'),
                'bulan' => $this->input->post('bulan'),
                'total_pendapatan' => $total_pendapatan,
                'total_kasbon' => $total_kasbon,
                'total_tabungan' => $total_tabungan,
                'pemasukan_bpjs' => $pemasukan_bpjs,  // Changed from potongan_bpjs
                'uang_makan' => $uang_makan,
                'gaji_bersih' => $gaji_bersih,
                'catatan' => $this->input->post('catatan')
            ];

            if ($this->SlipGaji_model->update_slip_gaji($id, $slip_data)) {
                $this->session->set_flashdata('success', 'Slip gaji berhasil diupdate.');
                redirect('slipgaji');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat mengupdate slip gaji.');
                redirect('slipgaji/edit/' . $id);
            }
        }
    }

    public function print($id) {
        // Validasi ID
        if (!$id || !is_numeric($id)) {
            $this->session->set_flashdata('error', 'ID Slip Gaji tidak valid');
            redirect('slipgaji');
        }

        $data['slip_gaji'] = $this->SlipGaji_model->get_slip_gaji_by_id($id);
        if (!$data['slip_gaji']) {
            $this->session->set_flashdata('error', 'Slip gaji tidak ditemukan');
            redirect('slipgaji');
        }
        $data['detail_pekerjaan'] = $data['slip_gaji']->detail_pekerjaan ?? [];
        $data['title'] = 'Print Slip Gaji';
        $this->load->view('slip_gaji/print', $data);
    }
    

    public function delete($id) {
        // Tambahkan pengecekan akses admin
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('slipgaji');
        }

        if ($this->SlipGaji_model->delete_slip_gaji($id)) {
            $this->session->set_flashdata('success', 'Slip gaji berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menghapus slip gaji.');
        }

        redirect('slipgaji');
    }

    public function get_financial_data() {
        // Validasi input
        $id_karyawan = $this->input->post('id_karyawan');
        $tanggal_awal = $this->input->post('tanggal_awal');
        $tanggal_akhir = $this->input->post('tanggal_akhir');
        
        if (!$id_karyawan || !$tanggal_awal || !$tanggal_akhir) {
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
            return;
        }

        try {
            // Ambil data finansial dari model FinancialData
            $financial_data = $this->FinancialData_model->get_data($id_karyawan, $tanggal_awal, $tanggal_akhir);
            
            // Hitung gaji bersih
            $gaji_bersih = ($financial_data['total_pendapatan'] + $financial_data['total_uang_makan'] + $financial_data['pemasukan_bpjs']) - 
                          ($financial_data['total_kasbon'] + $financial_data['total_tabungan']);
            
            $response = [
                'total_pendapatan' => $financial_data['total_pendapatan'],
                'total_uang_makan' => $financial_data['total_uang_makan'],
                'total_kasbon' => $financial_data['total_kasbon'],
                'total_tabungan' => $financial_data['total_tabungan'],
                'pemasukan_bpjs' => $financial_data['pemasukan_bpjs'],
                'gaji_bersih' => $gaji_bersih
            ];
            
            echo json_encode($response);
        } catch (Exception $e) {
            $this->output->set_status_header(500);
            echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}