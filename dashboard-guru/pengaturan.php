<?php
require_once __DIR__ . '/../function.php';
requireRole(['guru']);

$pageTitle = 'Pengaturan Akun - EduCare';
$file = 'users.json';
$currentUserId = (int) ($_SESSION['user']['id'] ?? 0);
$currentUser = findJSON($file, $currentUserId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit;
    }
    $nama = trim($_POST['nama'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    
    // Validasi
    $emailExists = false;
    $usersList = readJSON($file);
    foreach ($usersList as $u) {
        if ($u['id'] !== $currentUserId && $u['email'] === $email) {
            $emailExists = true;
            break;
        }
    }
    
    if (empty($nama) || empty($email)) {
        setFlashMessage('error', 'msg.guru.pengaturan.required');
    } elseif (!validateEmail($email)) {
        setFlashMessage('error', 'msg.guru.user.invalid_email');
    } elseif ($emailExists) {
        setFlashMessage('error', 'msg.guru.user.email_exists_other');
    } else {
        $updateData = [
            'nama' => $nama,
            'email' => $email
        ];
        
        // Update password jika diisi
        $passValid = true;
        if (!empty($password)) {
            // Wajib isi password saat ini untuk mengganti kata sandi.
            if (empty($currentPassword) || !password_verify($currentPassword, $currentUser['password'] ?? '')) {
                setFlashMessage('error', 'msg.guru.pengaturan.current_password_wrong');
                $passValid = false;
            } elseif (strlen($password) < 6) {
                setFlashMessage('error', 'msg.guru.pengaturan.password_min');
                $passValid = false;
            } elseif ($password !== $confirm) {
                setFlashMessage('error', 'msg.guru.pengaturan.password_mismatch');
                $passValid = false;
            } else {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
        }
        
        if ($passValid) {
            updateJSON($file, $currentUserId, $updateData);
            
            // Sync session
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['email'] = $email;
            
            setFlashMessage('success', 'msg.guru.pengaturan.update_success');
            header('Location: pengaturan.php');
            exit;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen flex" x-data="{
    nama: <?= htmlspecialchars(json_encode($currentUser['nama'] ?? '', JSON_UNESCAPED_UNICODE)) ?>,
    email: <?= htmlspecialchars(json_encode($currentUser['email'] ?? '', JSON_UNESCAPED_UNICODE)) ?>,
    password: '',
    confirmPassword: '',
    currentPassword: '',
    loading: false
}">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <!-- Topbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 leading-tight" data-i18n="guru.pengaturan.title">Pengaturan Akun</h2>
                <p class="text-sm text-slate-500 mt-1" data-i18n="guru.pengaturan.subtitle">Perbarui profil pribadi, alamat email, dan kata sandi Anda.</p>
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

        <!-- Form Card -->
        <div class="max-w-2xl glassmorphism rounded-3xl p-8 border border-white/60 shadow-xl bg-white/40">
            <h3 class="text-xl font-bold text-slate-950 mb-6 flex items-center gap-2">
                <i data-lucide="user" class="text-blue-600"></i> <span data-i18n="guru.pengaturan.my_profile">Profil Saya</span>
            </h3>
            
            <form action="" method="POST" class="space-y-5" @submit="loading = true">
                <?= csrfField() ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.full_name">Nama Lengkap</label>
                        <input type="text" name="nama" x-model="nama" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 font-semibold text-slate-800">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.user.email_label">Alamat Email</label>
                        <input type="email" name="email" x-model="email" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50 font-semibold text-slate-800">
                    </div>
                </div>
                
                <hr class="border-slate-100/50 my-6">
                
                <h3 class="text-xl font-bold text-slate-950 mb-6 flex items-center gap-2">
                    <i data-lucide="key" class="text-blue-600"></i> <span data-i18n="guru.pengaturan.change_password_title">Ubah Kata Sandi</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.pengaturan.current_password">Kata Sandi Saat Ini</span> <span class="text-slate-400 font-normal">(Wajib jika mengubah)</span></label>
                        <input type="password" name="current_password" x-model="currentPassword" autocomplete="current-password" placeholder="Kata sandi saat ini" data-i18n-placeholder="guru.pengaturan.current_password_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2"><span data-i18n="guru.pengaturan.new_password">Kata Sandi Baru</span> <span class="text-slate-400 font-normal" data-i18n="guru.materi.optional">(Opsional)</span></label>
                        <input type="password" name="password" x-model="password" placeholder="Min. 6 karakter" data-i18n-placeholder="guru.pengaturan.password_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2" data-i18n="guru.pengaturan.confirm_password">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="confirm_password" x-model="confirmPassword" placeholder="Ulangi kata sandi baru" data-i18n-placeholder="guru.pengaturan.confirm_password_placeholder" class="w-full px-4 py-3 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 transition text-xs bg-slate-50">
                    </div>
                </div>
                
                <div class="flex justify-end pt-6">
                    <button type="submit" :disabled="loading" class="px-6 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 text-xs flex items-center gap-2 disabled:opacity-50">
                        <span x-show="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'" data-i18n="guru.dynamic.save_changes">Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

</body>
</html>
