<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_slip_gaji extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model(['SlipGaji_model', 'Karyawan_model', 'Pengaturan_model']);
        $this->load->helper(['money', 'access']); // Tambahkan helper money dan access
    }

    public function index() {
        // Tambahkan pengecekan akses admin
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('dashboard');
        }

        $data['title'] = 'Laporan Slip Gaji';
        $data['kategori'] = 'laporan';
    
        if ($this->input->post()) {
            $tanggal_awal = $this->input->post('tanggal_awal');
            $tanggal_akhir = $this->input->post('tanggal_akhir');
            $id_karyawan = $this->input->post('id_karyawan');
    
            $query = http_build_query([
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir,
                'id_karyawan' => $id_karyawan
            ]);
            redirect('laporan_slip_gaji?' . $query);
        }
    
        // Set default tanggal untuk 1 bulan terakhir
        $data['tanggal_awal'] = $this->input->get('tanggal_awal') ?? date('Y-m-d', strtotime('-1 month'));
        $data['tanggal_akhir'] = $this->input->get('tanggal_akhir') ?? date('Y-m-d');
        $data['id_karyawan'] = $this->input->get('id_karyawan');
    
        $data['karyawan_list'] = $this->Karyawan_model->get_all();
    
        $this->db->select('slip_gaji.*, karyawan.nama_karyawan, 
            ROUND(slip_gaji.total_pendapatan) as total_pendapatan,
            ROUND(slip_gaji.total_kasbon) as total_kasbon,
            ROUND(slip_gaji.total_tabungan) as total_tabungan,
            ROUND((slip_gaji.total_pendapatan * 
                (SELECT pemasukan_bpjs FROM pengaturan LIMIT 1) / 100)) as pemasukan_bpjs,
            ROUND(slip_gaji.uang_makan) as uang_makan,
            ROUND(slip_gaji.gaji_bersih) as gaji_bersih,
            DATE_FORMAT(slip_gaji.bulan, "%d/%m/%Y") as tanggal_gaji');
        $this->db->from('slip_gaji');
        $this->db->join('karyawan', 'karyawan.id = slip_gaji.id_karyawan');
    
        if($this->input->get()) {
            if(!empty($data['tanggal_awal']) && !empty($data['tanggal_akhir'])) {
                $this->db->where('slip_gaji.bulan >=', $data['tanggal_awal']);
                $this->db->where('slip_gaji.bulan <=', $data['tanggal_akhir']);
            }
    
            if(!empty($data['id_karyawan'])) {
                $this->db->where('slip_gaji.id_karyawan', $data['id_karyawan']);
            }
        }
    
        $this->db->order_by('slip_gaji.bulan', 'DESC');
        $data['slip_gaji'] = $this->db->get()->result();
    
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('laporan_slip_gaji/index', $data);
        $this->load->view('templates/footer');
    }

    public function export_pdf() {
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('laporan_slip_gaji');
        }

        // Get data dari form atau default ke bulan ini
        $tanggal_awal = $this->input->get('tanggal_awal');
        $tanggal_akhir = $this->input->get('tanggal_akhir');
        $id_karyawan = $this->input->get('id_karyawan');
    
        // Jika tanggal tidak diset, gunakan bulan ini
        if(empty($tanggal_awal)) {
            $tanggal_awal = date('Y-m-01'); // Tanggal 1 bulan ini
        }
        if(empty($tanggal_akhir)) {
            $tanggal_akhir = date('Y-m-t'); // Tanggal terakhir bulan ini
        }
    
        // Query untuk mengambil data slip gaji
        $this->db->select('slip_gaji.*, karyawan.nama_karyawan');
        $this->db->from('slip_gaji');
        $this->db->join('karyawan', 'karyawan.id = slip_gaji.id_karyawan');
        
        if(!empty($tanggal_awal) && !empty($tanggal_akhir)) {
            $this->db->where('slip_gaji.bulan >=', $tanggal_awal);
            $this->db->where('slip_gaji.bulan <=', $tanggal_akhir);
        }
        
        if(!empty($id_karyawan)) {
            $this->db->where('slip_gaji.id_karyawan', $id_karyawan);
        }

        $data['slip_gaji'] = $this->db->get()->result();

        // Hitung total gaji
        $total_gaji = 0;
        foreach($data['slip_gaji'] as $slip) {
            $total_gaji += $slip->gaji_bersih;
        }
        $data['total_gaji'] = $total_gaji;

        // Kirim tanggal untuk periode di view
        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;

        // Load library DOMPDF
        require_once(FCPATH . 'vendor/autoload.php');
        $dompdf = new \Dompdf\Dompdf();
        
        // Load view untuk PDF
        $html = $this->load->view('laporan_slip_gaji/pdf', $data, TRUE);
        
        // Generate PDF
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'landscape');
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream("laporan_slip_gaji_" . date('d-m-Y') . ".pdf", array('Attachment' => 0));
    }

    public function export_excel() {
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('laporan_slip_gaji');
        }
    
        // Get data dari form atau default ke bulan ini
        $tanggal_awal = $this->input->get('tanggal_awal') ?? date('Y-m-01');
        $tanggal_akhir = $this->input->get('tanggal_akhir') ?? date('Y-m-t');
        $id_karyawan = $this->input->get('id_karyawan');
    
        // Query untuk mengambil data slip gaji
        $this->db->select('slip_gaji.*, karyawan.nama_karyawan, 
            ROUND(slip_gaji.total_pendapatan) as total_pendapatan,
            ROUND(slip_gaji.total_kasbon) as total_kasbon,
            ROUND(slip_gaji.total_tabungan) as total_tabungan,
            ROUND((slip_gaji.total_pendapatan * 
                (SELECT pemasukan_bpjs FROM pengaturan LIMIT 1) / 100)) as pemasukan_bpjs,
            ROUND(slip_gaji.uang_makan) as uang_makan,
            ROUND(slip_gaji.gaji_bersih) as gaji_bersih');
        $this->db->from('slip_gaji');
        $this->db->join('karyawan', 'karyawan.id = slip_gaji.id_karyawan');
    
        if(!empty($tanggal_awal) && !empty($tanggal_akhir)) {
            $this->db->where('slip_gaji.bulan >=', $tanggal_awal);
            $this->db->where('slip_gaji.bulan <=', $tanggal_akhir);
        }
    
        if(!empty($id_karyawan)) {
            $this->db->where('slip_gaji.id_karyawan', $id_karyawan);
        }
    
        $this->db->order_by('slip_gaji.bulan', 'DESC');
        $slip_gaji = $this->db->get()->result();
    
        // Load view untuk generate Excel
        $data['slip_gaji'] = $slip_gaji;
        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;
        
        $this->load->view('laporan_slip_gaji/excel', $data);
    }
}