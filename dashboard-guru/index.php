<?php
session_start();

// Belum login -> silakan login dulu.
if (!isset($_SESSION['user'])) {
  header('Location: ../auth/login.php');
  exit;
}

// Bukan guru (siswa/admin) -> arahkan ke dashboard yang sesuai,
// jangan dikembalikan ke halaman login.
if (($_SESSION['user']['role'] ?? '') !== 'guru') {
  header('Location: ../dashboard-siswa/index.php');
  exit;
}
$name = htmlspecialchars($_SESSION['user']['nama'] ?? 'Guru');

require_once __DIR__ . '/../function.php';

$materiFile = 'materi.json';
$quizFile = 'quiz.json';
$reportsFile = 'reports.json';
$usersFile = 'users.json';
$progressFile = 'progress.json';

$levelOpts = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'];
$colorOpts = ['#4f7cff', '#8b7bff', '#4f7cff', '#34d399', '#f5a623', '#fb7185'];

function slugify($str)
{
  $s = strtolower(trim($str));
  $s = preg_replace('/[^a-z0-9]+/', '-', $s);
  return trim($s, '-') ?: 'umum';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrfVerify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit;
  }

  if (isset($_POST['add_materi'])) {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Umum');
    $emoji = trim($_POST['emoji'] ?? '📘');
    $level = trim($_POST['level'] ?? 'beginner');
    if (!isset($levelOpts[$level])) $level = 'beginner';
    $color = trim($_POST['color'] ?? $colorOpts[0]);
    $desc = trim($_POST['desc'] ?? '');
    $chaptersJson = $_POST['chapters_json'] ?? '[]';
    $chapters = json_decode($chaptersJson, true);
    if (!is_array($chapters)) $chapters = [];

    $cleanChapters = [];
    $moduleCount = 0;
    foreach ($chapters as $ch) {
      $chTitle = trim($ch['ch'] ?? '');
      $items = $ch['items'] ?? [];
      if ($chTitle === '' || !is_array($items)) continue;
      $cleanItems = [];
      foreach ($items as $it) {
        $t = trim($it['t'] ?? '');
        if ($t === '') continue;
        $cleanItems[] = [
          't' => $t,
          'content' => trim($it['content'] ?? ''),
          'video_url' => trim($it['video_url'] ?? ''),
        ];
      }
      if (empty($cleanItems)) continue;
      $cleanChapters[] = ['ch' => $chTitle, 'items' => $cleanItems];
      $moduleCount += count($cleanItems);
    }

    if ($title !== '' && count($cleanChapters) > 0) {
      $materiList = readJSON($materiFile);
      $newId = count($materiList) ? (max(array_column($materiList, 'id')) + 1) : 1;
      $materiList[] = [
        'id' => $newId,
        'title' => $title,
        'category' => $category,
        'cat' => slugify($category),
        'emoji' => $emoji ?: '📘',
        'desc' => $desc,
        'content' => $desc,
        'level' => $level,
        'stars' => 4.8,
        'students' => 0,
        'modules' => $moduleCount,
        'color' => $color,
        'date' => date('Y-m-d'),
        'video_url' => '',
        'lessons' => $cleanChapters
      ];
      writeJSON($materiFile, $materiList);

      addActivity((int) ($_SESSION['user']['id'] ?? 0), $_SESSION['user']['nama'] ?? 'Guru', 'guru', '📘', "Menambahkan kursus \"$title\"");
      addNotification('siswa', '📚', 'Kursus Baru', "Kursus baru tersedia: $title");

      $_SESSION['guru_message'] = "Kursus \"$title\" berhasil ditambahkan dengan $moduleCount pelajaran.";
    } else {
      $_SESSION['guru_error'] = "Kursus gagal disimpan. Pastikan judul diisi dan minimal ada 1 chapter dengan 1 pelajaran.";
    }
    header('Location: ' . $_SERVER['REQUEST_URI'] . '#materi');
    exit;
  }

  if (isset($_POST['delete_materi'])) {
    $id = (int)($_POST['materi_id'] ?? 0);
    $materiList = readJSON($materiFile);
    $materiList = array_values(array_filter($materiList, function ($m) use ($id) {
      return $m['id'] !== $id;
    }));
    writeJSON($materiFile, $materiList);
    $_SESSION['guru_message'] = "Kursus berhasil dihapus.";
    header('Location: ' . $_SERVER['REQUEST_URI'] . '#materi');
    exit;
  }

  if (isset($_POST['add_quiz'])) {
    $quizName = trim($_POST['name'] ?? '');
    $status = trim($_POST['status'] ?? 'Draft');
    $materiId = (int) ($_POST['materi_id'] ?? 0);
    $itemsJson = $_POST['items_json'] ?? '[]';
    $items = json_decode($itemsJson, true);
    if (!is_array($items)) $items = [];

    $cleanItems = [];
    foreach ($items as $it) {
      $q = trim($it['q'] ?? '');
      $opts = $it['opts'] ?? [];
      $ans = (int)($it['ans'] ?? -1);
      if ($q === '' || count($opts) < 2 || $ans < 0 || $ans >= count($opts)) continue;
      $cleanItems[] = ['q' => $q, 'opts' => array_map('trim', $opts), 'ans' => $ans];
    }

    if ($quizName !== '' && count($cleanItems) > 0) {
      $quizList = readJSON($quizFile);
      $newId = count($quizList) ? (max(array_column($quizList, 'id')) + 1) : 1;
      $questions_list = array_map(function ($it) {
        return ['q' => $it['q'], 'o' => array_map('trim', $it['opts']), 'a' => (int) $it['ans']];
      }, $cleanItems);
      $newQuiz = [
        'id' => $newId,
        'name' => $quizName,
        'questions' => count($questions_list),
        'status' => $status,
        'items' => $cleanItems,
        'questions_list' => $questions_list
      ];
      if ($materiId > 0) {
        $newQuiz['materi_id'] = $materiId;
      }
      $quizList[] = $newQuiz;
      writeJSON($quizFile, $quizList);

      addActivity((int) ($_SESSION['user']['id'] ?? 0), $_SESSION['user']['nama'] ?? 'Guru', 'guru', '❓', "Membuat quiz \"$quizName\"");
      if ($status === 'Aktif') {
        addNotification('siswa', '❓', 'Quiz Baru', "Quiz baru tersedia: $quizName");
      }

      $linkNote = $materiId > 0 ? ' dan ditautkan ke materi.' : '.';
      $_SESSION['guru_message'] = "Quiz \"$quizName\" berhasil disimpan dengan " . count($cleanItems) . " soal$linkNote";
    } else {
      $_SESSION['guru_error'] = "Quiz gagal disimpan. Pastikan nama quiz diisi dan minimal ada 1 soal lengkap.";
    }
    header('Location: ' . $_SERVER['REQUEST_URI'] . '#quiz');
    exit;
  }

  if (isset($_POST['edit_quiz'])) {
    $id = (int) ($_POST['quiz_id'] ?? 0);
    $quizName = trim($_POST['name'] ?? '');
    $status = trim($_POST['status'] ?? 'Draft');
    $materiId = (int) ($_POST['materi_id'] ?? 0);

    $quizList = readJSON($quizFile);
    $updated = false;
    foreach ($quizList as &$q) {
      if ((int) ($q['id'] ?? 0) === $id) {
        if ($quizName !== '') $q['name'] = $quizName;
        $q['status'] = $status;
        if ($materiId > 0) {
          $q['materi_id'] = $materiId;
        } else {
          unset($q['materi_id']);
        }
        $updated = true;
        break;
      }
    }
    unset($q);

    if ($updated) {
      writeJSON($quizFile, $quizList);
      $_SESSION['guru_message'] = "Quiz \"$quizName\" berhasil diperbarui.";
    } else {
      $_SESSION['guru_error'] = "Quiz tidak ditemukan.";
    }
    header('Location: ' . $_SERVER['REQUEST_URI'] . '#quiz');
    exit;
  }

  if (isset($_POST['delete_quiz'])) {
    $id = (int)($_POST['quiz_id'] ?? 0);
    $quizList = readJSON($quizFile);
    $quizList = array_values(array_filter($quizList, function ($q) use ($id) {
      return $q['id'] !== $id;
    }));
    writeJSON($quizFile, $quizList);
    $_SESSION['guru_message'] = "Quiz berhasil dihapus.";
    header('Location: ' . $_SERVER['REQUEST_URI'] . '#quiz');
    exit;
  }

  if (isset($_POST['update_report_status'])) {
    $id = (int)($_POST['report_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? 'Menunggu');
    if (in_array($newStatus, ['Menunggu', 'Diproses', 'Selesai'], true)) {
      $reportsList = readJSON($reportsFile);
      foreach ($reportsList as &$r) {
        if ($r['id'] === $id) {
          $r['status'] = $newStatus;
          break;
        }
      }
      unset($r);
      writeJSON($reportsFile, $reportsList);
      $_SESSION['guru_message'] = "Status laporan diperbarui.";
    } else {
      $_SESSION['guru_error'] = "Status laporan tidak valid.";
    }
    header('Location: ' . $_SERVER['REQUEST_URI'] . '#laporan-masuk');
    exit;
  }

  if (isset($_POST['mark_notif_read'])) {
    markNotificationRead((int) ($_POST['notif_id'] ?? 0));
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
  }
}

$materi = readJSON($materiFile);
$quiz = readJSON($quizFile);
$reports = readJSON($reportsFile);
$usersList = readJSON($usersFile);
$progressList = readJSON($progressFile);

$totalMateri = count($materi);
$totalSiswa = count(array_filter($usersList, function ($u) {
  return ($u['role'] ?? '') === 'siswa';
}));
$totalQuiz = count($quiz);
$totalLaporanBaru = count(array_filter($reports, function ($r) {
  return ($r['status'] ?? '') === 'Menunggu';
}));

$guruId = (int) ($_SESSION['user']['id'] ?? 0);
$guruNotifs = getNotificationsFor($guruId, 'guru');
$unreadNotifCount = count(array_filter($guruNotifs, fn($n) => empty($n['read'])));
$studentActivities = array_values(array_filter(getActivities(null, 30), fn($a) => ($a['role'] ?? '') === 'siswa'));
$studentActivities = array_slice($studentActivities, 0, 8);

$MONTHS_SHORT = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$nowTs = time();
$todayKey = date('Y-m-d', $nowTs);
$yesterdayKey = date('Y-m-d', $nowTs - 86400);
$guruActivityGroups = [];
foreach ($studentActivities as $ga) {
    $gts = strtotime($ga['created_at'] ?? '');
    if (!$gts) $gts = $nowTs;
    $guruActivityGroups[date('Y-m-d', $gts)][] = $ga;
}
$leaderboardTop = getLeaderboard(5);

$classScores = [];
foreach ($progressList as $p) {
  $cls = $p['class'] ?? '-';
  $classScores[$cls][] = (float)($p['score'] ?? 0);
}
$classLabels = [];
$classAvg = [];
foreach ($classScores as $cls => $scores) {
  $classLabels[] = $cls;
  $classAvg[] = round(array_sum($scores) / max(count($scores), 1), 1);
}

$statusCounts = ['Menunggu' => 0, 'Diproses' => 0, 'Selesai' => 0];
foreach ($reports as $r) {
  $st = $r['status'] ?? 'Menunggu';
  if (!isset($statusCounts[$st])) $statusCounts[$st] = 0;
  $statusCounts[$st]++;
}

$catCounts = [];
foreach ($materi as $m) {
  $cat = strtoupper($m['cat'] ?? 'umum');
  $catCounts[$cat] = ($catCounts[$cat] ?? 0) + 1;
}

$subjectTotals = [];
$subjectCounts = [];
foreach ($progressList as $p) {
  foreach (($p['progress'] ?? []) as $subj => $val) {
    if (!is_numeric($val)) continue;
    $subjectTotals[$subj] = ($subjectTotals[$subj] ?? 0) + $val;
    $subjectCounts[$subj] = ($subjectCounts[$subj] ?? 0) + 1;
  }
}
$subjectLabels = [];
$subjectAvg = [];
foreach ($subjectTotals as $subj => $total) {
  $subjectLabels[] = $subj;
  $subjectAvg[] = round($total / $subjectCounts[$subj], 1);
}

$analyticsData = [
  'classLabels'   => $classLabels,
  'classAvg'      => $classAvg,
  'statusLabels'  => array_keys($statusCounts),
  'statusValues'  => array_values($statusCounts),
  'catLabels'     => array_keys($catCounts),
  'catValues'     => array_values($catCounts),
  'subjectLabels' => $subjectLabels,
  'subjectAvg'    => $subjectAvg,
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <title data-i18n="page.dashboard_guru">Dashboard Guru • EduCare</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Bricolage+Grotesque:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= htmlspecialchars(assetUrl('assets/css/dashboard-siswa.css')) ?>" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    /* ============================================================
       ANIMASI BACKGROUND BERGERAK
       ============================================================ */
    

    .stars {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 0;
      overflow: hidden;
    }

    .star {
      position: absolute;
      width: 2px;
      height: 2px;
      background: white;
      border-radius: 50%;
      animation: twinkle var(--duration) ease-in-out infinite alternate;
    }

    body.light-mode .star {
      background: rgba(79, 70, 229, 0.35);
      box-shadow: 0 0 6px rgba(79, 70, 229, 0.25);
    }

    /* Pastikan latar mode terang ikut terang (selector bersama .light-mode body
       pada dashboard-siswa.css tidak terpicu karena class diletakkan di <body>). */
    body.light-mode {
      background: #ffffff;
    }

    .star:nth-child(1) { left: 5%; top: 10%; --duration: 2s; opacity: 0.6; }
    .star:nth-child(2) { left: 15%; top: 25%; --duration: 3s; opacity: 0.4; width: 3px; height: 3px; }
    .star:nth-child(3) { left: 25%; top: 5%; --duration: 2.5s; opacity: 0.8; }
    .star:nth-child(4) { left: 35%; top: 40%; --duration: 1.8s; opacity: 0.5; }
    .star:nth-child(5) { left: 45%; top: 15%; --duration: 3.2s; opacity: 0.7; width: 3px; height: 3px; }
    .star:nth-child(6) { left: 55%; top: 50%; --duration: 2.2s; opacity: 0.4; }
    .star:nth-child(7) { left: 65%; top: 8%; --duration: 2.8s; opacity: 0.9; }
    .star:nth-child(8) { left: 75%; top: 35%; --duration: 1.5s; opacity: 0.5; width: 3px; height: 3px; }
    .star:nth-child(9) { left: 85%; top: 20%; --duration: 3.5s; opacity: 0.6; }
    .star:nth-child(10) { left: 92%; top: 45%; --duration: 2.1s; opacity: 0.4; }
    .star:nth-child(11) { left: 10%; top: 70%; --duration: 2.7s; opacity: 0.7; }
    .star:nth-child(12) { left: 30%; top: 80%; --duration: 1.9s; opacity: 0.5; width: 3px; height: 3px; }
    .star:nth-child(13) { left: 50%; top: 65%; --duration: 3.1s; opacity: 0.8; }
    .star:nth-child(14) { left: 70%; top: 75%; --duration: 2.4s; opacity: 0.4; }
    .star:nth-child(15) { left: 88%; top: 85%; --duration: 2.6s; opacity: 0.6; width: 3px; height: 3px; }
    .star:nth-child(16) { left: 20%; top: 55%; --duration: 3.3s; opacity: 0.5; }
    .star:nth-child(17) { left: 60%; top: 30%; --duration: 1.7s; opacity: 0.9; }
    .star:nth-child(18) { left: 78%; top: 60%; --duration: 2.9s; opacity: 0.4; width: 3px; height: 3px; }
    .star:nth-child(19) { left: 42%; top: 90%; --duration: 2.3s; opacity: 0.7; }
    .star:nth-child(20) { left: 95%; top: 15%; --duration: 3s; opacity: 0.5; }

    @keyframes twinkle {
      0% { opacity: 0.1; transform: scale(0.5); }
      100% { opacity: 1; transform: scale(1.2); }
    }


    /* ============================================================
       PERBAIKAN FORM KELOLA KURSUS
       ============================================================ */


    .chbuilder {
      background: rgba(15, 23, 42, 0.35);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 8px;
      transition: all 0.3s ease;
    }

    .chbuilder:hover {
      border-color: rgba(79, 124, 255, 0.15);
      background: rgba(15, 23, 42, 0.45);
    }

    body.light-mode .chbuilder {
      background: rgba(255, 255, 255, 0.3);
      border: 1px solid rgba(0, 0, 0, 0.06);
    }

    body.light-mode .chbuilder:hover {
      background: rgba(255, 255, 255, 0.4);
      border-color: rgba(79, 124, 255, 0.15);
    }

    .chbuilder .ch-title {
      font-weight: 500;
      font-size: .9rem;
    }

    .chbuilder .ch-title::placeholder {
      color: #94a3b8;
      opacity: 0.6;
    }

    body.light-mode .chbuilder .ch-title::placeholder {
      color: #94a3b8;
      opacity: 0.5;
    }

    .lessrow {
      display: flex;
      flex-direction: column;
      gap: 8px;
      align-items: stretch;
      padding: 10px;
      margin-top: 8px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 8px;
      transition: background 0.2s ease;
    }

    .lessrow:hover {
      background: rgba(255, 255, 255, 0.06);
    }

    body.light-mode .lessrow {
      background: rgba(0, 0, 0, 0.02);
    }

    body.light-mode .lessrow:hover {
      background: rgba(0, 0, 0, 0.04);
    }

    .lessrow .less-title {
      flex: 2;
      min-width: 0;
      font-size: .85rem;
    }

    .lessrow textarea.fi,
    .lessrow input.fi {
      width: 100%;
    }

    .lessrow textarea.less-content {
      font-size: .82rem;
      line-height: 1.5;
      resize: vertical;
    }

    .lessrow .less-video {
      font-size: .82rem;
      color: #a5b4fc;
    }

    .lessrow .less-title::placeholder {
      color: #94a3b8;
      opacity: 0.6;
    }

    body.light-mode .lessrow .less-title::placeholder {
      color: #94a3b8;
      opacity: 0.5;
    }

    .dashed-add {
      width: 100%;
      border: 1.5px dashed rgba(255, 255, 255, 0.12);
      border-radius: 10px;
      padding: 12px;
      text-align: center;
      color: #4f7cff;
      font-size: .82rem;
      font-weight: 600;
      cursor: pointer;
      background: rgba(79, 124, 255, 0.03);
      transition: all 0.3s ease;
      font-family: 'Inter', sans-serif;
    }

    body.light-mode .dashed-add {
      border: 1.5px dashed rgba(0, 0, 0, 0.1);
      background: rgba(79, 124, 255, 0.02);
      color: #4f7cff;
    }

    .dashed-add:hover {
      background: rgba(79, 124, 255, 0.08);
      border-color: rgba(79, 124, 255, 0.25);
      transform: scale(1.01);
    }

    body.light-mode .dashed-add:hover {
      background: rgba(79, 124, 255, 0.06);
      border-color: rgba(79, 124, 255, 0.2);
    }

    .colorpick {
      position: relative;
      width: 32px;
      height: 32px;
      border-radius: 10px;
      cursor: pointer;
      display: inline-block;
      transition: transform 0.2s ease;
    }

    .colorpick:hover {
      transform: scale(1.1);
    }

    .colorpick input {
      position: absolute;
      opacity: 0;
      inset: 0;
      cursor: pointer;
    }

    .colorpick .ring {
      display: none;
      position: absolute;
      inset: -4px;
      border-radius: 14px;
      box-shadow: 0 0 0 2.5px #4f7cff, 0 0 0 5px rgba(79, 124, 255, 0.15);
    }

    .colorpick input:checked + .ring {
      display: block;
    }


    #materiForm {
      background: rgba(15, 23, 42, 0.2) !important;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 16px;
      padding: 24px;
      transition: all 0.3s ease;
    }

    #materiForm:hover {
      border-color: rgba(79, 124, 255, 0.1);
    }

    body.light-mode #materiForm {
      background: rgba(255, 255, 255, 0.15) !important;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    body.light-mode #materiForm:hover {
      border-color: rgba(79, 124, 255, 0.1);
    }


    #materi .dc p {
      color: #94a3b8;
      font-size: .85rem;
      line-height: 1.6;
    }

    body.light-mode #materi .dc p {
      color: #64748b;
    }

    /* ============================================================
       PERBAIKAN FORM KELOLA QUIZ
       ============================================================ */

    #quizForm {
      background: rgba(15, 23, 42, 0.2) !important;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 16px;
      padding: 24px;
      transition: all 0.3s ease;
    }

    #quizForm:hover {
      border-color: rgba(79, 124, 255, 0.1);
    }

    body.light-mode #quizForm {
      background: rgba(255, 255, 255, 0.15) !important;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    body.light-mode #quizForm:hover {
      border-color: rgba(79, 124, 255, 0.1);
    }

    #quizForm .fg .fl {
      display: block;
      margin-bottom: 6px;
      font-size: .82rem;
      font-weight: 600;
      color: #e2e8f0;
      letter-spacing: 0.3px;
    }

    body.light-mode #quizForm .fg .fl {
      color: #0f172a;
    }

    #quizForm .fi {
      background: rgba(15, 23, 42, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #e2e8f0;
      border-radius: 10px;
      padding: 12px 16px;
      font-size: .88rem;
      transition: all 0.3s ease;
      width: 100%;
      font-family: 'Inter', sans-serif;
    }

    #quizForm .fi:focus {
      border-color: rgba(79, 124, 255, 0.5);
      box-shadow: 0 0 0 4px rgba(79, 124, 255, 0.08);
      outline: none;
      background: rgba(15, 23, 42, 0.6);
    }

    body.light-mode #quizForm .fi {
      background: rgba(255, 255, 255, 0.6);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: #0f172a;
    }

    body.light-mode #quizForm .fi:focus {
      border-color: rgba(79, 124, 255, 0.4);
      box-shadow: 0 0 0 4px rgba(79, 124, 255, 0.06);
      background: rgba(255, 255, 255, 0.8);
    }

    #quizForm .fi::placeholder {
      color: #94a3b8;
      opacity: 0.7;
      font-weight: 400;
    }

    body.light-mode #quizForm .fi::placeholder {
      color: #94a3b8;
      opacity: 0.6;
    }

    #quizForm select.fi {
      background: rgba(15, 23, 42, 0.5);
      color: #e2e8f0;
      appearance: auto;
      cursor: pointer;
    }

    body.light-mode #quizForm select.fi {
      background: rgba(255, 255, 255, 0.6);
      color: #0f172a;
    }

    #quizForm select.fi option {
      background: #1e293b;
      color: #e2e8f0;
      padding: 8px;
    }

    body.light-mode #quizForm select.fi option {
      background: #ffffff;
      color: #0f172a;
    }

    #quizForm .fr {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-bottom: 14px;
    }

    @media (max-width: 600px) {
      #quizForm .fr {
        grid-template-columns: 1fr;
      }
    }

    #quizForm .fg {
      margin-bottom: 14px;
    }

    #quizForm .dashed-add {
      width: 100%;
      border: 1.5px dashed rgba(255, 255, 255, 0.12);
      border-radius: 10px;
      padding: 12px;
      text-align: center;
      color: #4f7cff;
      font-size: .82rem;
      font-weight: 600;
      cursor: pointer;
      background: rgba(79, 124, 255, 0.03);
      transition: all 0.3s ease;
      font-family: 'Inter', sans-serif;
    }

    body.light-mode #quizForm .dashed-add {
      border: 1.5px dashed rgba(0, 0, 0, 0.1);
      background: rgba(79, 124, 255, 0.02);
      color: #4f7cff;
    }

    #quizForm .dashed-add:hover {
      background: rgba(79, 124, 255, 0.08);
      border-color: rgba(79, 124, 255, 0.25);
      transform: scale(1.01);
    }

    body.light-mode #quizForm .dashed-add:hover {
      background: rgba(79, 124, 255, 0.06);
      border-color: rgba(79, 124, 255, 0.2);
    }

    .qbuilder {
      background: rgba(15, 23, 42, 0.35);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 12px;
      padding: 18px;
      margin-bottom: 8px;
      transition: all 0.3s ease;
    }

    .qbuilder:hover {
      border-color: rgba(79, 124, 255, 0.15);
      background: rgba(15, 23, 42, 0.45);
    }

    body.light-mode .qbuilder {
      background: rgba(255, 255, 255, 0.3);
      border: 1px solid rgba(0, 0, 0, 0.06);
    }

    body.light-mode .qbuilder:hover {
      background: rgba(255, 255, 255, 0.4);
      border-color: rgba(79, 124, 255, 0.15);
    }

    .qbuilder .qb-number {
      font-size: .72rem;
      font-weight: 700;
      color: #4f7cff;
      font-family: var(--mono);
      letter-spacing: 0.5px;
      background: rgba(79, 124, 255, 0.1);
      padding: 4px 12px;
      border-radius: 6px;
    }

    body.light-mode .qbuilder .qb-number {
      background: rgba(79, 124, 255, 0.08);
      color: #4f7cff;
    }

    .qbuilder .qb-text {
      margin-bottom: 12px;
      font-size: .9rem;
      padding: 12px 16px;
      font-weight: 500;
    }

    .qbuilder .qb-text::placeholder {
      color: #94a3b8;
      opacity: 0.6;
      font-weight: 400;
    }

    body.light-mode .qbuilder .qb-text::placeholder {
      color: #94a3b8;
      opacity: 0.5;
    }

    .qbuilder .qb-option {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 6px 10px;
      margin-bottom: 4px;
      border-radius: 8px;
      transition: background 0.2s ease;
    }

    .qbuilder .qb-option:hover {
      background: rgba(255, 255, 255, 0.03);
    }

    body.light-mode .qb-option:hover {
      background: rgba(0, 0, 0, 0.02);
    }

    .qbuilder .qb-option input[type="radio"] {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
      accent-color: #4f7cff;
      cursor: pointer;
    }

    .qbuilder .qb-option .qb-opt {
      flex: 1;
      min-width: 0;
      font-size: .85rem;
      padding: 8px 14px;
    }

    .qbuilder .qb-option .qb-opt::placeholder {
      color: #94a3b8;
      opacity: 0.5;
      font-weight: 400;
    }

    body.light-mode .qb-option .qb-opt::placeholder {
      color: #94a3b8;
      opacity: 0.4;
    }

    .qbuilder .qb-delete {
      all: unset;
      font-size: .7rem;
      color: #fb7185;
      font-weight: 600;
      cursor: pointer;
      padding: 4px 8px;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .qbuilder .qb-delete:hover {
      background: rgba(251, 113, 133, 0.1);
      color: #f43f5e;
    }

    body.light-mode .qb-delete {
      color: #dc2626;
    }

    body.light-mode .qb-delete:hover {
      background: rgba(220, 38, 38, 0.08);
      color: #b91c1c;
    }

    .qbuilder .qb-hint {
      font-size: .68rem;
      color: #94a3b8;
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    body.light-mode .qbuilder .qb-hint {
      color: #64748b;
      border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    #quizQuestionsList::-webkit-scrollbar {
      width: 4px;
    }

    #quizQuestionsList::-webkit-scrollbar-track {
      background: transparent;
    }

    #quizQuestionsList::-webkit-scrollbar-thumb {
      background: rgba(79, 124, 255, 0.2);
      border-radius: 10px;
    }

    #quizQuestionsList::-webkit-scrollbar-thumb:hover {
      background: rgba(79, 124, 255, 0.3);
    }

    /* ============================================================
       PERBAIKAN TAMPILAN NILAI SISWA
       ============================================================ */

    .nilai-container {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .nilaicard {
      background: rgba(15, 23, 42, 0.3);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 14px;
      padding: 18px 20px;
      transition: all 0.3s ease;
    }

    .nilaicard:hover {
      background: rgba(15, 23, 42, 0.45);
      border-color: rgba(79, 124, 255, 0.15);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    body.light-mode .nilaicard {
      background: rgba(255, 255, 255, 0.25);
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    body.light-mode .nilaicard:hover {
      background: rgba(255, 255, 255, 0.35);
      border-color: rgba(79, 124, 255, 0.15);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .nilaicard .nilai-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    body.light-mode .nilaicard .nilai-header {
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .nilaicard .nilai-name {
      font-weight: 700;
      font-size: .9rem;
      color: #e2e8f0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    body.light-mode .nilaicard .nilai-name {
      color: #0f172a;
    }

    .nilaicard .nilai-name .nilai-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, rgba(79, 124, 255, 0.2), rgba(139, 123, 255, 0.2));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .8rem;
      font-weight: 700;
      color: #4f7cff;
    }

    body.light-mode .nilaicard .nilai-name .nilai-avatar {
      background: linear-gradient(135deg, rgba(79, 124, 255, 0.1), rgba(139, 123, 255, 0.1));
    }

    .nilaicard .nilai-class {
      font-size: .7rem;
      color: #94a3b8;
      font-family: var(--mono);
      margin-top: 2px;
    }

    body.light-mode .nilaicard .nilai-class {
      color: #64748b;
    }

    .nilaicard .nilai-score {
      font-family: var(--mono);
      font-weight: 800;
      font-size: 1.2rem;
      padding: 4px 14px;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.05);
    }

    body.light-mode .nilaicard .nilai-score {
      background: rgba(0, 0, 0, 0.04);
    }

    .nilaicard .nilai-progress {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px 16px;
      margin-top: 4px;
    }

    @media (max-width: 600px) {
      .nilaicard .nilai-progress {
        grid-template-columns: 1fr;
      }
    }

    .nilaicard .cpn {
      font-size: .75rem;
      font-weight: 500;
      color: #e2e8f0;
    }

    body.light-mode .nilaicard .cpn {
      color: #1e293b;
    }

    .nilaicard .cpp {
      font-size: .7rem;
      font-weight: 600;
      color: #94a3b8;
      font-family: var(--mono);
    }

    body.light-mode .nilaicard .cpp {
      color: #64748b;
    }

    .nilaicard .cpb {
      background: rgba(255, 255, 255, 0.06);
      border-radius: 6px;
      height: 5px;
      overflow: hidden;
      position: relative;
    }

    body.light-mode .nilaicard .cpb {
      background: rgba(0, 0, 0, 0.06);
    }

    .nilaicard .cpb i {
      display: block;
      height: 100%;
      border-radius: 6px;
      transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      background: linear-gradient(90deg, #4f7cff, #8b7bff);
    }

    .nilaicard .cpb i.green {
      background: linear-gradient(90deg, #34d399, #6ee7b7);
    }
    .nilaicard .cpb i.blue {
      background: linear-gradient(90deg, #4f7cff, #8b7bff);
    }
    .nilaicard .cpb i.orange {
      background: linear-gradient(90deg, #f5a623, #fbbf24);
    }
    .nilaicard .cpb i.red {
      background: linear-gradient(90deg, #fb7185, #f43f5e);
    }

    .nilai-search {
      position: relative;
      margin-bottom: 16px;
    }

    .nilai-search span {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: .85rem;
      opacity: 0.5;
    }

    .nilai-search .fi {
      padding-left: 40px;
    }

    .nilai-empty {
      text-align: center;
      padding: 40px 16px;
      color: #94a3b8;
    }

    body.light-mode .nilai-empty {
      color: #64748b;
    }

    .nilai-empty .nilai-empty-icon {
      font-size: 2.5rem;
      margin-bottom: 12px;
      opacity: 0.6;
    }

    .nilai-empty h3 {
      font-weight: 700;
      font-size: .9rem;
      color: #e2e8f0;
    }

    body.light-mode .nilai-empty h3 {
      color: #0f172a;
    }

    .leaderboard-card .lb-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .leaderboard-card .lb-header h3 {
      font-size: .95rem;
      font-weight: 700;
      color: #e2e8f0;
      margin: 0;
    }

    body.light-mode .leaderboard-card .lb-header h3 {
      color: #0f172a;
    }

    /* ============================================================
       PERBAIKAN TAMPILAN LAPORAN MASUK
       ============================================================ */

    .report-container {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .report-card {
      background: rgba(15, 23, 42, 0.3);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 14px;
      padding: 20px 22px;
      transition: all 0.3s ease;
    }

    .report-card:hover {
      background: rgba(15, 23, 42, 0.45);
      border-color: rgba(79, 124, 255, 0.15);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    body.light-mode .report-card {
      background: rgba(255, 255, 255, 0.25);
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    body.light-mode .report-card:hover {
      background: rgba(255, 255, 255, 0.35);
      border-color: rgba(79, 124, 255, 0.15);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .report-card .report-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 8px;
    }

    .report-card .report-title {
      font-weight: 700;
      font-size: 1rem;
      color: #e2e8f0;
      margin: 0;
    }

    body.light-mode .report-card .report-title {
      color: #0f172a;
    }

    .report-card .report-status {
      flex-shrink: 0;
      padding: 4px 14px;
      border-radius: 20px;
      font-size: .7rem;
      font-weight: 600;
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    body.light-mode .report-card .report-status {
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .report-card .report-status.done {
      background: rgba(52, 211, 153, 0.2);
      color: #34d399;
    }
    body.light-mode .report-card .report-status.done {
      background: rgba(52, 211, 153, 0.12);
      color: #059669;
    }

    .report-card .report-status.process {
      background: rgba(245, 166, 35, 0.2);
      color: #f5a623;
    }
    body.light-mode .report-card .report-status.process {
      background: rgba(245, 166, 35, 0.12);
      color: #d97706;
    }

    .report-card .report-status.pending {
      background: rgba(251, 113, 133, 0.2);
      color: #fb7185;
    }
    body.light-mode .report-card .report-status.pending {
      background: rgba(251, 113, 133, 0.12);
      color: #dc2626;
    }

    .report-card .report-desc {
      font-size: .88rem;
      line-height: 1.7;
      color: #e2e8f0;
      margin: 8px 0 12px 0;
      padding: 10px 14px;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 10px;
      border-left: 3px solid rgba(79, 124, 255, 0.3);
    }

    body.light-mode .report-card .report-desc {
      color: #1e293b;
      background: rgba(0, 0, 0, 0.02);
      border-left: 3px solid rgba(79, 124, 255, 0.2);
    }

    .report-card .report-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 6px 16px;
      font-size: .75rem;
      color: #94a3b8;
      font-family: var(--mono);
      padding: 8px 0;
      border-top: 1px solid rgba(255, 255, 255, 0.04);
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      margin-bottom: 14px;
    }

    body.light-mode .report-card .report-meta {
      color: #64748b;
      border-top: 1px solid rgba(0, 0, 0, 0.04);
      border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }

    .report-card .report-meta .meta-item {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .report-card .report-meta .meta-item .meta-label {
      font-weight: 500;
      color: #64748b;
    }

    body.light-mode .report-card .report-meta .meta-item .meta-label {
      color: #475569;
    }

    .report-card .report-meta .meta-item .meta-value {
      color: #e2e8f0;
    }

    body.light-mode .report-card .report-meta .meta-item .meta-value {
      color: #0f172a;
    }

    .report-card .report-actions {
      display: flex;
      gap: 10px;
      padding-top: 4px;
      align-items: center;
    }

    .report-card .report-actions select {
      flex: 1;
      padding: 10px 14px;
      border-radius: 10px;
      background: rgba(15, 23, 42, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.08);
      color: #e2e8f0;
      font-size: .82rem;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .report-card .report-actions select:focus {
      border-color: rgba(79, 124, 255, 0.4);
      box-shadow: 0 0 0 3px rgba(79, 124, 255, 0.08);
      outline: none;
    }

    body.light-mode .report-card .report-actions select {
      background: rgba(255, 255, 255, 0.5);
      border: 1px solid rgba(0, 0, 0, 0.08);
      color: #0f172a;
    }

    body.light-mode .report-card .report-actions select:focus {
      border-color: rgba(79, 124, 255, 0.4);
      box-shadow: 0 0 0 3px rgba(79, 124, 255, 0.06);
    }

    .report-card .report-actions select option {
      background: #1e293b;
      color: #e2e8f0;
      padding: 8px;
    }

    body.light-mode .report-card .report-actions select option {
      background: #ffffff;
      color: #0f172a;
    }

    .report-card .report-actions .btn-update {
      padding: 10px 20px;
      border-radius: 10px;
      background: rgba(79, 124, 255, 0.2);
      color: #4f7cff;
      border: 1px solid rgba(79, 124, 255, 0.2);
      font-size: .82rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: 'Inter', sans-serif;
      white-space: nowrap;
    }

    body.light-mode .report-card .report-actions .btn-update {
      background: rgba(79, 124, 255, 0.12);
      color: #4f7cff;
      border: 1px solid rgba(79, 124, 255, 0.15);
    }

    .report-card .report-actions .btn-update:hover {
      background: rgba(79, 124, 255, 0.3);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(79, 124, 255, 0.15);
    }

    body.light-mode .report-card .report-actions .btn-update:hover {
      background: rgba(79, 124, 255, 0.2);
      box-shadow: 0 4px 12px rgba(79, 124, 255, 0.1);
    }

    .report-empty {
      text-align: center;
      padding: 50px 20px;
      color: #94a3b8;
    }

    body.light-mode .report-empty {
      color: #64748b;
    }

    .report-empty .report-empty-icon {
      font-size: 3rem;
      margin-bottom: 16px;
      opacity: 0.5;
    }

    .report-empty h3 {
      font-weight: 700;
      font-size: 1rem;
      color: #e2e8f0;
      margin-bottom: 4px;
    }

    body.light-mode .report-empty h3 {
      color: #0f172a;
    }

    .report-empty p {
      font-size: .85rem;
      opacity: 0.7;
    }

    /* ============================================================
       PENYESUAIAN LAINNYA
       ============================================================ */


    .welcome-hero h1 { color: #e2e8f0; }
    body.light-mode .welcome-hero h1 { color: #0f172a; }
    .welcome-hero p { color: #94a3b8; }
    body.light-mode .welcome-hero p { color: #64748b; }
    .welcome-hero h1 .tc {
      background: linear-gradient(135deg, #4f7cff, #8b7bff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 40px rgba(79, 124, 255, 0.2);
    }


    .empty-state { text-align: center; padding: 40px 16px; color: #94a3b8; }
    body.light-mode .empty-state { color: #64748b; }
    .empty-state .ic { font-size: 2.2rem; margin-bottom: .6rem; opacity: .6; }
    .empty-state h3 { color: #e2e8f0; }
    body.light-mode .empty-state h3 { color: #0f172a; }

    .gtable { width: 100%; border-collapse: collapse; font-size: .82rem; color: #e2e8f0; }
    body.light-mode .gtable { color: #1e293b; }
    .gtable th { text-align: left; color: #94a3b8; font-family: var(--mono); font-size: .65rem; text-transform: uppercase; letter-spacing: .06em; padding-bottom: 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-weight: 600; }
    body.light-mode .gtable th { color: #64748b; border-bottom: 1px solid rgba(0, 0, 0, 0.08); }
    .gtable td { padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.04); color: #e2e8f0; overflow-wrap: anywhere; word-break: break-word; }
    body.light-mode .gtable td { border-bottom: 1px solid rgba(0, 0, 0, 0.04); color: #1e293b; }
    .gtable tbody tr { transition: background-color .15s ease; }
    .gtable tbody tr:hover { background: rgba(255, 255, 255, 0.05); }
    body.light-mode .gtable tbody tr:hover { background: rgba(0, 0, 0, 0.03); }

    /* NOTIF DROPDOWN */

    /* LANG DROPDOWN */
    .lang-dropdown {
      background: rgba(15, 23, 42, 0.9);
      backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    body.light-mode .lang-dropdown {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(16px);
      border: 1px solid rgba(0, 0, 0, 0.06);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }
    .lang-option { color: #e2e8f0; }
    body.light-mode .lang-option { color: #1e293b; }
    .lang-option:hover { background: rgba(79, 124, 255, 0.1); }
    body.light-mode .lang-option:hover { background: rgba(79, 124, 255, 0.05); }

    /* DASHBOARD TITLE */
    .dtb h1 { color: #e2e8f0; }
    body.light-mode .dtb h1 { color: #0f172a; }
    .dtb p { color: #94a3b8; }
    body.light-mode .dtb p { color: #64748b; }

    /* ALERT */

    /* ACTIVITY LIST */
    .alist .ai .atext { color: #e2e8f0; }
    body.light-mode .alist .ai .atext { color: #1e293b; }

    /* GRID */

    .gstats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.75rem; }
    @media (max-width: 900px) { .gstats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .gstats { grid-template-columns: 1fr 1fr; gap: 8px; } }

    .gcharts { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    @media (max-width: 900px) { .gcharts { grid-template-columns: 1fr; } }

    .chart-box { position: relative; height: 240px; padding: 10px 0; }

    .dsic { font-size: 1.5rem; margin-bottom: 4px; }

    .searchbox { position: relative; margin-bottom: .85rem; }
    .searchbox span { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: .8rem; opacity: .6; }
    .searchbox .fi { padding-left: 34px; }



    .lang-current-label { font-size: .7rem; font-weight: 600; margin-left: 2px; }

    /* Dropdown notif & profil: anchor relatif ke tombolnya (pola sama seperti
       lang-dropdown yang sudah berfungsi) — hindari position:fixed yang rusak
       karena backdrop-filter pada .dhead. */
    .notif-dropdown {
      position: absolute;
      right: 0;
      top: calc(100% + 10px);
    }
    .profile-dropdown {
      position: absolute;
      right: 0;
      top: calc(100% + 10px);
    }
    .notif-wrap, .prof-wrap {
      z-index: 500;
    }

    /* Notif guru memakai <button> — reset gaya bawaan agar serapi versi siswa (div). */
    .notif-dropdown .notif-item {
      width: 100%;
      border: none;
      background: transparent;
      text-align: left;
      font-family: inherit;
      color: inherit;
    }





    /* PERBAIKAN CHART */
    .chart-box canvas {
      max-height: 100%;
      max-width: 100%;
    }
    .dc .gcharts .dc {
      padding: 16px;
    }
    .dc .gcharts .dc .fl {
      margin-bottom: 16px !important;
    }

    /* ============ PENYEMPURNAAN UI (POLISH) ============ */

    .panel-title { font-family: var(--ff-display); font-size: 1.6rem; font-weight: 700; letter-spacing: -.5px; color: var(--text); }
    .panel-sub { font-size: .875rem; margin-top: 4px; color: var(--text2); }
    .dtb-sep { margin-bottom: 1.5rem; }
    .dcpanel { margin-bottom: 1.5rem; }

    .stat-big { text-align: center; padding: 14px 0; }
    .stat-big-value { font-size: 2rem; font-weight: 800; font-family: var(--mono); }
    .stat-big-label { font-size: .78rem; color: var(--text2); }
    .stat-big-value.c-cyan { color: var(--cyan); }
    .stat-big-value.c-amber { color: var(--amber); }
    .gsep { margin-top: 1.5rem; }

    .chart-panel {
      background: rgba(15, 23, 42, 0.30);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      border: 1px solid rgba(255, 255, 255, 0.05);
      padding: 16px;
      border-radius: var(--rmd);
    }
    body.light-mode .chart-panel {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(0, 0, 0, 0.05);
    }
    .chart-label { margin-bottom: 16px; font-weight: 600; }
    .chart-live { font-size: .7rem; font-family: var(--mono); color: var(--text3); }

    /* TIMELINE AKTIVITAS */
    .timeline { display: flex; flex-direction: column; }
    .t-day + .t-day { margin-top: 4px; }
    .t-day-label {
      display: inline-flex; align-items: center; gap: 7px;
      font-size: .66rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
      color: var(--text3); margin: 2px 0 10px;
    }
    .t-day-label::before {
      content: ""; width: 5px; height: 5px; border-radius: 50%;
      background: var(--cyan); opacity: .7;
    }
    .t-item { position: relative; display: flex; gap: 14px; padding-bottom: 18px; }
    .t-day:last-child .t-item:last-child { padding-bottom: 0; }
    .t-line {
      position: absolute; left: 18px; top: 40px; bottom: 0; width: 2px;
      background: var(--border2); border-radius: 2px; pointer-events: none;
    }
    .t-day:last-child .t-line { display: none; }
    .t-bubble {
      width: 38px; height: 38px; flex-shrink: 0; border-radius: 12px;
      display: flex; align-items: center; justify-content: center; font-size: 1.05rem;
      background: rgba(var(--accent), .14); color: rgb(var(--accent));
      border: 1px solid rgba(var(--accent), .3);
    }
    .t-item.latest .t-bubble { box-shadow: 0 0 0 3px var(--cd); border-color: rgb(var(--accent)); }
    .t-main { flex: 1; min-width: 0; padding-top: 3px; }
    .t-head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .t-name { font-weight: 700; font-size: .8rem; color: var(--text); }
    .t-chip {
      font-size: .6rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
      padding: 2px 8px; border-radius: 999px;
      background: rgba(var(--accent), .12); color: rgb(var(--accent));
    }
    .t-time { margin-left: auto; font-size: .67rem; font-family: var(--mono); color: var(--text3); flex-shrink: 0; }
    .t-body { font-size: .78rem; margin-top: 3px; color: var(--text2); line-height: 1.5; }
    .t-item.tone-0 { --accent: 79,124,255; }
    .t-item.tone-1 { --accent: 139,92,246; }
    .t-item.tone-2 { --accent: 16,185,129; }
    .t-item.tone-3 { --accent: 245,158,11; }
    .t-item.tone-4 { --accent: 236,72,153; }
    .t-item.tone-5 { --accent: 6,182,212; }
    @media (max-width: 480px) {
      .t-time { display: none; }
      .t-day-label { font-size: .62rem; }
    }

    /* TOMBOL HAPUS */
    .btn.bdel { color: var(--rose); }
    .btn.bdel:hover { color: var(--rose); background: var(--rd); transform: translateY(-1px); }
    body.light-mode .btn.bdel:hover { color: var(--rose); background: var(--rd); }

    /* WARNA SCORE NILAI */
    .nilaicard .nilai-score.high { color: var(--green); background: var(--gd); }
    .nilaicard .nilai-score.medium { color: var(--amber); background: var(--ad); }
    .nilaicard .nilai-score.low { color: var(--rose); background: var(--rd); }

    /* IKON KECIL (lucide) */
    .icon-sm { width: 13px; height: 13px; vertical-align: -2px; }

    /* BARIS ITEM */
    .itemmain { flex: 1; min-width: 0; }

    /* EMPTY STATE KONSISTEN */
    .empty-state h3 { font-size: .9rem; }
    .report-empty h3 { font-size: 1rem; }

    /* HASIL FILTER KOSONG */
    .filter-empty { display: none; padding: 12px 0; text-align: center; font-size: .85rem; color: var(--text3); }

    /* CATATAN PROGRESS KOSONG */
    .nilai-note { font-size: .78rem; font-style: italic; color: var(--text3); padding: 8px 0; }

    /* LEADERBOARD PANEL */
    .lbpanel { margin-bottom: 1.5rem; }

    /* LABEL SEKSI */
    .fl.fsep { margin-top: 6px; margin-bottom: 8px; }

    /* DAFTAR SCROLL CHAPTER / SOAL */
    #chaptersList, #quizQuestionsList {
      overflow-y: auto;
      margin-bottom: 8px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    #chaptersList { max-height: 340px; }
    #quizQuestionsList { max-height: 400px; padding-right: 4px; }
    .dashed-add { margin-bottom: 12px; }

    /* SEL TABEL */
    .gtable td.td-name { font-weight: 600; }
    .gtable td.td-email { color: var(--text2); }
    .itemicon.itemicon-quiz { background: var(--cd); color: var(--cyan); }

    /* FOOTER */
    .dmain-footer { margin-top: 1.5rem; text-align: center; font-size: .75rem; color: var(--text3); }

    /* PEMISAH ALERT */
    .altsep { margin-bottom: 1.25rem; }

    /* DESKRIPSI CATATAN */
    .desc-note { font-size: .85rem; margin: -.75rem 0 1.25rem; color: var(--text2); line-height: 1.6; }

    /* AKSESIBILITAS KEYBOARD */
    .fi:focus-visible, .btn:focus-visible, .dashed-add:focus-visible, .colorpick:focus-within {
      outline: 2px solid var(--cyan);
      outline-offset: 2px;
    }

    /* LIGHT-MODE: semua input .fi (materiForm, modal, searchbox, builder)
       ikut terang kontras, tidak hanya #quizForm .fi. */
    body.light-mode .fi {
      background: rgba(255, 255, 255, 0.85);
      border: 1px solid rgba(0, 0, 0, 0.12);
      color: #0f172a;
    }
    body.light-mode .fi:focus {
      background: #ffffff;
      border-color: rgba(79, 124, 255, 0.4);
      box-shadow: 0 0 0 4px rgba(79, 124, 255, 0.08);
    }
    body.light-mode .fi::placeholder {
      color: #64748b;
      opacity: 0.6;
    }
    body.light-mode select.fi option {
      background: #ffffff;
      color: #0f172a;
    }
  </style>
</head>

<body>

  <div class="stars">
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
  </div>

  <div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

  <div class="dlayout">
    <aside class="sidebar" id="sidebar">
      <div class="sb-brand">
        <div class="sb-logo">
          <img
            src="<?= htmlspecialchars(assetUrl('assets/logo/EduCare-logo.png')) ?>"
            alt="Logo EduCare"
            width="40"
            height="40"
            class="w-10 h-10 object-contain rounded-xl">
        </div>
        <div class="sb-brand-text">
          <span class="sb-brand-name">EduCare</span>
          <span class="sb-brand-tag" data-i18n="guru.sidebar.brand_tag">Platform Sekolah</span>
        </div>
        <!-- TAMBAHAN: Tombol panah untuk expand/collapse -->
        <button class="sb-collapse-toggle" id="sbCollapseBtn" type="button" onclick="toggleSidebarCollapse()" aria-label="Ciutkan menu"><i data-lucide="chevron-left"></i></button>
        <button class="sb-close" id="sbCloseBtn" type="button" onclick="closeSidebar()" aria-label="Tutup menu"><i data-lucide="x"></i></button>
      </div>

      <nav class="sb-nav-scroll">
        <p class="sbsl" data-i18n="guru.sidebar.section_utama">Utama</p>
        <ul class="sbnav">
          <li><a class="act" onclick="goSection('dashboard'); closeSidebar()"><span class="sbic"><i data-lucide="layout-dashboard"></i></span><span data-i18n="guru.sidebar.nav_dashboard">Dashboard</span></a></li>
          <li><a onclick="goSection('analitik'); closeSidebar()"><span class="sbic"><i data-lucide="bar-chart-3"></i></span><span data-i18n="guru.sidebar.nav_analitik">Analitik</span></a></li>
        </ul>

        <p class="sbsl" data-i18n="guru.sidebar.section_pembelajaran">Pembelajaran</p>
        <ul class="sbnav">
          <li><a onclick="goSection('materi'); closeSidebar()"><span class="sbic"><i data-lucide="book-open"></i></span><span data-i18n="guru.sidebar.nav_materi">Kelola Kursus</span></a></li>
          <li><a onclick="goSection('quiz'); closeSidebar()"><span class="sbic"><i data-lucide="file-question"></i></span><span data-i18n="guru.sidebar.nav_quiz">Kelola Quiz</span></a></li>
          <li><a onclick="goSection('nilai'); closeSidebar()"><span class="sbic"><i data-lucide="award"></i></span><span data-i18n="guru.sidebar.nav_nilai">Nilai Siswa</span></a></li>
        </ul>

        <p class="sbsl" data-i18n="guru.sidebar.section_siswa">Siswa</p>
        <ul class="sbnav">
          <li><a onclick="goSection('data-siswa'); closeSidebar()"><span class="sbic"><i data-lucide="users"></i></span><span data-i18n="guru.sidebar.nav_data_siswa">Data Siswa</span></a></li>
          <li><a onclick="goSection('aktivitas'); closeSidebar()"><span class="sbic"><i data-lucide="activity"></i></span><span data-i18n="guru.sidebar.nav_aktivitas">Aktivitas Siswa</span></a></li>
        </ul>

        <p class="sbsl" data-i18n="guru.sidebar.section_silapor">SiLapor</p>
        <ul class="sbnav">
          <li><a onclick="goSection('laporan-masuk'); closeSidebar()">
              <span class="sbic"><i data-lucide="inbox"></i></span><span data-i18n="guru.sidebar.nav_laporan_masuk">Laporan Masuk</span>
              <?php if ($totalLaporanBaru > 0): ?><div class="sbbdg"><?= $totalLaporanBaru ?></div><?php endif; ?>
            </a></li>
        </ul>
      </nav>
    </aside>

    <div class="dcol">
      <header class="dhead" id="dhead">
        <button class="dhead-burger" id="mnavToggle" type="button" onclick="toggleSidebar()" aria-label="Buka menu"><i data-lucide="menu"></i></button>
        <div class="dhead-crumb">
          <span class="dhead-crumb-path" id="pageBreadcrumb"><span data-i18n="guru.header.breadcrumb_dashboard">Dashboard Guru</span> / <span class="cur" data-i18n="guru.header.breadcrumb_overview">Overview</span></span>
        </div>
        <div class="dhead-actions">
          <div class="dhead-clock" id="topbarClock">
            <span class="dhead-clock-time" id="topbarClockTime">--:--:--</span>
            <span class="dhead-clock-date" id="topbarClockDate">-</span>
          </div>
          <div class="lang-switcher" style="position:relative">
            <button type="button" class="sb-toggle lang-toggle-btn" id="langToggleBtn" onclick="toggleLangDropdown(event)" aria-label="Ganti bahasa">
              <i data-lucide="languages"></i>
              <span class="lang-current-label">ID</span>
            </button>
            <div class="lang-dropdown" id="langDropdown" style="display:none;position:absolute;right:0;top:110%;border-radius:10px;min-width:140px;overflow:hidden;z-index:50">
              <button type="button" data-lang-set="id" class="lang-option" style="display:flex;align-items:center;gap:8px;width:100%;text-align:left;padding:10px 14px;font-size:.85rem;background:none;border:none;cursor:pointer;">🇮🇩 Indonesia</button>
              <button type="button" data-lang-set="en" class="lang-option" style="display:flex;align-items:center;gap:8px;width:100%;text-align:left;padding:10px 14px;font-size:.85rem;background:none;border:none;cursor:pointer;">🇬🇧 English</button>
            </div>
          </div>
          <button class="theme-toggle sb-toggle" id="themeToggle" type="button" onclick="toggleTheme()" aria-label="Ganti mode"><i data-lucide="sun"></i></button>
          <div class="notif-wrap" style="position:relative">
            <button class="sb-toggle notif-bell" id="notifBellBtn" type="button" onclick="toggleNotifDropdown()" aria-label="Notifikasi">
              <i data-lucide="bell"></i>
              <span class="notif-badge" id="notifBadge" <?= $unreadNotifCount > 0 ? '' : 'style="display:none"' ?>><?= $unreadNotifCount ?></span>
            </button>
            <!-- NOTIFIKASI DROPDOWN -->
            <div class="notif-dropdown" id="notifDropdown">
              <div class="notif-dropdown-head">
                <h4><i data-lucide="bell" style="width:16px;height:16px"></i> <span data-i18n="guru.header.notif_title">Notifikasi</span></h4>
                <button type="button" class="notif-close" onclick="toggleNotifDropdown()" aria-label="Tutup notifikasi"><i data-lucide="x"></i></button>
              </div>
              <div class="notif-dropdown-body">
                <?php if (empty($guruNotifs)): ?>
                  <div class="empty-state" style="padding:24px">
                    <div class="ic"><i data-lucide="bell-off"></i></div>
                    <span data-i18n="guru.dynamic.no_notifications">Belum ada notifikasi.</span>
                  </div>
                <?php else: ?>
                  <?php foreach (array_slice($guruNotifs, 0, 10) as $n):
                    $_nt = strtotime($n['created_at'] ?? '');
                    if (!$_nt) $_nt = $nowTs;
                    $_diff = $nowTs - $_nt;
                    if ($_diff < 60) $_rel = 'Baru saja';
                    elseif ($_diff < 3600) $_rel = max(1, (int)($_diff / 60)) . ' mnt lalu';
                    elseif ($_diff < 86400) $_rel = (int)($_diff / 3600) . ' jam lalu';
                    else $_rel = (int)($_diff / 86400) . ' hr lalu';
                    ?>
                    <form method="POST" action="" style="display:block;margin:0;padding:0;">
                      <?= csrfField() ?>
                      <input type="hidden" name="mark_notif_read" value="1">
                      <input type="hidden" name="notif_id" value="<?= (int)($n['id'] ?? 0) ?>">
                      <button type="submit" class="notif-item <?= empty($n['read']) ? 'unread' : '' ?>">
                        <span class="notif-item-icon"><?= htmlspecialchars($n['icon'] ?? '🔔') ?></span>
                        <span class="notif-item-body">
                          <div class="notif-item-title"><?= htmlspecialchars($n['title'] ?? '') ?></div>
                          <div class="notif-item-msg"><?= htmlspecialchars($n['msg'] ?? '') ?></div>
                          <div class="notif-item-time"><?= htmlspecialchars($_rel) ?></div>
                        </span>
                        <?php if (empty($n['read'])): ?><span class="notif-item-dot"></span><?php endif; ?>
                      </button>
                    </form>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="prof-wrap" style="position:relative">
            <button class="dhead-user" id="profileMenuBtn" type="button" onclick="toggleProfileDropdown()" aria-label="Menu profil">
              <div class="dhead-avi"><?= strtoupper(substr($name, 0, 1)) ?></div>
              <div class="dhead-user-info">
                <span class="dhead-user-name"><?= $name ?></span>
                <span class="dhead-user-role" data-i18n="guru.header.role">Guru</span>
              </div>
              <span class="dhead-user-caret"><i data-lucide="chevron-down"></i></span>
            </button>
            <!-- PROFILE DROPDOWN -->
            <div class="profile-dropdown" id="profileDropdown">
              <div class="profile-dropdown-head">
                <div class="dhead-avi"><?= strtoupper(substr($name, 0, 1)) ?></div>
                <div style="min-width:0">
                  <div class="profile-dropdown-head-name"><?= $name ?></div>
                  <div class="profile-dropdown-head-role" data-i18n="guru.header.profile_role">Guru · EduCare</div>
                </div>
              </div>
              <div class="profile-dropdown-body">
                <a class="plogout" href="<?= htmlspecialchars(pageUrl('auth/logout.php')) ?>" data-i18n="guru.header.menu_logout">🚪 Keluar</a>
              </div>
            </div>
          </div>
          </div>
      </header>

      <main class="dmain">

        <?php if (isset($_SESSION['guru_message'])): ?>
          <div class="alt aok altsep">✅ <?= htmlspecialchars($_SESSION['guru_message']) ?></div>
          <?php unset($_SESSION['guru_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['guru_error'])): ?>
          <div class="alt aerr altsep">⚠️ <?= htmlspecialchars($_SESSION['guru_error']) ?></div>
          <?php unset($_SESSION['guru_error']); ?>
        <?php endif; ?>

        <!-- OVERVIEW -->
        <div class="dp act" id="dp-dashboard">
          <div id="dashboard" class="dtb welcome-hero">
            <div>
              <h1><span data-i18n="guru.overview.greeting">Selamat datang,</span> <span class="tc"><?= $name ?></span> 👋</h1>
              <p data-i18n="guru.overview.subtitle">Kelola kursus, quiz, dan pantau perkembangan siswa.</p>
            </div>
          </div>

          <div class="gstats">
            <div class="dcard">
              <div class="dsic">📚</div>
              <div class="dsv"><?= $totalMateri ?></div>
              <div class="dsl" data-i18n="guru.overview.stat_courses">Total Kursus</div>
            </div>
            <div class="dcard">
              <div class="dsic">👥</div>
              <div class="dsv"><?= $totalSiswa ?></div>
              <div class="dsl" data-i18n="guru.overview.stat_students">Total Siswa</div>
            </div>
            <div class="dcard">
              <div class="dsic">❓</div>
              <div class="dsv"><?= $totalQuiz ?></div>
              <div class="dsl" data-i18n="guru.overview.stat_quiz">Total Quiz</div>
            </div>
            <div class="dcard">
              <div class="dsic">📮</div>
              <div class="dsv"><?= $totalLaporanBaru ?></div>
              <div class="dsl" data-i18n="guru.overview.stat_reports">Laporan Baru</div>
            </div>
          </div>

          <div class="dg2 gsep">
            <div class="dc">
              <div class="dch">
                <h3 data-i18n="guru.overview.recent_courses_title">📚 Kursus Terbaru</h3>
                <button class="btn bghost bsm" type="button" onclick="goSection('materi')" data-i18n="guru.overview.link_manage">Kelola →</button>
              </div>
              <?php if (empty($materi)): ?>
                <div class="empty-state">
                  <div class="ic">📭</div>
                  <h3 data-i18n="guru.dynamic.no_courses">Belum ada kursus</h3>
                </div>
              <?php else: ?>
                <?php foreach (array_slice(array_reverse($materi), 0, 3) as $m): ?>
                  <div class="itemrow">
                    <div class="itemicon" style="background:<?= htmlspecialchars($m['color'] ?? '#4f7cff') ?>;color:#fff"><?= htmlspecialchars($m['emoji'] ?? '📘') ?></div>
                    <div class="itemmain">
                      <div class="itemtitle"><?= htmlspecialchars($m['title']) ?></div>
                      <div class="itemmeta"><?= count($m['lessons'] ?? []) ?> <span data-i18n="guru.dynamic.chapter_unit">chapter</span> · <span data-i18n="guru.dynamic.level_<?= htmlspecialchars($m['level'] ?? 'beginner') ?>"><?= htmlspecialchars($levelOpts[$m['level'] ?? 'beginner'] ?? 'Pemula') ?></span></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <div class="dc">
              <div class="dch">
                <h3 data-i18n="guru.overview.recent_activity_title">📝 Aktivitas Siswa</h3>
                <button class="btn bghost bsm" type="button" onclick="goSection('aktivitas')" data-i18n="guru.overview.link_detail">Detail →</button>
              </div>
              <?php if (empty($studentActivities)): ?>
                <div class="empty-state">
                  <div class="ic">📝</div>
                  <h3 data-i18n="guru.dynamic.no_activity">Belum ada aktivitas siswa</h3>
                </div>
              <?php else: ?>
                <div class="alist">
                  <?php foreach (array_slice($studentActivities, 0, 4) as $a): ?>
                    <div class="ai">
                      <div class="atext"><?= htmlspecialchars($a['name'] ?? 'Siswa') ?> <?= htmlspecialchars($a['icon'] ?? '📘') ?> — <?= htmlspecialchars($a['text'] ?? '') ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="dg3 gsep">
            <div class="dc">
              <div class="dch">
                <h3 data-i18n="guru.overview.quiz_title">❓ Kelola Quiz</h3>
                <button class="btn bghost bsm" type="button" onclick="goSection('quiz')" data-i18n="guru.overview.link_manage">Kelola →</button>
              </div>
              <div class="stat-big">
                <div class="stat-big-value c-cyan"><?= $totalQuiz ?></div>
                <div class="stat-big-label" data-i18n="guru.overview.stat_quiz">Total Quiz</div>
              </div>
            </div>
            <div class="dc">
              <div class="dch">
                <h3 data-i18n="guru.overview.reports_title">📮 Laporan Masuk</h3>
                <button class="btn bghost bsm" type="button" onclick="goSection('laporan-masuk')" data-i18n="guru.overview.link_detail">Detail →</button>
              </div>
              <div class="stat-big">
                <div class="stat-big-value c-amber"><?= $totalLaporanBaru ?></div>
                <div class="stat-big-label" data-i18n="guru.overview.stat_reports">Laporan Baru</div>
              </div>
            </div>
            <div class="dc">
              <div class="dch">
                <h3 data-i18n="guru.overview.top_students_title">🏆 Top Siswa</h3>
                <button class="btn bghost bsm" type="button" onclick="goSection('nilai')" data-i18n="guru.overview.link_view">Lihat →</button>
              </div>
              <?php if (empty($leaderboardTop)): ?>
                <div class="empty-state">
                  <div class="ic">🏆</div>
                  <h3 data-i18n="guru.dynamic.no_data">Belum ada data</h3>
                </div>
              <?php else: ?>
                <?php foreach (array_slice($leaderboardTop, 0, 3) as $i => $l): ?>
                  <div class="itemrow">
                    <div class="itemicon" style="background:var(--vd);color:var(--violet);font-family:var(--mono);font-weight:700">#<?= $i + 1 ?></div>
                    <div class="itemmain">
                      <div class="itemtitle"><?= htmlspecialchars($l['name'] ?? 'Siswa') ?></div>
                      <div class="itemmeta"><span data-i18n="guru.dynamic.level">Level</span> <?= (int)($l['level'] ?? 1) ?></div>
                    </div>
                    <span class="bdg bvl"><?= (int)($l['xp'] ?? 0) ?> XP</span>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- ANALITIK -->
        <div class="dp" id="dp-analitik">
          <div class="dtb dtb-sep">
            <div>
              <h1 class="panel-title" data-i18n="guru.analitik.title">Analitik Pembelajaran 📊</h1>
              <p class="panel-sub" data-i18n="guru.analitik.subtitle">Pantau performa belajar siswa secara real-time.</p>
            </div>
          </div>
          <div id="analitik" class="dc dcpanel">
            <div class="dch">
              <h3 data-i18n="guru.analitik.title">Analitik Pembelajaran</h3>
              <span class="chart-live">real-time</span>
            </div>
            <div class="gcharts">
              <div class="dc chart-panel">
                <p class="fl chart-label" data-i18n="guru.analitik.chart_kelas">Rata-rata Nilai per Kelas</p>
                <div class="chart-box"><canvas id="chartKelas"></canvas></div>
              </div>
              <div class="dc chart-panel">
                <p class="fl chart-label" data-i18n="guru.analitik.chart_laporan">Status Laporan Masuk</p>
                <div class="chart-box"><canvas id="chartLaporan"></canvas></div>
              </div>
              <div class="dc chart-panel">
                <p class="fl chart-label" data-i18n="guru.analitik.chart_kategori">Kursus per Kategori</p>
                <div class="chart-box"><canvas id="chartKategori"></canvas></div>
              </div>
              <div class="dc chart-panel">
                <p class="fl chart-label" data-i18n="guru.analitik.chart_mapel">Rata-rata Pemahaman per Mapel</p>
                <div class="chart-box"><canvas id="chartMapel"></canvas></div>
              </div>
            </div>
          </div>
        </div>

        <!-- KELOLA KURSUS -->
        <div class="dp" id="dp-materi">
          <div class="dtb dtb-sep">
            <div>
              <h1 class="panel-title" data-i18n="guru.materi.title">Kelola Kursus 📚</h1>
              <p class="panel-sub" data-i18n="guru.materi.subtitle">Tambah, lihat, dan kelola kursus untuk siswa.</p>
            </div>
          </div>
          <div id="materi" class="dc dcpanel">
            <div class="dch">
              <h3 data-i18n="guru.materi.title">Kelola Kursus</h3>
            </div>
            <p class="desc-note" data-i18n="guru.dynamic.manage_course_desc">Kursus yang kamu buat di sini langsung tampil di halaman "Jelajahi Kursus" milik siswa, lengkap dengan chapter & pelajarannya — dan siswa otomatis dapat notifikasi.</p>

            <form action="" method="POST" id="materiForm" onsubmit="return prepareMateriSubmit()">
              <?= csrfField() ?>
              <input type="hidden" name="add_materi" value="1">
              <input type="hidden" name="chapters_json" id="chaptersJson" value="[]">

              <div class="fg">
                <label class="fl" data-i18n="guru.dynamic.course_title">Judul Kursus</label>
                <input class="fi" type="text" name="title" required placeholder="Contoh: Belajar HTML Dasar" data-i18n-placeholder="guru.dynamic.course_title_placeholder" />
              </div>
              <div class="fr">
                <div class="fg">
                  <label class="fl" data-i18n="guru.dynamic.category">Kategori</label>
                  <input class="fi" type="text" name="category" required placeholder="web / ai / data" />
                </div>
                <div class="fg">
                  <label class="fl">Emoji</label>
                  <input class="fi" type="text" name="emoji" placeholder="💻" maxlength="4" />
                </div>
                <div class="fg">
                  <label class="fl" data-i18n="guru.dynamic.level">Level</label>
                  <select class="fi" name="level">
                    <?php foreach ($levelOpts as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="fg">
                <label class="fl" data-i18n="guru.dynamic.short_description">Deskripsi Singkat</label>
                <textarea class="fi" name="desc" placeholder="Deskripsi kursus untuk ditampilkan ke siswa..." data-i18n-placeholder="guru.dynamic.course_description_placeholder"></textarea>
              </div>
              <div class="fg">
                <label class="fl" data-i18n="guru.dynamic.theme_color">Warna Tema</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                  <?php foreach ($colorOpts as $i => $c): ?>
                    <label class="colorpick" style="background:<?= $c ?>">
                      <input type="radio" name="color" value="<?= $c ?>" <?= $i === 0 ? 'checked' : '' ?>>
                      <span class="ring"></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <label class="fl fsep" data-i18n="guru.dynamic.chapters_lessons">Chapter & Pelajaran</label>
              <div id="chaptersList"></div>
              <button type="button" onclick="addChapter()" class="dashed-add" data-i18n="guru.dynamic.add_chapter">+ Tambah Chapter</button>
              <button type="submit" class="btn bcyan bfull" data-i18n="guru.dynamic.save_course">Simpan Kursus</button>
            </form>

            <?php if (empty($materi)): ?>
              <div class="empty-state">
                <div class="ic">📭</div>
                <h3 data-i18n="guru.dynamic.no_courses">Belum ada kursus</h3>
              </div>
            <?php else: ?>
              <?php foreach (array_reverse($materi) as $m):
                $lessonCount = 0;
                foreach (($m['lessons'] ?? []) as $ch) $lessonCount += count($ch['items'] ?? []);
              ?>
                <div class="itemrow">
                  <div class="itemicon" style="background:<?= htmlspecialchars($m['color'] ?? '#4f7cff') ?>;color:#fff"><?= htmlspecialchars($m['emoji'] ?? '📘') ?></div>
                  <div class="itemmain">
                    <div class="itemtitle"><?= htmlspecialchars($m['title']) ?></div>
                    <div class="itemmeta"><?= count($m['lessons'] ?? []) ?> <span data-i18n="guru.dynamic.chapter_unit">chapter</span> · <?= $lessonCount ?> <span data-i18n="guru.dynamic.lessons">pelajaran</span> · <span data-i18n="guru.dynamic.level_<?= htmlspecialchars($m['level'] ?? 'beginner') ?>"><?= htmlspecialchars($levelOpts[$m['level'] ?? 'beginner'] ?? 'Pemula') ?></span></div>
                  </div>
                  <form action="" method="POST" onsubmit="return confirm('Hapus kursus ini?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="delete_materi" value="1">
                    <input type="hidden" name="materi_id" value="<?= (int)$m['id'] ?>">
                    <button type="submit" class="btn bghost bsm" data-i18n="guru.dynamic.delete_course">Hapus</button>
                  </form>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- DATA SISWA -->
        <div class="dp" id="dp-data-siswa">
          <div class="dtb dtb-sep">
            <div>
              <h1 class="panel-title" data-i18n="guru.data_siswa.title">Data Siswa 👥</h1>
              <p class="panel-sub" data-i18n="guru.data_siswa.subtitle">Daftar seluruh siswa yang terdaftar di EduCare.</p>
            </div>
          </div>
          <div id="data-siswa" class="dc dcpanel">
            <div class="dch">
              <h3 data-i18n="guru.data_siswa.title">Data Siswa</h3>
            </div>
            <?php $siswaList = array_filter($usersList, function ($u) {
              return ($u['role'] ?? '') === 'siswa';
            }); ?>
            <?php if (empty($siswaList)): ?>
              <div class="empty-state">
                <div class="ic">👥</div>
                <h3 data-i18n="guru.dynamic.no_students">Belum ada siswa terdaftar</h3>
              </div>
            <?php else: ?>
              <div class="searchbox"><span><i data-lucide="search" class="icon-sm"></i></span><input class="fi" type="text" id="siswaSearch" placeholder="Cari nama atau email siswa..." data-i18n-placeholder="guru.dynamic.search_students" oninput="filterTable('siswaSearch','siswaTable')"></div>
              <table class="gtable" id="siswaTable">
                <thead>
                  <tr>
                    <th data-i18n="guru.dynamic.name">Nama</th>
                    <th data-i18n="guru.dynamic.email">Email</th>
                    <th data-i18n="guru.dynamic.status">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($siswaList as $s): ?>
                    <tr>
                      <td class="td-name"><?= htmlspecialchars($s['nama']) ?></td>
                      <td class="td-email"><?= htmlspecialchars($s['email']) ?></td>
                      <td><span class="bdg bgrn" data-i18n="guru.dynamic.active">Aktif</span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <div class="filter-empty" id="siswaTableEmpty" data-i18n="guru.dynamic.no_match">Tidak ada siswa yang cocok.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- LAPORAN MASUK -->
        <div class="dp" id="dp-laporan-masuk">
          <div class="dtb dtb-sep">
            <div>
              <h1 class="panel-title" data-i18n="guru.laporan_masuk.title">Laporan Masuk 📮</h1>
              <p class="panel-sub" data-i18n="guru.laporan_masuk.subtitle">Kelola laporan yang dikirim oleh siswa.</p>
            </div>
          </div>
          <div id="laporan-masuk" class="dc dcpanel">
            <div class="dch">
              <h3 data-i18n="guru.dynamic.incoming_reports">Laporan Masuk</h3>
              <?php if (!empty($reports)): ?>
                <span class="chart-live">Total: <?= count($reports) ?></span>
              <?php endif; ?>
            </div>
            
            <?php if (empty($reports)): ?>
              <div class="report-empty">
                <div class="report-empty-icon">📭</div>
                <h3 data-i18n="guru.dynamic.no_reports">Belum ada laporan masuk</h3>
                <p>Laporan dari siswa akan muncul di sini.</p>
              </div>
            <?php else: ?>
              <div class="report-container">
                <?php foreach ($reports as $item):
                  $statusClass = $item['status'] === 'Selesai' ? 'done' : ($item['status'] === 'Diproses' ? 'process' : 'pending');
                ?>
                  <div class="report-card">
                    <div class="report-header">
                      <div class="report-title"><?= htmlspecialchars($item['title']) ?></div>
                      <span class="report-status <?= $statusClass ?>"><?= htmlspecialchars($item['status']) ?></span>
                    </div>
                    
                    <div class="report-desc">
                      <?= htmlspecialchars($item['desc'] ?? 'Tidak ada deskripsi') ?>
                    </div>
                    
                    <div class="report-meta">
                      <span class="meta-item">
                        <span class="meta-label"><i data-lucide="user" class="icon-sm"></i> Pelapor:</span>
                        <span class="meta-value"><?= htmlspecialchars($item['by_name'] ?? 'Unknown') ?></span>
                      </span>
                      <span class="meta-item">
                        <span class="meta-label"><i data-lucide="folder" class="icon-sm"></i> Kategori:</span>
                        <span class="meta-value"><?= htmlspecialchars($item['category'] ?? 'Umum') ?></span>
                      </span>
                      <span class="meta-item">
                        <span class="meta-label"><i data-lucide="calendar" class="icon-sm"></i> Tanggal:</span>
                        <span class="meta-value"><?= htmlspecialchars($item['date'] ?? '-') ?></span>
                      </span>
                    </div>
                    
                    <form action="" method="POST" class="report-actions">
                      <?= csrfField() ?>
                      <input type="hidden" name="update_report_status" value="1">
                      <input type="hidden" name="report_id" value="<?= (int)$item['id'] ?>">
                      <select name="status">
                        <option value="Menunggu" <?= $item['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="Diproses" <?= $item['status'] === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="Selesai" <?= $item['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                      </select>
                      <button type="submit" class="btn-update" data-i18n="guru.dynamic.update">Update</button>
                    </form>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- KELOLA QUIZ -->
        <div class="dp" id="dp-quiz">
          <div class="dtb dtb-sep">
            <div>
              <h1 class="panel-title" data-i18n="guru.quiz.title">Kelola Quiz ❓</h1>
              <p class="panel-sub" data-i18n="guru.quiz.subtitle">Buat dan kelola quiz untuk siswa.</p>
            </div>
          </div>
          <div id="quiz" class="dc dcpanel">
            <div class="dch">
              <h3 data-i18n="guru.quiz.title">Kelola Quiz</h3>
            </div>

            <form action="" method="POST" id="quizForm" onsubmit="return prepareQuizSubmit()">
              <?= csrfField() ?>
              <input type="hidden" name="add_quiz" value="1">
              <input type="hidden" name="items_json" id="quizItemsJson" value="[]">

              <div class="fg">
                <label class="fl" data-i18n="guru.dynamic.quiz_name">Nama Quiz</label>
                <input class="fi" type="text" name="name" required placeholder="Contoh: Quiz HTML Dasar" data-i18n-placeholder="guru.dynamic.quiz_name_placeholder" />
              </div>
              <div class="fg">
                <label class="fl" data-i18n="guru.dynamic.status">Status</label>
                <select class="fi" name="status">
                  <option value="Aktif" data-i18n="guru.dynamic.active_visible">Aktif (tampil ke siswa)</option>
                  <option value="Draft" data-i18n="guru.dynamic.draft_hidden">Draft (belum tampil)</option>
                </select>
              </div>

              <div class="fg">
                <label class="fl" data-i18n="guru.dynamic.link_materi">Tautkan ke Materi/Kursus (opsional)</label>
                <select class="fi" name="materi_id">
                  <option value="0">— Tidak ditautkan (quiz berdiri sendiri) —</option>
                  <?php foreach ($materi as $m): ?>
                    <option value="<?= (int)($m['id'] ?? 0) ?>"><?= htmlspecialchars($m['title'] ?? 'Materi') ?></option>
                  <?php endforeach; ?>
                </select>
                <small style="display:block;margin-top:6px;font-size:.72rem;color:var(--text3)" data-i18n="guru.dynamic.link_materi_desc">Jika dipilih, quiz ini akan otomatis muncul di detail materi tersebut DAN di daftar Quiz &amp; Latihan siswa dengan judul yang sama.</small>
              </div>

              <label class="fl fsep" data-i18n="guru.dynamic.questions_label">Soal</label>
              <div id="quizQuestionsList"></div>
              
              <button type="button" onclick="addQuizQuestion()" class="dashed-add" data-i18n="guru.dynamic.add_question">+ Tambah Soal</button>
              <button type="submit" class="btn bcyan bfull" data-i18n="guru.dynamic.save_quiz">Simpan Quiz</button>
            </form>

            <?php if (empty($quiz)): ?>
              <div class="empty-state">
                <div class="ic">❓</div>
                <h3 data-i18n="guru.dynamic.no_quizzes">Belum ada quiz</h3>
              </div>
            <?php else: ?>
              <?php
                // Petakan materi_id -> judul materi agar bisa ditampilkan sebagai
                // badge "tertaut" pada setiap quiz di daftar.
                $materiById = [];
                foreach ($materi as $m) {
                    $materiById[(int)($m['id'] ?? 0)] = $m['title'] ?? 'Materi';
                }
              ?>
              <?php foreach (array_reverse($quiz) as $q): ?>
                <div class="itemrow">
<div class="itemicon itemicon-quiz">❓</div>
                  <div class="itemmain">
                    <div class="itemtitle">
                      <?= htmlspecialchars($q['name']) ?>
                      <?php if (isset($q['materi_id']) && (int)$q['materi_id'] > 0 && isset($materiById[(int)$q['materi_id']])): ?>
                        <span class="bdg" style="font-size:.55rem;margin-left:6px;background:rgba(61,123,255,.15);color:var(--cyan)" data-i18n="guru.dynamic.linked_to">tertaut ke "<?= htmlspecialchars($materiById[(int)$q['materi_id']]) ?>"</span>
                      <?php else: ?>
                        <span class="bdg" style="font-size:.55rem;margin-left:6px;background:rgba(148,163,184,.15);color:var(--text3)" data-i18n="guru.dynamic.not_linked">belum tertaut</span>
                      <?php endif; ?>
                    </div>
                    <div class="itemmeta"><?= (int)($q['questions'] ?? count($q['questions_list'] ?? [])) ?> <span data-i18n="guru.dynamic.questions_word">soal</span></div>
                  </div>
                  <span class="bdg <?= ($q['status'] ?? '') === 'Aktif' ? 'bgrn' : 'bamb' ?>"><?php if (($q['status'] ?? '') === 'Aktif'): ?><span data-i18n="guru.dynamic.active">Aktif</span><?php else: ?><span data-i18n="guru.dynamic.draft">Draft</span><?php endif; ?></span>
                  <button type="button" class="btn bghost bsm" onclick="openEditQuiz(<?= (int)$q['id'] ?>, <?= htmlspecialchars(json_encode($q['name']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($q['status'] ?? 'Draft'), ENT_QUOTES) ?>, <?= (int)($q['materi_id'] ?? 0) ?>)" data-i18n="guru.dynamic.edit">Edit</button>
                  <form action="" method="POST" onsubmit="return confirm('Hapus quiz?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="delete_quiz" value="1">
                    <input type="hidden" name="quiz_id" value="<?= (int)$q['id'] ?>">
                    <button type="submit" class="btn bghost bsm bdel" data-i18n-title="guru.dynamic.delete_quiz_confirm" title="Hapus quiz">✕</button>
                  </form>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- NILAI SISWA -->
        <div class="dp" id="dp-nilai">
          <div class="dtb dtb-sep">
            <div>
              <h1 class="panel-title" data-i18n="guru.nilai.title">Nilai Siswa 🏅</h1>
              <p class="panel-sub" data-i18n="guru.nilai.subtitle">Rekap nilai dan progress belajar siswa.</p>
            </div>
          </div>
          <div id="nilai" class="dc dcpanel">
            <div class="dch">
              <h3 data-i18n="guru.dynamic.student_grades">Nilai Siswa</h3>
            </div>
            <?php if (empty($progressList)): ?>
              <div class="nilai-empty">
                <div class="nilai-empty-icon">🏅</div>
                <h3 data-i18n="guru.dynamic.no_grades">Belum ada rekap nilai</h3>
              </div>
            <?php else: ?>
              <div class="nilai-search">
                <span><i data-lucide="search" class="icon-sm"></i></span>
                <input class="fi" type="text" id="nilaiSearch" placeholder="Cari nama siswa atau kelas..." data-i18n-placeholder="guru.dynamic.search_grades" oninput="filterCards('nilaiSearch','nilaiList','.nilaicard')">
              </div>
              <div class="nilai-container" id="nilaiList">
                <?php foreach ($progressList as $p):
                  $score = (float)($p['score'] ?? 0);
                  $scoreClass = $score >= 80 ? 'high' : ($score >= 60 ? 'medium' : 'low');
                  $initial = strtoupper(substr($p['name'] ?? 'S', 0, 1));
                ?>
                  <div class="nilaicard" data-search="<?= htmlspecialchars(strtolower($p['name'] . ' ' . ($p['class'] ?? ''))) ?>">
                    <div class="nilai-header">
                      <div>
                        <div class="nilai-name">
                          <span class="nilai-avatar"><?= htmlspecialchars($initial) ?></span>
                          <?= htmlspecialchars($p['name'] ?? 'Siswa') ?>
                        </div>
                        <div class="nilai-class">
                          <span data-i18n="guru.dynamic.class_word">Kelas</span> <?= htmlspecialchars($p['class'] ?? '-') ?>
                        </div>
                      </div>
                      <div class="nilai-score <?= $scoreClass ?>">
                        <?= (int)$score ?>
                      </div>
                    </div>
                    <?php if (!empty($p['progress']) && is_array($p['progress'])): ?>
                      <div class="nilai-progress">
                        <?php foreach ($p['progress'] as $subj => $val):
                          $val = (int)$val;
                          $barClass = $val >= 80 ? 'green' : ($val >= 60 ? 'blue' : ($val >= 40 ? 'orange' : 'red'));
                        ?>
                          <div class="cpi">
                            <div class="cph">
                              <span class="cpn"><?= htmlspecialchars($subj) ?></span>
                              <span class="cpp"><?= $val ?>%</span>
                            </div>
                            <div class="cpb">
                              <i class="<?= $barClass ?>" style="width:<?= max(0, min(100, $val)) ?>%"></i>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php else: ?>
                      <div class="nilai-note" data-i18n="guru.dynamic.no_progress">Belum ada progress belajar tercatat.</div>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="filter-empty" id="nilaiListEmpty" data-i18n="guru.dynamic.no_match">Tidak ada siswa yang cocok.</div>
            <?php endif; ?>
          </div>

          <!-- LEADERBOARD -->
          <?php if (!empty($leaderboardTop)): ?>
            <div class="leaderboard-card lbpanel">
              <div class="lb-header">
                <h3 data-i18n="guru.overview.leaderboard_title">🏆 Leaderboard</h3>
                <span class="chart-live">Top 5</span>
              </div>
              <?php foreach ($leaderboardTop as $i => $l):
                $rankClass = $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : ''));
              ?>
                <div class="lb-item">
                  <span class="lb-rank <?= $rankClass ?>">#<?= $i + 1 ?></span>
                  <span class="lb-name"><?= htmlspecialchars($l['name'] ?? 'Siswa') ?></span>
                  <span class="lb-level"><span data-i18n="guru.dynamic.level">Level</span> <?= (int)($l['level'] ?? 1) ?></span>
                  <span class="lb-xp"><?= (int)($l['xp'] ?? 0) ?> XP</span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- AKTIVITAS SISWA -->
        <div class="dp" id="dp-aktivitas">
          <div class="dtb dtb-sep">
            <div>
              <h1 class="panel-title" data-i18n="guru.aktivitas.title">Aktivitas Siswa 📝</h1>
              <p class="panel-sub" data-i18n="guru.aktivitas.subtitle">Riwayat aktivitas belajar siswa terbaru.</p>
            </div>
          </div>
          <div id="aktivitas" class="dc dcpanel">
            <div class="dch">
              <h3 data-i18n="guru.dynamic.recent_activity">Aktivitas Siswa Terbaru</h3>
            </div>
            <?php if (empty($studentActivities)): ?>
              <div class="empty-state">
                <div class="ic">📝</div>
                <h3 data-i18n="guru.dynamic.no_activity">Belum ada aktivitas siswa</h3>
              </div>
            <?php else: ?>
              <div class="timeline">
                <?php $actCount = 0; ?>
                <?php foreach ($guruActivityGroups as $gday => $gitems):
                  $glabel = $gday === $todayKey
                    ? 'Hari Ini'
                    : ($gday === $yesterdayKey
                      ? 'Kemarin'
                      : (int)substr($gday, 8, 2) . ' ' . $MONTHS_SHORT[(int)substr($gday, 5, 2)] . (substr($gday, 0, 4) !== date('Y', $nowTs) ? ' ' . substr($gday, 0, 4) : ''));
                ?>
                  <div class="t-day">
                    <span class="t-day-label"><?= $glabel ?></span>
                    <?php foreach ($gitems as $ga): $actCount++;
                      $gt = strtotime($ga['created_at'] ?? '');
                      if (!$gt) $gt = $nowTs;
                      $gac = $ga['text'] ?? '';
                      $gacLower = mb_strtolower($gac);
                      $chip = 'Aktivitas';
                      if (str_contains($gacLower, 'topik forum')) $chip = 'Forum';
                      elseif (str_contains($gacLower, 'membuka materi') || str_contains($gacLower, 'menyelesaikan pelajaran')) $chip = 'Materi';
                      elseif (str_contains($gacLower, 'menyelesaikan') || str_contains($gacLower, 'mengerjakan')) $chip = 'Quiz';
                      elseif (str_contains($gacLower, 'mendaftar')) $chip = 'Kursus';
                      $diff = $nowTs - $gt;
                      if ($diff < 60) $rel = 'Baru saja';
                      elseif ($diff < 3600) $rel = max(1, (int)($diff / 60)) . ' mnt lalu';
                      elseif ($diff < 86400) $rel = (int)($diff / 3600) . ' jam lalu';
                      elseif ($diff < 2592000) $rel = (int)($diff / 86400) . ' hari lalu';
                      else $rel = (int)substr($gday, 8, 2) . ' ' . $MONTHS_SHORT[(int)substr($gday, 5, 2)];
                    ?>
                      <div class="t-item <?= $actCount === 1 ? 'latest ' : '' ?>tone-<?= ($actCount - 1) % 6 ?>">
                        <span class="t-line"></span>
                        <span class="t-bubble"><?= htmlspecialchars($ga['icon'] ?? '⚡') ?></span>
                        <div class="t-main">
                          <div class="t-head">
                            <span class="t-name"><?= htmlspecialchars($ga['name'] ?? 'Siswa') ?></span>
                            <span class="t-chip"><?= $chip ?></span>
                            <span class="t-time"><?= $rel ?></span>
                          </div>
                          <p class="t-body"><?= htmlspecialchars($ga['text'] ?? '') ?></p>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <footer class="dmain-footer">© <?= date('Y') ?> <span data-i18n="guru.dynamic.footer_rights">EduCare. All rights reserved.</span></footer>
      </main>
    </div>
  </div>

  <!-- ============ MODAL EDIT QUIZ ============ -->
  <div id="editQuizModal" style="display:none;position:fixed;inset:0;z-index:10000;align-items:center;justify-content:center;background:rgba(10,15,30,.55);backdrop-filter:blur(2px)">

    <div style="background:var(--card,#fff);width:min(480px,92vw);border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);border:1px solid var(--border2,#e8ecf3);overflow:hidden">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--border2,#e8ecf3)">
        <h3 style="margin:0;font-size:1rem;font-weight:700"><span data-i18n="guru.dynamic.edit_quiz_title">Edit Quiz</span></h3>
        <button type="button" onclick="closeEditQuiz()" style="all:unset;cursor:pointer;font-size:1.4rem;line-height:1;color:var(--text3,#8896ab)">&times;</button>
      </div>

      <form method="POST" action="" style="padding:22px">
        <?= csrfField() ?>
        <input type="hidden" name="edit_quiz" value="1">
        <input type="hidden" name="quiz_id" id="eqQuizId" value="0">

        <div style="margin-bottom:16px">
          <label style="display:block;font-size:.78rem;font-weight:600;margin-bottom:6px;color:var(--text2,#52627a)">Nama Quiz</label>
          <input class="fi" type="text" name="name" id="eqName" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
          <div>
            <label style="display:block;font-size:.78rem;font-weight:600;margin-bottom:6px;color:var(--text2,#52627a)" data-i18n="guru.dynamic.status">Status</label>
            <select class="fi" name="status" id="eqStatus">
              <option value="Aktif">Aktif (tampil ke siswa)</option>
              <option value="Draft">Draft (belum tampil)</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom:20px">
          <label style="display:block;font-size:.78rem;font-weight:600;margin-bottom:6px;color:var(--text2,#52627a)" data-i18n="guru.dynamic.link_materi">Tautkan ke Materi/Kursus (opsional)</label>
          <select class="fi" name="materi_id" id="eqMateri">
            <option value="0">— Tidak ditautkan (quiz berdiri sendiri) —</option>
            <?php foreach ($materi as $m): ?>
              <option value="<?= (int)($m['id'] ?? 0) ?>"><?= htmlspecialchars($m['title'] ?? 'Materi') ?></option>
            <?php endforeach; ?>
          </select>
          <small style="display:block;margin-top:6px;font-size:.72rem;color:var(--text3,#8896ab)" data-i18n="guru.dynamic.link_materi_desc">Jika dipilih, quiz ini akan otomatis muncul di detail materi tersebut DAN di daftar Quiz &amp; Latihan siswa dengan judul yang sama.</small>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px">
          <button type="button" class="btn bghost bsm" onclick="closeEditQuiz()" data-i18n="guru.dynamic.cancel">Batal</button>
          <button type="submit" class="btn bcyan bsm" data-i18n="guru.dynamic.save_quiz">Simpan Quiz</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const analyticsData = <?= json_encode($analyticsData, JSON_UNESCAPED_UNICODE) ?>;
    const guruText = (key, fallback) => window.EduCareI18n?.t(key) || fallback;

    const GURU_BREADCRUMB_LABELS = {
      dashboard: 'guru.header.breadcrumb_overview',
      analitik: 'guru.analitik.title',
      materi: 'guru.materi.title',
      quiz: 'guru.quiz.title',
      nilai: 'guru.nilai.title',
      'data-siswa': 'guru.data_siswa.title',
      aktivitas: 'guru.aktivitas.title',
      'laporan-masuk': 'guru.laporan_masuk.title'
    };

    function updateGuruBreadcrumb(name) {
      const el = document.getElementById('pageBreadcrumb');
      if (!el) return;
      const translate = window.EduCareI18n?.t;
      const label = translate ?
        translate(GURU_BREADCRUMB_LABELS[name] || GURU_BREADCRUMB_LABELS.dashboard) :
        (GURU_BREADCRUMB_LABELS[name] || 'Overview');
      const dashboardLabel = translate ? translate('guru.header.breadcrumb_dashboard') : 'Dashboard Guru';
      el.innerHTML = `${dashboardLabel} / <span class="cur">${label}</span>`;
    }
    window.updateGuruBreadcrumb = updateGuruBreadcrumb;

    function goSection(name) {
      document.querySelectorAll('.dp').forEach(p => p.classList.remove('act'));
      const target = document.getElementById('dp-' + name);
      if (target) target.classList.add('act');

      document.querySelectorAll('.sbnav li a').forEach(a => {
        const onclick = a.getAttribute('onclick') || '';
        a.classList.toggle('act', onclick.includes(`'${name}'`));
      });

      updateGuruBreadcrumb(name);

      if (name === 'analitik' && typeof window._buildGuruCharts === 'function') {
        setTimeout(window._buildGuruCharts, 100);
      }

      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function filterTable(inputId, tableId) {
      const q = document.getElementById(inputId).value.trim().toLowerCase();
      const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
      let visible = 0;
      rows.forEach(r => {
        const match = r.textContent.toLowerCase().includes(q);
        r.style.display = match ? '' : 'none';
        if (match) visible++;
      });
      const empty = document.getElementById(tableId + 'Empty');
      if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
    }

    function filterCards(inputId, containerId, cardSelector) {
      const q = document.getElementById(inputId).value.trim().toLowerCase();
      const cards = document.querySelectorAll('#' + containerId + ' ' + cardSelector);
      let visible = 0;
      cards.forEach(c => {
        const hay = c.getAttribute('data-search') || c.textContent.toLowerCase();
        const match = hay.toLowerCase().includes(q);
        c.style.display = match ? '' : 'none';
        if (match) visible++;
      });
      const empty = document.getElementById(containerId + 'Empty');
      if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
    }

    /* ============ CHART.JS ============ */
    (function initCharts() {
      if (typeof Chart === 'undefined') return;

      const cssVar = (name) => getComputedStyle(document.body).getPropertyValue(name).trim();
      const gridColor = () => document.body.classList.contains('light-mode') ? 'rgba(15,23,42,.07)' : 'rgba(255,255,255,.08)';
      const textColor = () => cssVar('--text2') || '#9aa3b8';

      Chart.defaults.font.family = "'Inter', sans-serif";
      Chart.defaults.color = textColor();

      const palette = ['#4f7cff', '#34d399', '#f5a623', '#8b7bff', '#fb7185', '#4f7cff'];

      let chartKelas, chartLaporan, chartKategori, chartMapel;

      function buildCharts() {
        [chartKelas, chartLaporan, chartKategori, chartMapel].forEach(c => c && c.destroy());

        chartKelas = new Chart(document.getElementById('chartKelas'), {
          type: 'bar',
          data: {
            labels: analyticsData.classLabels.length ? analyticsData.classLabels : ['Belum ada data'],
            datasets: [{
              label: 'Rata-rata Nilai',
              data: analyticsData.classAvg.length ? analyticsData.classAvg : [0],
              backgroundColor: '#4f7cff',
              borderRadius: 6,
              maxBarThickness: 40
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: {
                beginAtZero: true,
                max: 100,
                grid: { color: gridColor() },
                ticks: { color: textColor(), stepSize: 20 }
              },
              x: {
                grid: { display: false },
                ticks: { color: textColor() }
              }
            }
          }
        });

        chartLaporan = new Chart(document.getElementById('chartLaporan'), {
          type: 'doughnut',
          data: {
            labels: analyticsData.statusLabels.length ? analyticsData.statusLabels : ['Menunggu', 'Diproses', 'Selesai'],
            datasets: [{
              data: analyticsData.statusValues.length ? analyticsData.statusValues : [0, 0, 0],
              backgroundColor: ['#fb7185', '#f5a623', '#34d399'],
              borderWidth: 2,
              borderColor: document.body.classList.contains('light-mode') ? '#ffffff' : '#1E293B'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: textColor(),
                  boxWidth: 10,
                  padding: 12,
                  font: { size: 11 }
                }
              }
            }
          }
        });

        chartKategori = new Chart(document.getElementById('chartKategori'), {
          type: 'pie',
          data: {
            labels: analyticsData.catLabels.length ? analyticsData.catLabels : ['Belum ada kursus'],
            datasets: [{
              data: analyticsData.catValues.length ? analyticsData.catValues : [1],
              backgroundColor: palette,
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: textColor(),
                  boxWidth: 10,
                  padding: 12,
                  font: { size: 11 }
                }
              }
            }
          }
        });

        chartMapel = new Chart(document.getElementById('chartMapel'), {
          type: 'radar',
          data: {
            labels: analyticsData.subjectLabels.length ? analyticsData.subjectLabels : ['Belum ada data'],
            datasets: [{
              label: 'Rata-rata Pemahaman',
              data: analyticsData.subjectAvg.length ? analyticsData.subjectAvg : [0],
              backgroundColor: 'rgba(79,124,255,.18)',
              borderColor: '#4f7cff',
              pointBackgroundColor: '#4f7cff',
              pointBorderColor: '#4f7cff',
              pointRadius: 4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false }
            },
            scales: {
              r: {
                beginAtZero: true,
                max: 100,
                grid: { color: gridColor() },
                angleLines: { color: gridColor() },
                pointLabels: {
                  color: textColor(),
                  font: { size: 10 }
                },
                ticks: {
                  display: false,
                  backdropColor: 'transparent',
                  stepSize: 20
                }
              }
            }
          }
        });
      }

      window._buildGuruCharts = function() { buildCharts(); };
      window._rebuildCharts = function() {
        if (chartKelas || chartLaporan || chartKategori || chartMapel) buildCharts();
      };
    })();

    /* ============ CHAPTER / LESSON BUILDER ============ */
    let chCount = 0;

    function addChapter() {
      const idx = chCount++;
      const wrap = document.createElement('div');
      wrap.className = 'chbuilder';
      wrap.id = 'ch-' + idx;
      wrap.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;gap:8px">
          <input type="text" class="fi ch-title" style="flex:1" placeholder="Judul Chapter, contoh: Chapter 1: HTML Dasar">
          <button type="button" style="all:unset;font-size:.7rem;color:var(--rose);font-weight:600;cursor:pointer;flex-shrink:0" onclick="document.getElementById('ch-${idx}').remove()">Hapus</button>
        </div>
        <div class="ch-lessons" id="ch-lessons-${idx}"></div>
        <button type="button" onclick="addLesson(${idx})" class="dashed-add" style="margin-top:8px">+ Tambah Pelajaran</button>
      `;
      document.getElementById('chaptersList').appendChild(wrap);
      addLesson(idx);
    }

    function addLesson(chIdx) {
      const row = document.createElement('div');
      row.className = 'lessrow';
      row.innerHTML = `
        <div style="display:flex;align-items:center;gap:8px">
          <input type="text" class="fi less-title" style="flex:2" placeholder="Judul pelajaran">
          <button type="button" style="all:unset;font-size:.7rem;color:var(--rose);font-weight:600;cursor:pointer;flex-shrink:0" onclick="this.parentElement.remove()">✕</button>
        </div>
        <textarea class="fi less-content" rows="3" placeholder="Isi materi pelajaran (opsional). Mendukung teks biasa, list, dan blok kode fenced \`\`\`...\`\`\`"></textarea>
        <input type="text" class="fi less-video" placeholder="Link video YouTube pelajaran ini (opsional)">
      `;
      document.getElementById('ch-lessons-' + chIdx).appendChild(row);
    }

    function prepareMateriSubmit() {
      const chapterBlocks = document.querySelectorAll('#chaptersList > div');
      const chapters = [];
      chapterBlocks.forEach(cb => {
        const chTitle = cb.querySelector('.ch-title').value.trim();
        const lessonRows = cb.querySelectorAll('.lessrow');
        const items = [];
        lessonRows.forEach(lr => {
          const t = lr.querySelector('.less-title').value.trim();
          const content = lr.querySelector('.less-content').value.trim() || '';
          const video_url = lr.querySelector('.less-video').value.trim() || '';
          if (t) items.push({ t, content, video_url });
        });
        if (chTitle && items.length) chapters.push({ ch: chTitle, items });
      });
      if (chapters.length === 0) {
        showToast('Tambahkan minimal 1 chapter dengan 1 pelajaran.', 'err');
        return false;
      }
      document.getElementById('chaptersJson').value = JSON.stringify(chapters);
      return true;
    }

    addChapter();

    /* ============ QUIZ BUILDER ============ */
    let qbCount = 0;

    function addQuizQuestion() {
      const idx = qbCount++;
      const wrap = document.createElement('div');
      wrap.className = 'qbuilder';
      wrap.id = 'qb-' + idx;
      wrap.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <span class="qb-number">SOAL #${idx + 1}</span>
          <button type="button" class="qb-delete" onclick="document.getElementById('qb-${idx}').remove()">Hapus</button>
        </div>
        <input type="text" class="fi qb-text" placeholder="Tulis pertanyaan...">
        ${[0,1,2,3].map(i => `
          <div class="qb-option">
            <input type="radio" name="qb-correct-${idx}" class="qb-correct" value="${i}" ${i===0?'checked':''}>
            <input type="text" class="fi qb-opt" placeholder="Pilihan ${String.fromCharCode(65+i)}">
          </div>
        `).join('')}
        <div class="qb-hint">Pilih radio di samping jawaban yang benar</div>
      `;
      document.getElementById('quizQuestionsList').appendChild(wrap);
    }

    function prepareQuizSubmit() {
      const blocks = document.querySelectorAll('#quizQuestionsList > div');
      const items = [];
      blocks.forEach(b => {
        const q = b.querySelector('.qb-text').value.trim();
        const opts = Array.from(b.querySelectorAll('.qb-opt')).map(i => i.value.trim());
        const correctRadio = b.querySelector('.qb-correct:checked');
        const ans = correctRadio ? parseInt(correctRadio.value) : 0;
        if (q && opts.every(o => o)) items.push({ q, opts, ans });
      });
      if (items.length === 0) {
        showToast('Tambahkan minimal 1 soal lengkap dengan 4 pilihan.', 'err');
        return false;
      }
      document.getElementById('quizItemsJson').value = JSON.stringify(items);
      return true;
    }
    addQuizQuestion();

    /* ============ EDIT QUIZ MODAL ============ */
    function openEditQuiz(id, name, status, materiId) {
      document.getElementById('eqQuizId').value = id;
      document.getElementById('eqName').value = name || '';
      document.getElementById('eqStatus').value = status || 'Draft';
      const gt = window.EduCareI18n?.t || (() => '');
      const sel = document.getElementById('eqMateri');
      let found = false;
      for (const opt of sel.options) {
        if (String(opt.value) === String(materiId || 0)) {
          sel.value = opt.value;
          found = true;
          break;
        }
      }
      if (!found) sel.value = '0';
      const modal = document.getElementById('editQuizModal');
      if (modal) {
        modal.style.display = 'flex';
        if (window.lucide) lucide.createIcons();
      }
    }
    function closeEditQuiz() {
      const modal = document.getElementById('editQuizModal');
      if (modal) modal.style.display = 'none';
    }
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeEditQuiz();
    });

    /* ============ TOAST ============ */
    function showToast(msg, type = 'ok') {
      let el = document.getElementById('toast');
      if (!el) {
        el = document.createElement('div');
        el.id = 'toast';
        el.style.cssText = 'position:fixed;bottom:24px;right:24px;border-radius:12px;padding:14px 20px;font-size:.875rem;z-index:99999;max-width:340px;transform:translateY(80px);opacity:0;transition:all .3s';
        document.body.appendChild(el);
      }
      const ico = type === 'ok' ? '✅' : type === 'err' ? '❌' : 'ℹ️';
      el.innerHTML = ico + ' ' + msg;
      el.style.transform = 'translateY(0)';
      el.style.opacity = '1';
      clearTimeout(window._t);
      window._t = setTimeout(() => {
        el.style.transform = 'translateY(80px)';
        el.style.opacity = '0';
      }, 3500);
    }

    /* ============ SIDEBAR ============ */
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
      if (window.innerWidth > 768) closeSidebar();
    });

    function toggleSidebarCollapse() {
      const sb = document.getElementById('sidebar');
      if (!sb) return;
      const collapsed = sb.classList.toggle('collapsed');
      localStorage.setItem('guru_sidebarCollapsed', JSON.stringify(collapsed));
      
      // TAMBAHAN: Update icon panah (> / <)
      const btn = document.getElementById('sbCollapseBtn');
      if (btn) {
        btn.innerHTML = `<i data-lucide="${collapsed ? 'chevron-right' : 'chevron-left'}"></i>`;
        if (window.lucide) lucide.createIcons();
      }
    }
    
    // DIUBAH: Selalu buka sidebar (expand) saat halaman dimuat, agar tidak tersangkut collapsed
    (function loadSidebarCollapse() {
      try {
        const collapsed = JSON.parse(localStorage.getItem('guru_sidebarCollapsed')) === true;
        // Hapus paksa collapsed jika tersimpan, agar kembali normal
        if (collapsed) {
          localStorage.removeItem('guru_sidebarCollapsed'); // Reset penyimpanan
        }
        // Sidebar akan selalu terbuka, dan tombol akan menampilkan panah kiri (<)
        const btn = document.getElementById('sbCollapseBtn');
        if (btn) {
          btn.innerHTML = `<i data-lucide="chevron-left"></i>`;
          if (window.lucide) lucide.createIcons();
        }
      } catch (e) {}
    })();

    /* ============ THEME TOGGLE (disinkronkan dgn landing page) ============ */
    // Sumber kebenaran tema global adalah key 'educare-theme' (dipakai bareng
    // landing page & dashboard siswa). Nilai: 'dark' | 'light'.
    // Terang (light)  -> body diberi class 'light-mode'
    // Gelap  (dark)   -> body TANPA 'light-mode' (default)
    function getSavedTheme() {
      var saved = localStorage.getItem('educare-theme');
      if (saved === 'dark' || saved === 'light') return saved;
      // Fallback: pengaturan lama dashboard-guru
      var legacy = localStorage.getItem('theme');
      if (legacy === 'dark' || legacy === 'light') return legacy;
      // Fallback ke preferensi sistem
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) return 'dark';
      return 'light';
    }
    function applyThemeMode(mode) {
      var isLight = mode !== 'dark';
      document.body.classList.toggle('light-mode', isLight);
      var btn = document.getElementById('themeToggle');
      if (btn) {
        btn.innerHTML = `<i data-lucide="${isLight ? 'sun' : 'moon'}"></i>`;
        if (window.lucide) lucide.createIcons();
      }
      if (typeof Chart !== 'undefined' && window._rebuildCharts) {
        Chart.defaults.color = getComputedStyle(document.body).getPropertyValue('--text2').trim();
        window._rebuildCharts();
      }
    }
    function toggleTheme() {
      var current = getSavedTheme();
      var next = current === 'dark' ? 'light' : 'dark';
      localStorage.setItem('educare-theme', next);
      localStorage.setItem('theme', next); // juga perbarui key lama agar tak bentrok
      applyThemeMode(next);
    }
    (function loadTheme() {
      applyThemeMode(getSavedTheme());
    })();
    document.addEventListener('DOMContentLoaded', function() {
      if (window.lucide) lucide.createIcons();
    });

    /* ============ NOTIF & PROFILE DROPDOWN ============ */
    function toggleNotifDropdown() {
      const dd = document.getElementById('notifDropdown');
      const pd = document.getElementById('profileDropdown');
      
      pd?.classList.remove('show');
      
      if (dd.classList.contains('show')) {
        dd.classList.remove('show');
        return;
      }
      
      dd?.classList.add('show');
    }

    function toggleProfileDropdown() {
      const dd = document.getElementById('profileDropdown');
      const nd = document.getElementById('notifDropdown');
      
      nd?.classList.remove('show');
      
      if (dd.classList.contains('show')) {
        dd.classList.remove('show');
        return;
      }
      
      dd?.classList.add('show');
    }

    document.addEventListener('click', function(e) {
      const nd = document.getElementById('notifDropdown');
      const pd = document.getElementById('profileDropdown');
      const nb = document.getElementById('notifBellBtn');
      const pm = document.getElementById('profileMenuBtn');
      
      if (nd && nb && !nb.contains(e.target) && !nd.contains(e.target)) {
        nd.classList.remove('show');
      }
      if (pd && pm && !pm.contains(e.target) && !pd.contains(e.target)) {
        pd.classList.remove('show');
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        document.getElementById('notifDropdown')?.classList.remove('show');
        document.getElementById('profileDropdown')?.classList.remove('show');
      }
    });

    /* ============ JAM DIGITAL ============ */
    function updateClock() {
      const now = new Date();
      const t = document.getElementById('topbarClockTime');
      const d = document.getElementById('topbarClockDate');
      if (t) t.textContent = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      if (d) d.textContent = now.toLocaleDateString('id-ID', {
        weekday: 'short',
        day: 'numeric',
        month: 'short'
      });
    }
    updateClock();
    setInterval(updateClock, 1000);
  </script>
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
      const activePanel = document.querySelector('.dp.act');
      const activeName = activePanel?.id?.replace('dp-', '') || 'dashboard';
      if (typeof window.updateGuruBreadcrumb === 'function') {
        window.updateGuruBreadcrumb(activeName);
      }
      if (activeName === 'analitik' && typeof window._rebuildCharts === 'function') {
        window._rebuildCharts();
      }
    });
  </script>
</body>

</html>
