<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

$role = $_SESSION['user']['role'] ?? '';
$qs = (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';
if ($role === 'guru') {
    header('Location: ../dashboard-guru/index.php' . $qs . '#laporan-masuk');
} else {
    // admin tidak memiliki dashboard sendiri; hanya bisa masuk dashboard-siswa
    header('Location: ../dashboard-siswa/index.php' . $qs . '#laporan');
}
exit;
