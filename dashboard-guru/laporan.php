<?php
require_once __DIR__ . '/../function.php';
requireRole(['guru']);

$pageTitle = 'Laporan Masuk (SiLapor) - EduCare';
$pageHeadCSS = <<<'CSS'
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    #sidebar, .no-print {
        display: none !important;
    }
    main {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    .print-card {
        border: 1px solid #e2e8f0 !important;
        box-shadow: none !important;
        background: transparent !important;
        page-break-inside: avoid;
    }
}
CSS;
$file = 'reports.json';
$reports = readJSON($file);

// Handle Export CSV
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan-silapor-' . date('Ymd-His') . '.csv');
    
    $output = fopen('php://output', 'w');
    // Tambahkan BOM untuk UTF-8 MS Excel agar terbaca dengan baik
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header CSV
    fputcsv($output, ['ID', 'Judul Laporan', 'Pelapor', 'Kategori', 'Status', 'Tanggal', 'Deskripsi']);
    
    foreach ($reports as $r) {
        // Cegah CSV formula injection: awali sel dengan ' bila diawali
        // oleh karakter berbahaya (=, +, -, @, tab, CR, LF).
        $csvSafe = function ($v) {
            $v = (string) $v;
            if ($v !== '' && in_array($v[0], ['=', '+', '-', '@', "\t", "\r", "\n"], true)) {
                $v = "'" . $v;
            }
            return $v;
        };
        fputcsv($output, [
            $csvSafe($r['id'] ?? ''),
            $csvSafe($r['title'] ?? ''),
            $csvSafe($r['by_name'] ?? ''),
            $csvSafe($r['category'] ?? ''),
            $csvSafe($r['status'] ?? ''),
            $csvSafe($r['date'] ?? ''),
            $csvSafe($r['desc'] ?? '')
        ]);
    }
    
    fclose($output);
    exit;
}

// Handle Update Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!csrfVerify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit;
    }
    $id = (int)$_POST['id'];
    $status = sanitize($_POST['status']);
    
    if (in_array($status, ['Menunggu', 'Diproses', 'Selesai'])) {
        updateJSON($file, $id, ['status' => $status]);
        setFlashMessage('success', 'msg.guru.laporan.status_updated');
    } else {
        setFlashMessage('error', 'msg.guru.laporan.invalid_status');
    }
    header('Location: laporan.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>


<div class="min-h-screen flex" x-data="{
    showStatusModal: false,
    selectedId: null,
    selectedTitle: '',
    selectedStatus: '',
    search: '',
    statusFilter: 'all',
    openStatusModal(r) {
        this.selectedId = r.id;
        this.selectedTitle = r.title;
        this.selectedStatus = r.status;
        this.showStatusModal = true;
    }
}">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <!-- Topbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight" data-i18n="guru.laporan.title">Laporan Pengaduan Siswa (SiLapor)</h2>
                <p class="text-sm text-slate-500 mt-1" data-i18n="guru.laporan.subtitle">Tindak lanjuti laporan, keluhan, dan saran fasilitas dari siswa secara real-time.</p>
            </div>
            
            <div class="flex items-center gap-2 self-end md:self-auto no-print">
                <button onclick="window.print()" class="px-4 py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition text-xs flex items-center gap-2 shadow-xs">
                    <i data-lucide="printer" class="w-4 h-4"></i> <span data-i18n="guru.laporan.print_pdf">Cetak PDF</span>
                </button>
                <a href="laporan.php?action=export_csv" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i> <span data-i18n="guru.laporan.export_csv">Ekspor CSV</span>
                </a>
            </div>
        </div>

        <!-- Alert Notification -->
        <?php if ($msg = getFlashMessage('success')): ?>
            <div class="mb-6 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-2xl text-xs flex justify-between items-center shadow-xs no-print animate-fadeIn">
<div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                <span data-i18n="<?= htmlspecialchars($msg) ?>"><?= htmlspecialchars($msg) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($msg = getFlashMessage('error')): ?>
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-2xl text-xs flex justify-between items-center shadow-xs no-print animate-fadeIn">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
                <span data-i18n="<?= htmlspecialchars($msg) ?>"><?= htmlspecialchars($msg) ?></span>
            </div>
        </div>
    <?php endif; ?>

        <!-- Search and Filter Bar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 no-print">
            <div class="md:col-span-2 glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                <i data-lucide="search" class="text-slate-400 w-5 h-5"></i>
                <input type="text" x-model="search" placeholder="Cari judul, pelapor, atau isi deskripsi..." data-i18n-placeholder="guru.laporan.search_placeholder" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full placeholder:text-slate-400">
            </div>
            
            <div class="glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                <i data-lucide="filter" class="text-slate-400 w-5 h-5"></i>
                <select x-model="statusFilter" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full text-slate-600">
                    <option value="all" data-i18n="guru.laporan.all_status">Semua Status</option>
                    <option value="Menunggu" data-i18n="guru.laporan.status_pending">Menunggu</option>
                    <option value="Diproses" data-i18n="guru.laporan.status_process">Diproses</option>
                    <option value="Selesai" data-i18n="guru.laporan.status_done">Selesai</option>
                </select>
            </div>
        </div>

        <!-- Reports List Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <template x-for="r in <?= htmlspecialchars(json_encode($reports)) ?>.filter(rep => {
                const matchSearch = rep.title.toLowerCase().includes(search.toLowerCase()) || 
                                    rep.by_name.toLowerCase().includes(search.toLowerCase()) || 
                                    (rep.desc && rep.desc.toLowerCase().includes(search.toLowerCase()));
                const matchStatus = statusFilter === 'all' || rep.status === statusFilter;
                return matchSearch && matchStatus;
            })" :key="r.id">
                <div class="glassmorphism rounded-3xl p-6 border border-white/60 shadow-xs flex flex-col justify-between hover:shadow-lg transition-all duration-300 bg-white/40 print-card">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                  :class="{
                                      'bg-amber-100 text-amber-800 ring-1 ring-amber-600/10': r.status === 'Menunggu',
                                      'bg-blue-100 text-blue-800 ring-1 ring-blue-600/10': r.status === 'Diproses',
                                      'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-600/10': r.status === 'Selesai'
                                  }"
                                  x-text="r.status"></span>
                            
                            <span class="text-[10px] text-slate-400 font-mono-tech" x-text="r.date"></span>
                        </div>
                        
                        <h4 class="text-base font-bold text-slate-900" x-text="r.title"></h4>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed" x-text="r.desc"></p>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100/50 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[10px] text-slate-400">
                            <div>
                                <span class="block text-slate-500 font-semibold" data-i18n="guru.dynamic.reporter_label">Pelapor:</span>
                                <span x-text="r.by_name"></span>
                            </div>
                            <div>
                                <span class="block text-slate-500 font-semibold" data-i18n="guru.laporan.category_label">Kategori:</span>
                                <span x-text="r.category || 'Umum'"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end no-print">
                        <button @click="openStatusModal(r)" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-semibold rounded-xl text-xs hover:bg-slate-100 hover:border-slate-300 transition flex items-center gap-1.5 shadow-xs">
                            <i data-lucide="check-square" class="w-3.5 h-3.5 text-blue-600"></i> <span data-i18n="guru.laporan.follow_up">Tindak Lanjut</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="<?= count($reports) ?> === 0" class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4"><i data-lucide="inbox" class="w-10 h-10"></i></div>
            <div class="font-bold text-slate-800" data-i18n="guru.laporan.empty_title">Tidak Ada Laporan</div>
            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed" data-i18n="guru.laporan.empty_desc">Belum ada laporan pengaduan masuk dari siswa saat ini.</p>
        </div>
    </main>
</div>

<!-- CHANGE STATUS MODAL -->
<div x-show="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 no-print" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100" @click.away="showStatusModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="check-square" class="text-blue-600"></i> <span data-i18n="guru.laporan.follow_up_modal_title">Tindak Lanjut Laporan</span></h3>
            <button @click="showStatusModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        
        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="update_status" value="1">
            <input type="hidden" name="id" :value="selectedId">
            
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.laporan.report_title_label">Judul Laporan</label>
                <div class="p-3 bg-slate-50 rounded-xl text-xs font-semibold text-slate-800" x-text="selectedTitle"></div>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.laporan.change_status_label">Ubah Status Laporan</label>
                <select name="status" x-model="selectedStatus" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700 font-semibold">
                    <option value="Menunggu" data-i18n="guru.laporan.status_pending">Menunggu</option>
                    <option value="Diproses" data-i18n="guru.laporan.status_process">Diproses</option>
                    <option value="Selesai" data-i18n="guru.laporan.status_done">Selesai</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showStatusModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.laporan.update_status_button">Perbarui Status</button>
            </div>
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
