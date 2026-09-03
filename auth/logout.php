<?php
session_start();

// Hapus sesi user
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

// Hapus session semua jika diperlukan
session_unset();
session_destroy();

// Hapus cookie remember jika ada
if (isset($_COOKIE['remember_email'])) {
    setcookie('remember_email', '', time() - 3600, '/');
}

// Redirect ke halaman login auth
header('Location: login.php');
exit;
