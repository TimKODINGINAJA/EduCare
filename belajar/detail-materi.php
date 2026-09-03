<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../translation.php';
requireRole(['siswa']);

$id = (int)($_GET['id'] ?? 0);
$m = findJSON('materi.json', $id);

if (!$m) {
    setFlashMessage('error', 'Materi tidak ditemukan!');
    header('Location: materi.php');
    exit;
}

$pageTitle = t_dynamic($m['title'], 'materi.' . $m['id'] . '.title') . ' - EduCare';
$name = htmlspecialchars($_SESSION['user']['nama'] ?? 'Siswa');

// Deteksi kelompok materi (IT / Matematika / Umum) berdasarkan kategori
$categories = readJSON('kategori.json');
$materiGroup = getCategoryGroup($m['category'], $categories);
$groupLabel = getGroupLabel($materiGroup);
$groupIcon = getGroupIcon($materiGroup);

// Navigasi Materi Sebelumnya / Selanjutnya
$allMateriList = readJSON('materi.json');
usort($allMateriList, fn($a, $b) => $a['id'] <=> $b['id']);
$prevMateri = null;
$nextMateri = null;
foreach ($allMateriList as $idx => $mat) {
    if ($mat['id'] == $m['id']) {
        $prevMateri = $allMateriList[$idx - 1] ?? null;
        $nextMateri = $allMateriList[$idx + 1] ?? null;
        break;
    }
}

// Cek status "selesai" materi ini untuk siswa yang sedang login
$isCompleted = false;
$progressCheck = readJSON('progress.json');
foreach ($progressCheck as $p) {
    if (isset($p['user_id']) && $p['user_id'] == ($_SESSION['user']['id'] ?? 0)) {
        $isCompleted = (($p['progress'][$m['category']] ?? 0) >= 100);
        break;
    }
}

// Setiap materi punya kuisnya sendiri (dicocokkan lewat materi_id di quiz.json)
$allQuizzes = readJSON('quiz.json');
$linkedQuiz = null;
foreach ($allQuizzes as $q) {
    if (isset($q['materi_id']) && (int)$q['materi_id'] === (int)$m['id']) {
        $linkedQuiz = $q;
        break;
    }
}
// Fallback: cocokkan lewat nama kategori (untuk kuis lama tanpa materi_id)
if (!$linkedQuiz) {
    foreach ($allQuizzes as $q) {
        if (($q['status'] ?? '') === 'Aktif' && strpos(strtolower($q['name']), strtolower($m['category'])) !== false) {
            $linkedQuiz = $q;
            break;
        }
    }
}

// Convert YouTube URL to embed format if applicable
function getYoutubeEmbedUrl(string $url): string
{
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|win/?\?v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
        return 'https://www.youtube.com/embed/' . $match[1];
    }
    return '';
}
$youtubeEmbed = '';
if (!empty($m['video_url'])) {
    $youtubeEmbed = getYoutubeEmbedUrl($m['video_url']);
}

include __DIR__ . '/../includes/header.php';
?>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
            <!-- Reading Progress -->
            <div class="reading-progress-track mb-6">
                <div class="reading-progress-bar" id="readingProgressBar"></div>
            </div>

            <!-- Topbar / Back Link -->
            <div class="flex items-center gap-2 text-slate-400 text-xs mb-6">
                <a href="materi.php" class="hover:text-blue-600 transition flex items-center gap-1"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> <span data-i18n="belajar.detail.back_to_materi">Kembali ke Materi</span></a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-medium" data-i18n="belajar.detail.breadcrumb">Detail Materi</span>
            </div>

            <div class="max-w-4xl mx-auto space-y-8">
                <!-- Header Block -->
                <div class="glassmorphism rounded-3xl p-6 md:p-8 border border-white/60 shadow-xl bg-white/40">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-600 text-white shadow-md inline-flex items-center gap-1">
                            <?= htmlspecialchars($m['category']) ?>
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-900 text-white shadow-md inline-flex items-center gap-1">
                            <i data-lucide="<?= htmlspecialchars($groupIcon) ?>" class="w-3 h-3"></i> <?= htmlspecialchars($groupLabel) ?>
                        </span>
                        <?php if ($isCompleted): ?>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500 text-white shadow-md inline-flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> <span data-i18n="belajar.detail.studied">Selesai Dipelajari</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-950 leading-tight mb-2"><?= htmlspecialchars(t_dynamic($m['title'], 'materi.' . $m['id'] . '.title')) ?></h1>
                    <p class="text-xs text-slate-400 font-mono-tech flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> <span data-i18n="belajar.detail.published_prefix">Diterbitkan pada:</span> <?= formatTanggalIndo($m['date']) ?></p>
                </div>

                <!-- Video Section (Only if exists) -->
                <?php if (!empty($youtubeEmbed) || !empty($m['video_url'])): ?>
                    <div class="glassmorphism rounded-3xl overflow-hidden border border-white/60 shadow-xl bg-white/40 p-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2"><i data-lucide="video" class="text-blue-600"></i> <span data-i18n="belajar.detail.video_title">Video Penjelasan</span></h3>
                        <div class="relative aspect-video rounded-2xl overflow-hidden bg-slate-900 border border-slate-950">
                            <?php if (!empty($youtubeEmbed)): ?>
                                <iframe src="<?= htmlspecialchars($youtubeEmbed) ?>" class="absolute inset-0 w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <?php else: ?>
                                <!-- Fallback to HTML5 Mock video player -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-gradient-to-br from-blue-900/90 to-indigo-950/90">
                                    <div class="w-16 h-16 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center mb-4"><i data-lucide="play" class="w-8 h-8"></i></div>
                                    <div class="font-bold text-white text-base" data-i18n="belajar.detail.video_active">Modul Video Aktif</div>
                                    <p class="text-xs text-slate-400 mt-1 max-w-xs" data-i18n="belajar.detail.video_fallback">Putar video eksternal melalui link:</p>
                                    <a href="<?= htmlspecialchars($m['video_url']) ?>" target="_blank" class="mt-4 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition inline-flex items-center gap-1.5"><i data-lucide="external-link" class="w-4 h-4"></i> <span data-i18n="siswa.chrome.materi.openVideo">Buka Link Video</span></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Content Area -->
                <article class="glassmorphism rounded-3xl p-6 md:p-8 border border-white/60 shadow-xl bg-white/40 max-w-none" id="materiArticle">
                    <h3 class="text-sm font-bold text-slate-800 mb-5 pb-2 border-b border-slate-100 flex items-center gap-2"><i data-lucide="book-open" class="text-blue-600"></i> <span data-i18n="belajar.materi.page_title">Materi Pembelajaran</span></h3>
                    <?= renderMateriContent(t_dynamic($m['content'] ?? '', 'materi.' . $m['id'] . '.content'), $materiGroup) ?>
                </article>

                <!-- Navigasi Antar Materi -->
                <?php if ($prevMateri || $nextMateri): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php if ($prevMateri): ?>
                            <a href="detail-materi.php?id=<?= $prevMateri['id'] ?>" class="glassmorphism rounded-2xl p-4 border border-white/60 bg-white/40 flex items-center gap-3 hover:shadow-lg transition group">
                                <i data-lucide="chevron-left" class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transition flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase" data-i18n="belajar.detail.prev_material">Materi Sebelumnya</div>
                                    <div class="text-xs font-bold text-slate-700 truncate"><?= htmlspecialchars(t_dynamic($prevMateri['title'], 'materi.' . $prevMateri['id'] . '.title')) ?></div>
                                </div>
                            </a>
                        <?php else: ?><div></div><?php endif; ?>
                        <?php if ($nextMateri): ?>
                            <a href="detail-materi.php?id=<?= $nextMateri['id'] ?>" class="glassmorphism rounded-2xl p-4 border border-white/60 bg-white/40 flex items-center gap-3 justify-end text-right hover:shadow-lg transition group">
                                <div class="min-w-0">
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase" data-i18n="belajar.detail.next_material">Materi Selanjutnya</div>
                                    <div class="text-xs font-bold text-slate-700 truncate"><?= htmlspecialchars(t_dynamic($nextMateri['title'], 'materi.' . $nextMateri['id'] . '.title')) ?></div>
                                </div>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transition flex-shrink-0"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Quiz Call-To-Action -->
                <?php if ($linkedQuiz): ?>
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-6 md:p-8 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="space-y-2 text-center md:text-left">
                            <span class="px-2.5 py-0.5 rounded-md text-[9px] font-extrabold uppercase bg-blue-500 text-white tracking-wider" data-i18n="belajar.detail.quiz_badge">Uji Kompetensi</span>
                            <h3 class="text-lg font-bold" data-i18n="belajar.detail.quiz_cta_title">Siap Menguji Pemahaman Anda?</h3>
                            <p class="text-xs text-blue-100 max-w-lg leading-relaxed"><span data-i18n="belajar.detail.quiz_cta_desc_prefix">Selesaikan kuis interaktif</span> <strong><?= htmlspecialchars(t_dynamic($linkedQuiz['name'], 'quiz.' . $linkedQuiz['id'] . '.name')) ?></strong> <span data-i18n="belajar.detail.quiz_cta_desc_min">(minimal</span> <?= (int)($linkedQuiz['questions'] ?? count($linkedQuiz['items'] ?? [])) ?> <span data-i18n="belajar.detail.quiz_cta_desc_questions">soal)</span> <span data-i18n="belajar.detail.quiz_cta_desc_suffix">untuk menguji materi ini dan klaim sertifikat belajar Anda.</span></p>
                        </div>

                        <a href="quiz.php?materi_id=<?= (int)$m['id'] ?>" class="px-6 py-4 bg-white text-blue-600 font-bold rounded-2xl hover:bg-blue-50 transition shadow-lg text-xs flex items-center gap-2 flex-shrink-0">
                            <span data-i18n="belajar.detail.take_quiz_now">Kerjakan Kuis Sekarang</span> <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="glassmorphism rounded-3xl p-6 md:p-8 border border-white/60 bg-white/40 text-center">
                        <p class="text-xs text-slate-400 italic" data-i18n="siswa.materi.no_quiz_category">Kuis untuk kategori materi ini belum tersedia. Silakan hubungi guru Anda.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Syntax Highlighting untuk Code / Source Code -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();

            // Syntax highlighting pada semua blok code materi
            if (window.hljs) {
                document.querySelectorAll('#materiArticle .code-block code').forEach((block) => {
                    hljs.highlightElement(block);
                });
            }

            // Tombol "Copy Code"
            document.querySelectorAll('.code-block__copy').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-copy-target');
                    const codeEl = document.getElementById(targetId);
                    if (!codeEl) return;
                    navigator.clipboard.writeText(codeEl.innerText).then(() => {
                        const label = btn.querySelector('span');
                        const originalText = label ? label.textContent : '';
                        btn.classList.add('is-copied');
                        if (label) label.textContent = window.EduCareI18n?.t('belajar.detail.copied') || 'Copied!';
                        setTimeout(() => {
                            btn.classList.remove('is-copied');
                            if (label) label.textContent = originalText;
                        }, 1600);
                    });
                });
            });

            // Reading progress bar berdasarkan scroll posisi artikel materi.
            // Saat pengguna scroll sampai akhir artikel, tandai materi "Selesai
            // Dipelajari" (progress=100) melalui endpoint mark-materi.php.
            const progressBar = document.getElementById('readingProgressBar');
            const article = document.getElementById('materiArticle');
            let markedDone = false;
            const materyCategory = <?= htmlspecialchars(json_encode($m['category'] ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>;
            if (progressBar && article && materyCategory) {
                const markCompleted = () => {
                    if (markedDone) return;
                    markedDone = true;
                    const body = new URLSearchParams();
                    body.append('category', materyCategory);
                    fetch('../dashboard-siswa/mark-materi.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: body.toString()
                    }).catch(() => {});
                };
                const updateProgress = () => {
                    const rect = article.getBoundingClientRect();
                    const articleTop = rect.top + window.scrollY;
                    const articleHeight = article.offsetHeight;
                    const scrolled = window.scrollY - articleTop + window.innerHeight * 0.4;
                    const percent = Math.max(0, Math.min(100, (scrolled / articleHeight) * 100));
                    progressBar.style.width = percent + '%';
                    if (percent >= 100) markCompleted();
                };
                window.addEventListener('scroll', updateProgress, {
                    passive: true
                });
                updateProgress();
            }
        });
    </script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>