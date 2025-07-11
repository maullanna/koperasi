<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bpjs_model extends CI_Model {
    
    public function get_all() {
        $this->db->select('bpjs.*, karyawan.nama_karyawan');
        $this->db->from('bpjs');
        $this->db->join('karyawan', 'karyawan.id = bpjs.id_karyawan');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }
    
    public function insert($data) {
        return $this->db->insert('bpjs', $data);
    }
    
    public function get_by_id($id) {
        $this->db->select('bpjs.*, karyawan.nama_karyawan');
        $this->db->from('bpjs');
        $this->db->join('karyawan', 'karyawan.id = bpjs.id_karyawan');
        $this->db->where('bpjs.id', $id);
        return $this->db->get()->row();
    }
    
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('bpjs', $data);
    }
    
    public function delete($id) {
        return $this->db->delete('bpjs', ['id' => $id]);
    }
}