<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan_model extends CI_Model {
    private $table = 'karyawan';
    
    public function get_by_username($username) {
        return $this->db->get_where('karyawan', ['username' => $username])->row();
    }
    
    public function get_all() {
        $this->db->order_by('nama_karyawan', 'ASC');
        return $this->db->get('karyawan')->result();
    }
    
    public function insert($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert('karyawan', $data);
    }
    
    public function get_by_id($id) {
        return $this->db->get_where('karyawan', ['id' => $id])->row();
    }
    
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('karyawan', $data);
    }
    
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function get_karyawan($user_id) {
        return $this->db->get_where('karyawan', ['id' => $user_id])->row();
    }
    
    public function update_karyawan($user_id, $data) {
        $this->db->where('id', $user_id);
        return $this->db->update('karyawan', $data);
    }

    public function get_by_name_like($nama) {
        return $this->db->like('nama_karyawan', $nama)
                        ->get('karyawan')
                        ->row();
    }
}