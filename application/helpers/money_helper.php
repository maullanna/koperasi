<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_money')) {
    function format_money($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('unformat_money')) {
    function unformat_money($formatted_amount) {
        return (float) str_replace(['Rp ', '.', ','], '', $formatted_amount);
    }
}

if (!function_exists('clean_money_input')) {
    function clean_money_input($value) {
        // Hapus 'Rp', spasi, titik, dan koma
        $clean = preg_replace('/[Rp\s\.,]/', '', $value);
        
        // Pastikan hanya angka yang tersisa
        $number = preg_replace('/\D/', '', $clean);
        
        // Konversi ke integer, return 0 jika invalid
        return is_numeric($number) ? (int)$number : 0;
    }
}

if (!function_exists('validate_money_input')) {
    function validate_money_input($value, $field_name = 'Nominal') {
        $clean_value = clean_money_input($value);
        if ($clean_value <= 0) {
            return "{$field_name} harus lebih dari 0";
        }
        return '';
    }
}