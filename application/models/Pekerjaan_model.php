<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pekerjaan_model extends CI_Model {
    
    public function get_all() {
        $this->db->order_by('id', 'ASC');
        return $this->db->get('pekerjaan')->result();
    }
    
    public function insert($data) {
        return $this->db->insert('pekerjaan', $data);
    }
    
    public function get_by_id($id) {
        return $this->db->get_where('pekerjaan', ['id' => $id])->row();
    }
    
    public function get_by_name($nama_pekerjaan) {
        $this->db->where('LOWER(TRIM(nama_pekerjaan))', strtolower(trim($nama_pekerjaan)));
        return $this->db->get('pekerjaan')->row();
    }
    
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('pekerjaan', $data);
    }
    
    public function delete($id) {
        return $this->db->delete('pekerjaan', ['id' => $id]);
    }
}