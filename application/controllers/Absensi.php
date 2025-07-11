<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model(['Absensi_model', 'Karyawan_model']);
        $this->load->helper('access','money'); // Tambahkan helper access
    }

    public function index() {
        $data['title'] = 'Absensi';
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
        
        // Cek role user
        $role = $this->session->userdata('role');
        $id_karyawan = $this->session->userdata('id_karyawan');
        
        // Jika role karyawan, hanya tampilkan data miliknya
        if ($role == 'karyawan') {
            $data['absensi'] = $this->Absensi_model->get_by_karyawan($id_karyawan);
        } else {
            // Untuk admin dan owner, tampilkan semua data
            $data['absensi'] = $this->Absensi_model->get_all();
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('absensi/index', $data);
        $this->load->view('templates/footer');
    }

    /*public function tambah() {
        // Gunakan helper untuk membatasi akses
        admin_only();
        
        $data['title'] = 'Tambah Absensi';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('absensi/form', $data);
            $this->load->view('templates/footer');
        } else {
            $absensi = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jam_masuk' => $this->input->post('jam_masuk'),
                'jam_pulang' => $this->input->post('jam_pulang'),
                'status' => $this->input->post('status'),
                'keterangan' => $this->input->post('keterangan')
            ];
            
            if($this->Absensi_model->insert($absensi)) {
                $this->session->set_flashdata('success', 'Data absensi berhasil ditambahkan');
                redirect('absensi');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('absensi/tambah');
            }
        }
    }*/

    public function lihat($id) {
        $data['absensi'] = $this->Absensi_model->get_by_id($id);
        
        if(empty($data['absensi'])) {
            show_404();
        }
        
        // Cek apakah karyawan mencoba melihat data karyawan lain
        if(is_karyawan() && $data['absensi']->id_karyawan != $this->session->userdata('id_karyawan')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk melihat data ini');
            redirect('absensi');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('absensi/lihat', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['absensi'] = $this->Absensi_model->get_by_id($id);
        
        if(empty($data['absensi'])) {
            show_404();
        }
        
        // Cek apakah karyawan mencoba mengedit data karyawan lain
        if(is_karyawan() && $data['absensi']->id_karyawan != $this->session->userdata('id_karyawan')) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk mengedit data ini');
            redirect('absensi');
        }
        
        $data['karyawan_list'] = $this->Karyawan_model->get_all();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('absensi/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update($id) {
        // Tambahkan pengecekan akses admin
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('absensi');
        }

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->edit($id);
        } else {
            // Ambil data dari form
            $data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jam_masuk' => $this->input->post('jam_masuk') ? $this->input->post('jam_masuk') . ':00' : null,
                'jam_pulang' => $this->input->post('jam_pulang') ? $this->input->post('jam_pulang') . ':00' : null,
                'status' => $this->input->post('status'),
                'keterangan' => $this->input->post('keterangan')
            ];

            if ($this->Absensi_model->update($id, $data)) {
                $this->session->set_flashdata('success', 'Data absensi berhasil diperbarui');
                redirect('absensi');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data absensi');
                redirect('absensi/edit/' . $id);
            }
        }
    }

    public function hapus($id) {
        $absensi = $this->Absensi_model->get_by_id($id);
        
        if(empty($absensi)) {
            show_404();
        }
        
        // Cek apakah karyawan mencoba menghapus data
        if(is_karyawan()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk menghapus data');
            redirect('absensi');
        }

        if($this->Absensi_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data absensi berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data absensi');
        }

        redirect('absensi');
    }

    public function import() {
        admin_only();
    
        if(!$_FILES['excel_file']['name']) {
            $this->session->set_flashdata('error', 'Pilih file Excel terlebih dahulu');
            redirect('absensi');
        }
    
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048;
    
        $this->load->library('upload', $config);
    
        if(!$this->upload->do_upload('excel_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('absensi');
        }
    
        $file_data = $this->upload->data();
        $file_path = './uploads/' . $file_data['file_name'];
    
        require_once FCPATH . 'vendor/autoload.php';
    
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
    
            $success = 0;
            $failed = 0;
            $errors = [];
    
            // Validasi header
            $header = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE)[0];
            $required_headers = ['No', 'Tanggal', 'Karyawan', 'Jam Masuk', 'Jam Keluar', 'Keterangan'];
            $header_mismatch = array_diff($required_headers, $header);
            if (!empty($header_mismatch)) {
                throw new Exception('Kolom wajib tidak ditemukan atau tidak cocok: ' . implode(', ', $header_mismatch));
            }
    
            for($row = 2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                if(empty(array_filter($rowData[0]))) {
                    continue;
                }
    
                $cellTanggal = $sheet->getCell('B' . $row);
                $tanggalValue = $cellTanggal->getValue();
                $tanggal = '';
                
                if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cellTanggal)) {
                    $tanggalDateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalValue);
                    $tanggal = $tanggalDateTime->format('d/m/Y'); // ubah ke format yang kamu minta
                } else {
                    $tanggal = trim($tanggalValue);
                }
                                $nama_karyawan = trim($rowData[0][2]);
                $jam_masuk = trim($rowData[0][3]);
                $jam_pulang = trim($rowData[0][4]);
                $keterangan = trim($rowData[0][5]);
                $error_messages = [];
    
                // Validasi tanggal (format: DD/MM/YYYY)
                if(!preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $tanggal)) {
                    $error_messages[] = "Format tanggal salah pada baris $row (gunakan DD/MM/YYYY), data: '$tanggal'";
                }
    
                // Validasi jam masuk dan jam keluar (format: HH:mm)
                if(!empty($jam_masuk)) {
                    // Bersihkan format jam dari karakter non-angka kecuali :
                    $jam_masuk = preg_replace('/[^0-9:]/', '', $jam_masuk);
                    // Cek apakah bisa dikonversi ke format waktu yang valid
                    $timestamp = strtotime($jam_masuk);
                    if($timestamp === false) {
                        $error_messages[] = "Format jam masuk salah pada baris $row (gunakan HH:mm), data: '$jam_masuk'";
                    } else {
                        // Standardisasi format
                        $jam_masuk = date('H:i', $timestamp);
                    }
                }
                
                if(!empty($jam_pulang)) {
                    // Bersihkan format jam dari karakter non-angka kecuali :
                    $jam_pulang = preg_replace('/[^0-9:]/', '', $jam_pulang);
                    // Cek apakah bisa dikonversi ke format waktu yang valid
                    $timestamp = strtotime($jam_pulang);
                    if($timestamp === false) {
                        $error_messages[] = "Format jam keluar salah pada baris $row (gunakan HH:mm), data: '$jam_pulang'";
                    } else {
                        // Standardisasi format
                        $jam_pulang = date('H:i', $timestamp);
                    }
                }
    
                // Validasi karyawan
                $karyawan = $this->Karyawan_model->get_by_name_like($nama_karyawan);
                if(!$karyawan) {
                    $error_messages[] = "Karyawan dengan nama '$nama_karyawan' tidak ditemukan pada baris $row";
                }
    
                if(!empty($error_messages)) {
                    $failed++;
                    $errors[] = "Baris $row: " . implode(", ", $error_messages);
                    continue;
                }
    
                try {
                    // Konversi format tanggal
                    $date = DateTime::createFromFormat('d/m/Y', $tanggal);
                    if($date) {
                        $tanggal = $date->format('Y-m-d');
                    }
    
                    // Konversi jam ke format time SQL (HH:MM:SS)
                    $jam_masuk_sql = !empty($jam_masuk) ? $jam_masuk . ':00' : null;
                    $jam_pulang_sql = !empty($jam_pulang) ? $jam_pulang . ':00' : null;
    
                    $data = [
                        'tanggal' => $tanggal,
                        'id_karyawan' => $karyawan->id,
                        'jam_masuk' => $jam_masuk_sql,
                        'jam_pulang' => $jam_pulang_sql,
                        'keterangan' => $keterangan,
                        'status' => !empty($jam_masuk) ? 'hadir' : 'hadir' // enum hanya 'hadir'
                    ];
    
                    if($this->Absensi_model->insert($data)) {
                        $success++;
                    } else {
                        throw new Exception("Gagal menyimpan ke database");
                    }
                } catch(Exception $e) {
                    $failed++;
                    $errors[] = "Baris $row: " . $e->getMessage();
                }
            }
    
            unlink($file_path);
    
            if($failed > 0) {
                $error_message = "Terdapat $failed data yang gagal diimport:\n" . implode("\n", $errors);
                $this->session->set_flashdata('error', $error_message);
            }
    
            if($success > 0) {
                $this->session->set_flashdata('success', "$success data berhasil diimport!");
            }
    
        } catch(Exception $e) {
            unlink($file_path);
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
        }
    
        redirect('absensi');
    }
    
    public function export() {
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('absensi');
        }
    
        require FCPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Sesuaikan header dengan format yang diminta
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Karyawan');
        $sheet->setCellValue('D1', 'Jam Masuk');
        $sheet->setCellValue('E1', 'Jam Keluar');
        $sheet->setCellValue('F1', 'Keterangan');
        
        $absensi = $this->Absensi_model->get_all();
        
        $row = 2;
        foreach($absensi as $index => $data) {
            $sheet->setCellValue('A' . $row, ($index + 1));
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($data->tanggal)));
            $sheet->setCellValue('C' . $row, $data->nama_karyawan);
            $sheet->setCellValue('D' . $row, $data->jam_masuk);
            $sheet->setCellValue('E' . $row, $data->jam_pulang);
            $sheet->setCellValue('F' . $row, $data->keterangan);
            $row++;
        }
        
        // Sesuaikan range kolom untuk styling
        foreach(range('A','F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $styleArray = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($styleArray);
        
        $styleArray = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A2:F'.($row-1))->applyFromArray($styleArray);
        
        $filename = 'Data_Absensi_'.date('Y-m-d_H-i-s').'.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}