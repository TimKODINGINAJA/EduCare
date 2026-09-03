<?php
// ============================================================
// Endpoint kecil untuk menandai materi "selesai dipelajari" ketika siswa
// membuka detail materi dari fitur Materi di dashboard siswa.
// Dipanggil lewat fetch() dari assets/js/dashboard-siswa.js supaya siswa
// tidak perlu reload halaman (mengikuti pola progress.json yang sudah
// dipakai oleh belajar/detail-materi.php).
// ============================================================
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../function.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Belum login']);
    exit;
}

$role = $_SESSION['user']['role'] ?? '';
if ($role !== 'siswa' && $role !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Tidak diizinkan']);
    exit;
}

$userId   = $_SESSION['user']['id'] ?? null;
$userName = $_SESSION['user']['nama'] ?? '';
$userClass = $_SESSION['user']['class'] ?? $_SESSION['user']['kelas'] ?? '';
$category = trim($_POST['category'] ?? '');

if (!$userId || $category === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Proteksi CSRF untuk endpoint yang mengubah data.
if (!csrfVerify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Token tidak valid']);
    exit;
}

// Validasi: kategori harus benar-benar ada di kategori.json agar tidak
// menyuntikkan key arbitrer (mis. path traversal / key palsu) ke progress.
$validCats = array_column(readJSON('kategori.json'), 'name');
if (!in_array($category, $validCats, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Kategori tidak valid']);
    exit;
}

// Gunakan helper readJSON/writeJSON (memakai LOCK_EX) untuk mencegah
// race condition saat dua request menulis progress.json secara bersamaan.
$progressList = readJSON('progress.json');

$updated = false;
foreach ($progressList as &$p) {
    if (isset($p['user_id']) && $p['user_id'] == $userId) {
        if (!isset($p['progress']) || !is_array($p['progress'])) {
            $p['progress'] = [];
        }
        $p['progress'][$category] = 100;
        $p['name'] = $userName ?: ($p['name'] ?? '');
        if ($userClass !== '') {
            $p['class'] = $userClass;
        }
        $updated = true;
        break;
    }
}
unset($p);

if (!$updated) {
    $progressList[] = [
        'user_id'  => $userId,
        'name'     => $userName,
        'class'    => $userClass !== '' ? $userClass : '',
        'progress' => [$category => 100],
        'score'    => 0,
    ];
}

writeJSON('progress.json', $progressList);

echo json_encode(['ok' => true]);
