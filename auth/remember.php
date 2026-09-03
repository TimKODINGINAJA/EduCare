<?php

// =======================================
// REMEMBER.PHP
// EduCare
// Auto Login Menggunakan Cookie
// =======================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =======================================
// JIKA SUDAH LOGIN
// =======================================

if (isset($_SESSION['user'])) {
    return;
}

// =======================================
// CEK COOKIE
// =======================================

if (!isset($_COOKIE['remember_email'])) {
    return;
}

$email = trim($_COOKIE['remember_email']);

// =======================================
// FILE JSON
// =======================================

$file = __DIR__ . "/../data/users.json";

if (!file_exists($file)) {
    return;
}

// =======================================
// AMBIL DATA USER
// =======================================

$users = json_decode(file_get_contents($file), true);

if (!is_array($users)) {
    return;
}

// =======================================
// CARI USER
// =======================================

foreach ($users as $user) {

    if (
        isset($user['email']) &&
        strtolower($user['email']) === strtolower($email)
    ) {

        $_SESSION['user'] = [

            "id"    => $user['id'],
            "nama"  => $user['nama'],
            "email" => $user['email'],
            "role"  => $user['role']

        ];

        return;
    }
}