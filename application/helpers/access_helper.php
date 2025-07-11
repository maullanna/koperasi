<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('has_owner_access')) {
    function has_owner_access() {
        $CI =& get_instance();
        return $CI->session->userdata('role') === 'owner';
    }
}

if (!function_exists('has_admin_access')) {
    function has_admin_access() {
        $CI =& get_instance();
        $role = $CI->session->userdata('role');
        return $role === 'admin' || $role === 'owner'; // owner juga memiliki akses admin
    }
}

/**
 * Fungsi untuk memeriksa apakah pengguna memiliki hak akses karyawan
 */
function is_karyawan() {
    $CI =& get_instance();
    return ($CI->session->userdata('role') == 'karyawan');
}

/**
 * Fungsi untuk membatasi akses hanya untuk admin/owner
 */
function admin_only() {
    $CI =& get_instance();
    if (!has_admin_access()) {
        $CI->session->set_flashdata('error', 'Anda tidak memiliki akses untuk fitur ini');
        redirect('dashboard');
    }
}