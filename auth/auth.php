<?php

// ======================================
// AUTH.PHP
// Proteksi Halaman Login
// EduCare
// ======================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/remember.php';

// ======================================
// REDIRECT KE LOGIN
// ======================================

function authRedirectToLogin(): void
{
    $baseUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '/';
    header("Location: " . $baseUrl . "auth/login.php");
    exit;
}

if (!isset($_SESSION['user'])) {
    authRedirectToLogin();
}

// ======================================
// AMBIL DATA USER
// ======================================

$user = $_SESSION['user'];

$id = $user['id'];
$nama = $user['nama'];
$email = $user['email'];
$role = $user['role'];

// ======================================
// FUNGSI ROLE
// ======================================

function isAdmin()
{
    return $_SESSION['user']['role'] === "admin";
}

function isGuru()
{
    return $_SESSION['user']['role'] === "guru";
}

function isSiswa()
{
    return $_SESSION['user']['role'] === "siswa";
}

// ======================================
// PROTEKSI ROLE
// ======================================

function onlyAdmin()
{
    if (!isAdmin()) {
        authRedirectToLogin();
    }
}

function onlyGuru()
{
    if (!isGuru()) {
        authRedirectToLogin();
    }
}

function onlySiswa()
{
    if (!isSiswa()) {
        authRedirectToLogin();
    }
}

// ======================================
// FORMAT NAMA
// ======================================

function userName()
{
    return htmlspecialchars($_SESSION['user']['nama']);
}

function userEmail()
{
    return htmlspecialchars($_SESSION['user']['email']);
}

function userRole()
{
    return htmlspecialchars($_SESSION['user']['role']);
}
