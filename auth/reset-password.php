<?php
session_start();
require_once __DIR__ . '/../function.php';

// Cek jika sudah login
if (isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'dashboard-siswa/index.php');
    exit;
}

// Ambil token dari URL
$token = $_GET['token'] ?? '';

// Validasi token
if (empty($token)) {
    $_SESSION['error'] = 'Token reset password tidak valid.';
    header('Location: forgot-password.php');
    exit;
}

// Cari user dengan token tersebut
$usersFile = __DIR__ . '/../data/users.json';
$users = [];

if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true) ?: [];
}

$userFound = null;
foreach ($users as $user) {
    if (isset($user['reset_token']) && $user['reset_token'] === $token) {
        // Cek apakah token masih berlaku
        if (isset($user['reset_expiry']) && $user['reset_expiry'] > time()) {
            $userFound = $user;
            break;
        }
    }
}

// Jika token tidak valid atau sudah kadaluarsa
if (!$userFound) {
    $_SESSION['error'] = 'Token reset password tidak valid atau sudah kadaluarsa. Silakan minta link baru.';
    header('Location: forgot-password.php');
    exit;
}

// Simpan email di session untuk proses reset
$_SESSION['reset_email'] = $userFound['email'];
$_SESSION['reset_token'] = $token;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script>
        (function() {
            try {
                var saved = localStorage.getItem('educare-theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var isDark = saved ? saved === 'dark' : prefersDark;
                if (isDark) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <title>Atur Ulang Kata Sandi — EduCare</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: '#4f46e5',
                        brandd: '#4338ca'
                    },
                    fontFamily: {
                        sans: ['Figtree', 'ui-sans-serif', 'system-ui']
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .auth-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .auth-bg__grid {
            position: absolute;
            inset: -4px;
            background-image:
                linear-gradient(rgba(148, 163, 184, .22) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, .22) 1px, transparent 1px);
            background-size: 46px 46px;
            -webkit-mask-image: radial-gradient(circle at 50% 32%, black, transparent 80%);
            mask-image: radial-gradient(circle at 50% 32%, black, transparent 80%);
            animation: authGridPan 46s linear infinite;
            opacity: .8;
        }

        .auth-bg__sweep {
            position: absolute;
            inset: -20%;
            background: linear-gradient(115deg, transparent 40%, rgba(129, 140, 248, .18) 50%, transparent 60%);
            background-size: 250% 250%;
            animation: authSweep 9s ease-in-out infinite;
        }

        @keyframes authSweep {
            0% { background-position: -100% -100%; }
            100% { background-position: 100% 100%; }
        }

        @keyframes authGridPan {
            0% { background-position: 0 0, 0 0; }
            100% { background-position: 220px 220px, 220px 220px; }
        }

        .auth-bg__glow {
            position: absolute;
            border-radius: 9999px;
            filter: blur(80px);
            opacity: .85;
            will-change: transform;
        }

        .auth-bg__glow--1 {
            width: 640px;
            height: 640px;
            top: -160px;
            left: -140px;
            background: radial-gradient(circle, rgba(99, 102, 241, .75), transparent 70%);
            animation: authDrift1 15s ease-in-out infinite;
        }

        .auth-bg__glow--2 {
            width: 720px;
            height: 720px;
            bottom: -200px;
            right: -180px;
            background: radial-gradient(circle, rgba(59, 130, 246, .7), transparent 70%);
            animation: authDrift2 17s ease-in-out infinite;
        }

        .auth-bg__glow--3 {
            width: 480px;
            height: 480px;
            top: 36%;
            left: 50%;
            background: radial-gradient(circle, rgba(192, 88, 250, .6), transparent 70%);
            animation: authDrift3 19s ease-in-out infinite;
        }

        .auth-bg__glow--4 {
            width: 360px;
            height: 360px;
            top: 8%;
            right: 8%;
            background: radial-gradient(circle, rgba(34, 211, 238, .5), transparent 70%);
            animation: authDrift4 13s ease-in-out infinite;
        }

        @keyframes authDrift1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(70px, 45px) scale(1.15); }
        }

        @keyframes authDrift2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-55px, -35px) scale(1.12); }
        }

        @keyframes authDrift3 {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-46%, -55%) scale(1.22); }
        }

        @keyframes authDrift4 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-40px, 50px) scale(1.18); }
        }

        html:not(.dark) .auth-bg__glow { opacity: .4; }
        html:not(.dark) .auth-bg__grid { opacity: .5; }
        html:not(.dark) .auth-bg__sweep { opacity: .5; }
        html.dark .auth-bg__glow { opacity: .9; }

        .login-card {
            position: relative;
        }

        .login-card::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(120deg, rgba(99, 102, 241, .55), rgba(34, 211, 238, .35), rgba(168, 85, 247, .55));
            background-size: 200% 200%;
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: authCardBorder 6s ease infinite;
            pointer-events: none;
            opacity: .8;
        }

        @keyframes authCardBorder {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @media (prefers-reduced-motion: reduce) {
            .auth-bg__grid,
            .auth-bg__glow--1,
            .auth-bg__glow--2,
            .auth-bg__glow--3,
            .auth-bg__glow--4,
            .auth-bg__sweep,
            .login-card::before {
                animation: none !important;
            }
        }

        /* Tim: background flat (ganti aurora dekoratif) */
        body {
            background: #C6E7FF;
        }
        .auth-bg {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-100 dark:bg-[#0b1120] transition-colors duration-300">

    <!-- Animated Background -->
    <div class="auth-bg" aria-hidden="true">
        <div class="auth-bg__grid"></div>
        <div class="auth-bg__sweep"></div>
        <div class="auth-bg__glow auth-bg__glow--1"></div>
        <div class="auth-bg__glow auth-bg__glow--2"></div>
        <div class="auth-bg__glow auth-bg__glow--3"></div>
        <div class="auth-bg__glow auth-bg__glow--4"></div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8">

        <!-- Toolbar: Bahasa & Dark Mode -->
        <div class="w-full sm:max-w-lg flex items-center justify-end gap-2 mb-3">
            <!-- Language Switcher -->
            <div class="relative">
                <button id="lang-toggle" type="button"
                    class="flex items-center gap-1.5 px-2.5 h-9 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-xs font-semibold shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M3 12h18M12 3a14.5 14.5 0 010 18M12 3a14.5 14.5 0 000 18"></path>
                    </svg>
                    <span class="lang-current-label">ID</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path d="M6 9l6 6 6-6"></path>
                    </svg>
                </button>
                <div id="lang-menu" class="hidden absolute right-0 mt-2 w-40 rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-lg shadow-slate-900/5 overflow-hidden z-50">
                    <button type="button" data-lang-set="id" class="lang-option w-full text-left px-4 py-2.5 text-sm text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                        <span>🇮🇩</span> Bahasa Indonesia
                    </button>
                    <button type="button" data-lang-set="en" class="lang-option w-full text-left px-4 py-2.5 text-sm text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                        <span>🇬🇧</span> English
                    </button>
                </div>
            </div>

            <!-- Toggle Dark Mode -->
            <button id="theme-toggle" type="button"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors shadow-sm">
                <svg id="theme-icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                </svg>
                <svg id="theme-icon-moon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                </svg>
            </button>
        </div>

        <!-- Logo -->
        <a href="<?= htmlspecialchars(pageUrl('index.php')) ?>" class="mb-5">
            <img
                src="<?= htmlspecialchars(assetUrl('assets/img/EduCare-logo.png')) ?>"
                alt="Logo EduCare"
                class="w-20 h-20 object-contain rounded-xl">
        </a>

        <!-- Reset Password Card -->
        <div class="login-card w-full sm:max-w-lg px-5 sm:px-10 py-8 sm:py-9 bg-white dark:bg-slate-900 shadow-md dark:shadow-none dark:border dark:border-slate-800 rounded-xl transition-colors duration-300">

            <!-- Heading -->
            <div class="mb-7">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                    Atur Ulang Kata Sandi
                </h2>
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    Masukkan kata sandi baru Anda.
                </p>
            </div>

            <!-- Error Message -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-5 px-4 py-3 rounded-md text-sm bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 font-medium border border-red-100 dark:border-red-500/30">
                    <?= htmlspecialchars($_SESSION['error']); ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-5 px-4 py-3 rounded-md text-sm bg-green-50 dark:bg-emerald-500/10 text-green-600 dark:text-emerald-400 font-medium border border-green-100 dark:border-emerald-500/30">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Reset Password Form -->
            <form action="process_reset.php" method="POST">

                <!-- Hidden token -->
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />

                <!-- Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Masukkan kata sandi baru"
                        class="block w-full h-12 px-4 rounded-md border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none" />
                </div>

                <!-- Konfirmasi Password -->
                <div class="mt-4">
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">
                        Konfirmasi Kata Sandi
                    </label>
                    <input
                        id="password_confirm"
                        type="password"
                        name="password_confirm"
                        required
                        autocomplete="new-password"
                        placeholder="Konfirmasi kata sandi baru"
                        class="block w-full h-12 px-4 rounded-md border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none" />
                </div>

                <!-- Submit Button -->
                <div class="mt-7">
                    <button type="submit"
                        class="w-full h-12 inline-flex items-center justify-center px-4 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                        Atur Ulang Kata Sandi
                    </button>
                </div>

            </form>

            <!-- Back to Login -->
            <div class="mt-7 text-center text-sm border-t border-gray-100 dark:border-slate-800 pt-6">
                <span class="text-gray-500 dark:text-slate-400">
                    Kembali ke
                </span>
                <a href="login.php"
                    class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 ml-1 transition">
                    Halaman Masuk
                </a>
            </div>

        </div>

        <!-- Copyright -->
        <p class="mt-6 text-center text-xs text-gray-400 dark:text-slate-600">
            &copy; <?= date('Y') ?> EduCare. Hak cipta dilindungi undang-undang.
        </p>

    </div>

    <!-- Toggle Dark Mode Script -->
    <script>
        (function() {
            function isDarkNow() {
                return document.documentElement.classList.contains('dark');
            }

            function syncIcons() {
                var dark = isDarkNow();
                var sun = document.getElementById('theme-icon-sun');
                var moon = document.getElementById('theme-icon-moon');
                if (sun) sun.classList.toggle('hidden', !dark);
                if (moon) moon.classList.toggle('hidden', dark);
            }

            function setTheme(dark) {
                document.documentElement.classList.toggle('dark', dark);
                try {
                    localStorage.setItem('educare-theme', dark ? 'dark' : 'light');
                } catch (e) {}
                syncIcons();
            }

            function toggleTheme() {
                setTheme(!isDarkNow());
            }

            document.addEventListener('DOMContentLoaded', function() {
                syncIcons();
                var btn = document.getElementById('theme-toggle');
                if (btn) btn.addEventListener('click', toggleTheme);
            });
        })();
    </script>

    <!-- Dropdown Bahasa Script -->
    <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                var toggle = document.getElementById('lang-toggle');
                var menu = document.getElementById('lang-menu');
                if (!toggle || !menu) return;

                function closeMenu() {
                    menu.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                }

                function openMenu() {
                    menu.classList.remove('hidden');
                    toggle.setAttribute('aria-expanded', 'true');
                }

                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menu.classList.contains('hidden') ? openMenu() : closeMenu();
                });
                document.addEventListener('click', function(e) {
                    if (!menu.contains(e.target) && e.target !== toggle) closeMenu();
                });
                menu.querySelectorAll('[data-lang-set]').forEach(function(item) {
                    item.addEventListener('click', closeMenu);
                });
            });
        })();
    </script>

    <!-- i18n -->
    <script src="<?= htmlspecialchars(assetUrl('assets/js/i18n.js')) ?>" defer></script>

</body>

</html>