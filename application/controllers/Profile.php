<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('Karyawan_model');
    }

    public function index() {
        $data['title'] = 'Profil Saya';
        $username = $this->session->userdata('username');
        
        // Ambil data karyawan berdasarkan username
        $data['user'] = $this->db->get_where('karyawan', ['username' => $username])->row();
        
        if (!$data['user']) {
            $this->session->set_flashdata('error', 'Data profil tidak ditemukan');
            redirect('dashboard');
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('profile/index', $data);
        $this->load->view('templates/footer');
    }
    
    public function edit() {
        $data['title'] = 'Edit Profil';
        $username = $this->session->userdata('username');
        
        // Ambil data karyawan berdasarkan username
        $data['user'] = $this->db->get_where('karyawan', ['username' => $username])->row();
        
        if (!$data['user']) {
            $this->session->set_flashdata('error', 'Data profil tidak ditemukan');
            redirect('dashboard');
        }
        
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('profile/edit', $data);
            $this->load->view('templates/footer');
        } else {
            // Update data profil
            $update_data = [
                'username' => $this->input->post('username')
            ];
            
            $this->db->where('id', $data['user']->id);
            if($this->db->update('karyawan', $update_data)) {
                // Update session data
                $this->session->set_userdata('username', $update_data['username']);
                
                $this->session->set_flashdata('success', 'Profil berhasil diperbarui');
                redirect('profile');
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan saat memperbarui profil');
                redirect('profile/edit');
            }
        }
    }
    
    public function change_password() {
        $data['title'] = 'Ubah Password';
        $username = $this->session->userdata('username');
        
        // Ambil data karyawan berdasarkan username
        $data['user'] = $this->db->get_where('karyawan', ['username' => $username])->row();
        
        if (!$data['user']) {
            $this->session->set_flashdata('error', 'Data profil tidak ditemukan');
            redirect('dashboard');
        }
        
        $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required|trim');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|trim|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|trim|matches[new_password]');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('profile/change_password', $data);
            $this->load->view('templates/footer');
        } else {
            $current_password = $this->input->post('current_password');
            
            if(!password_verify($current_password, $data['user']->password)) {
                $this->session->set_flashdata('error', 'Password saat ini salah');
                redirect('profile/change_password');
            } else {
                $new_password = password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);
                
                $this->db->where('id', $data['user']->id);
                if($this->db->update('karyawan', ['password' => $new_password])) {
                    $this->session->set_flashdata('success', 'Password berhasil diubah');
                    redirect('profile');
                } else {
                    $this->session->set_flashdata('error', 'Terjadi kesalahan saat mengubah password');
                    redirect('profile/change_password');
                }
            }
        }
    }
}
?>