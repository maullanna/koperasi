<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendapatan_model extends CI_Model {
    public function get_detail_by_month($id_karyawan, $bulan) {
        $this->db->select('pendapatan_detail.*, pekerjaan.nama_pekerjaan');
        $this->db->from('pendapatan_detail');
        $this->db->join('pendapatan', 'pendapatan.id = pendapatan_detail.id_pendapatan');
        $this->db->join('pekerjaan', 'pekerjaan.id = pendapatan_detail.id_pekerjaan');
        $this->db->where('pendapatan.tanggal >=', $bulan);
        $this->db->where('pendapatan.tanggal <=', $bulan);
        $this->db->where('pendapatan.id_karyawan', $id_karyawan);
        return $this->db->get()->result();
    }

    public function get_total_today() {
        $this->db->select_sum('total_pendapatan');
        $this->db->where('DATE(tanggal)', date('Y-m-d')); 
        $this->db->from('pendapatan');
        $query = $this->db->get();
        return $query->row()->total_pendapatan ?? 0;
    }
    
    public function get_all_total() {
        $this->db->select_sum('total_pendapatan');
        $this->db->from('pendapatan');
        $query = $this->db->get();
        return $query->row()->total_pendapatan ?? 0;
    }

    public function get_pengaturan() {
        return $this->db->get('pengaturan')->row();  // Assuming you only need one row of settings
    }

    public function get_all() {
        $this->db->select('pendapatan.*, karyawan.nama_karyawan');
        $this->db->from('pendapatan');
        $this->db->join('karyawan', 'karyawan.id = pendapatan.id_karyawan');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_karyawan($id_karyawan) {
        $this->db->select('p.*, k.nama_karyawan');
        $this->db->from('pendapatan p');
        $this->db->join('karyawan k', 'p.id_karyawan = k.id', 'left');
        $this->db->where('p.id_karyawan', $id_karyawan);
        $this->db->order_by('p.tanggal', 'desc');
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        $this->db->select('pendapatan.*, karyawan.nama_karyawan'); // Select the necessary fields
        $this->db->join('karyawan', 'karyawan.id = pendapatan.id_karyawan');
        return $this->db->get_where('pendapatan', ['pendapatan.id' => $id])->row(); // Ensure the condition is correct
    }

    public function get_detail($id) {
        $this->db->select('p.*, k.nama_karyawan as nama_karyawan'); // Corrected column name
        $this->db->from('pendapatan p');
        $this->db->join('karyawan k', 'k.id = p.id_karyawan');
        $this->db->where('p.id', $id);
        return $this->db->get()->row();
    }

    public function get_karyawan_id($id) {
        $pendapatan = $this->db->get_where('pendapatan', ['id' => $id])->row();
        return $pendapatan ? $pendapatan->id_karyawan : null;
    }

    public function insert($data, $details) {
        $this->db->trans_start();
        
        // Insert main data
        $this->db->insert('pendapatan', $data);
        $id_pendapatan = $this->db->insert_id();
        
        foreach($details as $detail) {
            $detail['id_pendapatan'] = $id_pendapatan;
            $this->db->insert('pendapatan_detail', $detail);
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('pendapatan', $data);
    }

    public function delete($id) {
        $this->db->trans_start();
    
        // Delete details first
        $this->db->where('id_pendapatan', $id);
        $this->db->delete('pendapatan_detail');
    
        // Delete the main pendapatan entry
        $this->db->where('id', $id);
        $this->db->delete('pendapatan');
    
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_total_by_karyawan_and_bulan($id_karyawan, $bulan) {
        // Ambil total pendapatan untuk karyawan pada bulan tertentu
        $this->db->select_sum('total_pendapatan');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan);
        $this->db->where('status', 'selesai'); // Hanya ambil pendapatan yang sudah selesai
        $result = $this->db->get('pendapatan')->row();
        
        // Ambil gaji pokok dan tunjangan dari tabel karyawan
        $this->db->select('gaji_pokok, tunjangan');
        $this->db->where('id', $id_karyawan);
        $karyawan = $this->db->get('karyawan')->row();
        
        // Hitung total pendapatan (gaji pokok + tunjangan + total_pendapatan)
        $total_pendapatan = ($result->total_pendapatan ?? 0) + 
                           ($karyawan->gaji_pokok ?? 0) + 
                           ($karyawan->tunjangan ?? 0);
        
        return $total_pendapatan;
    }

    public function get_details($id) {
        $this->db->select('pendapatan_detail.*, pekerjaan.nama_pekerjaan');
        $this->db->from('pendapatan_detail');
        $this->db->join('pekerjaan', 'pekerjaan.id = pendapatan_detail.id_pekerjaan');
        $this->db->where('pendapatan_detail.id_pendapatan', $id);
        return $this->db->get()->result();
    }

    public function get_total_today_by_karyawan($id_karyawan) {
        $this->db->select_sum('total_pendapatan');
        $this->db->from('pendapatan');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('DATE(tanggal)', date('Y-m-d'));
    
        $result = $this->db->get()->row();
        return $result->total_pendapatan ?? 0;
    }

    public function get_total_month_by_karyawan($id_karyawan) {
        $this->db->select_sum('total_pendapatan');
        $this->db->from('pendapatan');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('MONTH(tanggal)', date('m'));
        $this->db->where('YEAR(tanggal)', date('Y'));
        $this->db->where('status', 'selesai');
    
        $result = $this->db->get()->row();
        return $result->total_pendapatan ?? 0;
    }

    public function get_detail_by_period($id_karyawan, $tanggal_awal, $tanggal_akhir) {
        $this->db->select('pd.id, pd.id_pendapatan, pd.id_pekerjaan, pd.banyak, pd.total, p.nama_pekerjaan, SUM(pd.banyak) as banyak, SUM(pd.total) as total');
        $this->db->from('pendapatan_detail pd');
        $this->db->join('pendapatan pn', 'pn.id = pd.id_pendapatan');
        $this->db->join('pekerjaan p', 'p.id = pd.id_pekerjaan');
        $this->db->where('pn.id_karyawan', $id_karyawan);
        $this->db->where('pn.tanggal >=', $tanggal_awal);
        $this->db->where('pn.tanggal <=', $tanggal_akhir);
        $this->db->where('pn.status', 'selesai');
        $this->db->group_by('pd.id, pd.id_pendapatan, pd.id_pekerjaan, pd.banyak, pd.total, p.id, p.nama_pekerjaan');
        $this->db->order_by('pn.tanggal', 'ASC');
        
        return $this->db->get()->result();
    }

    public function get_laporan_pendapatan($tanggal_awal, $tanggal_akhir, $id_karyawan = null) {
        $this->db->select('p.*, k.nama_karyawan');
        $this->db->from('pendapatan p');
        $this->db->join('karyawan k', 'k.id = p.id_karyawan');
        
        if ($tanggal_awal && $tanggal_akhir) {
            $this->db->where('p.tanggal >=', $tanggal_awal);
            $this->db->where('p.tanggal <=', $tanggal_akhir);
        }
        
        if ($id_karyawan) {
            $this->db->where('p.id_karyawan', $id_karyawan);
        }
        
        $this->db->order_by('p.tanggal', 'ASC');
        return $this->db->get()->result();
    }
}

