<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_pendapatan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        $this->load->model('Pendapatan_model');
        $this->load->model('Pekerjaan_model');
        $this->load->helper('money'); // Tambahkan helper money
    }
public function index() {
    // Set default date range if not in post
    // Ubah format tanggal ke Y-m-d untuk database
    $tanggal_awal = date('Y-m-d', strtotime($this->input->post('tanggal_awal') ?? date('Y-m-01')));
    $tanggal_akhir = date('Y-m-d', strtotime($this->input->post('tanggal_akhir') ?? date('Y-m-t')));
    
    $data['tanggal_awal'] = $tanggal_awal;
    $data['tanggal_akhir'] = $tanggal_akhir;
    
    // Ambil data jenis pekerjaan untuk filter
    $data['jenis_pekerjaan'] = $this->Pekerjaan_model->get_all();
    
    // Tambahkan filter berdasarkan id_karyawan jika ada
    $data['id_karyawan'] = $this->input->post('id_karyawan');
    
    // Tambahkan ini setelah $data['id_karyawan']
    $data['id_pekerjaan'] = $this->input->post('id_pekerjaan');
    
    // Query untuk mengambil data pendapatan
    $this->db->select('pendapatan.id as id_pendapatan, pendapatan.*, karyawan.nama_karyawan, karyawan.nip, pekerjaan.nama_pekerjaan, 
        pendapatan_detail.banyak, pendapatan_detail.harga_karyawan, pendapatan_detail.harga_koperasi,
        ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_karyawan) as total_karyawan,
        ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_koperasi) as total_koperasi,
        pekerjaan.harga_karyawan as harga_karyawan_master');
    $this->db->from('pendapatan');
    $this->db->join('karyawan', 'karyawan.id = pendapatan.id_karyawan');
    $this->db->join('pendapatan_detail', 'pendapatan_detail.id_pendapatan = pendapatan.id');
    $this->db->join('pekerjaan', 'pekerjaan.id = pendapatan_detail.id_pekerjaan');
    
    // Filter based on date range
    $this->db->where('pendapatan.tanggal >=', $tanggal_awal);
    $this->db->where('pendapatan.tanggal <=', $tanggal_akhir);
    
    // Filter berdasarkan id_karyawan jika ada
    if (!empty($data['id_karyawan'])) {
        $this->db->where('karyawan.id', $data['id_karyawan']);
    }
    
    // Filter berdasarkan jenis pekerjaan jika ada
    if (!empty($data['id_pekerjaan'])) {
        $this->db->where('pekerjaan.id', $data['id_pekerjaan']);
    }
    
    $this->db->order_by('pendapatan.tanggal', 'DESC');
    $data['pendapatan'] = $this->db->get()->result();
    
    // Load view
    $data['title'] = 'Rekap Laporan Pendapatan';
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('laporan_pendapatan/index', $data);
    $this->load->view('templates/footer');
}
  

    public function view($id) {
        $data['title'] = 'Detail Laporan Pendapatan';
        
        // Query untuk mengambil data utama pendapatan
        $this->db->select('pendapatan.*, karyawan.nama_karyawan, karyawan.nip, 
            (SELECT SUM(ROUND(pd.banyak * pd.harga_karyawan)) 
             FROM pendapatan_detail pd 
             WHERE pd.id_pendapatan = pendapatan.id) as total_karyawan');
        $this->db->from('pendapatan');
        $this->db->join('karyawan', 'karyawan.id = pendapatan.id_karyawan');
        $this->db->where('pendapatan.id', $id);
        $data['pendapatan'] = $this->db->get()->row();
        
        if (!$data['pendapatan']) {
            $this->session->set_flashdata('error', 'Data pendapatan tidak ditemukan');
            redirect('laporan_pendapatan');
        }
        
        // Query untuk mengambil detail pendapatan
        $this->db->select('pendapatan_detail.*, pekerjaan.nama_pekerjaan,
            ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_karyawan) as total_karyawan,
            ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_koperasi) as total_koperasi');
        $this->db->from('pendapatan_detail');
        $this->db->join('pekerjaan', 'pekerjaan.id = pendapatan_detail.id_pekerjaan');
        $this->db->where('pendapatan_detail.id_pendapatan', $id);
        $data['pendapatan_detail'] = $this->db->get()->result();
        
        // Load view
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('laporan_pendapatan/view', $data);
        $this->load->view('templates/footer');
    }

    public function export_detail($id) {
        // Query untuk mengambil data utama pendapatan
        $this->db->select('pendapatan.*, karyawan.nama_karyawan, karyawan.nip');
        $this->db->from('pendapatan');
        $this->db->join('karyawan', 'karyawan.id = pendapatan.id_karyawan');
        $this->db->where('pendapatan.id', $id);
        $data['pendapatan'] = $this->db->get()->row();
        
        if (!$data['pendapatan']) {
            $this->session->set_flashdata('error', 'Data pendapatan tidak ditemukan');
            redirect('laporan_pendapatan');
        }
        
        // Query untuk mengambil detail pendapatan
        $this->db->select('pendapatan_detail.*, pekerjaan.nama_pekerjaan,
            ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_karyawan) as total_karyawan,
            ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_koperasi) as total_koperasi');
        $this->db->from('pendapatan_detail');
        $this->db->join('pekerjaan', 'pekerjaan.id = pendapatan_detail.id_pekerjaan');
        $this->db->where('pendapatan_detail.id_pendapatan', $id);
        $data['pendapatan_detail'] = $this->db->get()->result();
        
        // Load library DOMPDF
        require_once(FCPATH . 'vendor/autoload.php');
        $dompdf = new \Dompdf\Dompdf();
        
        // Load view untuk PDF
        $html = $this->load->view('laporan_pendapatan/pdf_detail', $data, TRUE);
        
        // Generate PDF
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream("detail_pendapatan_" . $data['pendapatan']->nama_karyawan . "_" . date('d-m-Y', strtotime($data['pendapatan']->tanggal)) . ".pdf", array('Attachment' => 0));
    }
    
    public function search_karyawan() {
        $search = $this->input->get('term');
        
        $this->db->select('id, nip, nama_karyawan');
        $this->db->from('karyawan');
        $this->db->where("(nama_karyawan LIKE '%$search%' OR nip LIKE '%$search%')");
        $this->db->limit(10);
        
        $result = $this->db->get()->result();
        echo json_encode($result);
    }
    
    public function export_excel()
    {
        // Increase memory limit
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes
        
        // Get and format filter parameters
        $tanggal_awal = $this->input->get('tanggal_awal');
        $tanggal_akhir = $this->input->get('tanggal_akhir');
        $id_karyawan = $this->input->get('id_karyawan');
        $id_pekerjaan = $this->input->get('id_pekerjaan');
        
        // Get data with all required fields
        $this->db->select('pendapatan.tanggal, pendapatan.status,
            karyawan.nama_karyawan, 
            pekerjaan.nama_pekerjaan,
            pendapatan_detail.banyak,
            pendapatan_detail.harga_koperasi,
            pendapatan_detail.harga_karyawan,
            ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_koperasi) as total_koperasi,
            ROUND(pendapatan_detail.banyak * pendapatan_detail.harga_karyawan) as total_karyawan');
        $this->db->from('pendapatan');
        $this->db->join('karyawan', 'karyawan.id = pendapatan.id_karyawan');
        $this->db->join('pendapatan_detail', 'pendapatan_detail.id_pendapatan = pendapatan.id');
        $this->db->join('pekerjaan', 'pekerjaan.id = pendapatan_detail.id_pekerjaan');
        
        // Filter based on date range
        if ($tanggal_awal) {
            $this->db->where('pendapatan.tanggal >=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $this->db->where('pendapatan.tanggal <=', $tanggal_akhir);
        }
        
        // Filter by karyawan if specified
        if (!empty($id_karyawan)) {
            $this->db->where('karyawan.id', $id_karyawan);
        }
    
        // Filter by pekerjaan if specified
        if (!empty($id_pekerjaan)) {
            $this->db->where('pekerjaan.id', $id_pekerjaan);
        }
        
        $this->db->order_by('pendapatan.tanggal', 'DESC');
        $data['pendapatan'] = $this->db->get()->result();
        
        // Load the Excel view
        $this->load->view('laporan_pendapatan/excel', $data);
    }
}
