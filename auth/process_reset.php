<?php
session_start();
require_once __DIR__ . '/../function.php';

// Cek jika sudah login — arahkan ke dashboard sesuai role
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? '';
    if ($role === 'guru') {
        header('Location: ' . BASE_URL . 'dashboard-guru/index.php');
    } else {
        header('Location: ' . BASE_URL . 'dashboard-siswa/index.php');
    }
    exit;
}

// Cek jika form tidak dikirim dengan benar
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Akses tidak valid.';
    header('Location: forgot-password.php');
    exit;
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

// Validasi token
if (empty($token)) {
    $_SESSION['error'] = 'Token tidak valid.';
    header('Location: forgot-password.php');
    exit;
}

// Validasi password
if (empty($password) || strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter.';
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}

if ($password !== $passwordConfirm) {
    $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok.';
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}

// Cari user dengan token tersebut
$users = readJSON('users.json');

$userFound = false;
foreach ($users as &$user) {
    if (isset($user['reset_token']) && $user['reset_token'] === $token) {
        // Cek apakah token masih berlaku
        if (isset($user['reset_expiry']) && $user['reset_expiry'] > time()) {
            // Update password
            $user['password'] = password_hash($password, PASSWORD_DEFAULT);
            // Hapus token
            unset($user['reset_token']);
            unset($user['reset_expiry']);
            $userFound = true;
            break;
        }
    }
}

if (!$userFound) {
    $_SESSION['error'] = 'Token reset password tidak valid atau sudah kadaluarsa.';
    header('Location: forgot-password.php');
    exit;
}

// Simpan kembali ke file (memakai LOCK_EX via helper)
writeJSON('users.json', $users);

$_SESSION['success'] = 'Password berhasil direset! Silakan login dengan password baru Anda.';

// Hapus session reset
unset($_SESSION['reset_token']);
unset($_SESSION['reset_email']);

header('Location: login.php');
exit;