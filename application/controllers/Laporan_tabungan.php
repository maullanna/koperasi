<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_tabungan extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model(['Tabungan_model', 'Karyawan_model', 'Pengaturan_model']);
        $this->load->helper('money');
    }

    public function index() {
        // Set default tanggal jika tidak ada di post
        $data['tanggal_mulai'] = $this->input->post('tanggal_mulai') ?? date('Y-m-d', strtotime('-1 month'));
        $data['tanggal_selesai'] = $this->input->post('tanggal_selesai') ?? date('Y-m-d');
        $data['id_karyawan'] = $this->input->post('id_karyawan');
        
        // Ambil data karyawan untuk filter
        $data['karyawan_list'] = $this->Karyawan_model->get_all();
        
        // Query untuk mengambil data tabungan
        $this->db->select('tabungan.*, karyawan.nama_karyawan');
        $this->db->from('tabungan');
        $this->db->join('karyawan', 'karyawan.id = tabungan.id_karyawan');
        
        // Filter berdasarkan rentang tanggal
        $this->db->where('tabungan.tanggal >=', $data['tanggal_mulai']);
        $this->db->where('tabungan.tanggal <=', $data['tanggal_selesai']);
        
        // Filter berdasarkan karyawan jika ada
        if (!empty($data['id_karyawan'])) {
            $this->db->where('tabungan.id_karyawan', $data['id_karyawan']);
        }
        
        $this->db->order_by('tabungan.tanggal', 'DESC');
        $data['tabungan'] = $this->db->get()->result();
        
        // Hitung total setor dan tarik
        $data['total_setor'] = 0;
        $data['total_tarik'] = 0;
        foreach($data['tabungan'] as $t) {
            if($t->jenis == 'setor') {
                $data['total_setor'] += $t->jumlah;
            } else {
                $data['total_tarik'] += $t->jumlah;
            }
        }
        
        // Load view
        $data['title'] = 'Laporan Tabungan';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('laporan_tabungan/index', $data);
        $this->load->view('templates/footer');
    }

    public function export_pdf() {
        // Ambil parameter filter
        $tanggal_mulai = $this->input->get('tanggal_mulai') ?? date('Y-m-d', strtotime('-1 month'));
        $tanggal_selesai = $this->input->get('tanggal_selesai') ?? date('Y-m-d');
        $id_karyawan = $this->input->get('id_karyawan');
        
        // Query untuk mengambil data tabungan
        $this->db->select('tabungan.*, karyawan.nama_karyawan');
        $this->db->from('tabungan');
        $this->db->join('karyawan', 'karyawan.id = tabungan.id_karyawan');
        
        // Filter berdasarkan rentang tanggal
        $this->db->where('tabungan.tanggal >=', $tanggal_mulai);
        $this->db->where('tabungan.tanggal <=', $tanggal_selesai);
        
        if (!empty($id_karyawan)) {
            $this->db->where('tabungan.id_karyawan', $id_karyawan);
        }
        
        $this->db->order_by('tabungan.tanggal', 'DESC');
        $data['tabungan'] = $this->db->get()->result();
        
        // Hitung total setor dan tarik
        $data['total_setor'] = 0;
        $data['total_tarik'] = 0;
        foreach($data['tabungan'] as $t) {
            if($t->jenis == 'setor') {
                $data['total_setor'] += $t->jumlah;
            } else {
                $data['total_tarik'] += $t->jumlah;
            }
        }
        
        // Load library DOMPDF
        require_once(FCPATH . 'vendor/autoload.php');
        $dompdf = new \Dompdf\Dompdf();
        
        // Load view untuk PDF
        $html = $this->load->view('laporan_tabungan/pdf', $data, TRUE);
        
        // Generate PDF
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'landscape');
        $dompdf->render();
        
        // Output PDF dengan nama file yang mencakup rentang tanggal
        $filename = "laporan_tabungan_" . date('Y-m-d', strtotime($tanggal_mulai)) . "_sd_" . date('Y-m-d', strtotime($tanggal_selesai)) . ".pdf";
        $dompdf->stream($filename, array('Attachment' => 0));
    }
}