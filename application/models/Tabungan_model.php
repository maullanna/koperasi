<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tabungan_model extends CI_Model {
    private $table = 'tabungan';
    
    public function get_total_all() {
        $this->db->select_sum('saldo_tabungan');
        $result = $this->db->get('karyawan')->row();
        return $result->saldo_tabungan ?? 0;
    }
    
    public function get_all() {
        $this->db->select('tabungan.*, karyawan.nama_karyawan');
        $this->db->from('tabungan');
        $this->db->join('karyawan', 'karyawan.id = tabungan.id_karyawan');
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }
    
    public function insert($data) {
        $this->db->trans_start();
        
        $this->db->insert('tabungan', $data);
        
        // Update saldo tabungan karyawan
        if($data['jenis'] == 'setor') {
            $this->db->set('saldo_tabungan', 'saldo_tabungan + ' . $data['jumlah'], FALSE);
        } else {
            $this->db->set('saldo_tabungan', 'saldo_tabungan - ' . $data['jumlah'], FALSE);
        }
        $this->db->where('id', $data['id_karyawan']);
        $this->db->update('karyawan');
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_by_id($id) {
        $this->db->select('tabungan.*, karyawan.nama_karyawan');
        $this->db->from('tabungan');
        $this->db->join('karyawan', 'karyawan.id = tabungan.id_karyawan');
        $this->db->where('tabungan.id', $id);
        return $this->db->get()->row();
    }

    public function update($id, $data) {
        $this->db->trans_start();

        // Get the original tabungan data
        $original = $this->get_by_id($id);

        // Update tabungan record
        $this->db->where('id', $id);
        $this->db->update('tabungan', $data);

        // Adjust saldo_tabungan
        if ($original->jenis == 'setor') {
            $this->db->set('saldo_tabungan', 'saldo_tabungan - ' . $original->jumlah, FALSE);
        } else {
            $this->db->set('saldo_tabungan', 'saldo_tabungan + ' . $original->jumlah, FALSE);
        }

        if ($data['jenis'] == 'setor') {
            $this->db->set('saldo_tabungan', 'saldo_tabungan + ' . $data['jumlah'], FALSE);
        } else {
            $this->db->set('saldo_tabungan', 'saldo_tabungan - ' . $data['jumlah'], FALSE);
        }

        $this->db->where('id', $data['id_karyawan']);
        $this->db->update('karyawan');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete($id) {
        $this->db->trans_start();

        // Get the original tabungan data
        $original = $this->get_by_id($id);

        // Delete tabungan record
        $this->db->where('id', $id);
        $this->db->delete('tabungan');

        // Adjust saldo_tabungan
        if ($original->jenis == 'setor') {
            $this->db->set('saldo_tabungan', 'saldo_tabungan - ' . $original->jumlah, FALSE);
        } else {
            $this->db->set('saldo_tabungan', 'saldo_tabungan + ' . $original->jumlah, FALSE);
        }

        $this->db->where('id', $original->id_karyawan);
        $this->db->update('karyawan');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_total_by_karyawan_and_bulan($id_karyawan, $bulan) {
        // Ambil total setoran tabungan
        $this->db->select_sum('jumlah', 'total_setor');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan);
        $this->db->where('jenis', 'setor');
        $setoran = $this->db->get('tabungan')->row();
        
        // Ambil total penarikan tabungan
        $this->db->select_sum('jumlah', 'total_tarik');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where("DATE_FORMAT(tanggal, '%Y-%m')", $bulan);
        $this->db->where('jenis', 'tarik');
        $penarikan = $this->db->get('tabungan')->row();
        
        // Hitung total tabungan (setoran - penarikan)
        $total_setor = $setoran->total_setor ?? 0;
        $total_tarik = $penarikan->total_tarik ?? 0;
        
        return $total_setor - $total_tarik;
    }
    
    public function get_by_karyawan($id_karyawan) {
        $this->db->select('tabungan.*, karyawan.nama_karyawan');
        $this->db->from('tabungan');
        $this->db->join('karyawan', 'karyawan.id = tabungan.id_karyawan');
        $this->db->where('tabungan.id_karyawan', $id_karyawan);
        $this->db->order_by('tanggal', 'DESC');
        return $this->db->get()->result();
    }
    
    public function get_total_by_karyawan($id_karyawan) {
        $this->db->select_sum('jumlah');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('jenis', 'setor'); // Only count deposits
        $result = $this->db->get($this->table)->row();
        $total_setor = $result->jumlah ?? 0;

        $this->db->select_sum('jumlah');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('jenis', 'tarik'); // Subtract withdrawals
        $result = $this->db->get($this->table)->row();
        $total_tarik = $result->jumlah ?? 0;

        return $total_setor - $total_tarik;
    }
}