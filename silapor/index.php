<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Pertahankan query string agar deep link (mis. ?id=5) tidak hilang saat redirect.
$qs = (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';

$role = $_SESSION['user']['role'] ?? '';
if ($role === 'guru') {
    header('Location: ../dashboard-guru/index.php' . $qs . '#laporan-masuk');
} else {
    // admin tidak memiliki dashboard sendiri; hanya bisa masuk dashboard-siswa
    header('Location: ../dashboard-siswa/index.php' . $qs . '#laporan');
}
exit;
