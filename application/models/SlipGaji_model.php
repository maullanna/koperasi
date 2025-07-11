<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SlipGaji_model extends CI_Model {

    public function get_all_slip_gaji() {
        $this->db->select('slip_gaji.id, slip_gaji.bulan, slip_gaji.gaji_bersih, karyawan.nama_karyawan');
        $this->db->from('slip_gaji');
        $this->db->join('karyawan', 'karyawan.id = slip_gaji.id_karyawan');
        return $this->db->get()->result();
    }

    public function get_slip_gaji_by_id($id) {
        // Query utama untuk slip gaji
        $this->db->select('slip_gaji.*, karyawan.nama_karyawan, karyawan.nip');
        $this->db->from('slip_gaji');
        $this->db->join('karyawan', 'karyawan.id = slip_gaji.id_karyawan');
        $this->db->where('slip_gaji.id', $id);
        $slip_gaji = $this->db->get()->row();
        
        if ($slip_gaji) {
            // Ambil detail pendapatan untuk periode slip gaji
            $this->db->select('p.nama_pekerjaan, SUM(pd.banyak) as total_banyak, SUM(pd.total) as total_pendapatan');
            $this->db->from('pendapatan_detail pd');
            $this->db->join('pendapatan pn', 'pn.id = pd.id_pendapatan');
            $this->db->join('pekerjaan p', 'p.id = pd.id_pekerjaan');
            $this->db->where('pn.id_karyawan', $slip_gaji->id_karyawan);
            $this->db->where('pn.tanggal >=', $slip_gaji->tanggal_awal);
            $this->db->where('pn.tanggal <=', $slip_gaji->tanggal_akhir);
            $this->db->where('pn.status', 'selesai');
            $this->db->group_by('p.id, p.nama_pekerjaan');
            $this->db->order_by('p.nama_pekerjaan', 'ASC');
            $slip_gaji->detail_pekerjaan = $this->db->get()->result();            
        }
        
        return $slip_gaji;
    }

    public function insert_slip_gaji($data) {
        // Validasi data wajib
        if (!isset($data['total_pendapatan']) || !is_numeric($data['total_pendapatan'])) {
            throw new Exception('Total pendapatan tidak valid');
        }

        // Hitung pemasukan BPJS jika belum dihitung
        if (!isset($data['pemasukan_bpjs'])) {
            $pengaturan = $this->Pengaturan_model->get_pengaturan();
            if (!$pengaturan) {
                throw new Exception('Pengaturan BPJS tidak ditemukan');
            }
            $bpjs_percentage = $pengaturan->potongan_bpjs;
            $data['pemasukan_bpjs'] = round(($data['total_pendapatan'] * $bpjs_percentage) / 100);
        }

        // Pastikan semua nilai numerik
        $total_pendapatan = floatval($data['total_pendapatan']);
        $uang_makan = isset($data['uang_makan']) ? floatval($data['uang_makan']) : 0;
        $pemasukan_bpjs = floatval($data['pemasukan_bpjs']);
        $total_kasbon = isset($data['total_kasbon']) ? floatval($data['total_kasbon']) : 0;
        $total_tabungan = isset($data['total_tabungan']) ? floatval($data['total_tabungan']) : 0;

        // Hitung ulang gaji bersih dengan pemasukan BPJS
        $data['gaji_bersih'] = ($total_pendapatan + $uang_makan + $pemasukan_bpjs) 
                              - ($total_kasbon + $total_tabungan);

        return $this->db->insert('slip_gaji', $data);
    }

    public function update_slip_gaji($id, $data) {
        // Hitung pemasukan BPJS jika belum dihitung
        if (!isset($data['pemasukan_bpjs'])) {
            $bpjs_percentage = $this->Pengaturan_model->get_pengaturan()->potongan_bpjs;
            $data['pemasukan_bpjs'] = round(($data['total_pendapatan'] * $bpjs_percentage) / 100);
        }

        // Hitung ulang gaji bersih dengan pemasukan BPJS
        $data['gaji_bersih'] = ($data['total_pendapatan'] + $data['uang_makan'] + $data['pemasukan_bpjs']) 
                              - ($data['total_kasbon'] + $data['total_tabungan']);

        $this->db->where('id', $id);
        return $this->db->update('slip_gaji', $data);
    }

    public function delete_slip_gaji($id) {
        $this->db->where('id', $id);
        return $this->db->delete('slip_gaji');
    }

    public function get_slip_gaji_by_karyawan_and_bulan($id_karyawan, $bulan) {
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('bulan', $bulan);
        return $this->db->get('slip_gaji')->row();
    }

    public function get_by_period($bulan, $tahun) {
        $this->db->select('slip_gaji.*, karyawan.nama_karyawan');
        $this->db->from('slip_gaji');
        $this->db->join('karyawan', 'karyawan.id = slip_gaji.id_karyawan');
        $this->db->where('MONTH(bulan)', $bulan);
        $this->db->where('YEAR(bulan)', $tahun);
        return $this->db->get()->result();
    }
    public function get_slip_gaji_by_karyawan($id_karyawan) {
        $this->db->select('slip_gaji.*, karyawan.nama_karyawan');
        $this->db->from('slip_gaji');
        $this->db->join('karyawan', 'karyawan.id = slip_gaji.id_karyawan');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->order_by('bulan', 'DESC');
        return $this->db->get()->result();
    }
    
    public function check_slip_exists($id_karyawan, $tanggal_awal, $tanggal_akhir) {
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('tanggal_awal', $tanggal_awal);
        $this->db->where('tanggal_akhir', $tanggal_akhir);
        return $this->db->get('slip_gaji')->num_rows() > 0;
    }

}