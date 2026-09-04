<?php
session_start();

// ============================================================
// 1. AUTHENTICATION & AUTHORIZATION
// ============================================================
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

$role   = $_SESSION['user']['role'] ?? '';
$name   = $_SESSION['user']['nama'] ?? 'Pengguna';
$userId = $_SESSION['user']['id'] ?? null;

if ($role !== 'siswa' && $role !== 'admin') {
    header('Location: ../dashboard-guru/index.php');
    exit;
}

// ============================================================
// 2. LOAD DATA
// ============================================================
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../translation.php';

$materi          = readJSON('materi.json');
$builtinCourses  = readJSON('courses.json');
$quiz            = readJSON('quiz.json');
$reports         = readJSON('reports.json');
$progressAll     = readJSON('progress.json');
$kategoriList    = readJSON('kategori.json');

// ============================================================
// 3. HANDLE POST REQUESTS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['submit_report'])) {
        if (!csrfVerify($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit;
        }
        $title    = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'Umum');
        $desc     = trim($_POST['desc'] ?? '');

        // Validasi panjang input agar data JSON tidak membengkak.
        if (mb_strlen($title) > 200 || mb_strlen($desc) > 2000) {
            $_SESSION['success_message'] = "Judul maksimal 200 karakter dan deskripsi maksimal 2000 karakter.";
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '#') . '#laporan');
            exit;
        }
        // Whitelist kategori laporan.
        $validCategories = ['Umum', 'Fasilitas', 'Akademik', 'Kedisiplinan', 'Lainnya'];
        if (!in_array($category, $validCategories, true)) {
            $category = 'Umum';
        }

        if ($title !== '') {
            $reports = readJSON('reports.json');

            $newId      = (count($reports) ? (max(array_column($reports, 'id')) + 1) : 1);
            $reports[] = [
                'id'       => $newId,
                'title'    => $title,
                'by_id'    => $userId,
                'by_name'  => $name,
                'category' => $category,
                'status'   => 'Menunggu',
                'date'     => date('Y-m-d'),
                'desc'     => $desc
            ];

            writeJSON('reports.json', $reports);
            $_SESSION['success_message'] = "Laporan berhasil dikirim!";
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '#') . '#laporan');
            exit;
        }
    }

    if (isset($_POST['delete_report']) && isset($_POST['report_id'])) {
        if (!csrfVerify($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit;
        }
        $rid  = (int) $_POST['report_id'];
        $reports = readJSON('reports.json');
        $reports = array_values(array_filter($reports, function ($r) use ($rid, $userId) {
            return !($r['id'] == $rid && $r['by_id'] == $userId);
        }));
        writeJSON('reports.json', $reports);

        $_SESSION['success_message'] = "Laporan dibatalkan/dihapus.";
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '#') . '#laporan');
        exit;
    }

    if (isset($_POST['submit_quiz'])) {
        $quizId   = (int) ($_POST['quiz_id'] ?? 0);
        $materiId = (int) ($_POST['materi_id'] ?? 0);
        // Skor dikirim sebagai persentase (0-100). Klamp ke rentang aman
        // untuk mencegah forging skor di luar rentang via POST langsung.
        $score    = max(0, min(100, (int) ($_POST['score'] ?? 0)));
        $quizName = trim($_POST['quiz_name'] ?? '');

        if ($quizName === '') {
            foreach ($quiz as $q) {
                if ((int)$q['id'] === $quizId) {
                    $quizName = $q['name'];
                    break;
                }
            }
            if ($quizName === '' && $materiId > 0) {
                foreach ($materi as $m) {
                    if ((int)$m['id'] === $materiId) {
                        $quizName = 'Quiz: ' . $m['title'];
                        break;
                    }
                }
            }
            if ($quizName === '') {
                $quizName = 'Quiz';
            }
        }

        $progressList = readJSON('progress.json');

        $updated = false;
        foreach ($progressList as &$p) {
            if (isset($p['user_id']) && $p['user_id'] == $userId) {
                $p['score'] = max($p['score'] ?? 0, $score);
                if (!isset($p['progress'])) {
                    $p['progress'] = [];
                }
                $p['progress'][$quizName] = 100;
                $updated = true;
                break;
            }
        }
        unset($p);

        if (!$updated) {
            $progressList[] = [
                'user_id'  => $userId,
                'name'     => $name,
                'class'    => '9A',
                'progress' => [$quizName => 100],
                'score'    => $score
            ];
        }

        writeJSON('progress.json', $progressList);

        addXp($userId, $name, 20 + intdiv($score, 10));
        addActivity($userId, $name, 'siswa', '✅', "Menyelesaikan \"$quizName\" dengan nilai $score");
        addNotification('guru', '📝', 'Quiz Selesai', "$name menyelesaikan \"$quizName\" dengan nilai $score");

        $_SESSION['success_message'] = "Selamat! Anda menyelesaikan $quizName dengan skor $score.";
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '#') . '#quiz');
        exit;
    }
}

// ============================================================
// 4. LOAD DATA & PROSES UNTUK TAMPILAN
// ============================================================
$myProgress = null;
foreach ($progressAll as $p) {
    if (isset($p['user_id']) && $p['user_id'] == $userId) {
        $myProgress = $p;
        break;
    }
}

$myReports = array_values(array_filter($reports, function ($r) use ($userId) {
    return isset($r['by_id']) && $r['by_id'] == $userId;
}));

$myDoneQuizNames = $myProgress['progress'] ?? [];

$guruQuizzes = [];
foreach ($quiz as $q) {
    if (($q['status'] ?? '') !== 'Aktif') continue;

    $qCount = (int) ($q['questions'] ?? count($q['questions_list'] ?? $q['items'] ?? []));
    if ($qCount < 0) {
        $qCount = 0;
    }

    $tiedMateriId = isset($q['materi_id']) ? (int) $q['materi_id'] : null;
    $tiedMateri   = null;
    if ($tiedMateriId) {
        foreach ($materi as $m) {
            if ((int) ($m['id'] ?? 0) === $tiedMateriId) {
                $tiedMateri = $m;
                break;
            }
        }
    }

    $guruQuizzes[] = [
        'id'         => $q['id'],
        'name'       => t_dynamic($q['name'], 'quiz.' . $q['id'] . '.name'),
        'questions'  => $qCount,
        'done'       => isset($myDoneQuizNames[$q['name']]),
        'fromMateri' => $tiedMateriId !== null,
        'materiId'   => $tiedMateriId,
        'emoji'      => $tiedMateri['emoji'] ?? '❓',
        'color'      => $tiedMateri['color'] ?? '#4f7cff',
        'category'   => $tiedMateri['category'] ?? null,
    ];
}

$materiIdsWithQuiz = [];
foreach ($quiz as $q) {
    if (isset($q['materi_id'])) {
        $materiIdsWithQuiz[(int) $q['materi_id']] = true;
    }
}

// Materi yang BELUM punya quiz asli di quiz.json: jangan sampai muncul
// sebagai "Quiz: ..." palsu dengan 0 soal di daftar Semua Quiz, karena
// tautannya hanya ujung ke halaman "belum berisi soal". Generate entri
// fallback hanya jika materi tersebut benar-benar punya sumber soal
// (quiz_questions / quiz_questions_list / questions / items).
foreach ($materi as $m) {
    $mId = (int) ($m['id'] ?? 0);
    if (isset($materiIdsWithQuiz[$mId])) {
        continue;
    }

    $mInline = $m['quiz_questions_list'] ?? $m['questions'] ?? $m['items'] ?? [];
    $qCount  = (int) ($m['quiz_questions'] ?? 0);
    if ($qCount <= 0 && (!is_array($mInline) || count($mInline) === 0)) {
        continue;
    }
    if ($qCount <= 0 && is_array($mInline)) {
        $qCount = count($mInline);
    }

    $mTitle   = $m['title'] ?? 'Materi';
    $quizName = 'Quiz: ' . $mTitle;

    $guruQuizzes[] = [
        'id'         => 6000 + $mId,
        'name'       => t_dynamic($quizName, 'materi.' . $mId . '.quizname'),
        'questions'  => $qCount,
        'done'       => isset($myDoneQuizNames[$quizName]),
        'fromMateri' => true,
        'materiId'   => $mId,
    ];
}

$statusCounts = ['Menunggu' => 0, 'Diproses' => 0, 'Selesai' => 0];
foreach ($myReports as $mr) {
    $st = $mr['status'] ?? 'Menunggu';
    if (isset($statusCounts[$st])) {
        $statusCounts[$st]++;
    }
}

$totalMateri       = count($materi);
$totalQuiz         = count($guruQuizzes);
$myDoneQuizCount   = count(array_filter($guruQuizzes, fn($gq) => $gq['done']));
$myScore = $myProgress['score'] ?? 0;

$itCats = ['web', 'ai', 'data', 'mobile', 'uiux', 'tech', 'it', 'programming'];
$guruCourses = [];
foreach ($materi as $m) {
    $catRaw = $m['cat'] ?? 'umum';
    $guruCourses[] = [
        'id'        => 5000 + (int)($m['id'] ?? 0),
        'catGroup'  => in_array($catRaw, $itCats) ? 'it' : 'umum',
        'cat'       => $catRaw,
        'emoji'     => $m['emoji'] ?? '📘',
        'title'     => t_dynamic($m['title'] ?? 'Kursus', 'materi.' . ($m['id'] ?? 0) . '.title'),
        'desc'      => t_dynamic($m['desc'] ?? '', 'materi.' . ($m['id'] ?? 0) . '.desc'),
        'level'     => $m['level'] ?? 'beginner',
        'stars'     => $m['stars'] ?? 4.8,
        'students'  => $m['students'] ?? 0,
        'modules'   => $m['modules'] ?? 0,
        'color'     => $m['color'] ?? '#0d1f3c',
        'lessons'   => $m['lessons'] ?? [],
        'fromGuru'  => true,
    ];
}

function ec_youtube_embed(string $url): string
{
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|win/?\?v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
        return 'https://www.youtube.com/embed/' . $match[1];
    }
    return '';
}

$myProgressMap = $myProgress['progress'] ?? [];

$materiSorted = $materi;
usort($materiSorted, fn($a, $b) => ($a['id'] ?? 0) <=> ($b['id'] ?? 0));

$materiFull = [];
foreach ($materiSorted as $idx => $m) {
    $mId       = (int) ($m['id'] ?? 0);
    $category  = $m['category'] ?? 'Umum';
    $group     = getCategoryGroup($category, $kategoriList);

    $linkedQuizName = null;
    $linkedQuizId   = null;
    foreach ($quiz as $q) {
        if (isset($q['materi_id']) && (int) $q['materi_id'] === $mId) {
            $linkedQuizName = $q['name'];
            $linkedQuizId   = $q['id'] ?? null;
            break;
        }
    }
    if ($linkedQuizName === null) {
        foreach ($quiz as $q) {
            if (($q['status'] ?? '') === 'Aktif' && stripos($q['name'], $category) !== false) {
                $linkedQuizName = $q['name'];
                $linkedQuizId   = $q['id'] ?? null;
                break;
            }
        }
    }

    $prevMateri = $materiSorted[$idx - 1] ?? null;
    $nextMateri = $materiSorted[$idx + 1] ?? null;

    $videoUrl = $m['video_url'] ?? '';
    $materiFull[] = [
        'id'            => $mId,
        'title'         => t_dynamic($m['title'] ?? 'Materi', 'materi.' . $mId . '.title'),
        'category'      => $category,
        'group'         => $group,
        'groupLabel'    => t_dynamic(getGroupLabel($group), 'group.' . $group . '.label'),
        'groupIcon'     => getGroupIcon($group),
        'emoji'         => $m['emoji'] ?? '📘',
        'desc'          => t_dynamic($m['desc'] ?? '', 'materi.' . $mId . '.desc'),
        'color'         => $m['color'] ?? '#1e293b',
        'dateLabel'     => formatTanggalIndo($m['date'] ?? date('Y-m-d')),
        'video_url'     => $videoUrl,
        'embedUrl'      => $videoUrl !== '' ? ec_youtube_embed($videoUrl) : '',
        'contentHtml'   => renderMateriContent(t_dynamic($m['content'] ?? '', 'materi.' . $mId . '.content'), $group),
        'lessonsHtml'   => renderMateriLessons(is_array($m['lessons'] ?? null) ? $m['lessons'] : [], $group),
        'done'          => (($myProgressMap[$category] ?? 0) >= 100),
        'quizAvailable' => $linkedQuizName !== null,
        'quizName'      => $linkedQuizName !== null ? t_dynamic($linkedQuizName, 'quiz.' . ($linkedQuizId ?: '') . '.name') : '',
        'prevId'        => $prevMateri['id'] ?? null,
        'prevTitle'     => $prevMateri ? t_dynamic($prevMateri['title'] ?? '', 'materi.' . $prevMateri['id'] . '.title') : '',
        'nextId'        => $nextMateri['id'] ?? null,
        'nextTitle'     => $nextMateri ? t_dynamic($nextMateri['title'] ?? '', 'materi.' . $nextMateri['id'] . '.title') : '',
    ];
}

$categoryToGuruCourseId = [];
foreach ($materi as $m) {
    if (!empty($m['category'])) {
        $categoryToGuruCourseId[$m['category']] = 5000 + (int) ($m['id'] ?? 0);
    }
}

$realEnrolledCourses = [];
foreach ($myProgressMap as $catKey => $pct) {
    if (stripos((string) $catKey, 'quiz') !== false) {
        continue;
    }
    if (!isset($categoryToGuruCourseId[$catKey])) {
        continue;
    }
    $pctNum = is_numeric($pct) ? max(0, min(100, (int) $pct)) : 0;
    if ($pctNum <= 0) {
        continue;
    }

    $gcId = $categoryToGuruCourseId[$catKey];
    $gc   = null;
    foreach ($guruCourses as $c) {
        if ($c['id'] === $gcId) {
            $gc = $c;
            break;
        }
    }
    if (!$gc) {
        continue;
    }

    $allLessonKeys = [];
    foreach (($gc['lessons'] ?? []) as $ci => $ch) {
        foreach (($ch['items'] ?? []) as $li => $item) {
            $allLessonKeys[] = $ci . '_' . $li;
        }
    }
    $totalLessons = count($allLessonKeys);
    $doneCount    = $totalLessons > 0 ? (int) round($totalLessons * $pctNum / 100) : 0;

    $realEnrolledCourses[] = [
        'id'               => $gcId,
        'progress'         => $pctNum,
        'completedLessons' => array_slice($allLessonKeys, 0, $doneCount),
        'enrolledAt'       => date('c'),
    ];
}

// Persentase progres dihitung dari kursus yang benar-benar diikuti
// (progress.json), BUKAN dari jumlah entri di map progress. Entri quiz
// ("Quiz X", "Quiz: Y") juga jangan dihitung — kalau dihitung, siswa yang
// baru mengerjakan 2-4 quiz akan tampak 50-100% tanpa menyentuh materi.
$progressPercent = 0;
if (count($realEnrolledCourses) > 0) {
    $envSum = array_sum(array_column($realEnrolledCourses, 'progress'));
    $progressPercent = (int) round($envSum / count($realEnrolledCourses));
}
$progressPercent = max(0, min(100, $progressPercent));

$realCompletedCourseIds = array_values(array_map(
    fn($e) => $e['id'],
    array_filter($realEnrolledCourses, fn($e) => $e['progress'] >= 100)
));

$realCertificates = [];
foreach ($realEnrolledCourses as $e) {
    if ($e['progress'] < 100) {
        continue;
    }
    foreach ($guruCourses as $c) {
        if ($c['id'] === $e['id']) {
            $realCertificates[] = [
                'courseId' => $e['id'],
                'title'    => $c['title'],
                'date'     => date('d/m/Y'),
                'emoji'    => $c['emoji'] ?? '🏅',
            ];
            break;
        }
    }
}

$realActivity = [];
foreach ($realEnrolledCourses as $e) {
    foreach ($guruCourses as $c) {
        if ($c['id'] === $e['id']) {
            $label = $e['progress'] >= 100
                ? "Menyelesaikan kursus \"{$c['title']}\""
                : "Belajar \"{$c['title']}\" ({$e['progress']}%)";
            $realActivity[] = ['text' => $label, 'icon' => $e['progress'] >= 100 ? '✅' : '📖', 'time' => 'Terbaru'];
            break;
        }
    }
}
if (!$realActivity) {
    $realActivity[] = ['text' => 'Belum ada aktivitas belajar. Yuk mulai dari Materi Pembelajaran!', 'icon' => '📘', 'time' => 'Sekarang'];
}

// Leaderboard asli dari data/leaderboard.json (getLeaderboard mengurutkan
// berdasarkan XP tertinggi). Pastikan user yang sedang login selalu tampil,
// termasuk jika belum masuk 20 besar.
$leaderboardRows = array_map(function ($r) use ($userId) {
    $nm = (string)($r['name'] ?? 'Siswa');
    return [
        'name'   => $nm,
        'xp'     => (int)($r['xp'] ?? 0),
        'level'  => (int)($r['level'] ?? 1),
        'avatar' => mb_strtoupper(mb_substr(trim($nm), 0, 1)),
        'isYou'  => (string)($r['user_id'] ?? '') === (string)$userId,
    ];
}, getLeaderboard(20));

$meInLeaderboard = false;
foreach ($leaderboardRows as $lr) {
    if ($lr['isYou']) {
        $meInLeaderboard = true;
        break;
    }
}
if (!$meInLeaderboard) {
    $leaderboardRows[] = [
        'name'   => $name,
        'xp'     => max(120, intval($myScore * 8 + $progressPercent * 3)),
        'level'  => 1 + (int) floor(($progressPercent + $myScore) / 250),
        'avatar' => mb_strtoupper(mb_substr(trim($name), 0, 1)),
        'isYou'  => true,
    ];
}
usort($leaderboardRows, fn($a, $b) => $b['xp'] <=> $a['xp']);

// Petakan kategori kursus bawaan -> materi guru pertama berkategori sama,
// lalu tautkan quiz guru yang menempel pada materi itu. Hasilnya dipakai
// halaman quiz siswa supaya kursus bawaan tanpa soal bawaan tetap bisa
// "Kerjakan Quiz" ke quiz asli buatan guru (jika ada).
$catToMateriId = [];
foreach ($materi as $m) {
    $mc = $m['cat'] ?? '';
    if ($mc !== '' && !isset($catToMateriId[$mc])) {
        $catToMateriId[$mc] = (int) ($m['id'] ?? 0);
    }
}
$courseQuizMap = [];
foreach ($builtinCourses as $bc) {
    $bcCat = $bc['cat'] ?? $bc['catGroup'] ?? '';
    $mId   = $catToMateriId[$bcCat] ?? 0;
    if ($mId <= 0) {
        continue;
    }
    $linked = null;
    foreach ($guruQuizzes as $gq) {
        if ((int) ($gq['materiId'] ?? 0) === $mId) {
            $linked = $gq;
            break;
        }
    }
    if ($linked === null) {
        continue;
    }
    $courseQuizMap[(int) $bc['id']] = [
        'name'      => $linked['name'],
        'questions' => (int) ($linked['questions'] ?? 0),
        'done'      => !empty($linked['done']),
        'url'       => $linked['fromMateri']
            ? rtrim(BASE_URL, '/') . '/belajar/quiz.php?materi_id=' . (int) $linked['materiId']
            : rtrim(BASE_URL, '/') . '/belajar/quiz.php?id=' . (int) $linked['id'],
    ];
}

$dashboardUser = [
    'id'     => $userId,
    'fname'  => $name,
    'lname'  => '',
    'uname'  => $name,
    'email'  => $_SESSION['user']['email'] ?? '',
    'inst'   => $_SESSION['user']['sekolah'] ?? 'EduCare',
    'csrf'   => csrfToken(),
    'role'   => $role,
    'status' => 'Pelajar Aktif'
];

$dashboardSeed = [
    'courses'     => $builtinCourses,
    'guruCourses' => $guruCourses,
    'guruQuizzes' => $guruQuizzes,
    'courseQuiz'  => $courseQuizMap,
    'materi'      => $materiFull,
    'xp'         => max(120, intval($myScore * 8 + $progressPercent * 3)),
    'level'      => 1 + (int) floor(($progressPercent + $myScore) / 250),
    'streak'     => 1,
    'totalHours' => 0,
    'quizzesDoneCount' => $myDoneQuizCount,
    'enrolledCourses' => $realEnrolledCourses,
    'completedCourses' => $realCompletedCourseIds,
    'certificates'     => $realCertificates,
    'activity' => $realActivity,
    'leaderboard' => $leaderboardRows,
    'notifications' => [
        ['icon' => '📚', 'title' => 'Materi Baru', 'msg' => 'Ada materi terbaru yang siap dipelajari.', 'time' => 'Baru saja', 'read' => false],
        ['icon' => '🎯', 'title' => 'Target Mingguan', 'msg' => 'Selesaikan 2 modul lagi untuk naik level.', 'time' => '1 jam lalu', 'read' => false]
    ],
    'settings' => [
        'emailNotif'       => true,
        'courseNotif'      => true,
        'reminderNotif'    => true,
        'publicProfile'    => true,
        'leaderboard'      => true,
        'themeMode'        => 'light',
        'themeAccentDark'  => '#3D7BFF',
        'themeAccentLight' => '#2F5FE0'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title data-i18n="page.dashboard_siswa">Dashboard Siswa • EduCare</title>
    <script>
        (function() {
            try {
                // Sumber kebenaran tema global adalah 'educare-theme'
                // (dipakai bareng landing page & dashboard-guru).
                var savedEdu = localStorage.getItem('educare-theme');
                var mode;
                if (savedEdu === 'dark' || savedEdu === 'light') {
                    mode = savedEdu;
                } else {
                    var legacy = localStorage.getItem('themeMode');
                    mode = legacy ? (JSON.parse(legacy) || 'light') : 'light';
                }
                var root = document.documentElement;
                if (mode !== 'dark') {
                    root.classList.add('light-mode');
                    var accentRaw = localStorage.getItem('themeAccentLight');
                    var accent = accentRaw ? JSON.parse(accentRaw) : '#2F5FE0';
                    root.style.setProperty('--cyan', accent);
                    root.style.setProperty('--cd', accent + '1A');
                    root.style.setProperty('--cg', accent + '3D');
                } else {
                    var accentDRaw = localStorage.getItem('themeAccentDark');
                    var accentD = accentDRaw ? JSON.parse(accentDRaw) : '#4C8DFF';
                    root.style.setProperty('--cyan', accentD);
                    root.style.setProperty('--cd', accentD + '1A');
                    root.style.setProperty('--cg', accentD + '3D');
                }
            } catch (e) {
                document.documentElement.classList.add('light-mode');
            }
        })();
    </script>
    <link rel="stylesheet" href="<?= htmlspecialchars(assetUrl('assets/css/dashboard-siswa.css')) ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Bricolage+Grotesque:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Ratakan tengah judul & subjudul di halaman Papan Peringkat */
        #dp-leaderboard .dtb {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- ============================================================
    PARTIKEL BACKGROUND
    ============================================================ -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- ============================================================
    TOAST
    ============================================================ -->
    <div id="toast"></div>

    <!-- ============================================================
    MOBILE OVERLAY
    ============================================================ -->
    <div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

    <!-- ============================================================
    DASHBOARD LAYOUT
    ============================================================ -->
    <div class="dlayout">

        <!-- ============================================================
        SIDEBAR
        ============================================================ -->
        <aside class="sidebar" id="sidebar">
            <div class="sb-brand">
                <div class="sb-logo">
                    <img
                        src="<?= htmlspecialchars(assetUrl('assets/img/EduCare-logo.png')) ?>"
                        alt="Logo EduCare"
                        width="40"
                        height="40"
                        class="w-10 h-10 object-contain rounded-xl">
                </div>
                <div class="sb-brand-text">
                    <span class="sb-brand-name">EduCare</span>
                    <span class="sb-brand-tag" data-i18n="siswa.sidebar.brand_tag">Platform Sekolah</span>
                </div>
                <button class="sb-collapse-toggle" id="sbCollapseBtn" type="button" onclick="toggleSidebarCollapse()" aria-label="Ciutkan menu"><i data-lucide="chevron-left"></i></button>
                <button class="sb-close" id="sbCloseBtn" type="button" onclick="closeSidebar()" aria-label="Tutup menu"><i data-lucide="x"></i></button>
            </div>

            <nav class="sb-nav-scroll">
                <p class="sbsl" data-i18n="siswa.sidebar.section_utama">Utama</p>
                <ul class="sbnav">
                    <li><a class="act" data-tooltip="Dashboard" onclick="goDash('overview'); closeSidebar()"><span class="sbic"><i data-lucide="layout-dashboard"></i></span><span data-i18n="siswa.sidebar.nav_dashboard">Dashboard</span></a></li>
                </ul>

                <p class="sbsl" data-i18n="siswa.sidebar.section_akademik">Akademik</p>
                <ul class="sbnav">
                    <li><a data-tooltip="Kursus Saya" onclick="goDash('myCourses'); closeSidebar()"><span class="sbic"><i data-lucide="graduation-cap"></i></span><span data-i18n="siswa.sidebar.nav_courses">Kursus Saya</span></a></li>
                    <li><a data-tooltip="Materi Pembelajaran" onclick="goDash('materi'); closeSidebar()"><span class="sbic"><i data-lucide="book-open"></i></span><span data-i18n="siswa.sidebar.nav_materi">Materi Pembelajaran</span></a></li>
                    <li><a data-nav="lesson" data-tooltip="Lanjut Belajar" onclick="continueLearning(); closeSidebar()"><span class="sbic"><i data-lucide="play-circle"></i></span><span data-i18n="siswa.sidebar.nav_continue">Lanjut Belajar</span></a></li>
                    <li><a data-tooltip="Progress" onclick="goDash('progress'); closeSidebar()"><span class="sbic"><i data-lucide="bar-chart-3"></i></span><span data-i18n="siswa.sidebar.nav_progress">Progress</span></a></li>
                    <li><a data-tooltip="Quiz & Latihan" onclick="goDash('quiz'); closeSidebar()"><span class="sbic"><i data-lucide="file-question"></i></span><span data-i18n="siswa.sidebar.nav_quiz">Quiz &amp; Latihan</span></a></li>
                </ul>

                <p class="sbsl" data-i18n="siswa.sidebar.section_layanan">Layanan</p>
                <ul class="sbnav">
                    <li><a data-tooltip="Laporan Siswa" onclick="goDash('laporan'); closeSidebar()"><span class="sbic"><i data-lucide="inbox"></i></span><span data-i18n="siswa.sidebar.nav_laporan">Laporan Siswa</span>
                            <?php if ($statusCounts['Menunggu'] > 0): ?>
                                <div class="sbbdg"><?= $statusCounts['Menunggu'] ?></div>
                            <?php endif; ?>
                        </a></li>
                </ul>

                <p class="sbsl" data-i18n="siswa.sidebar.section_komunitas">Komunitas</p>
                <ul class="sbnav">
                    <li><a data-tooltip="Papan Peringkat" onclick="goDash('leaderboard'); closeSidebar()"><span class="sbic"><i data-lucide="trophy"></i></span><span data-i18n="siswa.sidebar.nav_leaderboard">Papan Peringkat</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- ============================================================
        MAIN COLUMN (Header + Content)
        ============================================================ -->
        <div class="dcol">

            <!-- STICKY HEADER -->
            <header class="dhead" id="dhead">
                <button class="dhead-burger" id="mnavToggle" type="button" onclick="toggleSidebar()" aria-label="Buka menu"><i data-lucide="menu"></i></button>
                <div class="dhead-crumb">
                    <span class="dhead-crumb-path" id="pageBreadcrumb"><span data-i18n="siswa.header.breadcrumb_dashboard">Dashboard</span> / <span class="cur" data-i18n="siswa.header.breadcrumb_overview">Overview</span></span>
                </div>
                <div class="dhead-actions">
                    <div class="dhead-clock" id="topbarClock">
                        <span class="dhead-clock-time" id="topbarClockTime">--:--:--</span>
                        <span class="dhead-clock-date" id="topbarClockDate">-</span>
                    </div>
                    <div class="lang-switcher" style="position:relative">
                        <button type="button" class="sb-toggle lang-toggle-btn" id="langToggleBtn" onclick="toggleLangDropdown(event)" aria-label="Ganti bahasa" title="Ganti bahasa">
                            <i data-lucide="languages"></i>
                            <span class="lang-current-label">ID</span>
                        </button>
                        <div class="lang-dropdown" id="langDropdown" style="display:none;position:absolute;right:0;top:110%;background:var(--card,#fff);border:1px solid rgba(0,0,0,.08);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:140px;overflow:hidden;z-index:50">
                            <button type="button" data-lang-set="id" class="lang-option" style="display:flex;align-items:center;gap:8px;width:100%;text-align:left;padding:10px 14px;font-size:.85rem;background:none;border:none;cursor:pointer;color:inherit">🇮🇩 Indonesia</button>
                            <button type="button" data-lang-set="en" class="lang-option" style="display:flex;align-items:center;gap:8px;width:100%;text-align:left;padding:10px 14px;font-size:.85rem;background:none;border:none;cursor:pointer;color:inherit">🇬🇧 English</button>
                        </div>
                    </div>
                    <button class="theme-toggle sb-toggle" id="themeToggle" type="button" onclick="toggleTheme()" aria-label="Ganti mode gelap/terang"><i data-lucide="sun"></i></button>
                    <button class="sb-toggle notif-bell" id="notifBellBtn" type="button" onclick="toggleNotifDropdown()" aria-label="Notifikasi" title="Notifikasi">
                        <i data-lucide="bell"></i>
                        <span class="notif-badge" id="notifBadge">0</span>
                    </button>
                    <button class="dhead-user" id="profileMenuBtn" type="button" onclick="toggleProfileDropdown()" aria-label="Menu profil">
                        <div class="dhead-avi" id="dheadAvi"><?= strtoupper(substr($name, 0, 1)) ?></div>
                        <div class="dhead-user-info">
                            <span class="dhead-user-name" id="dheadUserName"><?= htmlspecialchars($name) ?></span>
                            <span class="dhead-user-role" data-i18n="siswa.header.role">Siswa</span>
                        </div>
                        <span class="dhead-user-caret"><i data-lucide="chevron-down"></i></span>
                    </button>
                </div>

                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-dropdown-head">
                        <h4><i data-lucide="bell" style="width:16px;height:16px"></i> <span data-i18n="siswa.header.notif_title">Notifikasi</span></h4>
                        <div class="notif-dropdown-head-actions">
                            <button type="button" onclick="markAllRead()" data-i18n="siswa.header.notif_mark_all">Tandai semua dibaca</button>
                            <button type="button" class="notif-close" onclick="toggleNotifDropdown()" aria-label="Tutup notifikasi"><i data-lucide="x"></i></button>
                        </div>
                    </div>
                    <div class="notif-dropdown-body" id="notifList"></div>
                    <div class="notif-dropdown-foot">
                        <button type="button" onclick="openNotifSettings()" data-i18n="siswa.header.notif_settings">Pengaturan Notifikasi →</button>
                    </div>
                </div>

                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-dropdown-head">
                        <div class="davi" id="pdAvi"><?= strtoupper(substr($name, 0, 1)) ?></div>
                        <div style="min-width:0">
                            <div class="profile-dropdown-head-name" id="pdName"><?= htmlspecialchars($name) ?></div>
                            <div class="profile-dropdown-head-role" data-i18n="siswa.header.profile_role">Siswa · EduCare</div>
                        </div>
                    </div>
                    <div class="profile-dropdown-body">
                        <a onclick="closeProfileDropdown(); goDash('profile')" data-i18n="siswa.header.menu_profile">👤 Profil Saya</a>
                        <a onclick="closeProfileDropdown(); goDash('settings')" data-i18n="siswa.header.menu_settings">⚙️ Pengaturan</a>
                        <a class="plogout" href="<?= htmlspecialchars(pageUrl('auth/logout.php')) ?>" data-i18n="siswa.header.menu_logout">🚪 Keluar</a>
                    </div>
                </div>
            </header>

            <main class="dmain">

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alt aok">
                        <span>✅ <?= htmlspecialchars($_SESSION['success_message']) ?></span>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alt aerr">
                        <span>⚠️ <span data-i18n="<?= htmlspecialchars($_SESSION['error_message']) ?>"><?= htmlspecialchars($_SESSION['error_message']) ?></span></span>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <!-- OVERVIEW -->
                <div class="dp act" id="dp-overview">
                    <div class="dtb welcome-hero">
                        <div>
                            <h1><span data-i18n="siswa.overview.greeting">Selamat datang,</span> <span class="tc" id="dashName"><?= htmlspecialchars($name) ?></span> 👋</h1>
                            <p data-i18n="siswa.overview.subtitle">Mulai perjalanan belajarmu hari ini — dari nol, satu langkah sekarang!</p>
                        </div>
                        <button class="btn bcyan bsm" onclick="goDash('materi')" data-i18n="siswa.overview.cta_start">+ Mulai Kursus</button>
                    </div>

                    <div class="dsc" id="dashStats"></div>

                    <div class="dg2">
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.overview.card_progress_title">📖 Progres Kursus</h3>
                                <button class="btn bghost bsm" onclick="goDash('myCourses')" data-i18n="siswa.overview.card_progress_link">Detail →</button>
                            </div>
                            <div id="overviewCourses"></div>
                        </div>
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.overview.card_activity_title">⚡ Aktivitas Terbaru</h3>
                            </div>
                            <div class="alist" id="overviewActivity"></div>
                        </div>
                    </div>

                    <div class="dg3" style="margin-top:1.5rem">
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.overview.card_xp_title">⚡ XP &amp; Level</h3>
                            </div>
                            <div id="xpCard" style="text-align:center;padding:10px 0"></div>
                        </div>
                        <div class="dc" id="calendarCard"></div>
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.overview.card_leaderboard_title">🏆 Top Pelajar</h3>
                                <button class="btn bghost bsm" onclick="goDash('leaderboard')" data-i18n="siswa.overview.card_leaderboard_link">Lihat →</button>
                            </div>
                            <div id="lbMini"></div>
                        </div>
                    </div>
                </div>

                <!-- MY COURSES -->
                <div class="dp" id="dp-myCourses">
                    <div class="dtb">
                        <div>
                            <h1 data-i18n="siswa.my_courses.title">Kursus Saya 📚</h1>
                            <p data-i18n="siswa.my_courses.subtitle">Kursus yang sudah kamu selesaikan 100%. Masih ada yang berjalan? Klik "Lanjut Belajar" di menu.</p>
                        </div>
                        <button class="btn bcyan bsm" onclick="continueLearning()" data-i18n="siswa.my_courses.cta_continue">▶️ Lanjut Belajar</button>
                    </div>
                    <div id="myCoursesGrid" class="courses-grid"></div>
                </div>

                <!-- MATERI -->
                <div class="dp" id="dp-materi">
                    <div id="materiGridView">
                        <div class="dtb">
                            <div>
                                <h1 data-i18n="siswa.materi.title">Materi Pembelajaran 📘</h1>
                                <p data-i18n="siswa.materi.subtitle">Materi asli buatan guru, lengkap dengan video dan kuis — semua di dashboard ini.</p>
                            </div>
                        </div>
                        
                        <div class="materi-tabs" id="materiGroupTabs">
                            <button class="fbtn act" type="button" onclick="setMateriGroup('all',this)" data-i18n="siswa.materi.tab_all">Semua Materi</button>
                            <button class="fbtn" type="button" onclick="setMateriGroup('it',this)" data-i18n="siswa.materi.tab_it">💻 IT / Teknologi</button>
                            <button class="fbtn" type="button" onclick="setMateriGroup('umum',this)" data-i18n="siswa.materi.tab_umum">📚 Umum</button>
                        </div>
                        
                        <div class="materi-search">
                            <span>🔎</span>
                            <input class="fi" type="text" placeholder="Cari materi..." data-i18n-placeholder="siswa.materi.search_placeholder" oninput="onMateriSearch(this.value)" />
                        </div>
                        
                        <div class="materi-grid" id="materiGrid"></div>
                    </div>
                    <div id="materiDetailView" style="display:none"></div>
                </div>

                <!-- LESSON -->
                <div class="dp" id="dp-lesson">
                    <div id="lessonContainer"></div>
                </div>

                <!-- QUIZ -->
                <div class="dp" id="dp-quiz">
                    <div class="dtb">
                        <div>
                            <h1 data-i18n="siswa.quiz.title">Quiz &amp; Latihan ❓</h1>
                            <p data-i18n="siswa.quiz.subtitle">Uji pemahamanmu dengan soal interaktif. Setiap materi punya quiz sendiri, minimal 15 soal.</p>
                        </div>
                    </div>

                    <?php if (!empty($guruQuizzes)): ?>
                        <div class="dc" style="margin-bottom:1.5rem;border-color:rgba(139,92,246,.3)">
                            <div class="dch">
                                <h3 data-i18n="siswa.quiz.all_quiz_title">🧑‍🏫 Semua Quiz</h3>
                                <span style="font-size:.7rem;color:var(--text3);font-family:var(--mono)"><?= count($guruQuizzes) ?> <span data-i18n="siswa.dynamic.quizzes_available">quiz tersedia</span></span>
                            </div>
                            <?php foreach ($guruQuizzes as $gq): ?>
                                <div class="itemrow">
                                    <div class="itemicon" style="background:rgba(139,92,246,.12);color:#8B5CF6"><?= $gq['fromMateri'] ? '📘' : '❓' ?></div>
                                    <div style="flex:1">
                                        <div class="itemtitle">
                                            <?= htmlspecialchars($gq['name']) ?>
                                            <?php if ($gq['fromMateri']): ?>
                                                <span class="bdg" style="font-size:.55rem;margin-left:6px;background:rgba(61,123,255,.15);color:var(--cyan)" data-i18n="siswa.dynamic.material_quiz">Quiz Materi</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="itemmeta"><?= (int)$gq['questions'] ?> <span data-i18n="siswa.dynamic.questions">soal</span></div>
                                    </div>
                                    <?php if ($gq['done']): ?>
                                        <span class="bdg bgrn">✓ <span data-i18n="siswa.dynamic.completed">Selesai</span></span>
                                    <?php elseif ($gq['fromMateri']): ?>
                                        <a class="btn bcyan bsm" href="<?= htmlspecialchars(pageUrl('belajar/quiz.php')) ?>?materi_id=<?= (int)$gq['materiId'] ?>" data-i18n="siswa.dynamic.do_quiz">Kerjakan</a>
                                    <?php else: ?>
                                        <a class="btn bcyan bsm" href="<?= htmlspecialchars(pageUrl('belajar/quiz.php')) ?>?id=<?= (int)$gq['id'] ?>" data-i18n="siswa.dynamic.do_quiz">Kerjakan</a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PROGRESS -->
                <div class="dp" id="dp-progress">
                    <div class="dtb">
                        <div>
                            <h1 data-i18n="siswa.progress.title">Progress Belajar 📊</h1>
                            <p data-i18n="siswa.progress.subtitle">Pantau perkembangan belajarmu secara detail.</p>
                        </div>
                    </div>
                    <div class="dg2">
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.progress.weekly_title">📈 Aktivitas Mingguan</h3>
                            </div>
                            <div id="weekChart"></div>
                        </div>
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.progress.skill_title">🎯 Skill Progress</h3>
                            </div>
                            <div id="skillBars"></div>
                        </div>
                    </div>
                    <div class="dc" style="margin-top:1.5rem">
                        <div class="dch">
                            <h3 data-i18n="siswa.progress.detail_title">📚 Detail Per Kursus</h3>
                        </div>
                        <div id="progressDetail"></div>
                    </div>
                </div>

                <!-- LAPORAN SISWA -->
                <div class="dp" id="dp-laporan">
                    <div class="dtb">
                        <div>
                            <h1 data-i18n="siswa.laporan.title">Laporan Siswa 📮</h1>
                            <p data-i18n="siswa.laporan.subtitle">Kirim laporan atau keluhan terkait sekolah, dan pantau statusnya di sini.</p>
                        </div>
                    </div>
                    <div class="dg2">
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.laporan.form_title">📝 Buat Laporan Baru</h3>
                            </div>
                            <form method="POST" action="">
                                <?= csrfField() ?>
                                <div class="fg">
                                    <label class="fl" data-i18n="siswa.laporan.label_title">Judul Laporan</label>
                                    <input class="fi" type="text" name="title" placeholder="Contoh: Kerusakan fasilitas kelas" data-i18n-placeholder="siswa.laporan.placeholder_title" required />
                                </div>
                                <div class="fg">
                                    <label class="fl" data-i18n="siswa.laporan.label_category">Kategori</label>
                                    <select class="fi" name="category">
                                        <option value="Umum" data-i18n="siswa.dynamic.category_general">Umum</option>
                                        <option value="Fasilitas" data-i18n="siswa.dynamic.category_facility">Fasilitas</option>
                                        <option value="Akademik" data-i18n="siswa.dynamic.category_academic">Akademik</option>
                                        <option value="Kedisiplinan" data-i18n="siswa.dynamic.category_discipline">Kedisiplinan</option>
                                        <option value="Lainnya" data-i18n="siswa.dynamic.category_other">Lainnya</option>
                                    </select>
                                </div>
                                <div class="fg">
                                    <label class="fl" data-i18n="siswa.laporan.label_desc">Deskripsi</label>
                                    <textarea class="fi" name="desc" placeholder="Jelaskan laporanmu secara detail..." data-i18n-placeholder="siswa.laporan.placeholder_desc" required></textarea>
                                </div>
                                <button type="submit" name="submit_report" class="btn bcyan" data-i18n="siswa.laporan.submit">📤 Kirim Laporan</button>
                            </form>
                        </div>
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.laporan.summary_title">📊 Ringkasan Status</h3>
                            </div>
                            <div class="cpi">
                                <div class="cph"><span class="cpn" data-i18n="siswa.laporan.status_pending">⏳ Menunggu</span><span class="cpp"><?= $statusCounts['Menunggu'] ?></span></div>
                            </div>
                            <div class="cpi">
                                <div class="cph"><span class="cpn" data-i18n="siswa.laporan.status_process">🔧 Diproses</span><span class="cpp"><?= $statusCounts['Diproses'] ?></span></div>
                            </div>
                            <div class="cpi">
                                <div class="cph"><span class="cpn" data-i18n="siswa.laporan.status_done">✅ Selesai</span><span class="cpp"><?= $statusCounts['Selesai'] ?></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="dc" style="margin-top:1.5rem">
                        <div class="dch">
                            <h3 data-i18n="siswa.laporan.history_title">📋 Riwayat Laporan Saya</h3>
                        </div>
                        <?php if (empty($myReports)): ?>
                            <div class="es">
                                <div class="ei">📭</div>
                                <h3 data-i18n="siswa.laporan.empty_title">Belum ada laporan</h3>
                                <p data-i18n="siswa.laporan.empty_desc">Laporan yang kamu kirim akan muncul di sini.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_reverse($myReports) as $r):
                                $stCls = $r['status'] === 'Selesai' ? 'bgrn' : ($r['status'] === 'Diproses' ? 'bamb' : 'brs');
                            ?>
                                <div class="ai" style="align-items:center;gap:16px">
                                    <div style="flex:1">
                                        <div class="atext">
                                            <?= htmlspecialchars($r['title']) ?>
                                            <span class="bdg <?= $stCls ?>" style="font-size:.6rem;margin-left:6px">
                                                <span data-i18n="<?= $r['status'] === 'Selesai' ? 'siswa.laporan.status_done' : ($r['status'] === 'Diproses' ? 'siswa.laporan.status_process' : 'siswa.laporan.status_pending') ?>"><?= htmlspecialchars($r['status']) ?></span>
                                            </span>
                                        </div>
                                        <div style="font-size:.8rem;color:var(--text2);margin:4px 0">
                                            <?= htmlspecialchars($r['desc']) ?>
                                        </div>
                                        <div class="atime">
                                            <?= htmlspecialchars($r['category']) ?> · <?= htmlspecialchars($r['date']) ?>
                                        </div>
                                    </div>
                                    <?php if ($r['status'] === 'Menunggu'): ?>
                                        <form method="POST" action="" onsubmit="return confirm(window.EduCareI18n?.t('siswa.dynamic.cancel_report_confirm') || 'Batalkan laporan ini?')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>" />
                                            <button type="submit" name="delete_report" class="btn bghost bsm" style="color:var(--rose);border-color:var(--rose)" data-i18n="siswa.laporan.cancel">
                                                🗑 Batalkan
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- LEADERBOARD -->
                <div class="dp" id="dp-leaderboard">
                    <div class="dtb">
                        <div>
                            <h1 data-i18n="siswa.leaderboard.title">Papan Peringkat 🏆</h1>
                            <p data-i18n="siswa.leaderboard.subtitle">Kompetisi sehat mendorong semangat belajar.</p>
                        </div>
                    </div>
                    <div class="leaderboard-container">
                        <div class="leaderboard-tabs" id="lbTabs">
                            <button class="fbtn act" id="lbTabMonthly" onclick="setLbTab('monthly',this)" data-i18n="siswa.leaderboard.tab_monthly">📅 Bulanan</button>
                            <button class="fbtn" id="lbTabWeekly" onclick="setLbTab('weekly',this)" data-i18n="siswa.leaderboard.tab_weekly">📆 Mingguan</button>
                            <button class="fbtn" id="lbTabAlltime" onclick="setLbTab('alltime',this)" data-i18n="siswa.leaderboard.tab_alltime">🏅 Sepanjang Waktu</button>
                        </div>
                        <div class="leaderboard-card" id="lbFull"></div>
                    </div>
                </div>

                <!-- PROFILE -->
                <div class="dp" id="dp-profile">
                    <div class="dtb">
                        <div>
                            <h1 data-i18n="siswa.profile.title">Profil Saya 👤</h1>
                            <p data-i18n="siswa.profile.subtitle">Kelola informasi profil dan lihat pencapaianmu.</p>
                        </div>
                    </div>
                    
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <div class="profile-avatar-inner">
                                <?= strtoupper(substr($name, 0, 1)) ?>
                            </div>
                            <div class="profile-level-badge"><span data-i18n="siswa.dashboard.level">Level</span> <?= $dashboardSeed['level'] ?? 1 ?></div>
                        </div>
                        <div class="profile-info">
                            <h2 class="profile-name"><?= htmlspecialchars($name) ?></h2>
                            <div class="profile-role">🎓 <span data-i18n="siswa.dashboard.active_student">Pelajar Aktif</span></div>
                            <div class="profile-stats">
                                <div class="profile-stat">
                                    <span class="profile-stat-value"><?= $dashboardSeed['xp'] ?? 0 ?></span>
                                    <span class="profile-stat-label">XP</span>
                                </div>
                                <div class="profile-stat">
                                    <span class="profile-stat-value"><?= $dashboardSeed['streak'] ?? 0 ?></span>
                                    <span class="profile-stat-label">🔥 <span data-i18n="siswa.dashboard.streak">Streak</span></span>
                                </div>
                                <div class="profile-stat">
                                    <span class="profile-stat-value"><?= count($realCompletedCourseIds) ?></span>
                                    <span class="profile-stat-label">✅ <span data-i18n="siswa.dashboard.completed">Selesai</span></span>
                                </div>
                            </div>
                        </div>
                        <button class="btn bghost bsm" onclick="goDash('settings')" data-i18n="siswa.profile.edit">✏️ Edit Profil</button>
                    </div>

                    <div class="dg2" style="margin-top:1.5rem">
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.profile.achievements_title">🏅 Pencapaian</h3>
                            </div>
                            <div class="agrid" id="achieveGrid">
                                <?php if (!empty($realCertificates)): ?>
                                    <?php foreach ($realCertificates as $cert): ?>
                                        <div class="achievement-item">
                                            <div class="achievement-icon"><?= $cert['emoji'] ?? '🏅' ?></div>
                                            <div class="achievement-name"><?= htmlspecialchars($cert['title']) ?></div>
                                            <div class="achievement-date"><?= htmlspecialchars($cert['date']) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="achievement-empty">
                                        <div class="achievement-empty-icon">🏅</div>
                                        <p>Belum ada pencapaian. Selesaikan kursus untuk mendapatkan sertifikat!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="dc">
                            <div class="dch">
                                <h3 data-i18n="siswa.profile.certificates_title">📜 Sertifikat</h3>
                            </div>
                            <?php if (!empty($realCertificates)): ?>
                                <?php foreach ($realCertificates as $cert): ?>
                                    <div class="certificate-item">
                                        <div class="certificate-icon">📜</div>
                                        <div class="certificate-info">
                                            <div class="certificate-name"><?= htmlspecialchars($cert['title']) ?></div>
                                            <div class="certificate-date">Diterbitkan: <?= htmlspecialchars($cert['date']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="certificate-empty">
                                    <div class="certificate-empty-icon">📜</div>
                                    <p>Belum ada sertifikat. Selesaikan kursus untuk mendapatkan sertifikat!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- SETTINGS -->
                <div class="dp" id="dp-settings">
                    <div class="dtb">
                        <div>
                            <h1 data-i18n="siswa.settings.title">Pengaturan ⚙️</h1>
                            <p data-i18n="siswa.settings.subtitle">Kelola preferensi dan keamanan akunmu.</p>
                        </div>
                    </div>
                    <div class="sgrid">
                        <div class="snav">
                            <div class="snavi act" data-settings-tab="sprofile" onclick="setTab('sprofile',this)" data-i18n="siswa.settings.tab_profile">👤 Profil</div>
                            <div class="snavi" data-settings-tab="snotif" onclick="setTab('snotif',this)" data-i18n="siswa.settings.tab_notif">🔔 Notifikasi</div>
                            <div class="snavi" data-settings-tab="sprivacy" onclick="setTab('sprivacy',this)" data-i18n="siswa.settings.tab_privacy">🔐 Privasi</div>
                            <div class="snavi" data-settings-tab="sappear" onclick="setTab('sappear',this)" data-i18n="siswa.settings.tab_appearance">🎨 Tampilan</div>
                        </div>
                        <div class="spanel" id="settingsPanel"></div>
                    </div>
                </div>

            </main>
        </div><!-- /.dcol -->
    </div><!-- /.dlayout -->

    <!-- ============================================================
    JAVASCRIPT
    ============================================================ -->
    <script id="dashboard-init-data" type="application/json">
        <?= json_encode(['user' => $dashboardUser, 'seed' => $dashboardSeed], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
    </script>
    <script src="<?= htmlspecialchars(assetUrl('assets/js/dashboard-siswa.js')) ?>"></script>
    <!-- i18n: Fitur Bahasa (ID/EN) -->
    <script src="<?= htmlspecialchars(assetUrl('assets/js/i18n.js')) ?>" defer></script>
    <script>
        function toggleLangDropdown(e) {
            if (e) e.stopPropagation();
            var dd = document.getElementById('langDropdown');
            if (!dd) return;
            var willOpen = dd.style.display !== 'block';
            var notif = document.getElementById('notifDropdown');
            var profile = document.getElementById('profileDropdown');
            if (notif) notif.classList.remove('show');
            if (profile) profile.classList.remove('show');
            dd.style.display = willOpen ? 'block' : 'none';
        }
        document.addEventListener('click', function(e) {
            var wrap = document.querySelector('.lang-switcher');
            var dd = document.getElementById('langDropdown');
            if (wrap && dd && !wrap.contains(e.target)) dd.style.display = 'none';
        });
        document.querySelectorAll('#langDropdown [data-lang-set]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('langDropdown').style.display = 'none';
            });
        });

        document.addEventListener('educare:languagechange', function() {
            if (typeof window.refreshDashboardSiswaUI === 'function') {
                window.refreshDashboardSiswaUI();
            }
            if (typeof window.renderOverview === 'function') {
                window.renderOverview();
            }
            if (typeof window.renderMyCourses === 'function') {
                window.renderMyCourses();
            }
            if (typeof window.renderMateriList === 'function') {
                window.renderMateriList();
            }
            if (typeof window.renderProgress === 'function') {
                window.renderProgress();
            }
            if (typeof window.renderLB === 'function') {
                window.renderLB();
            }
            if (typeof window.renderProfile === 'function') {
                window.renderProfile();
            }
            if (typeof window.renderSettingsTab === 'function') {
                const activeTab = document.querySelector('.snavi.act');
                const tab = activeTab?.getAttribute('data-settings-tab') || 'sprofile';
                window.renderSettingsTab(tab, activeTab);
            }
            if (typeof window.updateBreadcrumb === 'function') {
                const activePanel = document.querySelector('.dp.act');
                const activeName = activePanel?.id?.replace('dp-', '') || 'overview';
                window.updateBreadcrumb(activeName);
            }
        });
    </script>
    <script>
        // ---- Mobile sidebar toggle ----
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sbOverlay').classList.toggle('show');
        }

        function closeSidebar() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sbOverlay').classList.remove('show');
            }
        }
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sbOverlay').classList.remove('show');
            }
        });

        // ---- Sidebar collapse ----
        function toggleSidebarCollapse() {
            const sb = document.getElementById('sidebar');
            if (!sb) return;
            const collapsed = sb.classList.toggle('collapsed');
            localStorage.setItem('siswa_sidebarCollapsed', JSON.stringify(collapsed));
        }
        (function loadSidebarCollapse() {
            try {
                if (JSON.parse(localStorage.getItem('siswa_sidebarCollapsed')) === true) {
                    document.getElementById('sidebar').classList.add('collapsed');
                }
            } catch (e) {}
        })();

        // ---- Leaderboard functions ----
        const SERVER_LEADERBOARD = <?= json_encode($leaderboardRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        function renderLB(tab) {
            const container = document.getElementById('lbFull');
            if (!container) return;

            const leaderboardData = SERVER_LEADERBOARD;

            const sorted = [...leaderboardData].sort((a, b) => b.xp - a.xp);
            const rankClasses = ['gold', 'silver', 'bronze'];
            const medals = ['🥇', '🥈', '🥉'];

            let html = '';
            sorted.forEach((item, index) => {
                const rank = index + 1;
                const isTop3 = rank <= 3;
                const rankClass = isTop3 ? rankClasses[index] : '';
                const medal = isTop3 ? medals[index] : '';
                const isYou = item.isYou || false;

                html += `
                    <div class="lb-item ${isTop3 ? 'top-' + rank : ''} ${isYou ? 'is-you' : ''}">
                        <div class="lb-rank ${rankClass}">
                            ${isTop3 ? medal : '#' + rank}
                        </div>
                        <div class="lb-avatar">${item.avatar}</div>
                        <div class="lb-info">
                            <div class="lb-name">${item.name}${isYou ? ' (Kamu)' : ''}</div>
                            <div class="lb-level">Level ${item.level}</div>
                        </div>
                        <div class="lb-xp">${item.xp.toLocaleString()} XP</div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function setLbTab(tab, btn) {
            document.querySelectorAll('#lbTabs .fbtn').forEach(b => b.classList.remove('act'));
            btn.classList.add('act');
            renderLB(tab);
        }
    </script>
</body>

</html>