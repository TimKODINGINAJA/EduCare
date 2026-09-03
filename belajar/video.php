<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

$role = $_SESSION['user']['role'] ?? '';
if ($role === 'guru') {
    header('Location: ../dashboard-guru/index.php');
} else {
    header('Location: ../dashboard-siswa/index.php');
}
exit;
