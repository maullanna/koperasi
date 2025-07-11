<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tabungan extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model(['Tabungan_model', 'Karyawan_model', 'Pengaturan_model']);
        $this->load->helper('money');
    }

    public function index() {
        $data['title'] = 'Tabungan';
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
        
        // Tambahkan pengecekan role
        if($this->session->userdata('role') == 'karyawan') {
            $id_karyawan = $this->session->userdata('id_karyawan');
            $data['tabungan'] = $this->Tabungan_model->get_by_karyawan($id_karyawan);
        } else {
            $data['tabungan'] = $this->Tabungan_model->get_all();
        }
        
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('tabungan/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah Tabungan';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('tabungan/form', $data);
            $this->load->view('templates/footer');
        } else {
            $jumlah = clean_money_input($this->input->post('jumlah'));
            $error = validate_money_input($jumlah, 'Jumlah Tabungan');
            
            if (!empty($error)) {
                $this->session->set_flashdata('error', $error);
                redirect('tabungan/tambah');
            }
            
            $data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jenis' => $this->input->post('jenis'),
                'jumlah' => $jumlah,
                'keterangan' => $this->input->post('keterangan')
            ];
            
            if($this->Tabungan_model->insert($data)) {
                $this->session->set_flashdata('success', 'Data tabungan berhasil ditambahkan');
                redirect('tabungan');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('tabungan/tambah');
            }
        }
    }

    public function lihat($id) {
        $data['tabungan'] = $this->Tabungan_model->get_by_id($id);
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        // Tambahkan pengecekan untuk karyawan
        if($this->session->userdata('role') == 'karyawan' && $data['tabungan']->id_karyawan != $this->session->userdata('id_karyawan')) {
            show_404();
        }
        
        if(empty($data['tabungan'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('tabungan/lihat', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['tabungan'] = $this->Tabungan_model->get_by_id($id);
        
        // Tambahkan pengecekan untuk karyawan
        if($this->session->userdata('role') == 'karyawan' && $data['tabungan']->id_karyawan != $this->session->userdata('id_karyawan')) {
            show_404();
        }
        
        $data['karyawan_list'] = $this->Karyawan_model->get_all();
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        if(empty($data['tabungan'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('tabungan/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update($id) {
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');

        if($this->form_validation->run() === FALSE) {
            $this->edit($id);
        } else {
            $jumlah = clean_money_input($this->input->post('jumlah'));
            $error = validate_money_input($jumlah, 'Jumlah Tabungan');
            
            if (!empty($error)) {
                $this->session->set_flashdata('error', $error);
                redirect('tabungan/edit/' . $id);
            }
            
            $data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jenis' => $this->input->post('jenis'),
                'jumlah' => $jumlah,
                'keterangan' => $this->input->post('keterangan')
            ];

            $this->Tabungan_model->update($id, $data);
            $this->session->set_flashdata('success', 'Data tabungan berhasil diperbarui');
            redirect('tabungan');
        }
    }

    public function hapus($id) {
        $tabungan = $this->Tabungan_model->get_by_id($id);
        
        // Tambahkan pengecekan untuk karyawan
        if($this->session->userdata('role') == 'karyawan' && $tabungan->id_karyawan != $this->session->userdata('id_karyawan')) {
            show_404();
        }
        
        if(empty($tabungan)) {
            show_404();
        }

        if($this->Tabungan_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data tabungan berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data tabungan');
        }

        redirect('tabungan');
    }

    public function import() {
        if(!$_FILES['excel_file']['name']) {
            $this->session->set_flashdata('error', 'Pilih file Excel terlebih dahulu');
            redirect('tabungan');
        }

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048;
        
        $this->load->library('upload', $config);
        
        if(!$this->upload->do_upload('excel_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('tabungan');
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
            $required_headers = ['No', 'Tanggal', 'Karyawan', 'Jenis', 'Jumlah', 'Keterangan'];
            $header_mismatch = array_diff($required_headers, $header);
            if (!empty($header_mismatch)) {
                throw new Exception('Kolom wajib tidak ditemukan atau tidak cocok: ' . implode(', ', $header_mismatch));
            }
            
            for($row = 2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                if(empty(array_filter($rowData[0]))) {
                    continue;
                }

                // Cek isi data mentah
                log_message('debug', "Row $row Data: " . json_encode($rowData[0]));

                // Ambil nilai cell tanggal secara eksplisit
                $cellTanggal = $sheet->getCell('B' . $row);
                $tanggalValue = $cellTanggal->getValue();
                log_message('debug', "Cell B$row (Tanggal) Value: $tanggalValue");

                // Cek apakah Excel menyimpan sebagai date atau string
                if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cellTanggal)) {
                    $tanggalDateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalValue);
                    $tanggal = $tanggalDateTime->format('Y-m-d');
                    log_message('debug', "Baris $row: Dikonversi dari Excel Date ke: $tanggal");
                } else {
                    $date = DateTime::createFromFormat('d/m/Y', $tanggalValue);
                    if ($date) {
                        $tanggal = $date->format('Y-m-d');
                        log_message('debug', "Baris $row: Dikonversi dari string ke: $tanggal");
                    } else {
                        $error_messages[] = "Format tanggal salah pada baris $row (gunakan DD/MM/YYYY), data: '$tanggalValue'";
                    }
                }
// Ambil nama karyawan dan simpan hasil pencariannya
$nama_karyawan = trim($rowData[0][2]);
$karyawan = $this->Karyawan_model->get_by_name_like($nama_karyawan);
if (!$karyawan) {
    $error_messages[] = "Karyawan dengan nama '$nama_karyawan' tidak ditemukan pada baris $row";
    log_message('error', "Baris $row: Karyawan '$nama_karyawan' tidak ditemukan di DB");
} else {
    log_message('debug', "Baris $row: Karyawan ditemukan ID: " . $karyawan->id);
}

// Ambil jenis, jumlah, dan keterangan
$jenis = strtolower(trim($rowData[0][3]));
$jumlah = str_replace(['Rp', '.', ','], '', trim($rowData[0][4]));
$keterangan = trim($rowData[0][5]);

// Validasi tanggal (gunakan hasil konversi dari cell sebelumnya)
if(empty($tanggal)) {
    $error_messages[] = "Tanggal tidak terbaca atau format tidak sesuai pada baris $row";
}

// Validasi jenis
if(!in_array($jenis, ['setor', 'tarik'])) {
    $error_messages[] = "Jenis transaksi pada baris $row harus 'setor' atau 'tarik', data: '$jenis'";
}

// Validasi jumlah
if(!is_numeric($jumlah) || $jumlah <= 0) {
    $error_messages[] = "Jumlah pada baris $row harus angka positif, data: '$jumlah'";
}

// Tidak perlu lagi pemanggilan get_by_name_like di bawah ini
// if(!$karyawan) {
//     $error_messages[] = "Karyawan dengan nama '$nama_karyawan' tidak ditemukan pada baris $row";
// }

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

                    $data = [
                        'tanggal' => $tanggal,
                        'id_karyawan' => $karyawan->id,
                        'jenis' => $jenis,
                        'jumlah' => $jumlah,
                        'keterangan' => $keterangan
                    ];

                    if($this->Tabungan_model->insert($data)) {
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
        
        redirect('tabungan');
    }
    
    public function export() {
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('tabungan');
        }
    
        require FCPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Jumlah Tabungan');
        $sheet->setCellValue('E1', 'Jenis');
        $sheet->setCellValue('F1', 'Keterangan');
        
        $tabungan = $this->Tabungan_model->get_all();
        
        $row = 2;
        foreach($tabungan as $index => $data) {
            $sheet->setCellValue('A' . $row, ($index + 1));
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($data->tanggal)));
            $sheet->setCellValue('C' . $row, $data->nama_karyawan);
            $sheet->setCellValue('D' . $row, $data->jumlah);
            $sheet->setCellValue('E' . $row, $data->jenis);
            $sheet->setCellValue('F' . $row, $data->keterangan);
            $row++;
        }
        
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
        
        $filename = 'Data_Tabungan_'.date('Y-m-d_H-i-s').'.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
