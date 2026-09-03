<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['user']['role'] ?? 'siswa';
$name = htmlspecialchars($_SESSION['user']['nama'] ?? 'Pengguna');
$currentPage = basename($_SERVER['PHP_SELF']);

// Deteksi base URL secara dinamis
$baseUrlManuallySet = defined('BASE_URL') && BASE_URL !== '/';
$baseUrl = '/';
if (defined('BASE_URL')) {
    $baseUrl = rtrim(BASE_URL, '/') . '/';
}
if (!$baseUrlManuallySet) {
    $scriptFilename  = realpath($_SERVER['SCRIPT_FILENAME'] ?? '');
    $projectRootPath = realpath(dirname(__DIR__));
    $rawRequestPath  = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

    if ($scriptFilename && $projectRootPath && str_starts_with($scriptFilename, $projectRootPath)) {
        $relativePath = substr($scriptFilename, strlen($projectRootPath));
        if ($relativePath !== '' && str_ends_with($rawRequestPath, $relativePath)) {
            $computedBase = substr($rawRequestPath, 0, -strlen($relativePath));
            $baseUrl = ($computedBase === '' ? '/' : rtrim($computedBase, '/') . '/');
        }
    }
}
?>
<aside id="sidebar" class="w-72 bg-white/60 glass border-r border-gray-200 p-6 transition-all duration-300 hidden md:block flex-shrink-0">
    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-display font-extrabold text-lg shadow-md shadow-blue-500/20">E</div>
        <div>
            <div class="font-bold text-lg text-slate-900 tracking-tight">EduCare</div>
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Platform Sekolah</div>
        </div>
    </div>

    <nav class="space-y-6 text-sm">
        <?php if ($role === 'guru'): ?>
            <!-- MENU GURU -->
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3" data-i18n="guru.sidebar.section_utama">Menu Utama</div>
                <ul class="space-y-1.5">
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-guru/index.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'index.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="home" class="w-4 h-4"></i> <span data-i18n="guru.sidebar.nav_dashboard">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-guru/user.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'user.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="users" class="w-4 h-4"></i> <span data-i18n="guru.sidebar.nav_manage_users">Kelola User</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-guru/kategori.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'kategori.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="folder-open" class="w-4 h-4"></i> <span data-i18n="guru.sidebar.nav_kategori">Kategori Materi</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-guru/materi.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'materi.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="book-open" class="w-4 h-4"></i> <span data-i18n="guru.sidebar.nav_materi">Kelola Materi</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-guru/quiz.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'quiz.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="file-question" class="w-4 h-4"></i> <span data-i18n="guru.sidebar.nav_quiz">Kelola Quiz</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3" data-i18n="guru.sidebar.section_layanan">Layanan</div>
                <ul class="space-y-1.5">
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-guru/laporan.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'laporan.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="inbox" class="w-4 h-4"></i> <span data-i18n="guru.sidebar.nav_laporan_masuk">Laporan Masuk</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3" data-i18n="guru.sidebar.section_akun">Akun</div>
                <ul class="space-y-1.5">
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-guru/pengaturan.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'pengaturan.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="settings" class="w-4 h-4"></i> <span data-i18n="guru.sidebar.nav_pengaturan">Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </div>

        <?php else: ?>
            <!-- MENU SISWA -->
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3" data-i18n="siswa.sidebar.section_utama">Menu Utama</div>
                <ul class="space-y-1.5">
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-siswa/index.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'index.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="home" class="w-4 h-4"></i> <span data-i18n="siswa.sidebar.nav_dashboard">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3">Akademik</div>
                <ul class="space-y-1.5">
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>belajar/materi.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium <?= ($currentPage === 'materi.php' || $currentPage === 'detail-materi.php') ? 'bg-blue-50 text-blue-600 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i data-lucide="book" class="w-4 h-4"></i> <span data-i18n="siswa.sidebar.nav_materi">Materi Belajar</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-siswa/index.php#quiz" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium text-slate-600 hover:bg-slate-50">
                            <i data-lucide="file-text" class="w-4 h-4"></i> <span data-i18n="siswa.sidebar.nav_quiz">Quiz</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3" data-i18n="siswa.sidebar.section_layanan">Layanan</div>
                <ul class="space-y-1.5">
                    <li>
                        <a href="<?= htmlspecialchars($baseUrl) ?>dashboard-siswa/index.php#laporan" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium text-slate-600 hover:bg-slate-50">
                            <i data-lucide="edit-2" class="w-4 h-4"></i> <span data-i18n="siswa.sidebar.nav_laporan">Buat Laporan</span>
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="pt-4 border-t border-gray-100">
            <a href="<?= htmlspecialchars($baseUrl) ?>auth/logout.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition font-medium text-red-600 hover:bg-red-50">
                <i data-lucide="log-out" class="w-4 h-4"></i> <span data-i18n="nav.logout">Logout</span>
            </a>
        </div>
    </nav>
</aside>