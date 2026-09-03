<?php
session_start();

require_once __DIR__ . '/../function.php';

// Gunakan BASE_URL yang sudah didefinisikan oleh function.php,
// konsisten dengan redirect yang dipakai di auth/login.php.
$baseUrl = rtrim(BASE_URL, '/') . '/';

// File JSON (nama relatif — readJSON/writeJSON me-resolve ke data/)
$file = "users.json";

// Baca users (helper membuat file kosong otomatis bila belum ada)
$users = readJSON($file);

// Cek dan seed akun Guru jika belum ada di data/users.json
$guruExists = false;
foreach ($users as $u) {
    if (strtolower($u['email']) === 'guru@gmail.com') {
        $guruExists = true;
        break;
    }
}

if (!$guruExists) {
    $guruId = 2; // default id for Guru
    if (!empty($users)) {
        $last = end($users);
        $guruId = $last['id'] + 1;
    }
    $users[] = [
        "id" => $guruId,
        "nama" => "Guru",
        "email" => "guru@gmail.com",
        "password" => password_hash("123", PASSWORD_DEFAULT),
        "role" => "guru",
        "created_at" => date("Y-m-d H:i:s")
    ];
    writeJSON($file, $users);
}

// Ambil input dari form
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// Cari user berdasarkan email (case-insensitive)
$userFound = null;

foreach ($users as $user) {
    if (strtolower($user['email']) === $email) {
        $userFound = $user;
        break;
    }
}

// Jika email tidak ditemukan
if (!$userFound) {
    $_SESSION['error'] = "Email tidak ditemukan!";
    header("Location: login.php");
    exit;
}

// Cek password via hash yang tersimpan (berlaku untuk semua role, termasuk guru)
if (!password_verify($password, $userFound['password'])) {
    $_SESSION['error'] = "Password salah!";
    header("Location: login.php");
    exit;
}

// Simpan Session
$_SESSION['user'] = [

    "id" => $userFound['id'],
    "nama" => $userFound['nama'],
    "email" => $userFound['email'],
    "role" => $userFound['role']

];

// Remember Me
if (isset($_POST['remember'])) {

    setcookie(
        "remember_email",
        $userFound['email'],
        time() + (86400 * 30), // 30 Hari
        "/"
    );
}

// Redirect berdasarkan role

switch ($userFound['role']) {

    case "admin":
        // Tidak ada dashboard admin terpisah; arahkan ke dashboard-siswa
        // (satu-satunya dashboard yang mengizinkan role admin).
        header("Location: " . $baseUrl . "dashboard-siswa/index.php");
        break;

    case "guru":
        header("Location: " . $baseUrl . "dashboard-guru/index.php");
        break;

    case "siswa":
        header("Location: " . $baseUrl . "dashboard-siswa/index.php");
        break;

    default:
        header("Location: " . $baseUrl . "dashboard-siswa/index.php");
        break;
}

exit;
