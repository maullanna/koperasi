<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {
    
    public function get_all() {
        $this->db->select('absensi.*, karyawan.nama_karyawan, 
            (SELECT COUNT(*) FROM absensi a2 
             WHERE a2.id_karyawan = absensi.id_karyawan 
             AND a2.status = "hadir" 
             AND MONTH(a2.tanggal) = MONTH(absensi.tanggal)
             AND YEAR(a2.tanggal) = YEAR(absensi.tanggal)) as total_kehadiran');
        $this->db->from('absensi');
        $this->db->join('karyawan', 'karyawan.id = absensi.id_karyawan');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }
    
    public function get_total_kehadiran($id_karyawan, $tanggal_awal, $tanggal_akhir) {
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('tanggal >=', $tanggal_awal);
        $this->db->where('tanggal <=', $tanggal_akhir);
        $this->db->where('status', 'hadir');
        return $this->db->count_all_results('absensi');
    }
    
    public function exists($tanggal, $id_karyawan) {
        $this->db->where('tanggal', $tanggal);
        $this->db->where('id_karyawan', $id_karyawan);
        return $this->db->get('absensi')->num_rows() > 0;
    }
    
    public function insert($data) {
        return $this->db->insert('absensi', $data);
    }
    
    public function get_by_id($id) {
        $this->db->select('absensi.*, karyawan.nama_karyawan, 
            (SELECT COUNT(*) FROM absensi a2 
             WHERE a2.id_karyawan = absensi.id_karyawan 
             AND a2.status = "hadir" 
             AND MONTH(a2.tanggal) = MONTH(absensi.tanggal)
             AND YEAR(a2.tanggal) = YEAR(absensi.tanggal)) as total_kehadiran');
        $this->db->from('absensi');
        $this->db->join('karyawan', 'karyawan.id = absensi.id_karyawan');
        $this->db->where('absensi.id', $id);
        return $this->db->get()->row();
    }
    
    public function get_by_karyawan($id_karyawan) {
        $this->db->select('absensi.*, karyawan.nama_karyawan');
        $this->db->from('absensi');
        $this->db->join('karyawan', 'karyawan.id = absensi.id_karyawan');
        $this->db->where('absensi.id_karyawan', $id_karyawan);
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }
    
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('absensi', $data);
    }
    
    public function delete($id) {
        return $this->db->delete('absensi', ['id' => $id]);
    }
    
    public function get_jumlah_hadir($id_karyawan, $periode) {
        $start_date = date('Y-m-01', strtotime($periode));
        $end_date = date('Y-m-t', strtotime($periode));
        
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->where('status', 'hadir');
        return $this->db->count_all_results('absensi');
    }

    public function get_kehadiran_by_karyawan_and_bulan($id_karyawan, $bulan) {
        // Format tanggal awal dan akhir bulan
        $start_date = date('Y-m-01', strtotime($bulan));
        $end_date = date('Y-m-t', strtotime($bulan));
        
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->where('status', 'hadir');
        
        $jumlah_hadir = $this->db->count_all_results('absensi');
        
        // Tambahkan pengambilan data pengaturan
        $this->load->model('Pengaturan_model');
        $pengaturan = $this->Pengaturan_model->get_pengaturan();
        
        // Gunakan nilai uang makan dari pengaturan atau default 20.000
        $uang_makan_per_hari = $pengaturan->uang_makan ?? 20000;
        
        return [
            'jumlah_hadir' => $jumlah_hadir,
            'uang_makan' => $jumlah_hadir * $uang_makan_per_hari
        ];
    }
}