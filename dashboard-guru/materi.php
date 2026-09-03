<?php
require_once __DIR__ . '/../function.php';
requireRole(['guru']);

$pageTitle = 'Kelola Materi - EduCare';
$materiFile = 'materi.json';
$kategoriFile = 'kategori.json';

$materiList = readJSON($materiFile);
$categories = readJSON($kategoriFile);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit;
    }
    if (isset($_POST['add'])) {
        // Simpan content dalam bentuk mentah (raw) — bukan hasil sanitize()
        // yang meng-htmlspecialchars. Escape dilakukan SATU KALI saat
        // dirender (renderMateriContent / escMateri). Ini konsisten dengan
        // penyimpanan di dashboard utama (dashboard-guru/index.php) dan
        // isi data seed materi.json.
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        $emoji = trim($_POST['emoji'] ?? '') ?: '📘';
        $color = trim($_POST['color'] ?? '') ?: '#1e293b';

        // Slug kategori supaya konsisten dengan katalog Kursus (courses.json / guruCourses)
        $catSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '', $category)) ?: 'umum';

        if (empty($title) || empty($category) || empty($content)) {
            setFlashMessage('error', 'msg.guru.materi.required');
        } else {
            $newMateri = [
                'title' => $title,
                'category' => $category,
                'cat' => $catSlug,
                'emoji' => $emoji,
                'desc' => mb_substr(strip_tags($content), 0, 150),
                'level' => 'beginner',
                'stars' => 4.8,
                'students' => 0,
                'modules' => 0,
                'color' => $color,
                'date' => date('Y-m-d'),
                'content' => $content,
                'video_url' => $video_url,
                'lessons' => []
            ];
            insertJSON($materiFile, $newMateri);

            // ---- Terhubung ke Siswa: notifikasi + log aktivitas ----
            $guruId   = $_SESSION['user']['id'] ?? 0;
            $guruName = $_SESSION['user']['nama'] ?? 'Guru';
            addActivity((int) $guruId, $guruName, 'guru', '📘', "Menambahkan materi \"$title\"");
            addNotification('siswa', '📚', 'Materi Baru', "Materi baru tersedia: $title");

            setFlashMessage('success', 'msg.guru.materi.add_success');
        }
        header('Location: materi.php');
        exit;
    }

    if (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        $emoji = trim($_POST['emoji'] ?? '') ?: '📘';
        $color = trim($_POST['color'] ?? '') ?: '#1e293b';

        if (empty($title) || empty($category) || empty($content)) {
            setFlashMessage('error', 'msg.guru.materi.required');
        } else {
            $catSlugEdit = strtolower(preg_replace('/[^a-z0-9]+/i', '', $category)) ?: 'umum';
            $updateData = [
                'title' => $title,
                'category' => $category,
                'cat' => $catSlugEdit,
                'desc' => mb_substr(strip_tags($content), 0, 150),
                'content' => $content,
                'video_url' => $video_url,
                'emoji' => $emoji,
                'color' => $color
            ];
            updateJSON($materiFile, $id, $updateData);
            setFlashMessage('success', 'msg.guru.materi.update_success');
        }
        header('Location: materi.php');
        exit;
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        deleteJSON($materiFile, $id);
        setFlashMessage('success', 'msg.guru.materi.delete_success');
        header('Location: materi.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen flex" x-data="{
    showAddModal: false,
    showEditModal: false,
    showDeleteModal: false,
    editId: null,
    editTitle: '',
    editCategory: '',
    editContent: '',
    editVideoUrl: '',
    editEmoji: '',
    editColor: '',
    deleteId: null,
    deleteTitle: '',
    search: '',
    catFilter: 'all',
    newCategory: '',
    categoryGroupMap: <?= htmlspecialchars(json_encode(array_column($categories, 'group', 'name'), JSON_UNESCAPED_UNICODE)) ?>,
    isCodeSubject(catName) {
        return this.categoryGroupMap[catName] === 'it';
    },
    openEdit(m) {
        this.editId = m.id;
        this.editTitle = m.title;
        this.editCategory = m.category;
        this.editContent = m.content || '';
        this.editVideoUrl = m.video_url || '';
        this.editEmoji = m.emoji || '📘';
        this.editColor = m.color || '#1e293b';
        this.showEditModal = true;
    },
    openDelete(m) {
        this.deleteId = m.id;
        this.deleteTitle = m.title;
        this.showDeleteModal = true;
    }
}">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <!-- Topbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight" data-i18n="guru.materi.title">Kelola Materi</h2>
                <p class="text-sm text-slate-500 mt-1" data-i18n="guru.materi.subtitle">Buat, perbarui, dan hapus materi ajar interaktif untuk siswa.</p>
            </div>

            <div class="flex items-center gap-3 self-end md:self-auto">
                <button @click="showAddModal = true" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs flex items-center gap-2">
                    <i data-lucide="book-open" class="w-4 h-4"></i> <span data-i18n="guru.materi.add_materi">Tambah Materi</span>
                </button>
            </div>
        </div>

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

        <!-- Search and Filter Bar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="md:col-span-2 glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                <i data-lucide="search" class="text-slate-400 w-5 h-5"></i>
                <input type="text" x-model="search" placeholder="Cari judul materi..." data-i18n-placeholder="guru.materi.search_placeholder" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full placeholder:text-slate-400">
            </div>

            <div class="glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                <i data-lucide="filter" class="text-slate-400 w-5 h-5"></i>
                <select x-model="catFilter" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full">
                    <option value="all" data-i18n="guru.materi.all_categories">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Materi Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="m in <?= htmlspecialchars(json_encode($materiList)) ?>.filter(mat => {
                const matchSearch = mat.title.toLowerCase().includes(search.toLowerCase());
                const matchCat = catFilter === 'all' || (mat.category || mat.cat) === catFilter;
                return matchSearch && matchCat;
            })" :key="m.id">
                <div class="glassmorphism rounded-3xl overflow-hidden border border-white/60 shadow-xs flex flex-col justify-between hover:shadow-lg transition-all duration-300 bg-white/40 group">
                    <div class="relative h-44 overflow-hidden flex items-center justify-center" :style="'background: linear-gradient(160deg,' + (m.color || '#1e293b') + ' 0%, #0a0a0a 100%)'">
                        <span class="text-6xl drop-shadow-lg group-hover:scale-110 transition-transform duration-500" x-text="m.emoji || '📘'"></span>
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-600 text-white shadow-md" x-text="m.category || m.cat"></span>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-mono-tech" x-text="'Dibuat: ' + m.date"></span>
                            <h4 class="text-base font-bold text-slate-800 mt-1 line-clamp-2" x-text="m.title"></h4>
                            <p class="text-xs text-slate-500 mt-2.5 line-clamp-3 leading-relaxed" x-text="m.content || m.desc"></p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100/50 flex items-center justify-between">
                            <span class="text-[10px] inline-flex items-center gap-1 text-blue-600 font-semibold" x-show="m.video_url">
                                <i data-lucide="video" class="w-3.5 h-3.5"></i> <span data-i18n="guru.materi.video_active">Video Aktif</span>
                            </span>
                            <span class="text-[10px] text-slate-400" x-show="!m.video_url" data-i18n="guru.materi.text_only">Teks Saja</span>

                            <div class="flex items-center gap-2">
                                <button @click="openEdit(m)" class="p-2 rounded-xl bg-white text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-100 transition" data-i18n-title="guru.materi.edit_modal_title" title="Edit Materi">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <button @click="openDelete(m)" class="p-2 rounded-xl bg-white text-red-600 hover:bg-red-50 border border-slate-100 transition" data-i18n-title="guru.materi.delete_confirm_title" title="Hapus Materi">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="<?= count($materiList) ?> === 0" class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4"><i data-lucide="book-x" class="w-10 h-10"></i></div>
            <div class="font-bold text-slate-800" data-i18n="guru.materi.empty_title">Belum Ada Materi</div>
            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed" data-i18n="guru.materi.empty_desc">Materi pembelajaran digital belum tersedia. Silakan klik tombol "Tambah Materi" untuk mengunggah.</p>
        </div>
    </main>
</div>

<!-- ADD MODAL -->
<div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-lg w-full shadow-2xl border border-slate-100 overflow-y-auto max-h-[90vh]" @click.away="showAddModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="book-open" class="text-blue-600"></i> <span data-i18n="guru.materi.add_modal_title">Tambah Materi Baru</span></h3>
            <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="add" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.materi.title_label">Judul Materi</label>
                    <input type="text" name="title" required placeholder="Contoh: Dasar HTML & Tag" data-i18n-placeholder="guru.materi.title_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.materi.category_label">Kategori</label>
                    <select name="category" x-model="newCategory" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                        <option value="" data-i18n="guru.materi.select_category">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.materi.card_emoji_label">Emoji Kartu</span> <span class="text-slate-400 font-normal" data-i18n="guru.materi.optional">(Opsional)</span></label>
                    <input type="text" name="emoji" maxlength="4" placeholder="📘" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.materi.card_color_label">Warna Kartu</span> <span class="text-slate-400 font-normal" data-i18n="guru.materi.optional">(Opsional)</span></label>
                    <input type="color" name="color" value="#1e293b" class="w-full h-11 px-2 py-1 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition bg-slate-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.materi.content_label">Isi Materi Pembelajaran</label>
                <textarea name="content" required :placeholder="isCodeSubject(newCategory) ? '## Pengertian\nTuliskan penjelasan di sini...\n\n## Contoh\n## Code\n\`\`\`html\n<h1>Contoh</h1>\n\`\`\`\n\n## Kesimpulan' : '## Pengertian\nTuliskan penjelasan di sini...\n\n## Contoh Soal\nTuliskan contoh soal...\n\n## Penyelesaian\nTuliskan langkah penyelesaian...\n\n## Kesimpulan'" rows="10" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 font-mono-tech"></textarea>
                <p class="text-[10px] text-slate-400 mt-1.5 leading-relaxed" x-show="isCodeSubject(newCategory)" data-i18n-html="guru.materi.helper_code_subject">Gunakan heading <code class="materi-inline-code">## Judul Bagian</code> untuk memisahkan bagian (contoh: Pengertian, Tujuan Pembelajaran, Contoh, Kesimpulan), dan blok <code class="materi-inline-code">&#x60;&#x60;&#x60;html ... &#x60;&#x60;&#x60;</code> untuk kode yang otomatis diberi syntax highlighting. Materi tanpa format ini tetap bisa disimpan &mdash; akan ditampilkan sebagai paragraf biasa.</p>
                <p class="text-[10px] text-slate-400 mt-1.5 leading-relaxed" x-show="!isCodeSubject(newCategory)" data-i18n-html="guru.materi.helper_general_subject">Kategori ini adalah mapel Umum (bukan IT/Pemrograman), jadi <strong>tidak perlu</strong> menyertakan blok kode. Gunakan heading <code class="materi-inline-code">## Judul Bagian</code> untuk memisahkan bagian seperti Pengertian, Contoh Soal, Penyelesaian, dan Kesimpulan &mdash; materi akan ditampilkan sebagai teks/paragraf biasa.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.materi.video_label">Tautan Video Pembelajaran</span> <span class="text-slate-400 font-normal" data-i18n="guru.materi.optional">(Opsional)</span></label>
                <input type="url" name="video_url" placeholder="Contoh: https://www.youtube.com/watch?v=..." data-i18n-placeholder="guru.materi.video_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showAddModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.materi.upload">Unggah</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-lg w-full shadow-2xl border border-slate-100 overflow-y-auto max-h-[90vh]" @click.away="showEditModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="edit" class="text-blue-600"></i> <span data-i18n="guru.materi.edit_modal_title">Edit Materi</span></h3>
            <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="edit" value="1">
            <input type="hidden" name="id" :value="editId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.materi.title_label">Judul Materi</label>
                    <input type="text" name="title" x-model="editTitle" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.materi.category_label">Kategori</label>
                    <select name="category" x-model="editCategory" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.materi.card_emoji_label">Emoji Kartu</span></label>
                    <input type="text" name="emoji" x-model="editEmoji" maxlength="4" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.materi.card_color_label">Warna Kartu</span></label>
                    <input type="color" name="color" x-model="editColor" class="w-full h-11 px-2 py-1 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition bg-slate-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.materi.content_label">Isi Materi Pembelajaran</label>
                <textarea name="content" x-model="editContent" required rows="10" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 font-mono-tech"></textarea>
                <p class="text-[10px] text-slate-400 mt-1.5 leading-relaxed" x-show="isCodeSubject(editCategory)" data-i18n-html="guru.materi.edit_helper_code_subject">Gunakan heading <code class="materi-inline-code">## Judul Bagian</code> untuk memisahkan bagian, dan blok <code class="materi-inline-code">&#x60;&#x60;&#x60;html ... &#x60;&#x60;&#x60;</code> untuk kode.</p>
                <p class="text-[10px] text-slate-400 mt-1.5 leading-relaxed" x-show="!isCodeSubject(editCategory)" data-i18n-html="guru.materi.edit_helper_general_subject">Kategori ini adalah mapel Umum (bukan IT/Pemrograman) &mdash; sebaiknya isi materi tanpa blok kode, cukup heading <code class="materi-inline-code">## Judul Bagian</code> dan penjelasan/contoh soal dalam bentuk teks biasa.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.materi.video_label">Tautan Video Pembelajaran</span> <span class="text-slate-400 font-normal" data-i18n="guru.materi.optional">(Opsional)</span></label>
                <input type="url" name="video_url" x-model="editVideoUrl" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showEditModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.dynamic.save_changes">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-sm w-full shadow-2xl border border-slate-100 text-center" @click.away="showDeleteModal = false">
        <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4"><i data-lucide="trash-2" class="w-8 h-8"></i></div>
        <h3 class="text-lg font-bold text-slate-900" data-i18n="guru.materi.delete_confirm_title">Hapus Materi?</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed"><span data-i18n="guru.materi.delete_confirm_desc">Apakah Anda yakin ingin menghapus materi</span> <span class="font-bold text-slate-800" x-text="deleteTitle"></span>? <span data-i18n="guru.materi.delete_confirm_warning">Tindakan ini tidak dapat dibatalkan.</span></p>

        <form action="" method="POST" class="mt-6 flex justify-center gap-3">
            <?= csrfField() ?>
            <input type="hidden" name="delete" value="1">
            <input type="hidden" name="id" :value="deleteId">
            <button type="button" @click="showDeleteModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
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