<?php
session_start();

require_once __DIR__ . '/../function.php';

// Lokasi file JSON (nama relatif — readJSON/writeJSON me-resolve ke data/)
$file = "users.json";

// Baca seluruh data user (helper membuat file kosong bila belum ada)
$users = readJSON($file);

// Ambil data dari form
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? '';

// Validasi input kosong
if (
    empty($nama) ||
    empty($email) ||
    empty($password) ||
    empty($confirm) ||
    empty($role)
) {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: register.php");
    exit;
}

// Validasi email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Format email tidak valid!";
    header("Location: register.php");
    exit;
}

// Password minimal
if (strlen($password) < 6) {
    $_SESSION['error'] = "Password minimal 6 karakter!";
    header("Location: register.php");
    exit;
}

// Password sama?
if ($password !== $confirm) {
    $_SESSION['error'] = "Konfirmasi password tidak cocok!";
    header("Location: register.php");
    exit;
}

// Guru tidak boleh register
if ($role === 'guru') {
    $_SESSION['error'] = "Guru tidak diperbolehkan melakukan registrasi. Silakan gunakan akun guru yang sudah terdaftar.";
    header("Location: register.php");
    exit;
}

// Role hanya siswa
if ($role !== 'siswa') {
    $_SESSION['error'] = "Role tidak valid!";
    header("Location: register.php");
    exit;
}

// Cek email sudah ada?
foreach ($users as $user) {

    if (strtolower($user['email']) == strtolower($email)) {

        $_SESSION['error'] = "Email sudah digunakan!";

        header("Location: register.php");
        exit;
    }
}

// Generate ID baru (anti-duplikat: cari nilai tertinggi, bukan asumsi ID berurutan)
$id = 1;

$existingIds = array_column($users, 'id');

if (!empty($existingIds)) {

    $id = max($existingIds) + 1;
}

// Simpan user baru
$users[] = [

    "id" => $id,

    "nama" => $nama,

    "email" => strtolower($email),

    "password" => password_hash($password, PASSWORD_DEFAULT),

    "role" => $role,

    "created_at" => date("Y-m-d H:i:s")

];

// Simpan ke JSON (memakai LOCK_EX via helper)
writeJSON($file, $users);

// Redirect
$_SESSION['success'] = "Registrasi berhasil. Silakan login.";

header("Location: login.php");

exit;
