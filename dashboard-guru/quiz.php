<?php
require_once __DIR__ . '/../function.php';
requireRole(['guru']);

$pageTitle = 'Kelola Quiz - EduCare';
$file = 'quiz.json';
$quizList = readJSON($file);
$materiList = readJSON('materi.json');

// Handle sub-action for Questions
$action = sanitize($_GET['action'] ?? '');
$quiz_id = (int)($_GET['quiz_id'] ?? 0);
$selectedQuiz = null;

if ($quiz_id > 0) {
    foreach ($quizList as $q) {
        if ($q['id'] === $quiz_id) {
            $selectedQuiz = $q;
            break;
        }
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit;
    }
    // 1. Add Quiz
    if (isset($_POST['add_quiz'])) {
        $name = sanitize($_POST['name']);
        $status = sanitize($_POST['status']);
        $materiId = (int) ($_POST['materi_id'] ?? 0);

        if (empty($name)) {
            setFlashMessage('error', 'msg.guru.quiz.name_required');
        } else {
            $newQuiz = [
                'name' => $name,
                'status' => $status,
                'questions' => 0,
                'questions_list' => []
            ];
            if ($materiId > 0) {
                $newQuiz['materi_id'] = $materiId;
            }
            insertJSON($file, $newQuiz);

            // ---- Terhubung ke Siswa: notifikasi + log aktivitas ----
            $guruId   = $_SESSION['user']['id'] ?? 0;
            $guruName = $_SESSION['user']['nama'] ?? 'Guru';
            addActivity((int) $guruId, $guruName, 'guru', '❓', "Membuat quiz \"$name\"");
            if ($status === 'Aktif') {
                addNotification('siswa', '❓', 'Quiz Baru', "Quiz baru tersedia: $name");
            }

            setFlashMessage('success', 'msg.guru.quiz.add_success');
        }
        header('Location: quiz.php');
        exit;
    }

    // 2. Edit Quiz
    if (isset($_POST['edit_quiz'])) {
        $id = (int)$_POST['id'];
        $name = sanitize($_POST['name']);
        $status = sanitize($_POST['status']);
        $materiId = (int) ($_POST['materi_id'] ?? 0);

        if (empty($name)) {
            setFlashMessage('error', 'msg.guru.quiz.name_required');
        } else {
            $updateData = [
                'name' => $name,
                'status' => $status
            ];
            if ($materiId > 0) {
                $updateData['materi_id'] = $materiId;
            } else {
                $updateData['materi_id'] = 0;
            }
            updateJSON($file, $id, $updateData);
            setFlashMessage('success', 'msg.guru.quiz.update_success');
        }
        header('Location: quiz.php');
        exit;
    }

    // 3. Delete Quiz
    if (isset($_POST['delete_quiz'])) {
        $id = (int)$_POST['id'];
        deleteJSON($file, $id);
        setFlashMessage('success', 'msg.guru.quiz.delete_success');
        header('Location: quiz.php');
        exit;
    }

    // 4. Add Question
    if (isset($_POST['add_question'])) {
        $qText = sanitize($_POST['q_text']);
        $optA = sanitize($_POST['opt_a']);
        $optB = sanitize($_POST['opt_b']);
        $optC = sanitize($_POST['opt_c']);
        $optD = sanitize($_POST['opt_d']);
        $answer = (int)$_POST['answer'];

        if ($selectedQuiz) {
            $questions_list = $selectedQuiz['questions_list'] ?? [];
            $questions_list[] = [
                'q' => $qText,
                'o' => [$optA, $optB, $optC, $optD],
                'a' => $answer
            ];

            updateJSON($file, $quiz_id, [
                'questions_list' => $questions_list,
                'questions' => count($questions_list)
            ]);

            setFlashMessage('success', 'msg.guru.quiz.question_add_success');
        }
        header("Location: quiz.php?action=questions&quiz_id=$quiz_id");
        exit;
    }

    // 5. Edit Question
    if (isset($_POST['edit_question'])) {
        $qIdx = (int)$_POST['q_idx'];
        $qText = sanitize($_POST['q_text']);
        $optA = sanitize($_POST['opt_a']);
        $optB = sanitize($_POST['opt_b']);
        $optC = sanitize($_POST['opt_c']);
        $optD = sanitize($_POST['opt_d']);
        $answer = (int)$_POST['answer'];

        if ($selectedQuiz && isset($selectedQuiz['questions_list'][$qIdx])) {
            $questions_list = $selectedQuiz['questions_list'];
            $questions_list[$qIdx] = [
                'q' => $qText,
                'o' => [$optA, $optB, $optC, $optD],
                'a' => $answer
            ];

            updateJSON($file, $quiz_id, [
                'questions_list' => $questions_list
            ]);

            setFlashMessage('success', 'msg.guru.quiz.question_update_success');
        }
        header("Location: quiz.php?action=questions&quiz_id=$quiz_id");
        exit;
    }

    // 6. Delete Question
    if (isset($_POST['delete_question'])) {
        $qIdx = (int)$_POST['q_idx'];

        if ($selectedQuiz && isset($selectedQuiz['questions_list'][$qIdx])) {
            $questions_list = $selectedQuiz['questions_list'];
            unset($questions_list[$qIdx]);
            $questions_list = array_values($questions_list); // reindex

            updateJSON($file, $quiz_id, [
                'questions_list' => $questions_list,
                'questions' => count($questions_list)
            ]);

            setFlashMessage('success', 'msg.guru.quiz.question_delete_success');
        }
        header("Location: quiz.php?action=questions&quiz_id=$quiz_id");
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen flex" x-data="{
    showAddQuizModal: false,
    showEditQuizModal: false,
    showDeleteQuizModal: false,
    editQuizId: null,
    editQuizName: '',
    editQuizStatus: '',
    editQuizMateri: 0,
    deleteQuizId: null,
    deleteQuizName: '',
    
    showAddQuestionModal: false,
    showEditQuestionModal: false,
    showDeleteQuestionModal: false,
    editQuestionIdx: null,
    editQuestionText: '',
    editOptA: '',
    editOptB: '',
    editOptC: '',
    editOptD: '',
    editAnswer: 0,
    deleteQuestionIdx: null,
    
    openEditQuiz(q) {
        this.editQuizId = q.id;
        this.editQuizName = q.name;
        this.editQuizStatus = q.status;
        this.editQuizMateri = parseInt(q.materi_id || 0, 10);
        this.showEditQuizModal = true;
    },
    openDeleteQuiz(q) {
        this.deleteQuizId = q.id;
        this.deleteQuizName = q.name;
        this.showDeleteQuizModal = true;
    },
    openEditQuestion(idx, question) {
        this.editQuestionIdx = idx;
        this.editQuestionText = question.q;
        this.editOptA = question.o[0];
        this.editOptB = question.o[1];
        this.editOptC = question.o[2];
        this.editOptD = question.o[3];
        this.editAnswer = question.a;
        this.showEditQuestionModal = true;
    },
    openDeleteQuestion(idx) {
        this.deleteQuestionIdx = idx;
        this.showDeleteQuestionModal = true;
    }
}">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <!-- Alert Notification -->
        <?php if ($msg = getFlashMessage('success')): ?>
            <div class="mb-6 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-2xl text-xs flex justify-between items-center shadow-xs animate-fadeIn">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span data-i18n="<?= htmlspecialchars($msg) ?>"><?= htmlspecialchars($msg) ?></span>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($msg = getFlashMessage('error')): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl text-xs flex justify-between items-center shadow-xs animate-fadeIn">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                    <span data-i18n="<?= htmlspecialchars($msg) ?>"><?= htmlspecialchars($msg) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($action === 'questions' && $selectedQuiz): ?>
            <!-- VIEW: MANAGE QUESTIONS FOR A SPECIFIC QUIZ -->
            <div class="flex items-center gap-2 text-slate-400 text-xs mb-3">
                <a href="quiz.php" class="hover:text-blue-600 transition" data-i18n="guru.quiz.title">Kelola Quiz</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-medium" data-i18n="guru.quiz.manage_questions">Kelola Soal</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 leading-tight"><span data-i18n="guru.quiz.questions_title">Soal:</span> <?= htmlspecialchars($selectedQuiz['name']) ?></h2>
                    <p class="text-sm text-slate-500 mt-1" data-i18n="guru.quiz.questions_subtitle">Kelola pertanyaan pilihan ganda dan kunci jawaban kuis ini.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="quiz.php" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Kembali</a>
                    <button @click="showAddQuestionModal = true" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> <span data-i18n="guru.quiz.add_question_button">Tambah Soal</span>
                    </button>
                </div>
            </div>

            <!-- List Questions -->
            <div class="space-y-6">
                <?php
                $questionsList = $selectedQuiz['questions_list'] ?? [];
                if (empty($questionsList)):
                ?>
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4"><i data-lucide="file-question" class="w-8 h-8"></i></div>
                        <div class="font-bold text-slate-800" data-i18n="guru.quiz.no_questions_title">Belum Ada Soal</div>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs" data-i18n="guru.quiz.no_questions_desc">Silakan tambahkan soal pertama untuk kuis ini dengan mengklik tombol "Tambah Soal".</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($questionsList as $idx => $q): ?>
                        <div class="glassmorphism rounded-3xl p-6 border border-white/60 shadow-xs bg-white/40">
                            <div class="flex justify-between items-start gap-4">
                                <h4 class="font-bold text-slate-800 text-sm flex-1">
                                    <span class="text-blue-600 font-mono-tech mr-1">#<?= $idx + 1 ?></span>
                                    <?= htmlspecialchars($q['q']) ?>
                                </h4>

                                <div class="flex items-center gap-1">
                                    <button @click="openEditQuestion(<?= $idx ?>, <?= htmlspecialchars(json_encode($q)) ?>)" class="p-2 rounded-xl bg-white/80 hover:bg-blue-50 hover:text-blue-600 border border-slate-100 transition" data-i18n-title="guru.quiz.edit_question_modal_title" title="Edit Soal"><i data-lucide="edit-3" class="w-3.5 h-3.5"></i></button>
                                    <button @click="openDeleteQuestion(<?= $idx ?>)" class="p-2 rounded-xl bg-white/80 hover:bg-red-50 hover:text-red-600 border border-slate-100 transition" data-i18n-title="guru.quiz.delete_question_confirm_title" title="Hapus Soal"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4 text-xs">
                                <?php foreach ($q['o'] as $oIdx => $opt): ?>
                                    <div class="p-3 rounded-2xl border <?= ($oIdx === $q['a']) ? 'bg-emerald-50 border-emerald-200 text-emerald-800 font-semibold' : 'bg-slate-50/50 border-slate-100 text-slate-600' ?> flex items-center gap-3">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold <?= ($oIdx === $q['a']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' ?>">
                                            <?= chr(65 + $oIdx) ?>
                                        </span>
                                        <span><?= htmlspecialchars($opt) ?></span>
                                        <?php if ($oIdx === $q['a']): ?>
                                            <span class="ml-auto text-[9px] font-extrabold uppercase bg-emerald-600 text-white px-2 py-0.5 rounded-md" data-i18n="guru.quiz.answer_key">Kunci</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- VIEW: LIST QUIZZES -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 leading-tight" data-i18n="guru.quiz.title">Kelola Quiz</h2>
                    <p class="text-sm text-slate-500 mt-1" data-i18n="guru.quiz.subtitle">Buat kuis penilaian hasil belajar, status, dan soal.</p>
                </div>

                <div class="flex items-center gap-3 self-end md:self-auto">
                    <button @click="showAddQuizModal = true" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> <span data-i18n="guru.quiz.add_quiz_button">Tambah Quiz</span>
                    </button>
                </div>
            </div>

            <!-- Quiz List Table -->
            <div class="glassmorphism rounded-3xl border border-white/60 shadow-xs overflow-hidden bg-white/40">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 text-slate-500 font-semibold text-xs border-b border-slate-100 uppercase tracking-wider">
                                <th class="p-4 md:p-6" data-i18n="guru.dynamic.quiz_name">Nama Kuis</th>
                                <th class="p-4 md:p-6" data-i18n="guru.quiz.questions_count">Jumlah Soal</th>
                                <th class="p-4 md:p-6" data-i18n="guru.dynamic.status">Status</th>
                                <th class="p-4 md:p-6 text-right" data-i18n="guru.dynamic.action">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/50 text-slate-700 text-xs">
                            <?php if (empty($quizList)): ?>
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mx-auto mb-4"><i data-lucide="file-question" class="w-8 h-8"></i></div>
                                        <p class="font-semibold text-slate-500" data-i18n="guru.quiz.no_quiz_empty_title">Belum ada quiz yang dibuat.</p>
                                        <p class="text-slate-400 text-xs mt-1" data-i18n="guru.quiz.no_quiz_empty_desc">Buat quiz pertama untuk mulai menguji pemahaman siswa.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($quizList as $q): ?>
                                    <tr class="hover:bg-white/50 transition-colors">
                                        <td class="p-4 md:p-6 font-bold text-slate-800">
                                            <?= htmlspecialchars($q['name']) ?>
                                        </td>
                                        <td class="p-4 md:p-6 text-slate-500">
                                            <?= count($q['questions_list'] ?? []) ?> <span data-i18n="guru.dynamic.questions_word">Soal</span>
                                        </td>
                                        <td class="p-4 md:p-6">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $q['status'] === 'Aktif' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10' : 'bg-slate-100 text-slate-600' ?>">
                                                <?= htmlspecialchars($q['status']) ?>
                                            </span>
                                        </td>
                                        <td class="p-4 md:p-6 text-right flex justify-end gap-2">
                                            <a href="quiz.php?action=questions&quiz_id=<?= $q['id'] ?>" class="px-3 py-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold transition flex items-center gap-1.5" data-i18n-title="guru.quiz.questions_title" title="Kelola Soal">
                                                <i data-lucide="list-todo" class="w-3.5 h-3.5"></i> <span data-i18n="guru.dynamic.questions_word">Soal</span>
                                            </a>
                                            <button @click="openEditQuiz(<?= htmlspecialchars(json_encode($q)) ?>)" class="p-2 rounded-xl bg-white text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-100 transition" data-i18n-title="guru.quiz.edit_modal_title" title="Edit Kuis">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="openDeleteQuiz(<?= htmlspecialchars(json_encode($q)) ?>)" class="p-2 rounded-xl bg-white text-red-600 hover:bg-red-50 border border-slate-100 transition" data-i18n-title="guru.quiz.delete_confirm_title" title="Hapus Kuis">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- ========================================================================= -->
<!-- MODALS FOR QUIZ -->
<!-- ========================================================================= -->

<!-- ADD QUIZ MODAL -->
<div x-show="showAddQuizModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100" @click.away="showAddQuizModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="plus-circle" class="text-blue-600"></i> <span data-i18n="guru.quiz.add_quiz_modal_title">Buat Quiz Baru</span></h3>
            <button @click="showAddQuizModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="add_quiz" value="1">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.dynamic.quiz_name">Nama Kuis</label>
                <input type="text" name="name" required placeholder="Contoh: Kuis Dasar HTML" data-i18n-placeholder="guru.dynamic.quiz_name_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.dynamic.link_materi">Tautkan ke Materi/Kursus (opsional)</label>
                <select name="materi_id" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="0" data-i18n="guru.quiz.not_linked_option">— Tidak ditautkan (quiz berdiri sendiri) —</option>
                    <?php foreach ($materiList as $m): ?>
                        <option value="<?= (int)($m['id'] ?? 0) ?>"><?= htmlspecialchars($m['title'] ?? 'Materi') ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[10px] text-slate-400 mt-1.5" data-i18n="guru.quiz.link_materi_helper">Jika dipilih, kuis ini tampil di detail materi tersebut dan di daftar Quiz &amp; Latihan siswa dengan judul yang sama.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.quiz.status_label">Status Kuis</label>
                    <select name="status" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                        <option value="Aktif" data-i18n="guru.dynamic.active_visible">Aktif</option>
                        <option value="Draft" data-i18n="guru.dynamic.draft_hidden">Draft</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showAddQuizModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.dynamic.save">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT QUIZ MODAL -->
<div x-show="showEditQuizModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100" @click.away="showEditQuizModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="edit" class="text-blue-600"></i> <span data-i18n="guru.quiz.edit_modal_title">Edit Informasi Kuis</span></h3>
            <button @click="showEditQuizModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="edit_quiz" value="1">
            <input type="hidden" name="id" :value="editQuizId">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.dynamic.quiz_name">Nama Kuis</label>
                <input type="text" name="name" x-model="editQuizName" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.dynamic.link_materi">Tautkan ke Materi/Kursus</label>
                <select name="materi_id" x-model.number="editQuizMateri" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="0" data-i18n="guru.quiz.not_linked_option">— Tidak ditautkan (quiz berdiri sendiri) —</option>
                    <?php foreach ($materiList as $m): ?>
                        <option value="<?= (int)($m['id'] ?? 0) ?>"><?= htmlspecialchars($m['title'] ?? 'Materi') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.dynamic.status">Status</label>
                    <select name="status" x-model="editQuizStatus" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                        <option value="Aktif" data-i18n="guru.dynamic.active_visible">Aktif</option>
                        <option value="Draft" data-i18n="guru.dynamic.draft_hidden">Draft</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showEditQuizModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.dynamic.save_changes">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE QUIZ CONFIRM -->
<div x-show="showDeleteQuizModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-sm w-full shadow-2xl border border-slate-100 text-center" @click.away="showDeleteQuizModal = false">
        <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4"><i data-lucide="trash-2" class="w-8 h-8"></i></div>
        <h3 class="text-lg font-bold text-slate-900" data-i18n="guru.quiz.delete_confirm_title">Hapus Quiz?</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed"><span data-i18n="guru.quiz.delete_confirm_desc">Apakah Anda yakin ingin menghapus kuis</span> <span class="font-bold text-slate-800" x-text="deleteQuizName"></span>? <span data-i18n="guru.quiz.delete_confirm_warning">Semua data nilai progres siswa yang berkaitan juga terpengaruh.</span></p>

        <form action="" method="POST" class="mt-6 flex justify-center gap-3">
            <?= csrfField() ?>
            <input type="hidden" name="delete_quiz" value="1">
            <input type="hidden" name="id" :value="deleteQuizId">
            <button type="button" @click="showDeleteQuizModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
            <button type="submit" class="px-5 py-3 rounded-2xl bg-red-600 text-white font-semibold hover:bg-red-700 transition shadow-lg shadow-red-500/20 text-xs" data-i18n="guru.dynamic.confirm_delete">Ya, Hapus</button>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODALS FOR QUESTIONS -->
<!-- ========================================================================= -->

<!-- ADD QUESTION MODAL -->
<div x-show="showAddQuestionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100 overflow-y-auto max-h-[90vh]" @click.away="showAddQuestionModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="plus-circle" class="text-blue-600"></i> <span data-i18n="guru.quiz.add_question_modal_title">Tambah Soal Kuis</span></h3>
            <button @click="showAddQuestionModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="add_question" value="1">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.quiz.question_label">Pertanyaan / Soal</label>
                <textarea name="q_text" required placeholder="Tuliskan teks pertanyaan..." data-i18n-placeholder="guru.quiz.question_placeholder" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50"></textarea>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1" data-i18n="guru.quiz.options_label">Opsi Pilihan Jawaban</label>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">A</span>
                    <input type="text" name="opt_a" required placeholder="Jawaban A" data-i18n-placeholder="guru.quiz.answer_a" class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">B</span>
                    <input type="text" name="opt_b" required placeholder="Jawaban B" data-i18n-placeholder="guru.quiz.answer_b" class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">C</span>
                    <input type="text" name="opt_c" required placeholder="Jawaban C" data-i18n-placeholder="guru.quiz.answer_c" class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">D</span>
                    <input type="text" name="opt_d" required placeholder="Jawaban D" data-i18n-placeholder="guru.quiz.answer_d" class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.quiz.correct_answer_label">Jawaban yang Benar (Kunci)</label>
                <select name="answer" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="0" data-i18n="guru.quiz.option_a">Pilihan A</option>
                    <option value="1" data-i18n="guru.quiz.option_b">Pilihan B</option>
                    <option value="2" data-i18n="guru.quiz.option_c">Pilihan C</option>
                    <option value="3" data-i18n="guru.quiz.option_d">Pilihan D</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showAddQuestionModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.dynamic.save">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT QUESTION MODAL -->
<div x-show="showEditQuestionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100 overflow-y-auto max-h-[90vh]" @click.away="showEditQuestionModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="edit" class="text-blue-600"></i> <span data-i18n="guru.quiz.edit_question_modal_title">Edit Soal Kuis</span></h3>
            <button @click="showEditQuestionModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="edit_question" value="1">
            <input type="hidden" name="q_idx" :value="editQuestionIdx">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.quiz.question_label">Pertanyaan / Soal</label>
                <textarea name="q_text" x-model="editQuestionText" required rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50"></textarea>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1" data-i18n="guru.quiz.options_label">Opsi Pilihan Jawaban</label>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">A</span>
                    <input type="text" name="opt_a" x-model="editOptA" required class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">B</span>
                    <input type="text" name="opt_b" x-model="editOptB" required class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">C</span>
                    <input type="text" name="opt_c" x-model="editOptC" required class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">D</span>
                    <input type="text" name="opt_d" x-model="editOptD" required class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.quiz.correct_answer_label">Jawaban yang Benar (Kunci)</label>
                <select name="answer" x-model="editAnswer" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="0" data-i18n="guru.quiz.option_a">Pilihan A</option>
                    <option value="1" data-i18n="guru.quiz.option_b">Pilihan B</option>
                    <option value="2" data-i18n="guru.quiz.option_c">Pilihan C</option>
                    <option value="3" data-i18n="guru.quiz.option_d">Pilihan D</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showEditQuestionModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.dynamic.save_changes">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE QUESTION CONFIRM -->
<div x-show="showDeleteQuestionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-sm w-full shadow-2xl border border-slate-100 text-center" @click.away="showDeleteQuestionModal = false">
        <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4"><i data-lucide="trash-2" class="w-8 h-8"></i></div>
        <h3 class="text-lg font-bold text-slate-900" data-i18n="guru.quiz.delete_question_confirm_title">Hapus Soal Kuis?</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed" data-i18n="guru.quiz.delete_question_confirm_desc">Apakah Anda yakin ingin menghapus soal ini dari kuis? Tindakan ini tidak dapat dibatalkan.</p>

        <form action="" method="POST" class="mt-6 flex justify-center gap-3">
            <?= csrfField() ?>
            <input type="hidden" name="delete_question" value="1">
            <input type="hidden" name="q_idx" :value="deleteQuestionIdx">
            <button type="button" @click="showDeleteQuestionModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
            <button type="submit" class="px-5 py-3 rounded-2xl bg-red-600 text-white font-semibold hover:bg-red-700 transition shadow-lg shadow-red-500/20 text-xs" data-i18n="guru.dynamic.confirm_delete">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

</body>

</html>