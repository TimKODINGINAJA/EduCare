<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../translation.php';
requireRole(['siswa']);

$pageTitle = 'Portal Belajar - EduCare';
$materiList = readJSON('materi.json');
$categories = readJSON('kategori.json');
$name = htmlspecialchars($_SESSION['user']['nama'] ?? 'Siswa');

// Petakan setiap materi ke grup kategorinya (IT / Matematika / Umum) untuk filter tab
$categoryGroupMap = [];
foreach ($categories as $cat) {
    $categoryGroupMap[$cat['name']] = getCategoryGroup($cat['name'], $categories);
}
foreach ($materiList as &$mItem) {
    $mItem['group'] = $categoryGroupMap[$mItem['category']] ?? getCategoryGroup($mItem['category'], $categories);
    $mItem['title'] = t_dynamic($mItem['title'], 'materi.' . $mItem['id'] . '.title');
    $mItem['desc']  = t_dynamic($mItem['desc'] ?? '', 'materi.' . $mItem['id'] . '.desc');
}
unset($mItem);

// Ambil progres siswa yang sedang login untuk menampilkan badge "Selesai"
$myProgress = [];
$progressAll = readJSON('progress.json');
foreach ($progressAll as $p) {
    if (isset($p['user_id']) && $p['user_id'] == ($_SESSION['user']['id'] ?? 0)) {
        $myProgress = $p['progress'] ?? [];
        break;
    }
}
foreach ($materiList as &$mItem) {
    $mItem['done'] = (($myProgress[$mItem['category']] ?? 0) >= 100);
}
unset($mItem);

$groupTabs = [
    'all' => 'Semua Materi',
    'it'  => 'IT / Teknologi',
    'mtk' => 'Matematika',
    'umum' => 'Umum',
];
$groupTabKeys = [
    'all' => 'siswa.materi.tab_all',
    'it'  => 'siswa.materi.tab_it',
    'mtk' => 'belajar.materi.tab_mtk',
    'umum' => 'siswa.materi.tab_umum',
];

include __DIR__ . '/../includes/header.php';
?>

    <div class="min-h-screen flex" x-data="{
    search: '',
    catFilter: 'all',
    groupFilter: 'all'
    }">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <!-- Topbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight" data-i18n="belajar.materi.page_title">Materi Pembelajaran</h2>
                <p class="text-sm text-slate-500 mt-1" data-i18n="belajar.materi.page_subtitle">Jelajahi materi, tonton modul video, dan selesaikan quiz untuk menguji pemahamanmu.</p>
            </div>
            
            <div class="flex items-center gap-3 bg-white border border-slate-100 rounded-2xl px-4 py-2.5 shadow-xs self-end md:self-auto">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($name) ?>&background=2563EB&color=fff" alt="avatar" class="w-8 h-8 rounded-full">
                <div class="text-xs">
                    <div class="font-bold text-slate-800"><?= $name ?></div>
                    <div class="text-slate-400" data-i18n="belajar.materi.class_label">Kelas 9A • Siswa</div>
                </div>
            </div>
        </div>

        <!-- Group Tabs (IT / Matematika / Umum) -->
        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
            <?php foreach ($groupTabs as $key => $label): ?>
                <button type="button" @click="groupFilter = '<?= $key ?>'" class="group-tab" :class="{ 'is-active': groupFilter === '<?= $key ?>' }" data-i18n="<?= $groupTabKeys[$key] ?? '' ?>"><?= htmlspecialchars($label) ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Search and Filter Bar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="md:col-span-2 glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3 bg-white/40">
                <i data-lucide="search" class="text-slate-400 w-5 h-5"></i>
                <input type="text" x-model="search" placeholder="Cari materi..." data-i18n-placeholder="belajar.materi.search_ph" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full placeholder:text-slate-400">
            </div>
            
            <div class="glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3 bg-white/40">
                <i data-lucide="filter" class="text-slate-400 w-5 h-5"></i>
                <select x-model="catFilter" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full text-slate-600">
                    <option value="all" data-i18n="belajar.materi.all_categories">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Materi Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="m in <?= htmlspecialchars(json_encode($materiList)) ?>.filter(mat => {
                const matchSearch = mat.title.toLowerCase().includes(search.toLowerCase());
                const matchCat = catFilter === 'all' || mat.category === catFilter;
                const matchGroup = groupFilter === 'all' || mat.group === groupFilter;
                return matchSearch && matchCat && matchGroup;
            })" :key="m.id">
                <div class="relative glassmorphism rounded-3xl overflow-hidden border border-white/60 shadow-xs flex flex-col justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-300 bg-white/40 group">
                    <div class="relative h-44 overflow-hidden flex items-center justify-center" :style="'background: linear-gradient(160deg,' + (m.color || '#1e293b') + ' 0%, #0a0a0a 100%)'">
                        <span class="text-6xl drop-shadow-lg group-hover:scale-110 transition-transform duration-500" x-text="m.emoji || '📘'"></span>
                        <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-600 text-white shadow-md" x-text="m.category"></span>
                        <span class="materi-done-badge" x-show="m.done"><i data-lucide="check" class="w-3 h-3"></i> <span data-i18n="siswa.dynamic.completed">Selesai</span></span>
                    </div>
                    
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-mono-tech" x-text="(window.EduCareI18n?.t('siswa.chrome.materi.published', {date: m.date}) || 'Diterbitkan: ' + m.date)"></span>
                            <h4 class="text-base font-bold text-slate-800 mt-1 line-clamp-2" x-text="m.title"></h4>
                            <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed" x-text="m.desc || m.content"></p>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-slate-100/50 flex items-center justify-between">
                            <span class="text-[10px] inline-flex items-center gap-1 text-blue-600 font-semibold" x-show="m.video_url">
                                <i data-lucide="video" class="w-3.5 h-3.5"></i> <span data-i18n="belajar.materi.has_video">Ada Video</span>
                            </span>
                            <span class="text-[10px] text-slate-400" x-show="!m.video_url"><span data-i18n="belajar.materi.text_module">Modul Teks</span></span>
                            
                            <a :href="'detail-materi.php?id=' + m.id" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold transition shadow-md shadow-blue-500/10 flex items-center gap-1">
                                <span data-i18n="belajar.materi.start_learning">Mulai Belajar</span> <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="<?= count($materiList) ?> === 0" class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4"><i data-lucide="book-open" class="w-10 h-10"></i></div>
            <div class="font-bold text-slate-800" data-i18n="belajar.materi.empty_title">Materi Belajar Kosong</div>
            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed" data-i18n="belajar.materi.empty_desc">Belum ada materi pelajaran yang diunggah oleh guru saat ini.</p>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
