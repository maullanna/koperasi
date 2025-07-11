<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FinancialData_model extends CI_Model {
    
    public function get_data($id_karyawan, $tanggal_awal, $tanggal_akhir) {
        // Get total pendapatan dari hasil kerja dengan harga yang benar
        $this->db->select('COALESCE(SUM(pd.banyak * pd.harga_karyawan), 0) as total_pendapatan');
        $this->db->from('pendapatan_detail pd');
        $this->db->join('pendapatan p', 'p.id = pd.id_pendapatan');
        $this->db->where('p.id_karyawan', $id_karyawan);
        $this->db->where('p.tanggal >=', $tanggal_awal);
        $this->db->where('p.tanggal <=', $tanggal_akhir);
        $this->db->where('p.status', 'selesai');
        $total_pendapatan = $this->db->get()->row()->total_pendapatan;

        // Hitung jumlah hari kerja berdasarkan kehadiran
        $this->db->select('COUNT(DISTINCT DATE(tanggal)) as jumlah_hari');
        $this->db->from('absensi');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('tanggal >=', $tanggal_awal);
        $this->db->where('tanggal <=', $tanggal_akhir);
        $this->db->where('status', 'hadir'); // Hanya hitung hari dengan status hadir
        $jumlah_hari = $this->db->get()->row()->jumlah_hari;

        // Ambil nilai uang makan dari pengaturan dan kalikan dengan jumlah hari hadir
        $pengaturan = $this->Pengaturan_model->get_pengaturan();
        $uang_makan = ($pengaturan->uang_makan ?? 0) * $jumlah_hari; // Nilai uang makan per hari dikali jumlah hari hadir

        // Get total kasbon
        $this->db->select('COALESCE(SUM(jumlah), 0) as total_kasbon');
        $this->db->from('kasbon');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('tanggal >=', $tanggal_awal);
        $this->db->where('tanggal <=', $tanggal_akhir);
        $total_kasbon = $this->db->get()->row()->total_kasbon;

        // Get total tabungan
        $this->db->select('COALESCE(SUM(jumlah), 0) as total_tabungan');
        $this->db->from('tabungan');
        $this->db->where('id_karyawan', $id_karyawan);
        $this->db->where('tanggal >=', $tanggal_awal);
        $this->db->where('tanggal <=', $tanggal_akhir);
        $total_tabungan = $this->db->get()->row()->total_tabungan;

        // Calculate BPJS
        $pemasukan_bpjs = $pengaturan->pemasukan_bpjs ?? 0;
        $bpjs_amount = ($total_pendapatan * ($pemasukan_bpjs / 100));

        return [
            'total_pendapatan' => (float)$total_pendapatan,
            'total_uang_makan' => (float)$uang_makan,
            'total_kasbon' => (float)$total_kasbon,
            'total_tabungan' => (float)$total_tabungan,
            'pemasukan_bpjs' => (float)$bpjs_amount,
            'jumlah_hari' => (int)$jumlah_hari
        ];
    }
}
