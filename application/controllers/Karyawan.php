<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Karyawan extends CI_Controller {
    public $benchmark;
    public $hooks;
    public $config;
    public $log;
    public $utf8;
    public $uri;
    public $exceptions;
    public $router;
    public $output;
    public $security;
    public $input;
    public $lang;
    public $load;
    public $db;
    public $session;
    
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('Karyawan_model');
    }

    public function index() {
        $data['title'] = 'Karyawan';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('karyawan/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah Karyawan';
        
        $this->form_validation->set_rules('nip', 'NIP', 'required|is_unique[karyawan.nip]');
        $this->form_validation->set_rules('nama_karyawan', 'Nama Karyawan', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[karyawan.username]', [
            'is_unique' => 'This username is already taken. Please choose another one.'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('karyawan/form', $data);
            $this->load->view('templates/footer');
        } else {
            $karyawan = [
                'nip' => $this->input->post('nip'),
                'nama_karyawan' => $this->input->post('nama_karyawan'),
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'no_hp' => $this->input->post('no_hp'),
                'alamat' => $this->input->post('alamat'),
                'role' => $this->input->post('role')
            ];
            
            if($this->Karyawan_model->insert($karyawan)) {
                $this->session->set_flashdata('success', 'Data karyawan berhasil ditambahkan');
                redirect('karyawan');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('karyawan/tambah');
            }
        }
    }

    public function lihat($id) {
        $data['karyawan'] = $this->Karyawan_model->get_by_id($id);
        
        if(empty($data['karyawan'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('karyawan/lihat', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['karyawan'] = $this->Karyawan_model->get_by_id($id);
        
        if(empty($data['karyawan'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('karyawan/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update($id) {
        $this->form_validation->set_rules('nip', 'NIP', 'required');
        $this->form_validation->set_rules('nama_karyawan', 'Nama Karyawan', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');

        if($this->form_validation->run() === FALSE) {
            $this->edit($id);
        } else {
            $data = array(
                'nip' => $this->input->post('nip'),
                'nama_karyawan' => $this->input->post('nama_karyawan'),
                'username' => $this->input->post('username'),
                'no_hp' => $this->input->post('no_hp'),
                'alamat' => $this->input->post('alamat'), // Pastikan field ini ada
                'role' => $this->input->post('role')
            );

            $this->Karyawan_model->update($id, $data);
            $this->session->set_flashdata('success', 'Data karyawan berhasil diperbarui');
            redirect('karyawan');
        }
    }

    public function hapus($id) {
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        $karyawan = $this->Karyawan_model->get_by_id($id);
        
        if(empty($karyawan)) {
            show_404();
        }

        if($this->Karyawan_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data karyawan berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data karyawan');
        }

        redirect('karyawan');
    }

    public function import() {
        // Load PhpSpreadsheet
        require FCPATH . 'vendor/autoload.php';
        
        // Konfigurasi upload
        $config['upload_path'] = './uploads/temp/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048; // 2MB
        
        // Buat direktori jika belum ada
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('excel_file')) {
            $this->session->set_flashdata('error', 'Error: ' . $this->upload->display_errors('', ''));
            redirect('karyawan');
        }
        
        $upload_data = $this->upload->data();
        $file_path = './uploads/temp/' . $upload_data['file_name'];
        
        try {
            $spreadsheet = IOFactory::load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Inisialisasi array untuk tracking NIP dan username yang sudah ada
            $existing_nips = [];
            $existing_usernames = [];
            
            // Ambil header (baris pertama)
            $headers = array_map('strtolower', $rows[0]);
            
            // Definisikan mapping kolom yang dibutuhkan
            $required_columns = ['nip', 'nama karyawan', 'username', 'password', 'role'];
            $optional_columns = ['no hp', 'alamat'];
            
            // Validasi header yang diperlukan
            $missing_columns = [];
            foreach ($required_columns as $col) {
                if (!in_array($col, $headers)) {
                    $missing_columns[] = $col;
                }
            }
            
            if (!empty($missing_columns)) {
                $this->session->set_flashdata('import_message', "
                    <div class='import-container'>
                        <div class='import-error'>
                            <h5><i class='fa fa-times-circle'></i> Kesalahan Format Excel</h5>
                            <p>Kolom wajib tidak ditemukan: " . implode(', ', $missing_columns) . "</p>
                        </div>
                    </div>");
                redirect('karyawan');
                return;
            }
            
            // Buat mapping indeks kolom
            $column_indexes = array_flip($headers);
            
            // Hapus header
            array_shift($rows);
            
            $success_count = 0;
            $errors = [];
            $duplicates = [];
            
            foreach ($rows as $index => $row) {
                $row_number = $index + 2;
                
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Ambil data berdasarkan header
                $nip = trim($row[$column_indexes['nip']]);
                $nama_karyawan = trim($row[$column_indexes['nama karyawan']]);
                $username = trim($row[$column_indexes['username']]);
                $password = trim($row[$column_indexes['password']]);
                $role = trim(strtolower($row[$column_indexes['role']]));
                
                // Ambil data opsional
                $no_hp = isset($column_indexes['no hp']) ? trim($row[$column_indexes['no hp']]) : '';
                $alamat = isset($column_indexes['alamat']) ? trim($row[$column_indexes['alamat']]) : '';
                
                // Validasi data wajib
                if (empty($nip) || empty($nama_karyawan) || empty($username) || empty($password) || empty($role)) {
                    $errors[] = "Baris $row_number: Data wajib tidak boleh kosong";
                    continue;
                }
                
                // Cek duplikasi NIP dalam file Excel
                if (in_array($nip, $existing_nips)) {
                    $duplicates[] = "Baris $row_number: NIP '$nip' sudah ada di baris sebelumnya";
                    continue;
                }
                
                // Cek duplikasi username dalam file Excel
                if (in_array($username, $existing_usernames)) {
                    $duplicates[] = "Baris $row_number: Username '$username' sudah ada di baris sebelumnya";
                    continue;
                }
                
                // Cek duplikasi di database
                $existing_nip = $this->db->get_where('karyawan', ['nip' => $nip])->row();
                $existing_username = $this->db->get_where('karyawan', ['username' => $username])->row();
                
                if ($existing_nip) {
                    $duplicates[] = "Baris $row_number: NIP '$nip' sudah terdaftar di database";
                    continue;
                }
                
                if ($existing_username) {
                    $duplicates[] = "Baris $row_number: Username '$username' sudah terdaftar di database";
                    continue;
                }
                
                // Jika lolos semua validasi, simpan data
                $data = [
                    'nip' => $nip,
                    'nama_karyawan' => $nama_karyawan,
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'no_hp' => $no_hp,
                    'alamat' => $alamat,
                    'role' => $role
                ];
                
                if ($this->db->insert('karyawan', $data)) {
                    $success_count++;
                    $existing_nips[] = $nip;
                    $existing_usernames[] = $username;
                } else {
                    $errors[] = "Baris $row_number: Gagal menyimpan ke database";
                }
            }
            
            // Siapkan pesan hasil import
            $messages = [];
            
            if ($success_count > 0) {
                $messages[] = "<div class='import-success'>
                    <h5><i class='fa fa-check-circle'></i> Import Berhasil</h5>
                    <p>Berhasil mengimport $success_count data karyawan</p>
                </div>";
            }
            
            if (!empty($duplicates)) {
                $messages[] = "<div class='import-duplicate'>
                    <h5><i class='fa fa-exclamation-triangle'></i> Data Duplikat</h5>
                    <ul><li>" . implode("</li><li>", $duplicates) . "</li></ul>
                </div>";
            }
            
            if (!empty($errors)) {
                $messages[] = "<div class='import-error'>
                    <h5><i class='fa fa-times-circle'></i> Error Import</h5>
                    <ul><li>" . implode("</li><li>", $errors) . "</li></ul>
                </div>";
            }
            
            if (!empty($messages)) {
                $this->session->set_flashdata('import_message', "<div class='import-container'>" . implode("", $messages) . "</div>");
            }
            
        } catch (Exception $e) {
            $this->session->set_flashdata('import_message', "
                <div class='import-container'>
                    <div class='import-error'>
                        <h5><i class='fa fa-times-circle'></i> Error</h5>
                        <p>" . $e->getMessage() . "</p>
                    </div>
                </div>");
        }
        
        // Hapus file temporary
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        redirect('karyawan');
    }

    public function export() {
        // Load library PhpSpreadsheet
        require FCPATH . 'vendor/autoload.php';
        
        // Buat objek spreadsheet baru
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIP');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Username');
        $sheet->setCellValue('E1', 'No HP');
        $sheet->setCellValue('F1', 'Alamat');
        $sheet->setCellValue('G1', 'Role');
        
        // Ambil semua data karyawan
        $karyawan = $this->Karyawan_model->get_all();
        
        // Tulis data ke excel
        $row = 2;
        foreach($karyawan as $index => $data) {
            $sheet->setCellValue('A' . $row, ($index + 1));
            $sheet->setCellValue('B' . $row, $data->nip);
            $sheet->setCellValue('C' . $row, $data->nama_karyawan);
            $sheet->setCellValue('D' . $row, $data->username);
            $sheet->setCellValue('E' . $row, $data->no_hp);
            $sheet->setCellValue('F' . $row, $data->alamat);
            $sheet->setCellValue('G' . $row, ucfirst($data->role));
            $row++;
        }
        
        // Auto size kolom
        foreach(range('A','G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($styleArray);
        
        // Set style konten
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A2:G'.($row-1))->applyFromArray($styleArray);
        
        // Set nama file
        $filename = 'Data_Karyawan_'.date('Y-m-d_H-i-s').'.xlsx';
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        // Export ke Excel 2007 (.xlsx)
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}