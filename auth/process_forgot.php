<?php
// auth/process_forgot.php

session_start();
require_once __DIR__ . '/../function.php';

// Cek jika sudah login
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? '';
    if ($role === 'admin') {
        header("Location: " . BASE_URL . "dashboard-siswa/index.php");
    } elseif ($role === 'guru') {
        header("Location: " . BASE_URL . "dashboard-guru/index.php");
    } else {
        header("Location: " . BASE_URL . "dashboard-siswa/index.php");
    }
    exit;
}

// Cek jika form tidak dikirim dengan benar
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'])) {
    $_SESSION['error'] = 'Email tidak ditemukan. Silakan coba lagi.';
    header('Location: forgot-password.php');
    exit;
}

$email = trim($_POST['email']);
// Validasi email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Mohon masukkan email yang valid.';
    header('Location: forgot-password.php');
    exit;
}

// Cari user berdasarkan email
$users = readJSON('users.json');

$userFound = null;
$userIndex = null;
foreach ($users as $index => $user) {
    if (strcasecmp($user['email'], $email) === 0) {
        $userFound = $user;
        $userIndex = $index;
        break;
    }
}

// Jika user tidak ditemukan
if (!$userFound) {
    // Jangan kasih tahu email tidak ditemukan untuk keamanan
    $_SESSION['success'] = 'Jika email terdaftar, kami akan mengirimkan link reset password.';
    header('Location: forgot-password.php');
    exit;
}

// Generate token reset password
$token = bin2hex(random_bytes(32));
$expiry = time() + 3600; // 1 jam

// Simpan token ke user
$users[$userIndex]['reset_token'] = $token;
$users[$userIndex]['reset_expiry'] = $expiry;

// Simpan kembali ke file (memakai LOCK_EX via helper)
writeJSON('users.json', $users);

// Buat link reset memakai BASE_URL agar konsisten di semua environment
$resetLink = rtrim(BASE_URL, '/') . '/auth/reset-password.php?token=' . $token;

// ============================================================
// KIRIM EMAIL - LANGSUNG PANGGIL DARI function.php
// ============================================================
$result = sendResetEmail($userFound['email'], $userFound['nama'], $resetLink);

if ($result['success']) {
    $_SESSION['success'] = '📧 Link reset password telah dikirim ke email Anda. Cek inbox atau folder spam.';
} else {
    $_SESSION['error'] = 'Gagal mengirim email: ' . ($result['message'] ?? 'Silakan coba lagi.');
}

// Hapus data sensitif dari session
unset($_SESSION['reset_token']);
unset($_SESSION['reset_email']);

header('Location: forgot-password.php');
exit;
?>