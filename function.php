<?php

// =========================================================================
// EduCare Central Functions Helper
// =========================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// Muat file .env (jika ada) ke $_ENV / getenv().
// .env TIDAK ikut di-commit (lihat .gitignore) sehingga kredensial (mis.
// SMTP) tetap aman saat project di-push ke GitHub. Gunakan .env.example
// sebagai templat variabel yang perlu diisi.
// =========================================================================
function loadEnvFile(): void
{
    $envPath = __DIR__ . '/.env';
    if (!file_exists($envPath)) {
        return;
    }
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || isset($line[0]) && $line[0] === '#') {
            continue;
        }
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        // Hapus tanda kutip di sekitar nilai jika ada
        if (strlen($val) >= 2 && (($val[0] === '"' && substr($val, -1) === '"') || ($val[0] === "'" && substr($val, -1) === "'"))) {
            $val = substr($val, 1, -1);
        }
        if ($key !== '') {
            $_ENV[$key] = $val;
            putenv($key . '=' . $val);
        }
    }
}

// Jangan timpa variabel env yang sudah diset di sistem
if (getenv('SMTP_USERNAME') === false) {
    loadEnvFile();
}

function env(string $key, $default = null)
{
    $value = getenv($key);
    if ($value === false) {
        $value = $_ENV[$key] ?? null;
    }
    return $value === null || $value === '' ? $default : $value;
}


function getProjectBaseUrl(): string
{
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $projectRoot = realpath(__DIR__);

    if ($documentRoot && $projectRoot && str_starts_with($projectRoot, $documentRoot)) {
        $relativePath = str_replace('\\', '/', substr($projectRoot, strlen($documentRoot)));
        $relativePath = trim($relativePath, '/');
        return $relativePath === '' ? '/' : '/' . $relativePath . '/';
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $scriptDir = dirname($scriptName);
    if ($scriptDir === '/' || $scriptDir === '.') {
        return '/';
    }

    return rtrim($scriptDir, '/') . '/';
}

// Deteksi Base URL secara otomatis berdasarkan posisi folder project
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', $scheme . '://' . $host . getProjectBaseUrl());
}

function assetUrl(string $path): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function pageUrl(string $path): string
{
    return assetUrl($path);
}

/**
 * Mendapatkan absolute path ke folder data
 */
function getDataFilePath(string $filename): string
{
    return __DIR__ . '/data/' . ltrim($filename, '/');
}

/**
 * Membaca data dari file JSON
 */
function readJSON(string $filename): array
{
    $path = getDataFilePath($filename);
    if (!file_exists($path)) {
        // Buat file baru jika tidak ada
        writeJSON($filename, []);
        return [];
    }
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

/**
 * Menulis data ke file JSON
 */
function writeJSON(string $filename, array $data): bool
{
    $path = getDataFilePath($filename);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $content, LOCK_EX) !== false;
}

/**
 * Menambahkan data baru ke file JSON (Insert)
 */
function insertJSON(string $filename, array $record): int
{
    $data = readJSON($filename);

    // Generate ID baru jika belum ada
    if (!isset($record['id'])) {
        $id = 1;
        if (!empty($data)) {
            $ids = array_column($data, 'id');
            $id = max($ids) + 1;
        }
        $record['id'] = $id;
    }

    if (!isset($record['created_at'])) {
        $record['created_at'] = date('Y-m-d H:i:s');
    }

    $data[] = $record;
    writeJSON($filename, $data);
    return $record['id'];
}

/**
 * Mengubah data di file JSON berdasarkan ID
 */
function updateJSON(string $filename, $id, array $newData): bool
{
    $data = readJSON($filename);
    $updated = false;

    foreach ($data as $index => $item) {
        if (isset($item['id']) && $item['id'] == $id) {
            // Gabungkan data lama dengan data baru
            $data[$index] = array_merge($item, $newData);
            $data[$index]['id'] = $id; // Pastikan ID tidak berubah
            if (!isset($data[$index]['updated_at'])) {
                $data[$index]['updated_at'] = date('Y-m-d H:i:s');
            }
            $updated = true;
            break;
        }
    }

    if ($updated) {
        writeJSON($filename, $data);
    }
    return $updated;
}

/**
 * Menghapus data dari file JSON berdasarkan ID
 */
function deleteJSON(string $filename, $id): bool
{
    $data = readJSON($filename);
    $originalCount = count($data);

    $data = array_values(array_filter($data, function ($item) use ($id) {
        return !(isset($item['id']) && $item['id'] == $id);
    }));

    if (count($data) !== $originalCount) {
        writeJSON($filename, $data);
        return true;
    }
    return false;
}

/**
 * Mengambil record single dari file JSON berdasarkan ID
 */
function findJSON(string $filename, $id): ?array
{
    $data = readJSON($filename);
    foreach ($data as $item) {
        if (isset($item['id']) && $item['id'] == $id) {
            return $item;
        }
    }
    return null;
}

// =========================================================================
// Autentikasi & Sesi Helpers
// =========================================================================

/**
 * Mengecek apakah user sudah login
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Melakukan proteksi halaman berdasarkan role
 */
function requireRole(array $roles): void
{
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        header('Location: ' . BASE_URL . 'auth/login.php');
        exit;
    }

    $userRole = $_SESSION['user']['role'] ?? '';
    if (!in_array($userRole, $roles)) {
        setFlashMessage('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        if ($userRole === 'guru') {
            header('Location: ' . BASE_URL . 'dashboard-guru/index.php');
        } else {
            header('Location: ' . BASE_URL . 'dashboard-siswa/index.php');
        }
        exit;
    }
}

// =========================================================================
// Sanitasi & Validasi Input
// =========================================================================

/**
 * Sanitasi data input untuk mencegah XSS dasar
 */
function sanitize($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
    } else {
        $data = htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

/**
 * Validasi email
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// =========================================================================
// UI & Tampilan Helpers
// =========================================================================

/**
 * Format tanggal ke format Indonesia (Contoh: 20 Juli 2026)
 */
function formatTanggalIndo(string $dateStr): string
{
    if (empty($dateStr)) return '-';
    $timestamp = strtotime($dateStr);
    if (!$timestamp) return $dateStr;

    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $numHari = date('w', $timestamp);
    $tgl = date('j', $timestamp);
    $numBulan = (int)date('n', $timestamp);
    $tahun = date('Y', $timestamp);

    return "$tgl " . $bulan[$numBulan] . " $tahun";
}

/**
 * Mengatur session flash message
 */
function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Mengambil session flash message
 */
function getFlashMessage(string $type): ?string
{
    if (isset($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    return null;
}

/**
 * Mendapatkan nilai lama input form (old input)
 */
function old(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}

// =========================================================================
// Sistem Kategori & Konten Materi (IT / Matematika / Umum)
// =========================================================================

/**
 * Menentukan kelompok kategori materi: 'it', 'mtk', atau 'umum'.
 * Prioritas: field 'group' pada data kategori (jika tersedia di kategori.json),
 * lalu fallback ke deteksi kata kunci pada nama kategori.
 * Ini menghindari hardcode kaku selama guru mengisi kolom "Grup" saat membuat kategori.
 */
function getCategoryGroup(string $categoryName, array $categories = []): string
{
    foreach ($categories as $cat) {
        if (isset($cat['name']) && strcasecmp($cat['name'], $categoryName) === 0 && !empty($cat['group'])) {
            return $cat['group'];
        }
    }

    $name = strtolower($categoryName);

    $mtkKeywords = ['matematika', 'mtk', 'aljabar', 'geometri', 'trigonometri', 'statistika', 'peluang', 'kalkulus', 'aritmatika', 'spldv', 'persamaan'];
    foreach ($mtkKeywords as $kw) {
        if (str_contains($name, $kw)) return 'mtk';
    }

    $itKeywords = [
        'html',
        'css',
        'javascript',
        ' js',
        'php',
        'python',
        'java',
        'c++',
        'sql',
        'database',
        'basis data',
        'jaringan',
        'tkj',
        'rpl',
        'dkv',
        'pemrograman',
        'programming',
        'web',
        'mobile',
        'flutter',
        'dart',
        'iot',
        'ui/ux',
        'uiux',
        'desain grafis',
        'keamanan',
        'sistem operasi',
        'git',
        'github',
        'version control',
        'coding',
        'developer',
        'algoritma',
        'data structure',
        'framework',
        'teknologi',
        'komputer',
        'informatika',
    ];
    foreach ($itKeywords as $kw) {
        if (str_contains($name, $kw)) return 'it';
    }

    return 'umum';
}

/**
 * Label tampilan untuk sebuah grup kategori.
 */
function getGroupLabel(string $group): string
{
    switch ($group) {
        case 'it':
            return 'IT / Teknologi';
        case 'mtk':
            return 'Matematika';
        default:
            return 'Umum';
    }
}

/**
 * Ikon Lucide untuk sebuah grup kategori.
 */
function getGroupIcon(string $group): string
{
    switch ($group) {
        case 'it':
            return 'code-2';
        case 'mtk':
            return 'calculator';
        default:
            return 'book-open';
    }
}

/**
 * Escape teks aman untuk disisipkan ke HTML (bukan atribut).
 */
function escMateri(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Mengubah URL YouTube menjadi URL embed (iframe). Mengembalikan string kosong
 * jika bukan URL YouTube yang valid.
 */
function materiYoutubeEmbed(string $url): string
{
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|win/?\?v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
        return 'https://www.youtube.com/embed/' . $match[1];
    }
    return '';
}

/**
 * Merender struktur chapter & pelajaran (lessons) buatan guru menjadi HTML.
 * Setiap pelajaran bisa berisi judul (t), durasi (dur), isi materi (content),
 * dan video (video_url). Konten dirender dengan renderMateriBodyWithCode.
 */
function renderMateriLessons(array $lessons, string $group = 'umum'): string
{
    if (empty($lessons)) {
        return '';
    }

    $html = '<div class="materi-lessons materi-content materi-content--' . escMateri($group) . '">';

    foreach ($lessons as $ci => $ch) {
        $chTitle = trim($ch['ch'] ?? '');
        $items   = $ch['items'] ?? [];
        if (is_array($items) && !empty($items)) {
            $html .= '<section class="materi-section">';
            $html .= '<h3 class="materi-section__title">'
                    . '<i data-lucide="book-open" class="materi-section__icon"></i>'
                    . escMateri($chTitle !== '' ? $chTitle : ('Chapter ' . ($ci + 1)))
                    . '</h3>';
            $html .= '<div class="materi-section__body">';

            foreach ($items as $li => $item) {
                $t        = trim($item['t'] ?? '');
                $dur      = trim($item['dur'] ?? '');
                $content  = trim($item['content'] ?? '');
                $videoUrl = trim($item['video_url'] ?? '');
                if ($t === '' && $content === '' && $videoUrl === '') {
                    continue;
                }

                $html .= '<div class="materi-lesson">';

                $badge = '';
                if ($dur !== '') {
                    $badge .= '<span class="materi-lesson__dur">' . escMateri($dur) . '</span>';
                }
                if ($videoUrl !== '') {
                    $badge .= '<span class="materi-lesson__video">' . escMateri('🎬 Video') . '</span>';
                }

                if ($t !== '') {
                    $html .= '<h4 class="materi-lesson__title">'
                            . escMateri(($li + 1) . '. ' . $t)
                            . ($badge !== '' ? '<span class="materi-lesson__badges">' . $badge . '</span>' : '')
                            . '</h4>';
                } elseif ($badge !== '') {
                    $html .= '<div class="materi-lesson__badges">' . $badge . '</div>';
                }

                $embed = '';
                if ($videoUrl !== '') {
                    $embed = materiYoutubeEmbed($videoUrl);
                }
                if ($embed !== '') {
                    $html .= '<div class="materi-lesson__embed">'
                            . '<div class="materi-lesson__video-wrap">'
                            . '<iframe src="' . escMateri($embed) . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                            . '</div></div>';
                }

                if ($content !== '') {
                    $html .= '<div class="materi-lesson__body">' . renderMateriBodyWithCode($content) . '</div>';
                }

                $html .= '</div>';
            }

            $html .= '</div></section>';
        }
    }

    $html .= '</div>';
    return $html;
}

/**
 * Mengubah teks inline (bold **x**, inline code `x`) menjadi HTML aman.
 */
function parseInlineMateri(string $text): string
{
    $text = escMateri($text);
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/`([^`]+?)`/', '<code class="materi-inline-code">$1</code>', $text);
    return $text;
}

/**
 * Mengubah satu blok teks (tanpa heading, tanpa code fence) menjadi HTML
 * berupa paragraf dan/atau list (ul/ol) sesuai formatnya.
 */
function parseMateriBlock(string $block): string
{
    $block = trim($block);
    if ($block === '') return '';

    $lines = preg_split('/\n/', $block);
    $html = '';
    $mode = null;
    $paragraphBuffer = [];

    $flushParagraph = function () use (&$paragraphBuffer, &$html) {
        if (!empty($paragraphBuffer)) {
            $html .= '<p>' . parseInlineMateri(implode(' ', $paragraphBuffer)) . '</p>';
            $paragraphBuffer = [];
        }
    };
    $closeList = function () use (&$mode, &$html) {
        if ($mode === 'ul') $html .= '</ul>';
        if ($mode === 'ol') $html .= '</ol>';
        $mode = null;
    };

    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '') {
            $flushParagraph();
            $closeList();
            continue;
        }
        if (preg_match('/^[-*]\s+(.*)$/', $trim, $m)) {
            $flushParagraph();
            if ($mode !== 'ul') {
                $closeList();
                $html .= '<ul class="materi-list">';
                $mode = 'ul';
            }
            $html .= '<li>' . parseInlineMateri($m[1]) . '</li>';
            continue;
        }
        if (preg_match('/^\d+[\.\)]\s+(.*)$/', $trim, $m)) {
            $flushParagraph();
            if ($mode !== 'ol') {
                $closeList();
                $html .= '<ol class="materi-list materi-list--ol">';
                $mode = 'ol';
            }
            $html .= '<li>' . parseInlineMateri($m[1]) . '</li>';
            continue;
        }
        $closeList();
        $paragraphBuffer[] = $trim;
    }
    $flushParagraph();
    $closeList();
    return $html;
}

/**
 * Menentukan ikon Lucide berdasarkan judul section (heuristik kata kunci).
 */
function getSectionIcon(string $heading): string
{
    $h = strtolower($heading);
    $map = [
        'deskripsi' => 'align-left',
        'pengertian' => 'lightbulb',
        'tujuan' => 'target',
        'penjelasan materi' => 'book-open',
        'penjelasan' => 'book-open',
        'contoh soal' => 'pencil',
        'contoh' => 'flask-conical',
        'source code' => 'code-2',
        'code' => 'code-2',
        'output' => 'terminal',
        'hasil' => 'terminal',
        'langkah' => 'list-checks',
        'praktik' => 'list-checks',
        'kesimpulan' => 'check-circle-2',
        'quiz' => 'help-circle',
        'latihan' => 'help-circle',
        'rumus' => 'sigma',
        'keterangan' => 'info',
        'diketahui' => 'clipboard-list',
        'ditanya' => 'help-circle',
        'penyelesaian' => 'calculator',
        'cara pengerjaan' => 'calculator',
        'jawaban' => 'check-circle-2',
        'poin penting' => 'list',
        'penerapan' => 'sparkles',
    ];
    foreach ($map as $kw => $icon) {
        if (str_contains($h, $kw)) return $icon;
    }
    return 'file-text';
}

/**
 * Merender satu blok kode fenced ( ```lang ... ``` ) menjadi HTML dengan
 * header nama bahasa dan tombol copy code.
 */
function renderCodeBlock(string $lang, string $code): string
{
    $lang = trim($lang);
    $langLabel = $lang !== '' ? strtoupper($lang) : 'CODE';
    $hlLang = $lang !== '' ? preg_replace('/[^a-z0-9+#]/i', '', $lang) : 'plaintext';
    $code = rtrim($code, "\n");
    $id = 'code-' . substr(md5($code . $lang . mt_rand()), 0, 10);
    $idAttr = escMateri($id);

    return '
    <div class="code-block">
        <div class="code-block__header">
            <span class="code-block__lang">' . escMateri($langLabel) . '</span>
            <button type="button" class="code-block__copy" data-copy-target="' . $idAttr . '">
                <i data-lucide="copy" class="w-3.5 h-3.5"></i><span>Copy</span>
            </button>
        </div>
        <pre><code id="' . $idAttr . '" class="language-' . escMateri($hlLang) . '">' . escMateri($code) . '</code></pre>
    </div>';
}

/**
 * Merender satu badan teks yang mungkin berisi campuran paragraf/list biasa
 * dan blok kode fenced ```lang ... ```.
 */
function renderMateriBodyWithCode(string $body): string
{
    $body = trim($body);
    if ($body === '') return '';

    $codePattern = '/```([a-zA-Z0-9+#]*)\n(.*?)```/s';
    if (!preg_match($codePattern, $body)) {
        return parseMateriBlock($body);
    }

    $html = '';
    $lastEnd = 0;
    if (preg_match_all($codePattern, $body, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $idx => $full) {
            $start = $full[1];
            $textBefore = substr($body, $lastEnd, $start - $lastEnd);
            if (trim($textBefore) !== '') {
                $html .= parseMateriBlock($textBefore);
            }
            $lang = $matches[1][$idx][0];
            $code = $matches[2][$idx][0];
            $html .= renderCodeBlock($lang, $code);
            $lastEnd = $start + strlen($full[0]);
        }
        $textAfter = substr($body, $lastEnd);
        if (trim($textAfter) !== '') {
            $html .= parseMateriBlock($textAfter);
        }
    }
    return $html;
}

/**
 * Parser utama konten materi.
 *
 * Mendukung format markdown ringan dengan heading level 2 ("## Judul Section")
 * untuk memisahkan bagian seperti Pengertian, Tujuan Pembelajaran, Contoh, Code,
 * Rumus, Kesimpulan, dsb — sesuai kelompok materi (IT / Matematika / Umum).
 * Fenced code block ```lang ... ``` didukung di bagian manapun dan otomatis
 * dirender dengan syntax highlighting + tombol copy.
 *
 * Jika konten TIDAK memiliki heading "## " sama sekali (misalnya materi lama
 * atau materi bebas yang ditulis guru tanpa struktur), fungsi ini otomatis
 * fallback merender konten sebagai paragraf biasa (tetap mendukung code block),
 * sehingga materi lama tetap bisa dibuka tanpa error.
 */
function renderMateriContent(string $content, string $group = 'umum'): string
{
    $content = str_replace(["\r\n", "\r"], "\n", $content);

    $pattern = '/^##\s+(.+?)\s*$/m';
    if (!preg_match($pattern, $content)) {
        return '<div class="materi-content materi-content--' . escMateri($group) . '">'
            . '<div class="materi-intro">' . renderMateriBodyWithCode($content) . '</div></div>';
    }

    $parts = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
    $html = '<div class="materi-content materi-content--' . escMateri($group) . '">';

    $intro = trim($parts[0] ?? '');
    if ($intro !== '') {
        $html .= '<div class="materi-intro">' . renderMateriBodyWithCode($intro) . '</div>';
    }

    for ($i = 1; $i < count($parts); $i += 2) {
        $heading = trim($parts[$i]);
        $body = $parts[$i + 1] ?? '';
        $icon = getSectionIcon($heading);
        $html .= '<section class="materi-section">';
        $html .= '<h3 class="materi-section__title"><i data-lucide="' . escMateri($icon) . '" class="materi-section__icon"></i>' . escMateri($heading) . '</h3>';
        $html .= '<div class="materi-section__body">' . renderMateriBodyWithCode($body) . '</div>';
        $html .= '</section>';
    }

    $html .= '</div>';
    return $html;
}

// =========================================================================
// Notifikasi Terhubung Guru <-> Siswa
// notifications.json dibuat otomatis oleh readJSON() saat pertama dipakai.
// =========================================================================

/**
 * Kirim notifikasi baru.
 * $to boleh: 'all' (semua orang), 'guru' (semua guru), 'siswa' (semua siswa),
 * atau ID user tertentu (int/string) untuk notifikasi personal.
 */
function addNotification($to, string $icon, string $title, string $msg): int
{
    return insertJSON('notifications.json', [
        'to'    => (string) $to,
        'icon'  => $icon,
        'title' => $title,
        'msg'   => $msg,
        'read'  => false,
    ]);
}

/**
 * Ambil notifikasi untuk user tertentu sesuai role-nya, terbaru dulu.
 */
function getNotificationsFor($userId, string $role): array
{
    $list = readJSON('notifications.json');
    $filtered = array_values(array_filter($list, function ($n) use ($userId, $role) {
        $to = (string) ($n['to'] ?? 'all');
        if ($to === 'all') return true;
        if ($to === 'guru' && $role === 'guru') return true;
        if ($to === 'siswa' && $role === 'siswa') return true;
        return $to === (string) $userId;
    }));
    usort($filtered, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
    return $filtered;
}

function markNotificationRead(int $id): bool
{
    return updateJSON('notifications.json', $id, ['read' => true]);
}

// =========================================================================
// Log Aktivitas Terhubung Guru <-> Siswa
// activities.json dibuat otomatis oleh readJSON() saat pertama dipakai.
// =========================================================================

function addActivity(int $userId, string $name, string $role, string $icon, string $text): int
{
    return insertJSON('activities.json', [
        'user_id' => $userId,
        'name'    => $name,
        'role'    => $role,
        'icon'    => $icon,
        'text'    => $text,
    ]);
}

/**
 * Ambil log aktivitas. $userId null = semua siswa (untuk dilihat Guru).
 */
function getActivities(?int $userId = null, int $limit = 20): array
{
    $list = readJSON('activities.json');
    if ($userId !== null) {
        $list = array_values(array_filter($list, fn($a) => ($a['user_id'] ?? null) == $userId));
    }
    usort($list, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
    return array_slice($list, 0, $limit);
}

// =========================================================================
// XP & Leaderboard Terhubung
// leaderboard.json dibuat otomatis oleh readJSON() saat pertama dipakai.
// =========================================================================

function addXp(int $userId, string $name, int $amount): array
{
    $list = readJSON('leaderboard.json');
    $found = false;
    foreach ($list as &$row) {
        if (($row['user_id'] ?? null) == $userId) {
            $row['xp']    = max(0, ($row['xp'] ?? 0) + $amount);
            $row['name']  = $name;
            $row['level'] = 1 + intdiv($row['xp'], 250);
            $found = true;
            break;
        }
    }
    unset($row);
    if (!$found) {
        $xp = max(0, $amount);
        $list[] = ['user_id' => $userId, 'name' => $name, 'xp' => $xp, 'level' => 1 + intdiv($xp, 250)];
    }
    writeJSON('leaderboard.json', $list);
    return $list;
}

function getLeaderboard(int $limit = 20): array
{
    $list = readJSON('leaderboard.json');
    usort($list, fn($a, $b) => ($b['xp'] ?? 0) <=> ($a['xp'] ?? 0));
    return array_slice($list, 0, $limit);
}

// =========================================================================
// CSRF Protection
// =========================================================================

/**
 * Ambil (atau buat bila belum ada) token CSRF per-sesi untuk form.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render input hidden <input type="hidden" name="csrf_token" value="...">.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validasi token CSRF dari request POST. Return true jika cocok.
 */
function csrfVerify(?string $token): bool
{
    $expected = $_SESSION['csrf_token'] ?? '';
    return is_string($token) && is_string($expected) && $expected !== '' && hash_equals($expected, $token);
}

// =========================================================================
// Password Reset Helpers
// =========================================================================

/**
 * Generate token reset password
 */
function generateResetToken(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * Simpan token reset password ke user
 */
function saveResetToken(string $email, string $token): bool
{
    $users = readJSON('users.json');
    
    foreach ($users as &$user) {
        if (strcasecmp($user['email'], $email) === 0) {
            $user['reset_token'] = $token;
            $user['reset_expiry'] = time() + 3600; // 1 jam
            writeJSON('users.json', $users);
            return true;
        }
    }
    return false;
}

/**
 * Cari user berdasarkan reset token
 */
function findUserByResetToken(string $token): ?array
{
    $users = readJSON('users.json');
    foreach ($users as $user) {
        if (isset($user['reset_token']) && $user['reset_token'] === $token) {
            // Cek apakah token masih berlaku
            if (isset($user['reset_expiry']) && $user['reset_expiry'] > time()) {
                return $user;
            }
        }
    }
    return null;
}

/**
 * Update password dan hapus token
 */
function updatePasswordByToken(string $token, string $newPassword): bool
{
    $users = readJSON('users.json');
    
    foreach ($users as &$user) {
        if (isset($user['reset_token']) && $user['reset_token'] === $token) {
            $user['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            unset($user['reset_token']);
            unset($user['reset_expiry']);
            writeJSON('users.json', $users);
            return true;
        }
    }
    return false;
}

// =========================================================================
// SMTP EMAIL HELPER - MENGGUNAKAN PHPMailer
// =========================================================================

/**
 * Kirim email menggunakan PHPMailer via Gmail SMTP
 */
function sendResetEmail($toEmail, $toName, $resetLink): array
{
    // Cari autoload PHPMailer
    $autoloadPaths = [
        __DIR__ . '/vendor/autoload.php',
        __DIR__ . '/../vendor/autoload.php',
    ];
    
    $loaded = false;
    foreach ($autoloadPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $loaded = true;
            break;
        }
    }
    
    if (!$loaded) {
        return ['success' => false, 'message' => 'PHPMailer tidak ditemukan. Silakan install: composer require phpmailer/phpmailer'];
    }
    
    // ============================================================
    // KONFIGURASI SMTP - DIAMBIL dari environment / file .env
    // (lihat .env.example). JANGAN hardcode kredensial di kode.
    // ============================================================
    $smtpUsername = env('SMTP_USERNAME', '');
    $smtpPassword = env('SMTP_PASSWORD', '');
    $smtpFromEmail = env('SMTP_FROM_EMAIL', $smtpUsername);
    $smtpFromName = env('SMTP_FROM_NAME', 'EduCare Official');
    $smtpHost = env('SMTP_HOST', 'smtp.gmail.com');
    $smtpPort = (int) env('SMTP_PORT', 587);

    if ($smtpUsername === '' || $smtpPassword === '') {
        return ['success' => false, 'message' => 'Konfigurasi SMTP belum diset. Buat file .env dari .env.example dan isi SMTP_USERNAME & SMTP_PASSWORD.'];
    }

    try {
        // Buat instance PHPMailer
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUsername;
        $mail->Password   = $smtpPassword;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtpPort;
        $mail->CharSet    = 'UTF-8';
        
        // Pengirim & Penerima
        $mail->setFrom($smtpFromEmail, $smtpFromName);
        $mail->addAddress($toEmail, $toName);
        $mail->addReplyTo($smtpFromEmail, $smtpFromName);
        
        // Konten Email - HTML
        $mail->isHTML(true);
        $mail->Subject = '🔐 Reset Password - EduCare';
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; margin: 0; }
                .container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
                .header { text-align: center; margin-bottom: 25px; }
                .header h1 { color: #4f46e5; font-size: 26px; margin: 0; }
                .header p { color: #888; font-size: 14px; margin: 4px 0 0; }
                .content { color: #333; line-height: 1.7; font-size: 15px; }
                .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; padding: 14px 40px; text-decoration: none; border-radius: 10px; font-weight: 700; margin: 20px 0; border: none; }
                .btn:hover { background: #4338ca; }
                .link-box { background: #f1f4f9; padding: 12px 16px; border-radius: 8px; font-size: 13px; word-break: break-all; color: #555; font-family: monospace; margin: 10px 0; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px; }
                .warning { color: #dc2626; font-size: 13px; }
                .text-center { text-align: center; }
                .logo { font-size: 40px; margin-bottom: 8px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="logo">📚</div>
                    <h1>EduCare</h1>
                    <p>Platform Belajar Online</p>
                </div>
                <div class="content">
                    <p>Halo <strong>' . htmlspecialchars($toName) . '</strong>,</p>
                    <p>Kami menerima permintaan untuk mereset kata sandi akun EduCare Anda.</p>
                    <p>Klik tombol di bawah ini untuk mengatur ulang kata sandi:</p>
                    <div class="text-center">
                        <a href="' . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . '" class="btn">🔑 Atur Ulang Kata Sandi</a>
                    </div>
                    <p style="font-size: 14px; color: #666;">Atau salin link ini ke browser:</p>
                    <div class="link-box">' . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . '</div>
                    <p class="warning">⏳ Link ini hanya berlaku selama 1 jam.</p>
                    <p style="font-size: 14px; color: #666;">Jika Anda tidak meminta reset password, abaikan email ini.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' EduCare. Hak cipta dilindungi undang-undang.</p>
                    <p style="margin-top:6px;">Email ini dikirim secara otomatis, harap tidak membalas.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        // Plain text version
        $mail->AltBody = "Halo $toName,\n\nKami menerima permintaan reset password akun EduCare Anda.\n\nKlik link berikut untuk mengatur ulang kata sandi:\n$resetLink\n\nLink ini berlaku selama 1 jam.\n\nJika Anda tidak meminta reset password, abaikan email ini.\n\nTerima kasih,\nTim EduCare";
        
        $mail->send();
        return ['success' => true, 'message' => 'Email berhasil dikirim'];
        
    } catch (\Exception $e) {
        error_log("Email gagal dikirim: " . $mail->ErrorInfo);
        return ['success' => false, 'message' => $mail->ErrorInfo];
    }
}