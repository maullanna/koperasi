<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if($this->session->userdata('role') != 'admin') {
            show_error('Akses ditolak', 403);
        }
    }

    public function index() {
        $data['title'] = 'Backup Data';
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('backup/index', $data);
        $this->load->view('templates/footer');
    }
    public function full_project() {
        $this->load->library('zip');
    
        // List folder/folder/file yang ingin dibackup
        $items_to_backup = [
            'application/', 
            'system/', 
            'assets/', 
            'uploads/', 
            'vendor/', 
            'index.php', 
            '.htaccess',
            'composer.json',
            'composer.lock'
        ];
    
        foreach ($items_to_backup as $item) {
            $path = FCPATH . $item;
            if (file_exists($path)) {
                if (is_dir($path)) {
                    $this->zip->read_dir($path, false);
                } else {
                    $this->zip->read_file($path);
                }
            }
        }
    
        $filename = 'full_project_backup_' . date('Y-m-d_H-i-s') . '.zip';
    
        if (!is_dir(FCPATH . 'backup/full_project')) {
            mkdir(FCPATH . 'backup/full_project', 0777, true);
        }
    
        if ($this->zip->archive(FCPATH . 'backup/full_project/' . $filename)) {
            $this->session->set_flashdata('success', 'Backup full project berhasil');
        } else {
            $this->session->set_flashdata('error', 'Backup full project gagal');
        }
    
        redirect('backup');
    }
    

    public function database() {
        $this->load->dbutil();
        
        $prefs = array(
            'format'      => 'zip',
            'filename'    => 'koperasi_backup_' . date('Y-m-d_H-i-s') . '.sql'
        );

        $backup = $this->dbutil->backup($prefs);

        $db_name = 'koperasi_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $save = FCPATH . 'backup/database/' . $db_name;

        if(!is_dir(FCPATH . 'backup/database')) {
            mkdir(FCPATH . 'backup/database', 0777, true);
        }

        if(write_file($save, $backup)) {
            $this->session->set_flashdata('success', 'Backup database berhasil');
        } else {
            $this->session->set_flashdata('error', 'Backup database gagal');
        }
        
        redirect('backup');
    }

    public function source_code() {
        $this->load->library('zip');
        
        // Add the entire /application directory
        $this->zip->read_dir(FCPATH . 'application/', false);
        
        // Create the backup file
        $filename = 'source_code_backup_' . date('Y-m-d_H-i-s') . '.zip';
        
        if(!is_dir(FCPATH . 'backup/source_code')) {
            mkdir(FCPATH . 'backup/source_code', 0777, true);
        }
        
        if($this->zip->archive(FCPATH . 'backup/source_code/' . $filename)) {
            $this->session->set_flashdata('success', 'Backup source code berhasil');
        } else {
            $this->session->set_flashdata('error', 'Backup source code gagal');
        }
        
        redirect('backup');
    }

    public function download($encoded_path) {
        if(!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        $path = base64_decode($encoded_path);
        
        if(file_exists($path)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        } else {
            show_404();
        }
    }
}