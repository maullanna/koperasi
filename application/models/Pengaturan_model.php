<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan_model extends CI_Model {
    
    private $table = 'pengaturan';
    
    public function get_pengaturan() {
        $pengaturan = $this->db->get($this->table)->row();
        
        // Ensure all required properties exist
        if (!isset($pengaturan->nama_koperasi)) {
            $pengaturan->nama_koperasi = 'Koperasi'; // Default value
        }
        if (!isset($pengaturan->pemasukan_bpjs)) {
            $pengaturan->pemasukan_bpjs = 3; // 
        }
        
        if (!isset($pengaturan->uang_makan)) {
            $pengaturan->uang_makan = 20000; // Default value
        }
        
        return $pengaturan;
    }

    public function get_pengaturan_gaji() {
        $pengaturan = $this->get_pengaturan();
        return [
            'pemasukan_bpjs' => $pengaturan->pemasukan_bpjs ?? 10000,
            'uang_makan' => $pengaturan->uang_makan ?? 20000
        ];
    }

    public function update($data) {
        return $this->db->update($this->table, $data);
    }
}
