<?php
// VALIDASI NAMA (hanya huruf dan tanda khusus)
function validateName(&$errors, $field_list, $field_name)
{
    $pattern = "/^[a-zA-Z'\- ]+$/";
    if (!isset($field_list[$field_name]) || empty(trim($field_list[$field_name]))) {
        $errors[$field_name] = 'required';
    } else if (!preg_match($pattern, $field_list[$field_name])) {
        $errors[$field_name] = 'invalid';
    }
}

// VALIDASI EMAIL (gunakan regex dan filter bawaan)
function validateEmail(&$errors, $field_list, $field_name)
{
    if (!isset($field_list[$field_name]) || empty(trim($field_list[$field_name]))) {
        $errors[$field_name] = 'required';
        return;
    }
    $email = trim($field_list[$field_name]);
    $pattern = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($pattern, $email)) {
        $errors[$field_name] = 'invalid';
    }
}

// VALIDASI PASSWORD (minimal 8 karakter, harus ada huruf besar dan angka)
function validatePassword(&$errors, $field_list, $field_name)
{
    if (!isset($field_list[$field_name]) || empty($field_list[$field_name])) {
        $errors[$field_name] = 'required';
        return;
    }
    $pwd = $field_list[$field_name];
    if (strlen($pwd) < 8) {
        $errors[$field_name] = 'too_short';
    }
    if (!preg_match('/[A-Z]/', $pwd)) {
        $errors[$field_name] = isset($errors[$field_name]) ? $errors[$field_name] . ',no_upper' : 'no_upper';
    }
    if (!preg_match('/\d/', $pwd)) {
        $errors[$field_name] = isset($errors[$field_name]) ? $errors[$field_name] . ',no_number' : 'no_number';
    }
}
