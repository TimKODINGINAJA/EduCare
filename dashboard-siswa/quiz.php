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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #0f1424;
            --bg-2: #1a1f3a;
            --card: #161c33;
            --card-border: rgba(148, 163, 184, 0.12);
            --accent-1: #6366f1;
            --accent-2: #8b5cf6;
            --accent-3: #ec4899;
            --text-main: #f1f5f9;
            --text-dim: #94a3b8;
            --success: #22c55e;
            --danger: #ef4444;
            --warn: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            color: var(--text-main);
            padding: 24px;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.18), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(236, 72, 153, 0.14), transparent 45%),
                linear-gradient(160deg, var(--bg-1), var(--bg-2));
            background-attachment: fixed;
        }

        .quiz-container {
            max-width: 800px;
            width: 100%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.035), rgba(255, 255, 255, 0.01));
            backdrop-filter: blur(20px);
            padding: 36px;
            border-radius: 24px;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.02) inset;
            border: 1px solid var(--card-border);
            animation: containerIn 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes containerIn {
            from {
                opacity: 0;
                transform: translateY(14px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 20px;
            flex-wrap: wrap;
            gap: 14px;
        }

        .quiz-title-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .quiz-eyebrow {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-dim);
        }

        .quiz-title {
            font-size: 1.7rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            background: linear-gradient(135deg, #a5b4fc, #f0abfc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .quiz-timer {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.05rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: #fde68a;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            padding: 8px 16px;
            border-radius: 999px;
            transition: all 0.3s ease;
        }

        .quiz-timer.timer-warn {
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.35);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.04);
            }
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(148, 163, 184, 0.12);
            border-radius: 999px;
            margin: 24px 0 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-1), var(--accent-2), var(--accent-3));
            border-radius: 999px;
            transition: width 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: var(--text-dim);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .question-block {
            margin: 22px 0 8px;
            animation: questionIn 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes questionIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .question-number-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            font-weight: 700;
            font-size: 0.85rem;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .question-text {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.18s ease;
            border: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
        }

        .option:hover {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.35);
            transform: translateY(-1px);
        }

        .option:has(input:checked) {
            background: rgba(99, 102, 241, 0.16);
            border-color: var(--accent-1);
            box-shadow: 0 0 0 1px var(--accent-1) inset;
        }

        .option input[type="radio"] {
            accent-color: var(--accent-1);
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .option span {
            font-size: 1rem;
            color: var(--text-main);
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 32px;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 26px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            font-family: inherit;
            letter-spacing: -0.01em;
        }

        .btn:active {
            transform: scale(0.97);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            color: #fff;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 10px 24px rgba(99, 102, 241, 0.45);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            font-size: 1.05rem;
            padding: 14px 44px;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.35);
        }

        .btn-success:hover {
            box-shadow: 0 10px 28px rgba(34, 197, 94, 0.5);
            transform: translateY(-1px);
        }

        .question-counter {
            font-size: 0.88rem;
            color: var(--text-dim);
            font-weight: 700;
            background: rgba(255, 255, 255, 0.04);
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .submit-area {
            text-align: center;
            margin-top: 28px;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .quiz-container {
                padding: 22px 18px;
                border-radius: 20px;
            }

            .quiz-title {
                font-size: 1.35rem;
            }

            .quiz-timer {
                width: 100%;
                justify-content: center;
                padding: 10px 14px;
            }

            .question-text {
                font-size: 1.02rem;
                word-break: break-word;
            }

            .nav-buttons {
                flex-wrap: wrap;
                gap: 10px;
                justify-content: center;
            }

            .quiz-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .option {
                padding: 13px 14px;
            }

            .option span {
                font-size: .95rem;
                word-break: break-word;
                line-height: 1.5;
            }

            .option input {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
            }

            .btn {
                width: 100%;
            }

            .btn-success {
                width: 100%;
            }

            .question-counter {
                order: 3;
            }
        }
    </style>
    <script defer src="../assets/js/i18n.js"></script>
</head>

<body>
    <div class="quiz-container">
        <div class="quiz-header">
            <div class="quiz-title-wrap">
                <span class="quiz-eyebrow" data-i18n="siswa.quiz.label">Kuis</span>
                <div class="quiz-title"><?= htmlspecialchars($quizNameDisplay) ?></div>
            </div>
            <div class="quiz-timer" id="timerDisplay">⏱️ 05:00</div>
        </div>

        <div class="progress-label">
            <span data-i18n="siswa.quiz.progress">Progres</span>
            <span id="progressPercentLabel">0%</span>
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
            return String(str).replace(/[&<>"']/g, function(c) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [c];
            });
        }

        // Timer 5 menit (300 detik)
        let timeLeft = 300;
        const timerDisplay = document.getElementById('timerDisplay');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `⏱️ ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            timerDisplay.classList.toggle('timer-warn', timeLeft <= 30);
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
            html += `<div class="question-text"><span class="question-number-badge">${index+1}</span><span>${escHtml(q.q)}</span></div>`;
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
            const pct = ((index + 1) / total) * 100;
            document.getElementById('progressFill').style.width = `${pct}%`;
            document.getElementById('progressPercentLabel').textContent = `${Math.round(pct)}%`;

            document.getElementById('prevBtn').style.display = (index === 0) ? 'none' : 'inline-block';
            document.getElementById('nextBtn').style.display = (index === total - 1) ? 'none' : 'inline-block';
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