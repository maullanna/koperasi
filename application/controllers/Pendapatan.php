<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Pendapatan extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('Pendapatan_model');
        $this->load->model('Karyawan_model');
        $this->load->model('Pekerjaan_model'); // Add this line to load Pekerjaan_model
        $this->load->helper('money');
    }
    
    public function index() {
        $data['title'] = 'Pendapatan';
        $data['kategori'] = 'transaksi'; // Tambahkan kategori transaksi
    
        // Cek role user
        $role = $this->session->userdata('role');
        $id_karyawan = $this->session->userdata('id_karyawan');
        
        // Jika role karyawan, hanya tampilkan data miliknya
        if ($role == 'karyawan') {
            $data['pendapatan'] = $this->Pendapatan_model->get_by_karyawan($id_karyawan);
        } else {
            // Untuk admin dan owner, tampilkan semua data
            $data['pendapatan'] = $this->Pendapatan_model->get_all();
        }
    
        // Contoh data excel
        // Di dalam method index()
        $data['contoh_excel'] = [
            ['No', 'Tanggal', 'Nama Karyawan', 'Nama Pekerjaan', 'Banyak', 'Total Pendapatan', 'Status'],
            ['1', '1/05/2024', 'Fadly Akbar', 'Panel Besar', '5', '28000', 'selesai'],
            ['2', '2/05/2024', 'Fadly Akbar', 'Bongkar FP 2', '3', '280000', 'selesai'],
            ['3', '4/05/2024', 'Reza', 'Bongkar FP 1', '4', '48000', 'selesai']
        ];  
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pendapatan/index', $data);
        $this->load->view('templates/footer');
    }
    

    public function import() {
        require FCPATH . 'vendor/autoload.php';
        
        $config['upload_path'] = './uploads/temp/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 2048;
        
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('excel_file')) {
            $this->session->set_flashdata('error', 'Error: ' . $this->upload->display_errors('', ''));
            redirect('pendapatan');
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
            $required_columns = ['tanggal', 'nama karyawan', 'nama pekerjaan', 'banyak', 'total pendapatan'];
            $missing_columns = array_diff($required_columns, array_keys($header_map));
        
            if (!empty($missing_columns)) {
                unlink($file_path);
                $this->session->set_flashdata('error', 'Kolom wajib tidak ditemukan: ' . implode(', ', $missing_columns));
                redirect('pendapatan');
            }
        
            $success_count = 0;
            $errors = [];
            $current_pendapatan = null;
            $current_details = [];
            
            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) continue;
        
                $row_number = $index + 2;
                
                // Ambil data dari row
                $tanggal = $row[$header_map['tanggal']] ?? '';
                $nama_karyawan = trim($row[$header_map['nama karyawan']] ?? '');
                $nama_pekerjaan = trim($row[$header_map['nama pekerjaan']] ?? '');
                $banyak = intval($row[$header_map['banyak']] ?? 0);
                $total_pendapatan = clean_money_input($row[$header_map['total pendapatan']] ?? '0');
                $status = isset($header_map['status']) ? strtolower(trim($row[$header_map['status']])) : 'selesai';
                
                // Validasi data
                if (empty($tanggal) || !strtotime(str_replace('/', '-', $tanggal))) {
                    $errors[] = "Baris $row_number: Format tanggal tidak valid";
                    continue;
                }
                
                if (empty($nama_karyawan)) {
                    $errors[] = "Baris $row_number: Nama Karyawan tidak boleh kosong";
                    continue;
                }
                
                // Cek karyawan exists dengan case insensitive
                $karyawan = $this->db->where('LOWER(nama_karyawan)', strtolower($nama_karyawan))
                                     ->get('karyawan')
                                     ->row();
                if (!$karyawan) {
                    $errors[] = "Baris $row_number: Karyawan '$nama_karyawan' tidak ditemukan dalam database. Silakan tambahkan karyawan terlebih dahulu di menu Karyawan";
                    continue;
                }
                
                // Cek pekerjaan exists
                $pekerjaan = $this->Pekerjaan_model->get_by_name($nama_pekerjaan);
                if (!$pekerjaan) {
                    $errors[] = "Baris $row_number: Pekerjaan '$nama_pekerjaan' tidak ditemukan";
                    continue;
                }
                
                // Format tanggal ke Y-m-d
                $formatted_date = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));
                
                // Jika ini adalah pendapatan baru
                if (!$current_pendapatan || 
                    $current_pendapatan['tanggal'] != $formatted_date || 
                    $current_pendapatan['id_karyawan'] != $karyawan->id) {
                    
                    // Simpan pendapatan sebelumnya jika ada
                    if ($current_pendapatan) {
                        $this->db->trans_start();
                        
                        // Insert pendapatan
                        $this->db->insert('pendapatan', $current_pendapatan);
                        $pendapatan_id = $this->db->insert_id();
                        
                        if ($pendapatan_id) {
                            // Insert details
                            foreach ($current_details as $detail) {
                                $detail['id_pendapatan'] = $pendapatan_id;
                                $this->db->insert('pendapatan_detail', $detail);
                            }
                            $success_count++;
                        }
                        
                        $this->db->trans_complete();
                    }
                    
                    // Buat pendapatan baru
                    $current_pendapatan = [
                        'tanggal' => $formatted_date,
                        'id_karyawan' => $karyawan->id,
                        'total_pendapatan' => $total_pendapatan,
                        'status' => $status
                    ];
                    $current_details = [];
                }
                
                // Tambah detail pekerjaan
                $current_details[] = [
                    'id_pekerjaan' => $pekerjaan->id,
                    'banyak' => $banyak,
                    'harga_koperasi' => $pekerjaan->harga_koperasi,
                    'harga_karyawan' => $pekerjaan->harga_karyawan,
                    'total' => $banyak * $pekerjaan->harga_karyawan
                ];
            }
            
            // Simpan pendapatan terakhir jika ada
            if ($current_pendapatan) {
                $this->db->trans_start();
                
                // Insert pendapatan
                $this->db->insert('pendapatan', $current_pendapatan);
                $pendapatan_id = $this->db->insert_id();
                
                if ($pendapatan_id) {
                    // Insert details
                    foreach ($current_details as $detail) {
                        $detail['id_pendapatan'] = $pendapatan_id;
                        $this->db->insert('pendapatan_detail', $detail);
                    }
                    $success_count++;
                }
                
                $this->db->trans_complete();
            }
            
            unlink($file_path);
        
            if ($success_count > 0) {
                $this->session->set_flashdata('success', "$success_count data pendapatan berhasil ditambahkan.");
            }
            
            if (!empty($errors)) {
                // Tambahkan informasi lebih detail untuk setiap error dengan timeout 30 detik
                $error_messages = [];
                foreach ($errors as $error) {
                    if (strpos($error, 'Karyawan') !== false) {
                        $error_messages[] = "<strong>Data Karyawan:</strong> " . $error;
                    } elseif (strpos($error, 'Pekerjaan') !== false) {
                        $error_messages[] = "<strong>Data Pekerjaan:</strong> " . $error;
                    } else {
                        $error_messages[] = $error;
                    }
                }
                
                // Set flashdata dengan timeout 30 detik
                $this->session->set_tempdata('error', "Beberapa data gagal diimport:<br>" . implode('<br>', $error_messages), 30);
            }
            
        } catch (Exception $e) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
        }
        
        redirect('pendapatan');
    }

    public function tambah() {
        // Gunakan helper untuk membatasi akses
        admin_only();
        
        $data['title'] = 'Tambah Pendapatan';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        $this->load->model('Pekerjaan_model');
        $data['pekerjaan'] = $this->Pekerjaan_model->get_all();
        
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required|numeric|callback_validate_karyawan');  // tambahkan callback
        $this->form_validation->set_rules('id_pekerjaan[]', 'Pekerjaan', 'required');
        $this->form_validation->set_rules('banyak[]', 'Banyak', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            // Set pesan error dengan tempdata agar bertahan 30 detik
            if (validation_errors()) {
                $this->session->set_tempdata('error', validation_errors(), 30);
            }
            
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('pendapatan/form', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->trans_start();
            
            // Hitung total pendapatan
            $total_pendapatan = 0;
            $id_pekerjaan = $this->input->post('id_pekerjaan');
            $banyak = $this->input->post('banyak');
            $harga_karyawan = $this->input->post('harga_karyawan');
            $harga_koperasi = $this->input->post('harga_koperasi');
            
            // Menghitung total pendapatan berdasarkan pekerjaan
            for($i = 0; $i < count($id_pekerjaan); $i++) {
                if (!empty($id_pekerjaan[$i])) {
                    // Tentukan total berdasarkan harga_karyawan atau harga_koperasi
                    $harga = isset($harga_karyawan[$i]) ? $harga_karyawan[$i] : $harga_koperasi[$i];
                    $total_pendapatan += $banyak[$i] * $harga;
                }
            }
    
            // Insert data pendapatan utama
            $pendapatan_data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'total_pendapatan' => $total_pendapatan, // Gunakan total yang sudah dihitung
                'status' => 'selesai'
            ];
            
            $this->db->insert('pendapatan', $pendapatan_data);
            $id_pendapatan = $this->db->insert_id();
            
            // Insert detail pendapatan
            $total = $this->input->post('total');  // Ini sesuai dengan total per pekerjaan
            for($i = 0; $i < count($id_pekerjaan); $i++) {
                if(!empty($id_pekerjaan[$i])) {
                    $detail_data = [
                        'id_pendapatan' => $id_pendapatan,
                        'id_pekerjaan' => $id_pekerjaan[$i],
                        'banyak' => $banyak[$i],
                        'harga_karyawan' => $harga_karyawan[$i],
                        'harga_koperasi' => $harga_koperasi[$i],
                        'total' => $total[$i]
                    ];
                    $this->db->insert('pendapatan_detail', $detail_data);
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Data pendapatan berhasil ditambahkan');
                redirect('pendapatan');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('pendapatan/tambah');
            }
        }
    }
    
    public function edit($id) {
        // Gunakan helper untuk membatasi akses
        admin_only();
        
        $data['title'] = 'Edit Pendapatan';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        $this->load->model('Pekerjaan_model');
        $data['pekerjaan'] = $this->Pekerjaan_model->get_all();
        
        // Ambil data pendapatan yang akan diedit
        $data['pendapatan'] = $this->Pendapatan_model->get_by_id($id);
        if (!$data['pendapatan']) {
            $this->session->set_flashdata('error', 'Data pendapatan tidak ditemukan');
            redirect('pendapatan');
        }
        
        // Ambil detail pendapatan
        $data['details'] = $this->Pendapatan_model->get_details($id);
        
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('id_pekerjaan[]', 'Pekerjaan', 'required');
        $this->form_validation->set_rules('banyak[]', 'Banyak', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('pendapatan/form_edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->trans_start();
            
            // Hitung total pendapatan
            $total_pendapatan = 0;
            $id_pekerjaan = $this->input->post('id_pekerjaan');
            $banyak = $this->input->post('banyak');
            
            // Ambil harga dari database untuk setiap pekerjaan
            for($i = 0; $i < count($id_pekerjaan); $i++) {
                if (!empty($id_pekerjaan[$i])) {
                    $pekerjaan = $this->Pekerjaan_model->get_by_id($id_pekerjaan[$i]);
                    if ($pekerjaan) {
                        $total_pendapatan += $banyak[$i] * $pekerjaan->harga_karyawan;
                    }
                }
            }
    
            // Update data pendapatan utama
            $pendapatan_data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'total_pendapatan' => $total_pendapatan,
                'status' => 'selesai'
            ];
            
            $this->db->where('id', $id);
            $this->db->update('pendapatan', $pendapatan_data);
            
            // Hapus detail pendapatan lama
            $this->db->delete('pendapatan_detail', ['id_pendapatan' => $id]);
            
            // Insert detail pendapatan baru
            for($i = 0; $i < count($id_pekerjaan); $i++) {
                if(!empty($id_pekerjaan[$i])) {
                    $pekerjaan = $this->Pekerjaan_model->get_by_id($id_pekerjaan[$i]);
                    $detail_data = [
                        'id_pendapatan' => $id,
                        'id_pekerjaan' => $id_pekerjaan[$i],
                        'banyak' => $banyak[$i],
                        'harga_karyawan' => $pekerjaan->harga_karyawan,
                        'harga_koperasi' => $pekerjaan->harga_koperasi,
                        'total' => $banyak[$i] * $pekerjaan->harga_karyawan
                    ];
                    $this->db->insert('pendapatan_detail', $detail_data);
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Data pendapatan berhasil diperbarui');
                redirect('pendapatan');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat memperbarui data');
                redirect('pendapatan/edit/'.$id);
            }
        }
    }

    public function update($id) {
        // Gunakan helper untuk membatasi akses
        admin_only();
        
        $data['title'] = 'Tambah Pendapatan';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        $this->load->model('Pekerjaan_model');
        $data['pekerjaan'] = $this->Pekerjaan_model->get_all();
        
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('id_pekerjaan[]', 'Pekerjaan', 'required');
        $this->form_validation->set_rules('banyak[]', 'Banyak', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('pendapatan/form', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->trans_start();
            
            // Hitung total pendapatan
            $total_pendapatan = 0;
            $id_pekerjaan = $this->input->post('id_pekerjaan');
            $banyak = $this->input->post('banyak');
            $harga_karyawan = $this->input->post('harga_karyawan');
            $harga_koperasi = $this->input->post('harga_koperasi');
            
            // Menghitung total pendapatan berdasarkan pekerjaan
            for($i = 0; $i < count($id_pekerjaan); $i++) {
                if (!empty($id_pekerjaan[$i])) {
                    // Tentukan total berdasarkan harga_karyawan atau harga_koperasi
                    $harga = isset($harga_karyawan[$i]) ? $harga_karyawan[$i] : $harga_koperasi[$i];
                    $total_pendapatan += $banyak[$i] * $harga;
                }
            }
    
            // Insert data pendapatan utama
            $pendapatan_data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'total_pendapatan' => $total_pendapatan, // Gunakan total yang sudah dihitung
                'status' => 'selesai'
            ];
            
            $this->db->insert('pendapatan', $pendapatan_data);
            $id_pendapatan = $this->db->insert_id();
            
            // Insert detail pendapatan
            $total = $this->input->post('total');  // Ini sesuai dengan total per pekerjaan
            for($i = 0; $i < count($id_pekerjaan); $i++) {
                if(!empty($id_pekerjaan[$i])) {
                    $detail_data = [
                        'id_pendapatan' => $id_pendapatan,
                        'id_pekerjaan' => $id_pekerjaan[$i],
                        'banyak' => $banyak[$i],
                        'harga_karyawan' => $harga_karyawan[$i],
                        'harga_koperasi' => $harga_koperasi[$i],
                        'total' => $total[$i]
                    ];
                    $this->db->insert('pendapatan_detail', $detail_data);
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status()) {
                $this->session->set_flashdata('success', 'Data pendapatan berhasil ditambahkan');
                redirect('pendapatan');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('pendapatan/tambah');
            }
        }
    }
    
    public function hapus($id) {
        // Gunakan helper untuk membatasi akses
        admin_only();
        
        $this->db->trans_start();
        
        // Hapus detail pendapatan terlebih dahulu
        $this->db->delete('pendapatan_detail', ['id_pendapatan' => $id]);
        
        // Kemudian hapus data pendapatan utama
        $this->db->delete('pendapatan', ['id' => $id]);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Data pendapatan berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat menghapus data');
        }
        
        redirect('pendapatan');
    }
    
    public function detail($id) {
        $data['title'] = 'Detail Pendapatan';
        $data['pendapatan'] = $this->Pendapatan_model->get_detail($id);
        $data['details'] = $this->Pendapatan_model->get_details($id);
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pendapatan/detail', $data);
        $this->load->view('templates/footer');
    }

    public function export() {
        // Gunakan helper untuk membatasi akses
        admin_only();

        // Load library PhpSpreadsheet
        require FCPATH . 'vendor/autoload.php';

        // Buat objek spreadsheet baru
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Nama Karyawan');
        $sheet->setCellValue('D1', 'Nama Pekerjaan');
        $sheet->setCellValue('E1', 'Banyak');
        $sheet->setCellValue('F1', 'Total Pendapatan');
        $sheet->setCellValue('G1', 'Status');

        // Style header
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

        // Ambil data pendapatan
        $pendapatan = $this->Pendapatan_model->get_all();
        
        $row = 2;
        $no = 1;
        foreach ($pendapatan as $p) {
            // Ambil detail pendapatan
            $details = $this->Pendapatan_model->get_details($p->id);
            
            foreach ($details as $d) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($p->tanggal)));
                $sheet->setCellValue('C' . $row, $p->nama_karyawan);
                $sheet->setCellValue('D' . $row, $d->nama_pekerjaan);
                $sheet->setCellValue('E' . $row, $d->banyak);
                $sheet->setCellValue('F' . $row, $d->total);
                $sheet->setCellValue('G' . $row, ucfirst($p->status));
                
                // Format angka untuk kolom total
                $sheet->getStyle('F' . $row)->getNumberFormat()
                     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                
                $row++;
            }
            $no++;
        }

        // Auto size kolom
        foreach(range('A','G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set nama file
        $filename = 'Data_Pendapatan_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Export ke Excel 2007 format
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        // Pastikan tidak ada output sebelum ini
        ob_end_clean();
        
        // Tulis ke output
        $writer->save('php://output');
        exit;
    }
 // Add this missing closing brace for the class


    // Tambahkan method validasi karyawan
    public function validate_karyawan($id_karyawan) {
        if (empty($id_karyawan)) {
            $this->form_validation->set_message('validate_karyawan', 'Silakan pilih karyawan terlebih dahulu');
            return FALSE;
        }
        
        $karyawan = $this->Karyawan_model->get_by_id($id_karyawan);
        if (!$karyawan) {
            $this->form_validation->set_message('validate_karyawan', 'Karyawan tidak ditemukan');
            return FALSE;
        }
        
        return TRUE;
    }
}