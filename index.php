<?php
require_once __DIR__ . '/function.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Dark mode: terapkan tema tersimpan ke <html> SEBELUM CSS/paint pertama,
         supaya tidak ada kedipan (flash) dari light ke dark saat halaman dibuka/direfresh. -->
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('educare-theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var isDark = saved ? saved === 'dark' : prefersDark;
                if (isDark) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {
                /* localStorage tidak tersedia: fallback ke light mode */
            }
        })();
    </script>
    <!-- Alpine.js (opsional, jika nanti ingin menambah interaktivitas lain) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <title data-i18n="page.index">EduCare - Platform Belajar Digital</title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(assetUrl('assets/css/output.css')) ?>">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= htmlspecialchars(assetUrl('assets/css/style.css')) ?>">
</head>

<body class="bg-white dark:bg-[#0b1120] transition-colors duration-300">

    <!-- Navbar -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- Main Content -->
    <main>
        <?php include __DIR__ . '/views/lendingpage.php'; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Javascript -->
    <!-- Menambahkan atribut 'defer' agar pemuatan module js tidak memblokir rendering HTML -->
    <script type="module" src="<?= htmlspecialchars(assetUrl('assets/js/app.js')) ?>" defer></script>

</body>

</html>