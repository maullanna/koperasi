<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Pekerjaan extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('Pekerjaan_model');
        $this->load->helper('money');
    }

    public function index() {
        $data['title'] = 'Pekerjaan';
        $data['pekerjaan'] = $this->Pekerjaan_model->get_all();
        $data['contoh_excel'] = [
            ['No', 'Nama Pekerjaan', 'Harga Koperasi', 'Harga Karyawan', 'Status'],
            ['1', 'Cuci Mobil', '50000', '40000', 'aktif'],
            ['2', 'Cuci Motor', '25000', '20000', 'aktif']
        ];
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pekerjaan/index', $data);
        $this->load->view('templates/footer');
    }

    public function import() {
        $config['upload_path'] = './uploads/temp/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048;
        
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('excel_file')) {
            $this->session->set_flashdata('error', 'Error: ' . $this->upload->display_errors('', ''));
            redirect('pekerjaan');
        }
        
        $upload_data = $this->upload->data();
        $file_path = './uploads/temp/' . $upload_data['file_name'];
        
        try {
            $spreadsheet = IOFactory::load($file_path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            
            // Ambil dan normalisasi header
            $header_row = array_shift($rows);
            $normalized_header = array_map(function ($val) {
                return strtolower(trim(preg_replace('/\s+/', ' ', $val)));
            }, $header_row);
        
            $header_map = array_flip($normalized_header);
        
            // Cek kolom wajib
            $required_columns = ['nama pekerjaan', 'harga koperasi', 'harga karyawan'];
            $missing_columns = array_diff($required_columns, array_keys($header_map));
        
            if (!empty($missing_columns)) {
                unlink($file_path);
                $this->session->set_flashdata('error', 'Kolom wajib tidak ditemukan: ' . implode(', ', $missing_columns));
                redirect('pekerjaan');
            }
        
            $success_count = 0;
            $errors = [];
            $duplicates = [];
        
            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) continue;
    
                $row_number = $index + 2;
    
                $nama_pekerjaan = trim($row[$header_row_key = array_search('nama pekerjaan', array_map('strtolower', $header_row))] ?? '');
                $harga_koperasi = $row[$header_map['harga koperasi']] ?? '';
                $harga_karyawan = $row[$header_map['harga karyawan']] ?? '';
                $status = isset($header_map['status']) ? strtolower(trim($row[$header_map['status']])) : 'aktif';
    
                if (empty($nama_pekerjaan)) {
                    $errors[] = "Baris $row_number: Nama Pekerjaan tidak boleh kosong";
                    continue;
                }
    
                if ($harga_koperasi === '' || !is_numeric(str_replace(['Rp', '.', ',', ' '], '', $harga_koperasi))) {
                    $errors[] = "Baris $row_number: Harga Koperasi tidak valid";
                    continue;
                }
    
                if ($harga_karyawan === '' || !is_numeric(str_replace(['Rp', '.', ',', ' '], '', $harga_karyawan))) {
                    $errors[] = "Baris $row_number: Harga Karyawan tidak valid";
                    continue;
                }
    
                $harga_koperasi = (int)str_replace(['Rp', '.', ',', ' '], '', $harga_koperasi);
                $harga_karyawan = (int)str_replace(['Rp', '.', ',', ' '], '', $harga_karyawan);
    
                if (!in_array($status, ['aktif', 'tidak aktif'])) {
                    $status = 'aktif';
                }
    
                $data = [
                    'nama_pekerjaan' => $nama_pekerjaan,
                    'harga_koperasi' => $harga_koperasi,
                    'harga_karyawan' => $harga_karyawan,
                    'status' => $status
                ];
    
                $existing = $this->Pekerjaan_model->get_by_name($nama_pekerjaan);
                if ($existing) {
                    if ($this->Pekerjaan_model->update($existing->id, $data)) {
                        $duplicates[] = "Pekerjaan '$nama_pekerjaan' diperbarui (baris $row_number)";
                    } else {
                        $errors[] = "Baris $row_number: Gagal update '$nama_pekerjaan'";
                    }
                } else {
                    if ($this->Pekerjaan_model->insert($data)) {
                        $success_count++;
                    } else {
                        $errors[] = "Baris $row_number: Gagal insert data";
                    }
                }
            }
        
            unlink($file_path);
        
            if ($success_count > 0) {
                $this->session->set_flashdata('success', "$success_count data berhasil ditambahkan.");
            }
            
            if (!empty($duplicates)) {
                $this->session->set_flashdata('info', implode('<br>', $duplicates));
            }
            
            if (!empty($errors)) {
                $this->session->set_flashdata('error', "Beberapa data gagal diimport:<br>" . implode('<br>', $errors));
            }
            
        } catch (Exception $e) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
        }
        
        redirect('pekerjaan');
    }
    

    public function tambah() {
        $data['title'] = 'Tambah Pekerjaan';
        

        $this->form_validation->set_rules('nama_pekerjaan', 'Nama Pekerjaan', 'required');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('pekerjaan/form', $data);
            $this->load->view('templates/footer');
        } else {
            // Validasi input uang
            $harga_koperasi = clean_money_input($this->input->post('harga_koperasi'));
            $harga_karyawan = clean_money_input($this->input->post('harga_karyawan'));
            
            $error_koperasi = validate_money_input($harga_koperasi, 'Harga Koperasi');
            $error_karyawan = validate_money_input($harga_karyawan, 'Harga Karyawan');
            
            if (!empty($error_koperasi) || !empty($error_karyawan)) {
                $this->session->set_flashdata('error', $error_koperasi . "\n" . $error_karyawan);
                redirect('pekerjaan/tambah');
            }
            
            $pekerjaan = [
                'nama_pekerjaan' => $this->input->post('nama_pekerjaan'),
                'harga_koperasi' => $harga_koperasi,
                'harga_karyawan' => $harga_karyawan,
                'status' => 'aktif'
            ];
            
            if($this->Pekerjaan_model->insert($pekerjaan)) {
                $this->session->set_flashdata('success', 'Data pekerjaan berhasil ditambahkan');
                redirect('pekerjaan');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('pekerjaan/tambah');
            }
        }
    }

    // Method to view job details
    public function lihat($id) {
        $data['title'] = 'Detail Pekerjaan';
        $data['pekerjaan'] = $this->Pekerjaan_model->get_by_id($id);

        if(empty($data['pekerjaan'])) {
            $this->session->set_flashdata('error', 'Data pekerjaan tidak ditemukan');
            redirect('pekerjaan');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pekerjaan/lihat', $data);
        $this->load->view('templates/footer');
    }

    // Method to edit job details
    public function edit($id) {
        $data['title'] = 'Edit Pekerjaan';
        $data['pekerjaan'] = $this->Pekerjaan_model->get_by_id($id);

        if(empty($data['pekerjaan'])) {
            $this->session->set_flashdata('error', 'Data pekerjaan tidak ditemukan');
            redirect('pekerjaan');
        }

        $this->form_validation->set_rules('nama_pekerjaan', 'Nama Pekerjaan', 'required');
        $this->form_validation->set_rules('harga_koperasi', 'Harga Koperasi', 'required|numeric');
        $this->form_validation->set_rules('harga_karyawan', 'Harga Karyawan', 'required|numeric');

        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('pekerjaan/edit', $data);
            $this->load->view('templates/footer');
        } else {
            // Validasi input uang
            $harga_koperasi = clean_money_input($this->input->post('harga_koperasi'));
            $harga_karyawan = clean_money_input($this->input->post('harga_karyawan'));
            
            $error_koperasi = validate_money_input($harga_koperasi, 'Harga Koperasi');
            $error_karyawan = validate_money_input($harga_karyawan, 'Harga Karyawan');
            
            if (!empty($error_koperasi) || !empty($error_karyawan)) {
                $this->session->set_flashdata('error', $error_koperasi . ' ' . $error_karyawan);
                redirect('pekerjaan/edit/' . $id);
            }
            
            $data = [
                'nama_pekerjaan' => $this->input->post('nama_pekerjaan'),
                'harga_koperasi' => $harga_koperasi,
                'harga_karyawan' => $harga_karyawan,
                'status' => $this->input->post('status')
            ];

            $this->Pekerjaan_model->update($id, $data);
            $this->session->set_flashdata('success', 'Data pekerjaan berhasil diperbarui');
            redirect('pekerjaan');
        }
    }

    // Method to delete a job
    public function hapus($id) {
        if($this->Pekerjaan_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data pekerjaan berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menghapus data');
        }
        redirect('pekerjaan');
    }
    
    public function export() {
        // Tambahkan pengecekan akses admin
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('pekerjaan');
        }

        require FCPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Pekerjaan');
        $sheet->setCellValue('C1', 'Harga Koperasi');
        $sheet->setCellValue('D1', 'Harga Karyawan');
        $sheet->setCellValue('E1', 'Status');
        
        // Ambil semua data pekerjaan
        $pekerjaan = $this->Pekerjaan_model->get_all();
        
        $row = 2;
        foreach($pekerjaan as $index => $data) {
            $sheet->setCellValue('A' . $row, ($index + 1));
            $sheet->setCellValue('B' . $row, $data->nama_pekerjaan);
            $sheet->setCellValue('C' . $row, $data->harga_koperasi);
            $sheet->setCellValue('D' . $row, $data->harga_karyawan);
            $sheet->setCellValue('E' . $row, $data->status);
            $row++;
        }
        
        // Auto size kolom
        foreach(range('A','E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set style header
        $styleArray = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($styleArray);
        
        // Set style konten
        $styleArray = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A2:E'.($row-1))->applyFromArray($styleArray);
        
        $filename = 'Data_Pekerjaan_'.date('Y-m-d_H-i-s').'.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}