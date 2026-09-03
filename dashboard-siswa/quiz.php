<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../translation.php';

// ============================================================
// 1. AUTHENTICATION
// ============================================================
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Hanya role siswa (dan admin untuk kepentingan pengujian) yang boleh akses quiz siswa
$role = $_SESSION['user']['role'] ?? '';
if ($role !== 'siswa' && $role !== 'admin') {
    header('Location: ../dashboard-guru/index.php');
    exit;
}

$userId = $_SESSION['user']['id'] ?? null;
$name   = htmlspecialchars($_SESSION['user']['nama'] ?? 'Pengguna');

// ============================================================
// 2. AMBIL PARAMETER URL
// ============================================================
$quizId   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$materiId = isset($_GET['materi_id']) ? (int)$_GET['materi_id'] : 0;

if ($quizId === 0 && $materiId === 0) {
    die('Quiz tidak ditemukan.');
}

// Skema soal quiz berbeda-beda antar editor (questions_list / items /
// questions). Ambil field pertama yang terisi (array tidak kosong).
function pickQuizQuestions(array $q): array
{
    foreach (['questions_list', 'items', 'questions'] as $field) {
        if (!empty($q[$field]) && is_array($q[$field])) {
            return $q[$field];
        }
    }
    return [];
}

// ============================================================
// 3. LOAD DATA QUIZ (dari ../data/)
// ============================================================
$quizData = null;
$quizName = '';
$questions = [];

// --- Cari dari quiz.json jika ada quiz_id ---
if ($quizId > 0) {
    $quizFile = __DIR__ . '/../data/quiz.json';
    if (file_exists($quizFile)) {
        $allQuizzes = json_decode(file_get_contents($quizFile), true) ?: [];
        foreach ($allQuizzes as $q) {
            if ((int)$q['id'] === $quizId && ($q['status'] ?? '') === 'Aktif') {
                $quizData = $q;
                $quizName = $q['name'] ?? 'Quiz';
                $questions = pickQuizQuestions($q);
                break;
            }
        }
    }
}

// --- Cari berdasarkan materi_id di quiz.json ---
if ($quizData === null && $materiId > 0) {
    $quizFile = __DIR__ . '/../data/quiz.json';
    if (file_exists($quizFile)) {
        $allQuizzes = json_decode(file_get_contents($quizFile), true) ?: [];
        foreach ($allQuizzes as $q) {
            if ((int)($q['materi_id'] ?? 0) === $materiId && ($q['status'] ?? '') === 'Aktif') {
                $quizData = $q;
                $quizName = $q['name'] ?? 'Quiz';
                $questions = pickQuizQuestions($q);
                break;
            }
        }
    }
}

// --- Jika tidak ditemukan di quiz.json, coba dari materi.json (fallback lama) ---
if ($quizData === null && $materiId > 0) {
    $materiFile = __DIR__ . '/../data/materi.json';
    if (file_exists($materiFile)) {
        $allMateri = json_decode(file_get_contents($materiFile), true) ?: [];
        foreach ($allMateri as $m) {
            if ((int)$m['id'] === $materiId) {
                $quizName = 'Quiz: ' . ($m['title'] ?? 'Materi');
                // Coba ambil dari berbagai kemungkinan field
                $questions = $m['quiz_questions_list'] ?? $m['questions'] ?? $m['items'] ?? [];
                break;
            }
        }
    }
}

// --- Tanpa soal (atau quiz tidak ketemu): jangan buat soal dummy, arahkan
//      kembali ke dashboard dengan pesan yang jelas. ---
if ($quizData === null || empty($questions)) {
    $_SESSION['error_message'] = 'msg.quiz.no_questions';
    header('Location: ' . rtrim(BASE_URL, '/') . '/dashboard-siswa/index.php#quiz');
    exit;
}

// ============================================================
// 4. PROSES SUBMIT JAWABAN
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answers'])) {
    $userAnswers = $_POST['answers'] ?? [];
    $score = 0;
    foreach ($questions as $idx => $q) {
        // Jawaban benar ada di field 'a'
        if (isset($userAnswers[$idx]) && (int)$userAnswers[$idx] === (int)$q['a']) {
            $score++;
        }
    }
    $total = count($questions);
    $finalScore = round(($score / $total) * 100); // dalam persen

    // Redirect ke dashboard siswa (index.php di folder yang sama)
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Menyimpan hasil...</title>
        <meta charset="UTF-8">
    </head>
    <body>
        <form id="redirectForm" method="POST" action="./index.php">
            <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
            <input type="hidden" name="materi_id" value="<?= $materiId ?>">
            <input type="hidden" name="quiz_name" value="<?= htmlspecialchars($quizName) ?>">
            <input type="hidden" name="score" value="<?= $finalScore ?>">
            <input type="hidden" name="submit_quiz" value="1">
        </form>
        <script>
            document.getElementById('redirectForm').submit();
        </script>
    </body>
    </html>
    <?php
    exit;
}

// ============================================================
// 5. TAMPILAN QUIZ
// ============================================================
$quizKey = $quizId > 0 ? 'quiz.' . $quizId : 'materi.' . $materiId;
$quizNameDisplay = t_dynamic($quizName, $quizKey . '.name');
$qi = 0;
foreach ($questions as &$qItem) {
    $qItem['q'] = t_dynamic($qItem['q'] ?? '', $quizKey . '.q.' . $qi . '.text');
    $oi = 0;
    foreach ($qItem['o'] as &$opt) {
        $opt = t_dynamic($opt ?? '', $quizKey . '.q.' . $qi . '.o.' . $oi);
        $oi++;
    }
    unset($opt);
    $qi++;
}
unset($qItem);

$totalQuestions = count($questions);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quizNameDisplay) ?> • EduCare</title>
    <style>
        /* ===== CSS dasar yang rapi ===== */
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #0b1120;
            color: #e2e8f0;
            padding: 20px;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quiz-container {
            max-width: 800px;
            width: 100%;
            background: #1e293b;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.5);
            border: 1px solid #334155;
        }
        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #334155;
            padding-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .quiz-title {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #facc15, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .quiz-timer {
            font-size: 1.2rem;
            color: #facc15;
            font-weight: 600;
        }
        .question-block {
            margin: 25px 0;
        }
        .question-text {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #2d3b4f;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid transparent;
        }
        .option:hover {
            background: #3b4a62;
            border-color: #4b5a72;
        }
        .option input[type="radio"] {
            accent-color: #3b82f6;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .option span {
            font-size: 1rem;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-size: 0.95rem;
        }
        .btn-primary {
            background: #3b82f6;
            color: #fff;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-secondary {
            background: #475569;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #334155;
        }
        .btn-success {
            background: #22c55e;
            color: #fff;
            font-size: 1.1rem;
            padding: 12px 40px;
        }
        .btn-success:hover {
            background: #16a34a;
        }
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #334155;
            border-radius: 4px;
            margin: 20px 0;
        }
        .progress-fill {
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .question-counter {
            font-size: 0.9rem;
            color: #94a3b8;
            font-weight: 500;
        }
        .submit-area {
            text-align: center;
            margin-top: 30px;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        @media (max-width: 600px) {
            .quiz-container { padding: 20px 16px; }
            .quiz-title { font-size: 1.4rem; }
            .quiz-timer { width: 100%; text-align: center; padding: 10px 14px; }
            .question-text { font-size: 1rem; word-break: break-word; }
            .nav-buttons { flex-wrap: wrap; gap: 10px; justify-content: center; }
            .quiz-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .option { padding: 13px 13px; }
            .option span { font-size: .95rem; word-break: break-word; line-height: 1.5; }
            .option input { width: 18px; height: 18px; flex-shrink: 0; }
            .btn { width: 100%; }
            .btn-success { width: 100%; }
        }
    </style>
    <script defer src="../assets/js/i18n.js"></script>
</head>
<body>
<div class="quiz-container">
    <div class="quiz-header">
        <div class="quiz-title"><?= htmlspecialchars($quizNameDisplay) ?></div>
        <div class="quiz-timer" id="timerDisplay">⏱️ 05:00</div>
    </div>

    <div class="progress-bar">
        <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
    </div>

    <form method="POST" id="quizForm">
        <div id="questionContainer"></div>

        <div class="nav-buttons">
            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeQuestion(-1)" data-i18n="siswa.quiz.prev_question">← Sebelumnya</button>
            <span class="question-counter" id="counterLabel">1 / <?= $totalQuestions ?></span>
            <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeQuestion(1)" data-i18n="siswa.quiz.next_question">Berikutnya →</button>
        </div>

        <div class="submit-area">
            <button type="button" class="btn btn-success" onclick="confirmSubmit()" data-i18n="siswa.quiz.submit_quiz">📤 Selesai & Kirim</button>
        </div>
    </form>
</div>

<script>
    // Data soal dari PHP (field q, o, a)
    const questions = <?= json_encode($questions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
    const total = questions.length;
    let currentIndex = 0;
    let submitted = false;
    const userAnswers = {};

    // Escape HTML agar teks soal/opsi yang berisi tag seperti <h1>, <p>, <br>
    // tampil sebagai teks biasa, bukan dianggap tag HTML (mencegah opsi kosong).
    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    // Timer 5 menit (300 detik)
    let timeLeft = 300;
    const timerDisplay = document.getElementById('timerDisplay');

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `⏱️ ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            alert(window.EduCareI18n?.t('siswa.quiz.time_up') || 'Waktu habis! Jawaban akan dikirim otomatis.');
            doSubmit();
            return;
        }
        timeLeft--;
    }
    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    function renderQuestion(index) {
        const q = questions[index];
        const container = document.getElementById('questionContainer');
        let html = `<div class="question-block">`;
        html += `<div class="question-text">${index+1}. ${escHtml(q.q)}</div>`;
        html += `<div class="options">`;
        q.o.forEach((opt, i) => {
            const checked = (userAnswers[index] == i) ? 'checked' : '';
            html += `<label class="option">`;
            // name="q_${index}" cuma buat grouping radio per soal di layar saat ini —
            // BUKAN yang dikirim ke server. Semua jawaban disatukan lagi lewat
            // syncAnswersToForm() sebelum submit, supaya jawaban soal-soal
            // sebelumnya (yang elemennya sudah dihapus dari DOM) tidak hilang.
            html += `<input type="radio" name="q_${index}" value="${i}" ${checked} onchange="saveAnswer(${index}, ${i})">`;
            html += `<span>${escHtml(opt)}</span>`;
            html += `</label>`;
        });
        html += `</div></div>`;
        container.innerHTML = html;

        document.getElementById('counterLabel').textContent = `${index+1} / ${total}`;
        document.getElementById('progressFill').style.width = `${((index+1)/total)*100}%`;

        document.getElementById('prevBtn').style.display = (index === 0) ? 'none' : 'inline-block';
        document.getElementById('nextBtn').style.display = (index === total-1) ? 'none' : 'inline-block';
    }

    function changeQuestion(delta) {
        const newIndex = currentIndex + delta;
        if (newIndex >= 0 && newIndex < total) {
            currentIndex = newIndex;
            renderQuestion(currentIndex);
        }
    }

    function saveAnswer(index, value) {
        userAnswers[index] = value;
    }

    // Sinkronkan SEMUA jawaban tersimpan (dari objek userAnswers, bukan dari DOM)
    // menjadi hidden input, dipanggil tepat sebelum form dikirim.
    function syncAnswersToForm() {
        const form = document.getElementById('quizForm');
        form.querySelectorAll('input[data-synced-answer]').forEach(el => el.remove());

        Object.keys(userAnswers).forEach(idx => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `answers[${idx}]`;
            input.value = userAnswers[idx];
            input.setAttribute('data-synced-answer', '1');
            form.appendChild(input);
        });

        const submitFlag = document.createElement('input');
        submitFlag.type = 'hidden';
        submitFlag.name = 'submit_answers';
        submitFlag.value = '1';
        submitFlag.setAttribute('data-synced-answer', '1');
        form.appendChild(submitFlag);
    }

    function doSubmit() {
        if (submitted) return;
        submitted = true;
        syncAnswersToForm();
        document.getElementById('quizForm').submit();
    }

    function confirmSubmit() {
        if (confirm(window.EduCareI18n?.t('siswa.quiz.confirm_submit') || 'Yakin ingin mengumpulkan jawaban?')) {
            doSubmit();
        }
    }

    renderQuestion(0);
</script>
</body>
</html>