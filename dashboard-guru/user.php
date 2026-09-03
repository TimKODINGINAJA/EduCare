<?php
require_once __DIR__ . '/../function.php';
requireRole(['guru']);

$pageTitle = 'Kelola User - EduCare';
$file = 'users.json';
$usersList = readJSON($file);
$currentUserId = (int) ($_SESSION['user']['id'] ?? 0);
$allowedRoles = ['siswa', 'guru'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit;
    }
    if (isset($_POST['add'])) {
        $nama = trim($_POST['nama'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? '');

        if (!in_array($role, $allowedRoles, true)) {
            $role = 'siswa';
        }

        // Cek email duplikat
        $emailExists = false;
        foreach ($usersList as $u) {
            if ($u['email'] === $email) {
                $emailExists = true;
                break;
            }
        }

        if (empty($nama) || empty($email) || empty($password) || empty($role)) {
            setFlashMessage('error', 'msg.guru.user.required');
        } elseif (!validateEmail($email)) {
            setFlashMessage('error', 'msg.guru.user.invalid_email');
        } elseif ($emailExists) {
            setFlashMessage('error', 'msg.guru.user.email_exists');
        } else {
            $newUser = [
                'nama' => $nama,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'created_at' => date('Y-m-d H:i:s')
            ];
            insertJSON($file, $newUser);
            setFlashMessage('success', 'msg.guru.user.add_success');
        }
        header('Location: user.php');
        exit;
    }

    if (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $nama = trim($_POST['nama'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $role = trim($_POST['role'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!in_array($role, $allowedRoles, true)) {
            $role = 'siswa';
        }

        // Cek email duplikat
        $emailExists = false;
        foreach ($usersList as $u) {
            if ($u['id'] !== $id && $u['email'] === $email) {
                $emailExists = true;
                break;
            }
        }

        if (empty($nama) || empty($email) || empty($role)) {
            setFlashMessage('error', 'msg.guru.user.required_fields');
        } elseif (!validateEmail($email)) {
            setFlashMessage('error', 'msg.guru.user.invalid_email');
        } elseif ($emailExists) {
            setFlashMessage('error', 'msg.guru.user.email_exists_other');
        } else {
            $updateData = [
                'nama' => $nama,
                'email' => $email,
                'role' => $role
            ];

            // Update password jika diisi
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    setFlashMessage('error', 'msg.guru.user.password_min');
                } else {
                    $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
            }

            // Hanya tulis data jika tidak ada error validasi password
            $pendingValidation = !empty($password) && strlen($password) < 6;
            if (!$pendingValidation) {
                updateJSON($file, $id, $updateData);
            }

            // Update session jika mengedit diri sendiri
            if ($id === $currentUserId && !$pendingValidation) {
                $_SESSION['user']['nama'] = $nama;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['role'] = $role;
            }

            if ($pendingValidation) {
                setFlashMessage('error', 'msg.guru.user.password_min');
            } else {
                setFlashMessage('success', 'msg.guru.user.update_success');
            }
        }
        header('Location: user.php');
        exit;
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        if ($id === $currentUserId) {
            setFlashMessage('error', 'msg.guru.user.cannot_delete_self');
        } else {
            deleteJSON($file, $id);
            setFlashMessage('success', 'msg.guru.user.delete_success');
        }
        header('Location: user.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';

// JANGAN kirim hash password ke browser. Sajikan versi tanpa field sensitif
// untuk ditampilkan di tabel (dipakai template di bawah).
$usersSafe = array_map(function ($u) {
    return [
        'id'    => $u['id'] ?? '',
        'nama'  => $u['nama'] ?? '',
        'email' => $u['email'] ?? '',
        'role'  => $u['role'] ?? '',
    ];
}, $usersList);
?>

<div class="min-h-screen flex" x-data="{
    showAddModal: false,
    showEditModal: false,
    showDeleteModal: false,
    editId: null,
    editNama: '',
    editEmail: '',
    editRole: '',
    deleteId: null,
    deleteNama: '',
    search: '',
    roleFilter: 'all',
    openEdit(u) {
        this.editId = u.id;
        this.editNama = u.nama;
        this.editEmail = u.email;
        this.editRole = u.role;
        this.showEditModal = true;
    },
    openDelete(u) {
        this.deleteId = u.id;
        this.deleteNama = u.nama;
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
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight" data-i18n="guru.sidebar.nav_manage_users">Kelola User</h2>
                <p class="text-sm text-slate-500 mt-1" data-i18n="guru.user.subtitle">Kelola hak akses dan profil akun Siswa dan Guru.</p>
            </div>

            <div class="flex items-center gap-3 self-end md:self-auto">
                <button @click="showAddModal = true" class="px-5 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> <span data-i18n="guru.user.add_user">Tambah User</span>
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
                <input type="text" x-model="search" placeholder="Cari nama atau email..." data-i18n-placeholder="guru.user.search_placeholder" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full placeholder:text-slate-400">
            </div>

            <div class="glassmorphism rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center gap-3">
                <i data-lucide="filter" class="text-slate-400 w-5 h-5"></i>
                <select x-model="roleFilter" class="bg-transparent border-none outline-hidden focus:ring-0 text-sm text-slate-700 w-full">
                    <option value="all" data-i18n="guru.user.all_roles">Semua Role</option>
                    <option value="siswa" data-i18n="guru.user.role_student">Siswa</option>
                    <option value="guru" data-i18n="guru.user.role_teacher">Guru</option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glassmorphism rounded-3xl border border-white/60 shadow-xs overflow-hidden bg-white/40">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 text-slate-500 font-semibold text-xs border-b border-slate-100 uppercase tracking-wider">
                            <th class="p-4 md:p-6" data-i18n="guru.dynamic.name">Nama</th>
                            <th class="p-4 md:p-6" data-i18n="guru.dynamic.email">Email</th>
                            <th class="p-4 md:p-6" data-i18n="guru.user.role">Role</th>
                            <th class="p-4 md:p-6 text-right" data-i18n="guru.dynamic.action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/50 text-slate-700 text-xs">
                        <template x-for="u in <?= htmlspecialchars(json_encode($usersSafe)) ?>.filter(user => {
                            const matchSearch = user.nama.toLowerCase().includes(search.toLowerCase()) || user.email.toLowerCase().includes(search.toLowerCase());
                            const matchRole = roleFilter === 'all' || user.role === roleFilter;
                            return matchSearch && matchRole;
                        })" :key="u.id">
                            <tr class="hover:bg-white/50 transition-colors">
                                <td class="p-4 md:p-6 font-bold text-slate-800" x-text="u.nama"></td>
                                <td class="p-4 md:p-6 text-slate-500" x-text="u.email"></td>
                                <td class="p-4 md:p-6">
                                    <span :class="u.role === 'guru' ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/10' : 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/10'"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        x-text="u.role"></span>
                                </td>
                                <td class="p-4 md:p-6 text-right flex justify-end gap-2">
                                    <button @click="openEdit(u)" class="p-2 rounded-xl bg-white text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-100 hover:border-blue-200 transition" data-i18n-title="guru.user.edit_modal_title" title="Edit User">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="openDelete(u)" :disabled="u.id == <?= $currentUserId ?>" class="p-2 rounded-xl bg-white text-red-600 hover:bg-red-50 border border-slate-100 hover:border-red-200 transition disabled:opacity-40" data-i18n-title="guru.user.delete_confirm_title" title="Hapus User">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="<?= count($usersList) ?> === 0" class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4"><i data-lucide="users" class="w-10 h-10"></i></div>
            <div class="font-bold text-slate-800" data-i18n="guru.user.empty_title">Belum Ada User</div>
            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed" data-i18n="guru.user.empty_desc">Belum ada user yang terdaftar dalam database JSON.</p>
        </div>
    </main>
</div>

<!-- ADD MODAL -->
<div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" x-transition>
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100 animate-fadeIn" @click.away="showAddModal = false">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="user-plus" class="text-blue-600"></i> <span data-i18n="guru.user.add_modal_title">Tambah User Baru</span></h3>
            <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="add" value="1">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.full_name">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Contoh: Nadia Safitri" data-i18n-placeholder="guru.user.name_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.email_label">Alamat Email</label>
                <input type="email" name="email" required placeholder="nama@email.com" data-i18n-placeholder="guru.user.email_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.password">Password</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter" data-i18n-placeholder="guru.user.password_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.role_access">Role Akses</label>
                <select name="role" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="siswa" data-i18n="guru.user.role_student">Siswa</option>
                    <option value="guru" data-i18n="guru.user.role_teacher">Guru</option>
                </select>
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
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2"><i data-lucide="edit" class="text-blue-600"></i> <span data-i18n="guru.user.edit_modal_title">Edit User</span></h3>
            <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>

        <form action="" method="POST" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="edit" value="1">
            <input type="hidden" name="id" :value="editId">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.full_name">Nama Lengkap</label>
                <input type="text" name="nama" x-model="editNama" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.email_label">Alamat Email</label>
                <input type="email" name="email" x-model="editEmail" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.user.new_password">Password Baru</span> <span class="text-slate-400 font-normal" data-i18n="guru.user.leave_blank">(Kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" placeholder="Masukkan password baru jika ingin diganti" data-i18n-placeholder="guru.user.new_password_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.role_access">Role Akses</label>
                <select name="role" x-model="editRole" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 text-slate-700">
                    <option value="siswa" data-i18n="guru.user.role_student">Siswa</option>
                    <option value="guru" data-i18n="guru.user.role_teacher">Guru</option>
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
        <h3 class="text-lg font-bold text-slate-900" data-i18n="guru.user.delete_confirm_title">Hapus User?</h3>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed"><span data-i18n="guru.user.delete_confirm_desc">Apakah Anda yakin ingin menghapus user</span> <span class="font-bold text-slate-800" x-text="deleteNama"></span>? <span data-i18n="guru.user.delete_confirm_warning">Tindakan ini tidak dapat dibatalkan.</span></p>

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