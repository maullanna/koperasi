<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bpjs extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model(['Bpjs_model', 'Karyawan_model']);
    }

    public function index() {
        $data['title'] = 'BPJS';
        $data['bpjs'] = $this->Bpjs_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('bpjs/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah() {
        $data['title'] = 'Tambah BPJS';
        $data['karyawan'] = $this->Karyawan_model->get_all();
        
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('bpjs/form', $data);
            $this->load->view('templates/footer');
        } else {
            $bpjs = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jumlah' => $this->input->post('jumlah'),
                'keterangan' => $this->input->post('keterangan')
            ];
            
            if($this->Bpjs_model->insert($bpjs)) {
                $this->session->set_flashdata('success', 'Data BPJS berhasil ditambahkan');
                redirect('bpjs');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat menyimpan data');
                redirect('bpjs/tambah');
            }
        }
    }

    public function lihat($id) {
        $data['bpjs'] = $this->Bpjs_model->get_by_id($id);
        
        if(empty($data['bpjs'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('bpjs/lihat', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['bpjs'] = $this->Bpjs_model->get_by_id($id);
        $data['karyawan_list'] = $this->Karyawan_model->get_all();
        
        if(empty($data['bpjs'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('bpjs/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update($id) {
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('id_karyawan', 'Karyawan', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');

        if($this->form_validation->run() === FALSE) {
            $this->edit($id);
        } else {
            $data = [
                'tanggal' => $this->input->post('tanggal'),
                'id_karyawan' => $this->input->post('id_karyawan'),
                'jumlah' => $this->input->post('jumlah'),
                'keterangan' => $this->input->post('keterangan')
            ];

            $this->Bpjs_model->update($id, $data);
            $this->session->set_flashdata('success', 'Data BPJS berhasil diperbarui');
            redirect('bpjs');
        }
    }

    public function hapus($id) {
        $bpjs = $this->Bpjs_model->get_by_id($id);
        
        if(empty($bpjs)) {
            show_404();
        }

        if($this->Bpjs_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data BPJS berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data BPJS');
        }

        redirect('bpjs');
    }
}