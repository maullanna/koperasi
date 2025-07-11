<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    // Declare CI default properties as public to match parent class
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
    public $email;
    public $session;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Karyawan_model');
    }

    public function index() {
        if($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if($this->form_validation->run() === FALSE) {
            $this->load->view('auth/login');
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $user = $this->Karyawan_model->get_by_username($username);
            
            if($user && password_verify($password, $user->password)) {
                // Di bagian login
                $session_data = array(
                    'id_karyawan' => $user->id,  // Pastikan ini benar
                    'username' => $user->username,
                    'role' => $user->role,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Username or password is incorrect');
                redirect('auth');
            }
        }
    }

    public function logout() {
        $this->session->unset_userdata(['id_karyawan', 'username', 'nama', 'role', 'logged_in']);
        $this->session->set_flashdata('success', 'You have been logged out');
        redirect('auth');
    }
}