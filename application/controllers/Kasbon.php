<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class Kasbon extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model(['Kasbon_model', 'Karyawan_model']);
        $this->load->helper('money'); // Tambahkan helper money
    }

    public function index() {
        $data['title'] = 'Kasbon';
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
        
        // Tambahkan pengecekan role
        if($this->session->userdata('role') == 'karyawan') {
            $id_karyawan = $this->session->userdata('id_karyawan');
            $data['kasbon'] = $this->Kasbon_model->get_by_karyawan($id_karyawan);
        } else {
            $data['kasbon'] = $this->Kasbon_model->get_all();
        }
        
        // Load model pengaturan
        $this->load->model('Pengaturan_model');
        
        // Panggil get_pengaturan()
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();  // Memanggil method get_pengaturan yang baru ditambahkan
        
        // Data contoh Excel
        $data['contoh_excel'] = [
            ['No', 'Tanggal', 'Karyawan', 'Jenis', 'Jumlah', 'Keterangan'],
            ['1', '2024-01-25', 'John Doe', 'pinjaman', '500000', 'Pinjaman Darurat'],
            ['2', '2024-01-26', 'Jane Doe', 'pembayaran', '250000', 'Cicilan 1'],
            ['3', '2024-01-27', 'Steve Smith', 'pinjaman', '1000000', 'Biaya Sekolah']
        ];
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('kasbon/index', $data);
        $this->load->view('templates/footer');
    }
    

    public function tambah() {
        $data['title'] = 'Tambah Kasbon';
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
        $data['karyawan'] = $this->Karyawan_model->get_all();
        
        // Add pengaturan data
        $this->load->model('Pengaturan_model');
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('kasbon/form', $data);
            $this->load->view('templates/footer');
        } else {
            $jumlah = clean_money_input($this->input->post('jumlah'));
            $error = validate_money_input($jumlah, 'Jumlah Kasbon');
            
            if (!empty($error)) {
                $this->session->set_flashdata('error', $error);
                redirect('kasbon/tambah');
            }
            
            $data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jenis' => $this->input->post('jenis'),
                'jumlah' => $jumlah,
                'keterangan' => $this->input->post('keterangan')
            ];
            
            if($this->Kasbon_model->insert($data)) {
                $this->session->set_flashdata('success', 'Data kasbon berhasil ditambahkan');
                redirect('kasbon');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('kasbon/tambah');
            }
        }
    }

    public function lihat($id) {
        $data['kasbon'] = $this->Kasbon_model->get_by_id($id);
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
        
        // Tambahkan pengecekan untuk karyawan
        if($this->session->userdata('role') == 'karyawan' && $data['kasbon']->id_karyawan != $this->session->userdata('id_karyawan')) {
            show_404();
        }
        
        // Add pengaturan data
        $this->load->model('Pengaturan_model');
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        if(empty($data['kasbon'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('kasbon/lihat', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['kasbon'] = $this->Kasbon_model->get_by_id($id);
        $data['karyawan_list'] = $this->Karyawan_model->get_all();
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
        
        // Add pengaturan data
        $this->load->model('Pengaturan_model');
        $data['pengaturan'] = $this->Pengaturan_model->get_pengaturan();
        
        if(empty($data['kasbon'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('kasbon/edit', $data);
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
            $error = validate_money_input($jumlah, 'Jumlah Kasbon');
            
            if (!empty($error)) {
                $this->session->set_flashdata('error', $error);
                redirect('kasbon/edit/' . $id);
            }
            
            $data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jenis' => $this->input->post('jenis'),
                'jumlah' => $jumlah,
                'keterangan' => $this->input->post('keterangan')
            ];

            $this->Kasbon_model->update($id, $data);
            $this->session->set_flashdata('success', 'Data kasbon berhasil diperbarui');
            redirect('kasbon');
        }
    }

    public function hapus($id) {
        $kasbon = $this->Kasbon_model->get_by_id($id);
        
        // Tambahkan pengecekan untuk karyawan
        if($this->session->userdata('role') == 'karyawan' && $kasbon->id_karyawan != $this->session->userdata('id_karyawan')) {
            show_404();
        }
        
        if(empty($kasbon)) {
            show_404();
        }

        if($this->Kasbon_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data kasbon berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data kasbon');
        }

        redirect('kasbon');
    }

    public function import() {
        // Tambahkan pengecekan role
        if($this->session->userdata('role') == 'karyawan') {
            $this->session->set_flashdata('error', 'Akses ditolak');
            redirect('kasbon');
        }
        
        if(!$_FILES['excel_file']['name']) {
            $this->session->set_flashdata('error', 'Pilih file Excel terlebih dahulu');
            redirect('kasbon');
        }

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048;
        
        $this->load->library('upload', $config);
        
        if(!$this->upload->do_upload('excel_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('kasbon');
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
            $missing_headers = array_diff($required_headers, $header);
            
            if (!empty($missing_headers)) {
                throw new Exception('Kolom wajib tidak ditemukan: ' . implode(', ', $missing_headers));
            }
            
            for($row = 2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                
                // Skip baris kosong
                if(empty(array_filter($rowData[0]))) {
                    continue;
                }
                
                // Ubah format tanggal dari dd/mm/yyyy menjadi yyyy-mm-dd
                $tanggal_input = trim($rowData[0][1]);
                // Cek jika numeric (serial Excel)
                if (is_numeric($tanggal_input)) {
                    // Konversi serial Excel ke tanggal
                    $UNIX_DATE = ($tanggal_input - 25569) * 86400;
                    $tanggal_obj = new DateTime("@$UNIX_DATE");
                    $tanggal_obj->setTimezone(new DateTimeZone('Asia/Jakarta'));
                } else {
                    $tanggal_obj = DateTime::createFromFormat('d/m/Y', $tanggal_input);
                }
                if (!$tanggal_obj) {
                    $error_messages[] = "Format tanggal pada baris $row tidak valid. Gunakan format dd/mm/yyyy (contoh: 05/05/2025)";
                    $failed++;
                    $errors[] = "Baris $row: " . implode(", ", $error_messages);
                    continue;
                }
                $tanggal = $tanggal_obj->format('Y-m-d');
                $nama_karyawan = trim($rowData[0][2]);
                $jenis = strtolower(trim($rowData[0][3])); // Konversi ke huruf kecil
                // Bersihkan format jumlah
                $jumlah = trim($rowData[0][4]);
                $jumlah = str_replace(['Rp', 'Rp.', '.', ',', ' '], '', $jumlah);
                $keterangan = trim($rowData[0][5]);
                
                $error_messages = [];
                
                // Validasi nama karyawan
                if(empty($nama_karyawan)) {
                    $error_messages[] = "Nama karyawan tidak boleh kosong pada baris $row";
                } else {
                    // Cari dengan LIKE untuk lebih fleksibel
                    $karyawan = $this->Karyawan_model->get_by_name_like($nama_karyawan);
                    if(!$karyawan) {
                        $error_messages[] = "Karyawan dengan nama '$nama_karyawan' tidak ditemukan di database pada baris $row";
                    }
                }
                
                // Konversi jenis transaksi (case insensitive)
                if(empty($jenis)) {
                    $jenis = 'belum lunas'; // Default jika kosong
                } else {
                    if(in_array(strtolower($jenis), ['lunas'])) {
                        $jenis = 'lunas';
                    } else {
                        $jenis = 'belum lunas';
                    }
                }
                
                // Validasi jenis
                if(!in_array($jenis, ['lunas', 'belum lunas'])) {
                    $error_messages[] = "Jenis pada baris $row harus 'Lunas' atau 'Belum Lunas' (tidak case sensitive)";
                }
                
                // Validasi jumlah
                if(empty($jumlah)) {
                    $error_messages[] = "Jumlah tidak boleh kosong pada baris $row";
                } elseif(!is_numeric($jumlah)) {
                    $error_messages[] = "Jumlah pada baris $row ($jumlah) harus berupa angka. Hapus 'Rp' dan tanda titik/koma";
                } elseif($jumlah <= 0) {
                    $error_messages[] = "Jumlah pada baris $row harus lebih besar dari 0";
                }
                
                if(!empty($error_messages)) {
                    $failed++;
                    $errors[] = "Baris $row: " . implode(", ", $error_messages);
                    continue;
                }
                
                try {
                    $data = [
                        'tanggal' => $tanggal,
                        'id_karyawan' => $karyawan->id,
                        'jenis' => $jenis,
                        'jumlah' => $jumlah,
                        'keterangan' => $keterangan,
                    ];
                    
                    if($this->Kasbon_model->insert($data)) {
                        $success++;
                    } else {
                        $failed++;
                        $errors[] = "Baris $row: Gagal menyimpan data ke database";
                    }
                } catch (Exception $e) {
                    $failed++;
                    $errors[] = "Baris $row: " . $e->getMessage();
                }
            }
            
            // Hapus file excel setelah selesai
            unlink($file_path);
            
            if($failed > 0) {
                $error_message = "Terdapat $failed data yang gagal diimport:\n" . implode("\n", $errors);
                $this->session->set_flashdata('error', $error_message);
            }
            
            if($success > 0) {
                $this->session->set_flashdata('success', "$success data berhasil diimport!");
            }
            
            redirect('kasbon');
            
        } catch (Exception $e) {
            unlink($file_path); // Hapus file jika terjadi error
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('kasbon');
        }
        
        redirect('kasbon');
    }
    public function lunas_semua() {
        // Pastikan user memiliki akses admin
        if(!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
            redirect('kasbon');
        }
    
        // Update semua kasbon menjadi lunas
        $this->db->where('jenis', 'belum lunas');
        $this->db->update('kasbon', ['jenis' => 'lunas']);
    
        // Kirim response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success']));
    }
    
    public function export() {
        if (!has_admin_access()) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
            redirect('kasbon');
        }

        require FCPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header sesuai dengan format yang diminta
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Pekerja');
        $sheet->setCellValue('D1', 'Jenis');
        $sheet->setCellValue('E1', 'Jumlah');
        $sheet->setCellValue('F1', 'Keterangan');
        
        $kasbon = $this->Kasbon_model->get_all();
        
        $row = 2;
        foreach($kasbon as $index => $data) {
            $sheet->setCellValue('A' . $row, ($index + 1));
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($data->tanggal)));
            $sheet->setCellValue('C' . $row, $data->nama_karyawan);
            $sheet->setCellValue('D' . $row, ucfirst($data->jenis));
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($data->jumlah, 0, ',', '.'));
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
        
        $filename = 'Data_Kasbon_'.date('Y-m-d_H-i-s').'.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}