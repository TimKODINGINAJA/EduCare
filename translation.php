<?php
// =========================================================================
// EduCare Server-side Dynamic-Content Translation (DeepL)
// -------------------------------------------------------------------------
// Menerjemahkan KONTEN DINAMIS (nama quiz/materi/kategori, deskripsi, isi
// materi) otomatis ke bahasa Inggris lewat DeepL API, DENGAN PERLINDUNGAN
// agar tag HTML, url, heading markdown, dan blok kode (fenced ```) tidak
// ikut diterjemahkan / tidak rusak.
//
// KEAMANAN:
//  - Jika DEEPL_API_KEY kosong / DEEPL_ENABLED=false -> semua fungsi
//    langsung mengembalikan teks asli (fallback aman, app tetap jalan di
//    bahasa Indonesia). Tidak ada error.
//  - Gunakan panggilan server-side (PHP cURL); API key TIDAK dikirim ke
//    browser.
//  - Hasil terjemahan disimpan di cache file sehingga tidak translate ulang
//    terhadap teks yang sama (hemat kuota & cepat).
// =========================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/function.php';

/* ---------------------------------------------------------------------- */
/* 1. KONFIGURASI & KETERSEDIAAN                                          */
/* ---------------------------------------------------------------------- */

function deepl_available(): bool
{
    if (!function_exists('curl_init')) {
        return false; // ekstensi cURL tidak tersedia: nonaktifkan DeepL
    }
    $enabled = filter_var(env('DEEPL_ENABLED', 'false'), FILTER_VALIDATE_BOOLEAN);
    if (!$enabled) {
        return false;
    }
    $key = trim((string) env('DEEPL_API_KEY', ''));
    return $key !== '';
}

function deepl_api_url(): string
{
    return rtrim((string) env('DEEPL_API_URL', 'https://api-free.deepl.com/v2/translate'), '/');
}

/* ---------------------------------------------------------------------- */
/* 2. CACHE FILE (per teks)                                               */
/* ---------------------------------------------------------------------- */

function deepl_cache_file(): string
{
    return __DIR__ . '/data/deepl_cache.json';
}

function deepl_cache_load(): array
{
    $file = deepl_cache_file();
    if (!file_exists($file)) {
        return [];
    }
    $raw = file_get_contents($file);
    if ($raw === false) {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function deepl_cache_save(array $cache): void
{
    $file  = deepl_cache_file();
    $json  = json_encode($cache, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $json  = $json === false ? '{}' : $json;
    // Tulis tanpa BOM (jangan pakai Set-Content PowerShell pada data file).
    file_put_contents($file, $json);
}

/* ---------------------------------------------------------------------- */
/* 3. PERLINDUNGAN HTML / MARKDOWN / KODE                                  */
/*    - Token dipakai agar DeepL tidak menyentuh bagian yang terlindungi. */
/*    - Format token: «‹K<n>›» (tidak mungkin muncul dalam teks user).     */
/* ---------------------------------------------------------------------- */

function deepl_protect(string $text, array &$protected, string &$slug): string
{
    $idx = count($protected);

    // a) Fenced code blocks ``` ... ``` (termasuk ```html dst) -> lindungi utuh
    $patternCode = '/```[^\n]*\n(?:.*?\n)*?```/s';
    $text = preg_replace_callback($patternCode, function ($m) use (&$protected, &$idx) {
        $slug = 'C' . $idx;
        $protected[$slug] = $m[0];
        $idx++;
        return '«‹' . $slug . '›»';
    }, $text);

    // b) Inline code `...` -> lindungi
    $text = preg_replace_callback('/`[^`]+`/', function ($m) use (&$protected, &$idx) {
        $slug = 'I' . $idx;
        $protected[$slug] = $m[0];
        $idx++;
        return '«‹' . $slug . '›»';
    }, $text);

    // c) Markdown heading pada awal baris "## ..." -> lindungi penanda tingkat
    $text = preg_replace_callback('/(^|\n)(#{1,6})(\s)/', function ($m) use (&$protected, &$idx) {
        $slug = 'H' . $idx;
        $protected[$slug] = $m[1] . $m[2] . $m[3];
        $idx++;
        return $m[1] . '«‹' . $slug . '›»';
    }, $text);

    // d) Tag HTML <...> (attribute & isi tidak diterjemah) -> lindungi
    $text = preg_replace_callback('/<\/?[a-zA-Z][^>]*>/', function ($m) use (&$protected, &$idx) {
        $slug = 'T' . $idx;
        $protected[$slug] = $m[0];
        $idx++;
        return '«‹' . $slug . '›»';
    }, $text);

    // e) URL / tautan http(s) -> lindungi
    $text = preg_replace_callback('/https?:\/\/[^\s"<>]+/', function ($m) use (&$protected, &$idx) {
        $slug = 'U' . $idx;
        $protected[$slug] = $m[0];
        $idx++;
        return '«‹' . $slug . '›»';
    }, $text);

    return $text;
}

function deepl_restore(string $text, array $protected): string
{
    foreach ($protected as $slug => $original) {
        $text = str_replace('«‹' . $slug . '›»', $original, $text);
    }
    return $text;
}

/* ---------------------------------------------------------------------- */
/* 4. PANGGILAN DEEPL API                                                 */
/* ---------------------------------------------------------------------- */

function deepl_call(string $text): string
{
    if (!deepl_available()) {
        return $text; // fallback aman
    }

    $url = deepl_api_url();
    $ch  = curl_init($url);
    $post = http_build_query([
        'auth_key'          => trim((string) env('DEEPL_API_KEY', '')),
        'text'              => $text,
        'source_lang'       => 'ID',
        'target_lang'       => 'EN',
        'tag_handling'      => 'html',
        'preserve_formatting' => '1',
        'formality'         => 'default',
    ]);

    // Timeout dalam milidetik lebih ketat daripada detik: hindari halaman
    // menggantung lama bila jaringan lambat / API tidak membalas POST.
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_NOSIGNAL       => 1,
        CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_CONNECTTIMEOUT_MS => 6000,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_TIMEOUT_MS     => 12000,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: EduCare/1.0 (PHP cURL)',
        ],
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    ]);

    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $resp === '') {
        // Perluas batas waktu eksekusi PHP sebagai pengaman terakhir agar
        // halaman tidak sampai timeout fatal karena fase DNS/resolve.
        set_time_limit(20);
        return $text; // jaringan gagal -> fallback aman
    }

    $decoded   = json_decode($resp, true);
    $translated = isset($decoded['translations'][0]['text'])
        ? (string) $decoded['translations'][0]['text']
        : '';
    if ($translated === '') {
        return $text; // API error / format tak dikenal -> fallback aman
    }

    return $translated;
}

/* ---------------------------------------------------------------------- */
/* 5. FUNGSI UTAMA: TERJEMAHKAN TEKS DINAMIS DENGAN CACHE + PROTEKSI     */
/* ---------------------------------------------------------------------- */
/*  $text : teks asli (Indonesia)                                         */
/*  $lookupKey : string unik untuk cache (mis. 'materi.3.title')         */
/*  Return  : teks terkait permintaan bahasa (EN bila terjemah tersedia,   */
/*            atau kembali ke $text bila tidak / non-EN / tanpa key).      */
/* ---------------------------------------------------------------------- */

function t_dynamic(string $text, string $lookupKey = ''): string
{
    // Mode non-EN atau teks kosong -> kembalikan asli sekarang, tanpa API.
    if ($text === '') {
        return $text;
    }
    if (!is_en_lang() || !deepl_available()) {
        // Bahasa bukan English atau tanpa API key -> fallback aman (teks asli).
        return $text;
    }

    $cacheKey = $lookupKey !== '' ? $lookupKey : '#' . hash('sha256', $text);

    $cache = deepl_cache_load();
    if (isset($cache[$cacheKey]) && is_string($cache[$cacheKey]) && $cache[$cacheKey] !== '') {
        // Nilai string di cache bisa berupa hasil terjemah ATAU tombstone
        // prefiks "0:" (artinya gagal; jangan dipanggil ulang).
        if (strncmp($cache[$cacheKey], '0:', 2) === 0) {
            return $text; // pernah gagal -> jangan buang waktu lagi
        }
        return $cache[$cacheKey];
    }

    // Terjemahkan dengan proteksi HTML/markdown/kode.
    $protected  = [];
    $slug       = '';
    $prepared   = deepl_protect($text, $protected, $slug);
    $translated = deepl_call($prepared);
    $result     = deepl_restore($translated, $protected);

    // Jaga supaya hasil tidak lebih buruk dari aslinya.
    if (trim($result) === '' || $result === $text) {
        // Simpan tombstone agar tidak telepon berulang untuk teks sama saat
        // jaringan/API sedang bermasalah (mencegah halaman menggantung tiap load).
        $cache[$cacheKey] = '0:' . substr($text, 0, 32);
        deepl_cache_save($cache);
        return $text;
    }

    // Simpan cache.
    $cache[$cacheKey] = $result;
    deepl_cache_save($cache);

    return $result;
}

/* ---------------------------------------------------------------------- */
/* 6. Helper: cek apakah bahasa aktif English (untuk memanggil t_dynamic) */
/*    Bahasa disinkronkan dari client ke $_SESSION['educare_lang'] oleh   */
/*    i18n.js (via ajax-set-lang.php).                                    */
/* ---------------------------------------------------------------------- */

function is_en_lang(): bool
{
    return isset($_SESSION['educare_lang']) && $_SESSION['educare_lang'] === 'en';
}