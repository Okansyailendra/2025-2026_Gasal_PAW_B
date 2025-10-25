<?php
require 'validate.inc';

$errors = [];
$old = [];

if (!function_exists('validateNIM')) {
    function validateNIM(&$errors, $data, $field) {
        $value = isset($data[$field]) ? trim($data[$field]) : '';
        if ($value === '') {
            $errors[$field] = 'NIM harus diisi';
        } elseif (!ctype_digit($value)) {
            $errors[$field] = 'NIM harus berupa angka';
        } elseif (strlen($value) < 12) {
            $errors[$field] = 'NIM minimal 12 digit';
        }
    }
}

if (!function_exists('validateName')) {
    function validateName(&$errors, $data, $field) {
        $value = isset($data[$field]) ? trim($data[$field]) : '';
        if ($value === '') {
            $errors[$field] = 'Nama harus diisi';
        } elseif (mb_strlen($value) < 3) {
            $errors[$field] = 'Nama minimal 3 karakter';
        } elseif (!preg_match('/^[\p{L}\s\-\.]+$/u', $value)) {
            $errors[$field] = 'Nama hanya boleh berisi huruf, spasi, titik, atau tanda hubung';
        }
    }
}

if (!function_exists('validateEmail')) {
    function validateEmail(&$errors, $data, $field) {
        $value = isset($data[$field]) ? trim($data[$field]) : '';
        if ($value === '') {
            $errors[$field] = 'Email harus diisi';
        } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$field] = 'Format email tidak valid';
        }
    }
}

if (!function_exists('validatePassword')) {
    function validatePassword(&$errors, $data, $field) {
        $value = isset($data[$field]) ? $data[$field] : '';
        if ($value === '') {
            $errors[$field] = 'Password harus diisi';
        } elseif (strlen($value) < 6) {
            $errors[$field] = 'Password minimal 6 karakter';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $k => $v) {
        $old[$k] = htmlspecialchars(trim($v));
    }

    validateNIM($errors, $_POST, 'nim');
    validateName($errors, $_POST, 'nama');
    validateEmail($errors, $_POST, 'email');
    validatePassword($errors, $_POST, 'password');

    if ($errors) {
        echo "<h3>Terjadi kesalahan validasi:</h3><ul>";
        foreach ($errors as $field => $err) {
            echo "<li><b>$field</b> : $err</li>";
        }
        echo "</ul>";
        include 'mahasiswa_form.inc';
    } else {
        echo "<h2>Data Mahasiswa Valid!</h2>";
        echo "<ul>";
        foreach ($_POST as $k => $v) {
            echo "<li><b>$k</b>: " . htmlspecialchars($v) . "</li>";
        }
        echo "</ul>";
    }
} else {
    include 'mahasiswa_form.inc';
}
?>
