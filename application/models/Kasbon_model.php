<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasbon_model extends CI_Model {
    private $table = 'kasbon';
    
    public function get_total_outstanding() {
        $this->db->select_sum('saldo_kasbon');
        $result = $this->db->get('karyawan')->row();
        return $result->saldo_kasbon ?? 0;
    }
    
    public function get_all() {
        $this->db->select('kasbon.*, karyawan.nama_karyawan');
        $this->db->from('kasbon');
        $this->db->join('karyawan', 'karyawan.id = kasbon.id_karyawan');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }
    
    public function insert($data) {
        $this->db->trans_start();
        
        $this->db->insert('kasbon', $data);
        
        // Update saldo kasbon karyawan
        if($data['jenis'] == 'belum lunas') {  // Ubah dari 'pinjam' ke 'belum lunas'
            $this->db->set('saldo_kasbon', 'saldo_kasbon + ' . $data['jumlah'], FALSE);
        } else {
            $this->db->set('saldo_kasbon', 'saldo_kasbon - ' . $data['jumlah'], FALSE);
        }
        $this->db->where('id', $data['id_karyawan']);
        $this->db->update('karyawan');
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function get_by_id($id) {
        $this->db->select('kasbon.*, karyawan.nama_karyawan');
        $this->db->from('kasbon');
        $this->db->join('karyawan', 'karyawan.id = kasbon.id_karyawan');
        $this->db->where('kasbon.id', $id);
        return $this->db->get()->row();
    }

    public function update($id, $data) {
        $this->db->trans_start();

        // Get the original kasbon data
        $original = $this->get_by_id($id);

        // Update kasbon record
        $this->db->where('id', $id);
        $this->db->update('kasbon', $data);

        // Adjust saldo_kasbon
        if ($original->jenis == 'belum lunas') {  // Ubah dari 'pinjam' ke 'belum lunas'
            $this->db->set('saldo_kasbon', 'saldo_kasbon - ' . $original->jumlah, FALSE);
        } else {
            $this->db->set('saldo_kasbon', 'saldo_kasbon + ' . $original->jumlah, FALSE);
        }

        if ($data['jenis'] == 'belum lunas') {  // Ubah dari 'pinjam' ke 'belum lunas'
            $this->db->set('saldo_kasbon', 'saldo_kasbon + ' . $data['jumlah'], FALSE);
        } else {
            $this->db->set('saldo_kasbon', 'saldo_kasbon - ' . $data['jumlah'], FALSE);
        }

        $this->db->where('id', $data['id_karyawan']);
        $this->db->update('karyawan');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete($id) {
        $this->db->trans_start();

        // Get the original kasbon data
        $original = $this->get_by_id($id);

        // Delete kasbon record
        $this->db->where('id', $id);
        $this->db->delete('kasbon');

        // Adjust saldo_kasbon
        if ($original->jenis == 'belum lunas') {  // Ubah dari 'pinjam' ke 'belum lunas'
            $this->db->set('saldo_kasbon', 'saldo_kasbon - ' . $original->jumlah, FALSE);
        } else {
            $this->db->set('saldo_kasbon', 'saldo_kasbon + ' . $original->jumlah, FALSE);
        }

        $this->db->where('id', $original->id_karyawan);
        $this->db->update('karyawan');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function get_total_by_karyawan_and_bulan($id_karyawan, $bulan) {
        $this->db->select_sum('jumlah', 'total_kasbon');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan);
        $this->db->where('jenis', 'belum lunas'); // Ubah dari 'pinjam' ke 'belum lunas'
        $result = $this->db->get('kasbon')->row();
        
        // Ambil total pembayaran
        $this->db->select_sum('jumlah', 'total_bayar');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan);
        $this->db->where('jenis', 'lunas');  // Ubah dari 'bayar' ke 'lunas'
        $pembayaran = $this->db->get('kasbon')->row();
        
        // Hitung total kasbon (pinjaman - pembayaran)
        $total_pinjam = $result->total_kasbon ?? 0;
        $total_bayar = $pembayaran->total_bayar ?? 0;
        
        return $total_pinjam - $total_bayar;
    }
    
    public function get_total_by_karyawan($id_karyawan) {
        $this->db->select_sum('jumlah');
        $this->db->where('id_karyawan', $id_karyawan);
        // Jika ingin hanya menghitung pinjaman:
        // $this->db->where('jenis', 'pinjaman');
        $result = $this->db->get('kasbon')->row();
        return $result->jumlah ?? 0;
    }
    
    public function get_by_karyawan($id_karyawan) {
        $this->db->select('kasbon.*, karyawan.nama_karyawan');
        $this->db->from('kasbon');
        $this->db->join('karyawan', 'karyawan.id = kasbon.id_karyawan');
        $this->db->where('kasbon.id_karyawan', $id_karyawan);
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }
}