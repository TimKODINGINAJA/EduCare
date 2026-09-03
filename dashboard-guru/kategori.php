<?php
require_once __DIR__ . '/../function.php';
requireRole(['guru']);

$pageTitle = 'Kelola Kategori - EduCare';
$file = 'kategori.json';

// Initialize defaults if file empty
$categories = readJSON($file);
if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'HTML', 'desc' => 'Struktur Halaman Web'],
        ['id' => 2, 'name' => 'CSS', 'desc' => 'Desain Gaya Halaman Web'],
        ['id' => 3, 'name' => 'JavaScript', 'desc' => 'Interaktivitas Sisi Klien'],
        ['id' => 4, 'name' => 'PHP', 'desc' => 'Pemrograman Sisi Server native']
    ];
    writeJSON($file, $categories);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit;
    }
    if (isset($_POST['add'])) {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['desc'] ?? '');
        $group = sanitize($_POST['group'] ?? '');
        if (!in_array($group, ['it', 'mtk', 'umum'], true)) {
            $group = getCategoryGroup($name);
        }
        if (!empty($name)) {
            insertJSON($file, ['name' => $name, 'desc' => $desc, 'group' => $group]);
            setFlashMessage('success', 'msg.guru.kategori.add_success');
        } else {
            setFlashMessage('error', 'msg.guru.kategori.name_required');
        }
        header('Location: kategori.php');
        exit;
    }

    if (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['desc'] ?? '');
        $group = sanitize($_POST['group'] ?? '');
        if (!in_array($group, ['it', 'mtk', 'umum'], true)) {
            $group = getCategoryGroup($name);
        }
        if (!empty($name)) {
            updateJSON($file, $id, ['name' => $name, 'desc' => $desc, 'group' => $group]);
            setFlashMessage('success', 'msg.guru.kategori.update_success');
        } else {
            setFlashMessage('error', 'msg.guru.kategori.name_required');
        }
        header('Location: kategori.php');
        exit;
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        deleteJSON($file, $id);
        setFlashMessage('success', 'msg.guru.kategori.delete_success');
        header('Location: kategori.php');
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
    editName: '',
    editDesc: '',
    editGroup: 'it',
    deleteId: null,
    deleteName: '',
    search: '',
    openEdit(cat) {
        this.editId = cat.id;
        this.editName = cat.name;
        this.editDesc = cat.desc;
        this.editGroup = cat.group || 'it';
        this.showEditModal = true;
    },
    openDelete(cat) {
        this.deleteId = cat.id;
        this.deleteName = cat.name;
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
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight" data-i18n="guru.sidebar.nav_kategori">Kategori Materi</h2>
                <p class="text-sm text-slate-500 mt-1" data-i18n="guru.kategori.subtitle">Kelola kategori mata pelajaran untuk struktur pembelajaran.</p>
            </div>

            <div class="flex items-center gap-3 self-end md:self-auto">
                <button @click="showAddModal = true" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> <span data-i18n="guru.kategori.add_kategori">Tambah Kategori</span>
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

        <!-- Search Bar -->
        <div class="mb-6 glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
            <i data-lucide="search" class="text-slate-400 w-5 h-5"></i>
            <input type="text" x-model="search" placeholder="Cari nama kategori..." data-i18n-placeholder="guru.kategori.search_placeholder" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full placeholder:text-slate-400">
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="cat in <?= htmlspecialchars(json_encode($categories)) ?>.filter(c => c.name.toLowerCase().includes(search.toLowerCase()))" :key="cat.id">
                <div class="glassmorphism rounded-3xl p-6 border border-white/60 shadow-xs flex flex-col justify-between hover:shadow-lg transition-all duration-300 bg-white/40 group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform"><i data-lucide="folder" class="w-5 h-5"></i></div>
                        <h4 class="text-lg font-bold text-slate-800" x-text="cat.name"></h4>
                        <span class="inline-block mt-2 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-slate-900 text-white" x-text="cat.group === 'mtk' ? 'Matematika' : (cat.group === 'umum' ? 'Umum' : 'IT / Teknologi')"></span>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed" x-text="cat.desc || 'Tidak ada deskripsi.'"></p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100/50 flex items-center justify-end gap-2">
                        <button @click="openEdit(cat)" class="p-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition" data-i18n-title="guru.kategori.edit_modal_title" title="Edit Kategori">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <button @click="openDelete(cat)" class="p-2 rounded-xl bg-slate-50 text-red-600 hover:bg-red-50 transition" data-i18n-title="guru.kategori.delete_confirm_title" title="Hapus Kategori">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="<?= count($categories) ?> === 0" class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4"><i data-lucide="folder-x" class="w-10 h-10"></i></div>
            <div class="font-bold text-slate-800" data-i18n="guru.kategori.empty_title">Belum Ada Kategori</div>
            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed" data-i18n="guru.kategori.empty_desc">Kategori baru belum ditambahkan. Silakan klik tombol "Tambah Kategori" di pojok kanan atas.</p>
        </div>
    </main>
</div>

<!-- ADD MODAL -->
<div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100" @click.away="showAddModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="plus-circle" class="text-blue-600"></i> <span data-i18n="guru.kategori.add_modal_title">Tambah Kategori</span></h3>
            <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="add" value="1">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.kategori.name_label">Nama Kategori</span></label>
                <input type="text" name="name" required placeholder="Contoh: Pemrograman Web" data-i18n-placeholder="guru.kategori.name_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.kategori.desc_label">Deskripsi</span></label>
                <textarea name="desc" placeholder="Tuliskan deskripsi singkat kategori ini..." data-i18n-placeholder="guru.kategori.desc_placeholder" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.kategori.group_label">Grup Kategori</span></label>
                <select name="group" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="it" data-i18n="guru.kategori.group_it">IT / Teknologi (menampilkan penjelasan + code + praktik)</option>
                    <option value="mtk" data-i18n="guru.kategori.group_mtk">Matematika (menampilkan rumus + contoh soal)</option>
                    <option value="umum" data-i18n="guru.kategori.group_umum">Umum (penjelasan biasa)</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1.5" data-i18n="guru.kategori.group_helper">Menentukan bagaimana materi pada kategori ini akan ditampilkan ke siswa.</p>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="showAddModal = false" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition text-xs" data-i18n="guru.dynamic.cancel">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs" data-i18n="guru.dynamic.save">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100" @click.away="showEditModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="edit" class="text-blue-600"></i> <span data-i18n="guru.kategori.edit_modal_title">Edit Kategori</span></h3>
            <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="edit" value="1">
            <input type="hidden" name="id" :value="editId">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.kategori.name_label">Nama Kategori</span></label>
                <input type="text" name="name" x-model="editName" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.kategori.desc_label">Deskripsi</span></label>
                <textarea name="desc" x-model="editDesc" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.kategori.group_label">Grup Kategori</span></label>
                <select name="group" x-model="editGroup" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="it" data-i18n="guru.kategori.group_it">IT / Teknologi (menampilkan penjelasan + code + praktik)</option>
                    <option value="mtk" data-i18n="guru.kategori.group_mtk">Matematika (menampilkan rumus + contoh soal)</option>
                    <option value="umum" data-i18n="guru.kategori.group_umum">Umum (penjelasan biasa)</option>
                </select>
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
        <h3 class="text-lg font-bold text-slate-900" data-i18n="guru.kategori.delete_confirm_title">Hapus Kategori?</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed"><span data-i18n="guru.kategori.delete_confirm_desc">Apakah Anda yakin ingin menghapus kategori</span> <span class="font-bold text-slate-800" x-text="deleteName"></span>? <span data-i18n="guru.kategori.delete_confirm_warning">Tindakan ini tidak dapat dibatalkan.</span></p>

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