<?php
// =========================================================================
// EduCare AJAX: sinkronisasi pilihan bahasa (id/en) dari client ke session
// server. Dipanggil oleh i18n.js setiap kali user mengganti bahasa, agar
// halaman yang dirender server-side (PHP) tahu bahasa aktif dan bisa
// menerjemahkan konten dinamis lewat t_dynamic().
// =========================================================================

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$lang = isset($_POST['lang']) ? (string) $_POST['lang'] : '';

if ($lang === 'id' || $lang === 'en') {
    $_SESSION['educare_lang'] = $lang;
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'lang' => $lang]);
    exit;
}

header('Content-Type: application/json');
http_response_code(400);
echo json_encode(['ok' => false, 'error' => 'invalid lang']);
exit;