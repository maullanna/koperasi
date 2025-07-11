<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Financialdata extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['FinancialData_model', 'Pengaturan_model']);
        $this->load->helper('money');
    }

    public function get_data($id_karyawan, $tanggal_awal, $tanggal_akhir) {
        // Validasi format tanggal
        if (!$this->_validate_date($tanggal_awal) || !$this->_validate_date($tanggal_akhir)) {
            http_response_code(400);
            echo json_encode(['error' => 'Format tanggal tidak valid']);
            return;
        }

        try {
            // Ambil data finansial dari model
            $data = $this->FinancialData_model->get_data($id_karyawan, $tanggal_awal, $tanggal_akhir);
            echo json_encode($data);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function _validate_date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
