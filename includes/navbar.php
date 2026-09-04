<?php

declare(strict_types=1);

require_once __DIR__ . '/../function.php';

$baseUrl = rtrim(BASE_URL, '/') . '/';
$isLoggedIn = isset($_SESSION['user']);
$userName   = $_SESSION['user']['nama'] ?? null;

// Tentukan link dashboard berdasarkan role user (jika login)
$dashboardLink = pageUrl('dashboard-siswa/index.php');
if ($isLoggedIn) {
    $role = $_SESSION['user']['role'] ?? 'siswa';
    switch ($role) {
        case 'admin':
            // Tidak ada dashboard admin terpisah; arahkan ke dashboard-siswa
            // (satu-satunya dashboard yang mengizinkan role admin).
            $dashboardLink = pageUrl('dashboard-siswa/index.php');
            break;
        case 'guru':
            $dashboardLink = pageUrl('dashboard-guru/index.php');
            break;
        default:
            $dashboardLink = pageUrl('dashboard-siswa/index.php');
            break;
    }
}

$navItems = [
    ['path' => 'index.php', 'label' => 'Beranda', 'key' => 'nav.home'],
    ['path' => 'views/about.php', 'label' => 'Tentang', 'key' => 'nav.about'],
    ['path' => 'index.php', 'label' => 'Edukasi', 'anchor' => '#edukasi', 'key' => 'nav.edukasi'],
    ['path' => 'views/silapor.php', 'label' => 'silapor', 'key' => 'nav.silapor'],
    ['path' => 'index.php', 'label' => 'Cara Kerja', 'anchor' => '#alur', 'key' => 'nav.cara_kerja'],
    ['path' => 'index.php', 'label' => 'Testimoni', 'anchor' => '#testimoni', 'key' => 'nav.testimoni'],
    ['path' => 'index.php', 'label' => 'FAQ', 'anchor' => '#faq', 'key' => 'nav.faq'],
    ['path' => 'views/kontak.php', 'label' => 'Contact', 'key' => 'nav.contact'],
];

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$basePath = rtrim(parse_url($baseUrl, PHP_URL_PATH) ?? '', '/');
$currentPath = rawurldecode(ltrim(substr($requestPath, strlen($basePath)), '/'));

if ($currentPath === '' || $currentPath === '/') {
    $currentPath = 'index.php';
}

function isActiveLink(string $itemPath, string $currentPath, string $anchor = ''): bool
{
    if (!empty($anchor)) {
        return false;
    }
    return $currentPath === $itemPath;
}
?>
<style>
    /* === Navbar auto-hide saat scroll ke bawah, muncul lagi saat scroll ke atas === */
    #site-navbar {
        transition: transform 0.3s ease-in-out;
    }

    #site-navbar.navbar-hidden {
        transform: translateY(-100%);
    }

    /* === Animasi hamburger -> X (tombol utama di navbar) === */
    #navbar-toggle.active .bar-1 {
        transform: translateY(7px) rotate(45deg);
    }

    #navbar-toggle.active .bar-2 {
        opacity: 0;
    }

    #navbar-toggle.active .bar-3 {
        transform: translateY(-7px) rotate(-45deg);
    }

    /* === Drawer & overlay (CSS murni, tidak bergantung pada purge Tailwind) === */
    .navbar-drawer {
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        width: 18rem;
        max-width: 80%;
        background: #ffffff;
        z-index: 50;
        box-shadow: -12px 0 30px rgba(0, 0, 0, 0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
    }

    .navbar-drawer.open {
        transform: translateX(0);
    }

    .navbar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.5);
        z-index: 40;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease-out;
    }

    .navbar-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    /* Cegah scroll body saat drawer terbuka */
    body.navbar-drawer-open {
        overflow: hidden;
    }
</style>

<header id="site-navbar"
    class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-200 transition-all duration-300">

    <nav class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">

        <div class="flex items-center justify-between h-20">

            <!-- Logo -->
            <a
                href="<?= htmlspecialchars($baseUrl) ?>"
                class="flex items-center gap-3 group shrink-0">

                <img
                    src="<?= htmlspecialchars(assetUrl('assets/img/EduCare-logo.png')) ?>"
                    alt="Logo EduCare"
                    width="40"
                    height="40"
                    class="w-10 h-10 object-contain rounded-xl">

                <span
                    class="font-bold text-xl text-gray-900 group-hover:text-blue-600 transition-colors duration-300">
                    EduCare
                </span>

            </a>

            <!-- Desktop Menu -->
            <!-- Breakpoint dinaikkan dari lg -> xl karena 8 menu item + language switcher +
                 dark mode + jam digital + tombol auth terlalu banyak untuk muat nyaman di
                 lebar layar "lg" (1024px). Di antara lg dan xl sekarang otomatis jatuh ke
                 drawer mobile supaya tidak dempet. -->
            <ul class="hidden xl:flex items-center gap-6 2xl:gap-8">
                <?php foreach ($navItems as $item):
                    $file = $item['path'];
                    $label = $item['label'];
                    $anchor = $item['anchor'] ?? '';

                    if ($anchor && $currentPath === $file) {
                        $href = $anchor;
                    } else {
                        $href = pageUrl($file) . $anchor;
                    }

                    $active = isActiveLink($file, $currentPath, $anchor);
                ?>
                    <li>
                        <a href="<?= htmlspecialchars($href) ?>"
                            data-path="<?= htmlspecialchars($file) ?>"
                            data-anchor="<?= htmlspecialchars($anchor) ?>"
                            class="nav-link relative text-sm font-medium py-2 whitespace-nowrap transition-colors <?= $active ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' ?>">
                            <span data-i18n="<?= htmlspecialchars($item['key'] ?? '') ?>"><?= htmlspecialchars($label) ?></span>
                            <span class="line-indicator absolute left-0 -bottom-2 h-0.5 bg-blue-600 transition-all duration-300 <?= $active ? 'w-full' : 'w-0' ?>"></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Jam Digital & Auth (Desktop) -->
            <div class="hidden xl:flex items-center gap-3 2xl:gap-5 shrink-0">
                <!-- Language Switcher Desktop -->
                <div class="relative">
                    <button id="lang-toggle" type="button"
                        class="flex items-center gap-1.5 px-2.5 h-9 rounded-xl bg-gray-100/80 border border-gray-200/60 text-gray-700 hover:bg-gray-200/70 transition-colors text-xs font-semibold"
                        aria-haspopup="true" aria-expanded="false" aria-label="Ganti bahasa / Change language" title="Ganti bahasa / Change language">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M3 12h18M12 3a14.5 14.5 0 010 18M12 3a14.5 14.5 0 000 18"></path>
                        </svg>
                        <span class="lang-current-label">ID</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                    <div id="lang-menu" class="hidden absolute right-0 mt-2 w-40 rounded-xl bg-white border border-gray-200 shadow-lg shadow-slate-900/5 overflow-hidden z-50">
                        <button type="button" data-lang-set="id" class="lang-option w-full text-left px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors flex items-center gap-2">
                            <span>🇮🇩</span> Bahasa Indonesia
                        </button>
                        <button type="button" data-lang-set="en" class="lang-option w-full text-left px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors flex items-center gap-2">
                            <span>🇬🇧</span> English
                        </button>
                    </div>
                </div>
                <!-- Toggle Dark Mode Desktop -->
                <button id="theme-toggle" type="button"
                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100/80 border border-gray-200/60 text-gray-700 hover:bg-gray-200/70 transition-colors"
                    aria-label="Ganti mode gelap/terang" title="Ganti mode gelap/terang">
                    <svg id="theme-icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                    </svg>
                    <svg id="theme-icon-moon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                    </svg>
                </button>
                <!-- Digital Clock Desktop (baru muncul mulai 2xl supaya tidak menambah desakan di xl) -->
                <div class="hidden 2xl:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100/80 border border-gray-200/60 text-gray-700 font-mono text-xs font-semibold tracking-wider">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="digital-clock">00:00:00 WIB</span>
                </div>

                <?php if ($isLoggedIn): ?>
                    <a href="<?= htmlspecialchars($dashboardLink) ?>"
                        class="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors whitespace-nowrap">
                        <?= $userName ? 'Halo, ' . htmlspecialchars($userName) : 'Dashboard' ?>
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl . 'auth/logout.php') ?>"
                        data-i18n="nav.logout"
                        class="px-4 py-2 rounded-xl bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition-all duration-300 shadow-sm shadow-red-100 whitespace-nowrap">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($baseUrl . 'auth/login.php') ?>"
                        data-i18n="nav.login"
                        class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors py-2 px-3 rounded-xl hover:bg-gray-50 whitespace-nowrap">
                        Masuk
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl . 'auth/register.php') ?>"
                        data-i18n="nav.register"
                        class="px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all duration-300 shadow-lg shadow-blue-600/20 hover:-translate-y-0.5 transform whitespace-nowrap">
                        Daftar
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Button (sekarang tampil di bawah xl, bukan lg) -->
            <button id="navbar-toggle"
                type="button"
                class="xl:hidden relative w-9 h-9 flex items-center justify-center rounded-md text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                aria-expanded="false"
                aria-controls="navbar-mobile-menu"
                aria-label="Buka menu navigasi">
                <div class="w-5 h-4 relative flex flex-col justify-between">
                    <span class="bar bar-1 block h-0.5 w-full bg-current rounded transition-all duration-300 origin-center"></span>
                    <span class="bar bar-2 block h-0.5 w-full bg-current rounded transition-all duration-300"></span>
                    <span class="bar bar-3 block h-0.5 w-full bg-current rounded transition-all duration-300 origin-center"></span>
                </div>
            </button>
        </div>

    </nav>

    <!-- Overlay gelap di belakang drawer -->
    <div id="navbar-overlay" class="navbar-overlay xl:hidden"></div>

    <!-- Mobile Menu (Slide-in Drawer dari kanan) -->
    <div id="navbar-mobile-menu"
        class="navbar-drawer xl:hidden flex flex-col">

        <div class="flex items-center justify-between h-20 px-6 border-b border-gray-200">
            <span class="font-bold text-lg text-gray-900" data-i18n="nav.menu">Menu</span>
            <button id="navbar-close"
                type="button"
                class="w-9 h-9 flex items-center justify-center rounded-md text-gray-600 hover:bg-gray-100 transition-colors"
                aria-label="Tutup menu navigasi">
                <div class="w-5 h-4 relative flex flex-col justify-center">
                    <span class="close-bar close-bar-1 block h-0.5 w-full bg-current rounded absolute rotate-45"></span>
                    <span class="close-bar close-bar-2 block h-0.5 w-full bg-current rounded absolute -rotate-45"></span>
                </div>
            </button>
        </div>

        <ul class="flex flex-col gap-1 px-4 pt-4 overflow-y-auto">
            <?php foreach ($navItems as $item):
                $file = $item['path'];
                $label = $item['label'];
                $anchor = $item['anchor'] ?? '';

                if ($anchor && $currentPath === $file) {
                    $href = $anchor;
                } else {
                    $href = $baseUrl . $file . $anchor;
                }
                $active = isActiveLink($file, $currentPath, $anchor);
            ?>
                <li>
                    <a href="<?= htmlspecialchars($href) ?>"
                        data-path="<?= htmlspecialchars($file) ?>"
                        data-anchor="<?= htmlspecialchars($anchor) ?>"
                        data-i18n="<?= htmlspecialchars($item['key'] ?? '') ?>"
                        class="nav-link-mobile block px-3 py-2.5 rounded-md text-sm font-medium transition-colors <?= $active ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Jam Digital & Auth (Mobile) -->
        <div class="mt-auto flex flex-col gap-3 px-4 pb-6 pt-3 border-t border-gray-200">
            <!-- Language Switcher Mobile -->
            <div class="flex items-center rounded-xl bg-gray-50 border border-gray-200/80 p-1 text-xs font-semibold">
                <button type="button" data-lang-set="id" class="lang-option flex-1 py-2 rounded-lg transition-colors text-gray-500">🇮🇩 ID</button>
                <button type="button" data-lang-set="en" class="lang-option flex-1 py-2 rounded-lg transition-colors text-gray-500">🇬🇧 EN</button>
            </div>
            <!-- Toggle Dark Mode Mobile -->
            <button id="theme-toggle-mobile" type="button"
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl bg-gray-50 border border-gray-200/80 text-gray-700 text-xs font-semibold hover:bg-gray-100 transition-colors">
                <svg id="theme-icon-sun-mobile" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                </svg>
                <svg id="theme-icon-moon-mobile" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                </svg>
                <span id="theme-label-mobile">Mode Gelap</span>
            </button>
            <!-- Digital Clock Mobile -->
            <div class="flex items-center justify-center gap-2 py-2 rounded-xl bg-gray-50 border border-gray-200/80 text-gray-700 font-mono text-xs font-semibold tracking-wider">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="digital-clock">00:00:00 WIB</span>
            </div>

            <div class="flex items-center gap-3">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= htmlspecialchars($dashboardLink) ?>"
                        class="flex-1 text-center px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                        Dashboard
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl . 'auth/logout.php') ?>"
                        data-i18n="nav.logout"
                        class="flex-1 text-center px-4 py-2.5 rounded-xl bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition-colors">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($baseUrl . 'auth/login.php') ?>"
                        data-i18n="nav.login"
                        class="flex-1 text-center px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                        Masuk
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl . 'auth/register.php') ?>"
                        data-i18n="nav.register"
                        class="flex-1 text-center px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors shadow-md shadow-blue-600/10">
                        Daftar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- JavaScript Jam Digital -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        function updateDigitalClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds} WIB`;

            document.querySelectorAll('.digital-clock').forEach(clock => {
                clock.textContent = timeString;
            });
        }

        updateDigitalClock();
        setInterval(updateDigitalClock, 1000);
    });
</script>

<!-- JavaScript Toggle Dark Mode -->
<script>
    (function() {
        function isDarkNow() {
            return document.documentElement.classList.contains('dark');
        }

        function syncIcons() {
            var dark = isDarkNow();
            var sun = document.getElementById('theme-icon-sun');
            var moon = document.getElementById('theme-icon-moon');
            var sunM = document.getElementById('theme-icon-sun-mobile');
            var moonM = document.getElementById('theme-icon-moon-mobile');
            var labelM = document.getElementById('theme-label-mobile');
            if (sun) sun.classList.toggle('hidden', !dark);
            if (moon) moon.classList.toggle('hidden', dark);
            if (sunM) sunM.classList.toggle('hidden', !dark);
            if (moonM) moonM.classList.toggle('hidden', dark);
            if (labelM) labelM.textContent = dark ? 'Mode Terang' : 'Mode Gelap';
        }

        function setTheme(dark) {
            document.documentElement.classList.toggle('dark', dark);
            try {
                localStorage.setItem('educare-theme', dark ? 'dark' : 'light');
            } catch (e) {
                /* localStorage tidak tersedia, abaikan */
            }
            syncIcons();
        }

        function toggleTheme() {
            setTheme(!isDarkNow());
        }

        document.addEventListener('DOMContentLoaded', function() {
            syncIcons();
            var btn = document.getElementById('theme-toggle');
            var btnMobile = document.getElementById('theme-toggle-mobile');
            if (btn) btn.addEventListener('click', toggleTheme);
            if (btnMobile) btnMobile.addEventListener('click', toggleTheme);
        });
    })();
</script>

<!-- JavaScript Dropdown Bahasa (Desktop) -->
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

<!-- JavaScript Mobile Drawer (Buka/Tutup) -->
<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            var toggleBtn = document.getElementById('navbar-toggle');
            var closeBtn = document.getElementById('navbar-close');
            var drawer = document.getElementById('navbar-mobile-menu');
            var overlay = document.getElementById('navbar-overlay');
            if (!toggleBtn || !drawer || !overlay) return;

            function openDrawer() {
                drawer.classList.add('open');
                overlay.classList.add('open');
                toggleBtn.classList.add('active');
                toggleBtn.setAttribute('aria-expanded', 'true');
                document.body.classList.add('navbar-drawer-open');
            }

            function closeDrawer() {
                drawer.classList.remove('open');
                overlay.classList.remove('open');
                toggleBtn.classList.remove('active');
                toggleBtn.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('navbar-drawer-open');
            }

            toggleBtn.addEventListener('click', function() {
                drawer.classList.contains('open') ? closeDrawer() : openDrawer();
            });
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            overlay.addEventListener('click', closeDrawer);

            // Tutup drawer otomatis kalau layar di-resize ke atas breakpoint xl
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1280) closeDrawer();
            });
        });
    })();
</script>

<!-- JavaScript Navbar Auto-Hide saat Scroll -->
<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            var navbar = document.getElementById('site-navbar');
            if (!navbar) return;

            var lastScrollY = window.scrollY;
            var scrollThreshold = 80; // jarak minimal sebelum navbar mulai sembunyi
            var ticking = false;

            function handleScroll() {
                var currentScrollY = window.scrollY;

                // Jangan sembunyikan navbar kalau drawer mobile sedang terbuka
                if (document.body.classList.contains('navbar-drawer-open')) {
                    ticking = false;
                    return;
                }

                if (currentScrollY <= scrollThreshold) {
                    // Selalu tampil kalau masih di area atas halaman
                    navbar.classList.remove('navbar-hidden');
                } else if (currentScrollY > lastScrollY) {
                    // Scroll ke bawah -> sembunyikan
                    navbar.classList.add('navbar-hidden');
                } else {
                    // Scroll ke atas -> tampilkan
                    navbar.classList.remove('navbar-hidden');
                }

                lastScrollY = currentScrollY;
                ticking = false;
            }

            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(handleScroll);
                    ticking = true;
                }
            }, {
                passive: true
            });
        });
    })();
</script>

<!-- i18n: Fitur Bahasa (ID/EN) -->
<script src="<?= htmlspecialchars($baseUrl) ?>assets/js/i18n.js" defer></script>