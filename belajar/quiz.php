<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../translation.php';

// ============================================================
// 1. AUTENTIKASI
// ============================================================
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

$role = $_SESSION['user']['role'] ?? '';
if ($role !== 'siswa' && $role !== 'admin') {
    header('Location: ../dashboard-guru/index.php');
    exit;
}

$name = htmlspecialchars($_SESSION['user']['nama'] ?? 'Pengguna');

// ============================================================
// 2. AMBIL PARAMETER URL
// ============================================================
$quizId   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$materiId = isset($_GET['materi_id']) ? (int) $_GET['materi_id'] : 0;

if ($quizId === 0 && $materiId === 0) {
    die('Quiz tidak ditemukan.');
}

// Skema soal quiz berbeda-beda antar editor (questions_list / items /
// questions). Ambil field pertama yang terisi (array tidak kosong) sehingga
// quiz yang dibuat lewat editor mana pun tetap terbaca dengan benar.
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
// 3. MUAT DATA QUIZ (dari ../data/)
// ============================================================
$quizName  = '';
$questions = [];
$found     = false;

// --- Cari berdasarkan quiz_id di quiz.json ---
if ($quizId > 0) {
    $quizFile = __DIR__ . '/../data/quiz.json';
    if (file_exists($quizFile)) {
        $allQuizzes = json_decode(file_get_contents($quizFile), true) ?: [];
        foreach ($allQuizzes as $q) {
            if ((int) $q['id'] === $quizId && ($q['status'] ?? '') === 'Aktif') {
                $quizName  = $q['name'] ?? 'Quiz';
                $questions = pickQuizQuestions($q);
                $found     = true;
                break;
            }
        }
    }
}

// --- Kalau belum ketemu lewat quiz_id, cari berdasarkan materi_id di quiz.json ---
if (!$found && $materiId > 0) {
    $quizFile = __DIR__ . '/../data/quiz.json';
    if (file_exists($quizFile)) {
        $allQuizzes = json_decode(file_get_contents($quizFile), true) ?: [];
        foreach ($allQuizzes as $q) {
            if ((int) ($q['materi_id'] ?? 0) === $materiId && ($q['status'] ?? '') === 'Aktif') {
                $quizName  = $q['name'] ?? 'Quiz';
                $questions = pickQuizQuestions($q);
                $found     = true;
                break;
            }
        }
    }
}

// --- Kalau masih belum ketemu, coba cari lewat materi.json (fallback lama) ---
if (!$found && $materiId > 0) {
    $materiFile = __DIR__ . '/../data/materi.json';
    if (file_exists($materiFile)) {
        $allMateri = json_decode(file_get_contents($materiFile), true) ?: [];
        foreach ($allMateri as $m) {
            if ((int) $m['id'] === $materiId) {
                $quizName  = 'Quiz: ' . ($m['title'] ?? 'Materi');
                $questions = $m['quiz_questions_list'] ?? $m['questions'] ?? $m['items'] ?? [];
                $found     = true;
                break;
            }
        }
    }
}

// --- Kalau tidak ada soal sama sekali (mis. quiz dibuat guru tanpa soal,
//      atau skema field berbeda), jangan tampilkan soal dummy. Arahkan
//      kembali ke dashboard dengan pesan yang jelas. ---
if (empty($questions) || $found === false) {
    $_SESSION['error_message'] = 'msg.quiz.no_questions';
    header('Location: ' . rtrim(BASE_URL, '/') . '/dashboard-siswa/index.php#quiz');
    exit;
}

// --- Normalisasi soal: pastikan setiap soal punya format {q, o:[], a} yang
//      dipakai render dan scoring. Jika data berasal dari skema lama 'items'
//      ({q, opts:[], ans}), konversikan ke {q, o:[], a} supaya halaman tidak
//      blank dan skor tetap benar. Jangan menambah soal dummy (berarti menambah
//      pembagi skor); menyelesaikan sebanyak soal yang ada adalah perilaku benar. ---
$normalized = [];
foreach ($questions as $q) {
    $o  = $q['o'] ?? $q['opts'] ?? [];
    $a  = $q['a'] ?? $q['ans'] ?? -1;
    $qt = $q['q'] ?? '';
    if ($qt === '' || !is_array($o) || count($o) < 2) {
        continue;
    }
    $normalized[] = [
        'q' => $qt,
        'o' => array_values($o),
        'a' => (int) $a,
    ];
}
$questions = $normalized;

// Bila setelah normalisasi tidak ada soal valid tersisa, tolak / arahkan
// kembali alih-alih menampilkan soal dummy (skor fiktif).
if (empty($questions)) {
    $_SESSION['error_message'] = 'msg.quiz.no_valid_questions';
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
        if (isset($userAnswers[$idx]) && (int) $userAnswers[$idx] === (int) $q['a']) {
            $score++;
        }
    }

    $total      = count($questions);
    $finalScore = $total > 0 ? (int) round(($score / $total) * 100) : 0;

    // Kirim hasil ke dashboard siswa, satu-satunya tempat yang benar-benar
    // menyimpan skor ke progress.json.
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Menyimpan hasil...</title>
        <meta charset="UTF-8">
    </head>
    <body>
        <form id="redirectForm" method="POST" action="../dashboard-siswa/index.php">
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
// Terjemahkan konten dinamis (nama quiz & teks soal/opsi) bila bahasa EN.
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

$totalQuestions  = count($questions);
$dashboardUrl    = '../dashboard-siswa/index.php#quiz';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quizNameDisplay) ?> • EduCare</title>
    <style>
       *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    background:#ffffff;
    color:#1e293b;
    padding:40px 20px;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.quiz-container{
    width:100%;
    max-width:900px;
    background:#fff;
    border-radius:22px;
    padding:40px;
    border:1px solid #e2e8f0;
    box-shadow:
    0 10px 40px rgba(15,23,42,.08);
}

.back-link{
    display:inline-flex;
    align-items:center;
    gap:8px;
    color:#64748b;
    text-decoration:none;
    font-size:.9rem;
    font-weight:600;
    margin-bottom:25px;
    transition:.2s;
}

.back-link:hover{
    color:#2563eb;
}

.quiz-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding-bottom:25px;
    margin-bottom:25px;
    border-bottom:1px solid #e2e8f0;
}

.quiz-title{
    font-size:2rem;
    font-weight:700;
    color:#0f172a;
}

.quiz-timer{
    background:#eff6ff;
    color:#2563eb;
    padding:10px 18px;
    border-radius:999px;
    font-weight:700;
    font-size:.95rem;
}

.progress-bar{
    width:100%;
    height:8px;
    background:#e2e8f0;
    border-radius:100px;
    overflow:hidden;
    margin-bottom:35px;
}

.progress-fill{
    height:100%;
    background:linear-gradient(90deg,#2563eb,#3b82f6);
    transition:.35s;
}

.question-block{
    animation:fade .25s ease;
}

.question-text{
    font-size:1.25rem;
    font-weight:700;
    margin-bottom:25px;
    line-height:1.7;
    color:#0f172a;
}

.options{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.option{
    display:flex;
    align-items:center;
    gap:15px;
    padding:18px;
    border:1px solid #dbe4ef;
    border-radius:14px;
    cursor:pointer;
    background:#fff;
    transition:.2s;
}

.option:hover{
    border-color:#2563eb;
    background:#f8fbff;
    transform:translateY(-2px);
}

.option input{
    accent-color:#2563eb;
    width:20px;
    height:20px;
}

.option span{
    font-size:1rem;
    color:#334155;
}

.nav-buttons{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:40px;
    flex-wrap:wrap;
    gap:15px;
}

.question-counter{
    font-weight:600;
    color:#64748b;
}

.btn{
    border:none;
    cursor:pointer;
    border-radius:12px;
    padding:12px 24px;
    font-weight:600;
    transition:.2s;
    font-size:.95rem;
}

.btn-primary{
    background:#2563eb;
    color:#fff;
}

.btn-primary:hover{
    background:#1d4ed8;
}

.btn-secondary{
    background:#f1f5f9;
    color:#334155;
}

.btn-secondary:hover{
    background:#e2e8f0;
}

.btn-success{
    background:#16a34a;
    color:#fff;
    padding:14px 45px;
    font-size:1rem;
}

.btn-success:hover{
    background:#15803d;
}

.submit-area{
    margin-top:45px;
    text-align:center;
}

.btn:disabled{
    opacity:.5;
}

@keyframes fade{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@media(max-width:768px){

body{
padding:16px 12px;
}

.quiz-container{
padding:20px 16px;
border-radius:18px;
}

.quiz-header{
flex-direction:column;
align-items:flex-start;
gap:15px;
}

.quiz-title{
font-size:1.5rem;
}

.quiz-timer{
width:100%;
text-align:center;
padding:10px 14px;
}

.question-text{
font-size:1.1rem;
}

.question-text{
word-break:break-word;
}

.option{
padding:14px 13px;
gap:12px;
}

.option span{
font-size:.95rem;
word-break:break-word;
line-height:1.5;
}

.option input{
width:18px;
height:18px;
flex-shrink:0;
}

.nav-buttons{
justify-content:center;
}

.btn{
width:100%;
}

.submit-area .btn{
width:100%;
}

}
    </style>
    <script defer src="../assets/js/i18n.js"></script>
</head>
<body>
<div class="quiz-container">
    <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="back-link" onclick="return confirmLeave(event)" data-i18n="siswa.quiz.back_to_dashboard">
        ← Kembali ke Dashboard
    </a>

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
    const questions = <?= json_encode($questions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
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

    // ---- Timer 5 menit ----
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

    // ---- Render soal ----
    function renderQuestion(index) {
        const q = questions[index];
        const container = document.getElementById('questionContainer');

        let html = `<div class="question-block">`;
        html += `<div class="question-text">${index + 1}. ${escHtml(q.q)}</div>`;
        html += `<div class="options">`;
        q.o.forEach((opt, i) => {
            const checked = (userAnswers[index] == i) ? 'checked' : '';
            html += `<label class="option">`;
            // name="q_${index}" hanya untuk grouping radio per soal, BUKAN untuk
            // dikirim ke server — semua jawaban disinkronkan lewat syncAnswersToForm()
            // supaya jawaban soal sebelumnya tidak hilang saat pindah soal.
            html += `<input type="radio" name="q_${index}" value="${i}" ${checked} onchange="saveAnswer(${index}, ${i})">`;
            html += `<span>${escHtml(opt)}</span>`;
            html += `</label>`;
        });
        html += `</div></div>`;
        container.innerHTML = html;

        document.getElementById('counterLabel').textContent = `${index + 1} / ${total}`;
        document.getElementById('progressFill').style.width = `${((index + 1) / total) * 100}%`;
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

    // ---- Sinkronkan SEMUA jawaban tersimpan ke form sebagai hidden input,
    //      dipanggil tepat sebelum form dikirim (baik lewat tombol maupun timeout) ----
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

    // ---- Konfirmasi sebelum keluar lewat link "Kembali ke Dashboard" ----
    function confirmLeave(evt) {
        if (submitted) return true; // sudah selesai submit, boleh langsung pindah
        const answered = Object.keys(userAnswers).length;
        const msg = answered > 0
            ? window.EduCareI18n?.t('siswa.quiz.confirm_leave_progress') || 'Progres jawabanmu belum dikirim dan akan hilang jika keluar sekarang. Yakin ingin kembali ke dashboard?'
            : window.EduCareI18n?.t('siswa.quiz.confirm_leave') || 'Yakin ingin keluar dari quiz ini?';
        if (!confirm(msg)) {
            evt.preventDefault();
            return false;
        }
        return true;
    }

    renderQuestion(0);
</script>
</body>
</html>