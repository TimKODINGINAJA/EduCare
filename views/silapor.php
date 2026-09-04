<?php

require_once __DIR__ . '/../function.php';

$pageTitle = 'SiLapor Sekolah — Lapor Masalah Sekolah';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Dark mode sebelum paint -->
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('educare-theme');
                const prefersDark =
                    window.matchMedia &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (saved === 'dark' || (!saved && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Valley+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Existing CSS -->
    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(assetUrl('assets/css/style.css')) ?>">

    <script>
        tailwind.config = {
            darkMode: 'class',

            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        'primary-light': '#3b82f6'
                    },

                    fontFamily: {
                        sans: ['"Valley Sans"', 'sans-serif'],
                        serif: ['"Valley Sans"', 'sans-serif']
                    }
                }
            }
        };
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Valley Sans', sans-serif;
        }

        .font-serif {
            font-family: 'Valley Sans', sans-serif;
        }

        .about-card {
            transition:
                transform .2s ease,
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .about-card:hover {
            transform: translateY(-3px);
        }

        .fade-up {
            animation: fadeUp .6s ease both;
        }

        .delay-1 {
            animation-delay: .08s;
        }

        .delay-2 {
            animation-delay: .16s;
        }

        .delay-3 {
            animation-delay: .24s;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Panah accordion FAQ */
        details summary::-webkit-details-marker {
            display: none;
        }
    </style>
</head>

<body class="bg-white text-slate-900 dark:bg-slate-950 dark:text-white antialiased transition-colors duration-200">

    <?php include __DIR__ . '/../includes/navbar.php'; ?>


    <!-- =========================================================
     HERO + KATEGORI SINGKAT + LAPORAN MASUK
========================================================= -->

    <section class="px-6 pt-20 pb-16 md:pt-28 md:pb-20">

        <div class="max-w-6xl mx-auto">

            <!-- Badge -->
            <div class="fade-up inline-flex items-center gap-2
                  rounded-full border border-slate-200
                  dark:border-slate-800
                  px-3 py-1.5 mb-6">

                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>

                <span class="text-xs font-semibold tracking-wide
                     text-slate-500 dark:text-slate-400">
                    SiLapor Sekolah
                </span>

            </div>


            <div class="grid md:grid-cols-2 gap-10 items-start mb-14">

                <!-- Left: heading -->
                <div>
                    <h1 class="fade-up delay-1 font-serif text-4xl md:text-5xl
                     font-semibold leading-tight tracking-tight mb-5">
                        Lapor masalah sekolah,
                        secepat kirim pesan
                    </h1>

                    <p class="fade-up delay-2 text-base md:text-lg leading-relaxed
                    text-slate-600 dark:text-slate-400">
                        Siswa dapat melaporkan masalah lingkungan sekolah secara
                        langsung, dan memantau status penanganannya seperti melacak
                        tiket.
                    </p>

                    <div class="fade-up delay-3 mt-8">
                        <a
                            href="<?= htmlspecialchars($baseUrl . 'silapor/buat.php') ?>"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary
                     px-5 py-2.5 text-sm font-semibold text-white
                     hover:bg-blue-800 transition">
                            Buat Laporan Baru

                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </a>
                    </div>
                </div>


                <!-- Right: panel Laporan Masuk -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 overflow-hidden">

                    <div class="flex items-center justify-between px-6 py-4
                      border-b border-slate-100 dark:border-slate-800">
                        <p class="font-semibold text-sm">Laporan Masuk</p>
                        <span class="rounded-full bg-red-50 dark:bg-red-500/10
                         text-red-600 dark:text-red-400 text-xs
                         font-semibold px-3 py-1">
                            3 belum selesai
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">

                        <div class="flex items-center justify-between px-6 py-4">
                            <div>
                                <p class="text-sm font-semibold">#042 Kran air rusak — Kelas 9A</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    Fasilitas, dilaporkan 2 jam lalu
                                </p>
                            </div>
                            <span class="rounded-full bg-yellow-50 dark:bg-yellow-500/10
                           text-yellow-700 dark:text-yellow-400 text-xs
                           font-semibold px-3 py-1 shrink-0 ml-3">
                                Diperiksa
                            </span>
                        </div>

                        <div class="flex items-center justify-between px-6 py-4">
                            <div>
                                <p class="text-sm font-semibold">#041 Tas hilang di kantin</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    Kehilangan barang, 1 hari lalu
                                </p>
                            </div>
                            <span class="rounded-full bg-red-50 dark:bg-red-500/10
                           text-red-600 dark:text-red-400 text-xs
                           font-semibold px-3 py-1 shrink-0 ml-3">
                                Belum diproses
                            </span>
                        </div>

                        <div class="flex items-center justify-between px-6 py-4">
                            <div>
                                <p class="text-sm font-semibold">#039 Sampah menumpuk — Lab Komputer</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    Kebersihan, 3 hari lalu
                                </p>
                            </div>
                            <span class="rounded-full bg-green-50 dark:bg-green-500/10
                           text-green-700 dark:text-green-400 text-xs
                           font-semibold px-3 py-1 shrink-0 ml-3">
                                Selesai
                            </span>
                        </div>

                    </div>

                    <div class="px-6 py-3 bg-slate-50 dark:bg-slate-900/60">
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Dashboard admin memantau seluruh laporan hingga selesai ditangani.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
     CARA KERJA
  ========================================================= -->

    <section class="px-6 py-20 border-t border-slate-100 dark:border-slate-900">

        <div class="max-w-5xl mx-auto">

            <div class="max-w-2xl mb-12">

                <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary mb-3">
                    Cara Kerja
                </p>

                <h2 class="font-serif text-3xl md:text-4xl font-semibold mb-4">
                    Tiga langkah sederhana,
                    dari lapor sampai selesai.
                </h2>

                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    SiLapor dirancang agar siswa tidak perlu bingung harus lapor ke
                    siapa. Cukup buat laporan, dan sistem akan meneruskannya ke
                    pihak yang tepat.
                </p>

            </div>


            <div class="grid md:grid-cols-3 gap-6">

                <!-- Step 1 -->
                <div class="relative rounded-2xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 p-7">

                    <p class="font-serif text-3xl font-semibold text-primary dark:text-blue-400 mb-4">
                        01
                    </p>

                    <h3 class="text-lg font-semibold mb-2">
                        Pilih kategori & tulis laporan
                    </h3>

                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Pilih kategori yang sesuai — fasilitas rusak, bullying,
                        kebersihan, atau kehilangan barang — lalu jelaskan situasinya
                        secara singkat. Foto bisa dilampirkan sebagai bukti pendukung.
                    </p>

                </div>

                <!-- Step 2 -->
                <div class="relative rounded-2xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 p-7">

                    <p class="font-serif text-3xl font-semibold text-primary dark:text-blue-400 mb-4">
                        02
                    </p>

                    <h3 class="text-lg font-semibold mb-2">
                        Laporan diteruskan otomatis
                    </h3>

                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Sistem memberi nomor tiket dan meneruskan laporan ke guru atau
                        admin sekolah yang berwenang menangani kategori tersebut.
                    </p>

                </div>

                <!-- Step 3 -->
                <div class="relative rounded-2xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 p-7">

                    <p class="font-serif text-3xl font-semibold text-primary dark:text-blue-400 mb-4">
                        03
                    </p>

                    <h3 class="text-lg font-semibold mb-2">
                        Pantau status seperti tiket
                    </h3>

                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Status laporan berubah dari <em>Belum diproses</em> →
                        <em>Diperiksa</em> → <em>Selesai</em>, dan siswa bisa
                        memantaunya kapan saja lewat dashboard.
                    </p>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
     KATEGORI LAPORAN LENGKAP
  ========================================================= -->

    <section class="px-6 py-20 bg-slate-50 dark:bg-slate-900/40
                  border-y border-slate-100 dark:border-slate-900">

        <div class="max-w-5xl mx-auto">

            <div class="max-w-2xl mb-12">

                <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary mb-3">
                    Kategori Laporan
                </p>

                <h2 class="font-serif text-3xl md:text-4xl font-semibold mb-4">
                    Setiap jenis masalah,
                    punya jalur penanganannya sendiri.
                </h2>

                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Kategori membantu laporan langsung sampai ke pihak yang tepat,
                    tanpa perlu bolak-balik menjelaskan ke siapa harus melapor.
                </p>

            </div>


            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- Fasilitas Rusak -->
                <div class="about-card rounded-xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 p-6">

                    <div class="w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-500/10
                      flex items-center justify-center text-xl mb-5">
                        🏠
                    </div>

                    <h3 class="font-semibold text-lg mb-2">Fasilitas Rusak</h3>

                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400 mb-3">
                        Kelas, toilet, atau perlengkapan sekolah yang rusak dan
                        butuh perbaikan.
                    </p>

                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Ditangani oleh: Tim sarana &amp; prasarana
                    </p>

                </div>

                <!-- Bullying -->
                <div class="about-card rounded-xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 p-6">

                    <div class="w-11 h-11 rounded-xl bg-red-50 dark:bg-red-500/10
                      flex items-center justify-center text-xl mb-5">
                        🚫
                    </div>

                    <h3 class="font-semibold text-lg mb-2">Bullying</h3>

                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400 mb-3">
                        Laporan perundungan ditangani secara rahasia, cepat, dan
                        identitas pelapor dilindungi.
                    </p>

                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Ditangani oleh: Guru BK / Wali kelas
                    </p>

                </div>

                <!-- Kebersihan -->
                <div class="about-card rounded-xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 p-6">

                    <div class="w-11 h-11 rounded-xl bg-yellow-50 dark:bg-yellow-500/10
                      flex items-center justify-center text-xl mb-5">
                        🧹
                    </div>

                    <h3 class="font-semibold text-lg mb-2">Kebersihan</h3>

                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400 mb-3">
                        Area sekolah yang kotor atau butuh perhatian kebersihan
                        lebih lanjut.
                    </p>

                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Ditangani oleh: Tim kebersihan sekolah
                    </p>

                </div>

                <!-- Kehilangan Barang -->
                <div class="about-card rounded-xl border border-slate-200 dark:border-slate-800
                    bg-white dark:bg-slate-900 p-6">

                    <div class="w-11 h-11 rounded-xl bg-pink-50 dark:bg-pink-500/10
                      flex items-center justify-center text-xl mb-5">
                        🎒
                    </div>

                    <h3 class="font-semibold text-lg mb-2">Kehilangan Barang</h3>

                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400 mb-3">
                        Laporkan barang hilang agar lebih mudah dilacak dan
                        dikembalikan ke pemiliknya.
                    </p>

                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Ditangani oleh: Admin sekolah
                    </p>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
     STATUS LAPORAN (LEGEND)
  ========================================================= -->

    <section class="px-6 py-16">

        <div class="max-w-5xl mx-auto">

            <div class="rounded-2xl border border-slate-200 dark:border-slate-800
                  bg-white dark:bg-slate-900 p-8 md:p-10">

                <h3 class="font-serif text-2xl font-semibold mb-6">
                    Arti status laporan
                </h3>

                <div class="grid sm:grid-cols-3 gap-6">

                    <div class="flex items-start gap-3">
                        <span class="mt-1 shrink-0 rounded-full bg-red-50 dark:bg-red-500/10
                         text-red-600 dark:text-red-400 text-xs font-semibold
                         px-3 py-1">
                            Belum diproses
                        </span>
                    </div>
                    <div class="sm:col-span-2 -mt-1">
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Laporan baru masuk dan belum ditinjau oleh pihak sekolah.
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="mt-1 shrink-0 rounded-full bg-yellow-50 dark:bg-yellow-500/10
                         text-yellow-700 dark:text-yellow-400 text-xs font-semibold
                         px-3 py-1">
                            Diperiksa
                        </span>
                    </div>
                    <div class="sm:col-span-2 -mt-1">
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Laporan sedang ditinjau atau ditindaklanjuti oleh pihak
                            yang berwenang.
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="mt-1 shrink-0 rounded-full bg-green-50 dark:bg-green-500/10
                         text-green-700 dark:text-green-400 text-xs font-semibold
                         px-3 py-1">
                            Selesai
                        </span>
                    </div>
                    <div class="sm:col-span-2 -mt-1">
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Masalah sudah ditangani dan laporan dinyatakan tuntas.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
     FAQ
  ========================================================= -->

    <section class="px-6 py-20 border-t border-slate-100 dark:border-slate-900">

        <div class="max-w-3xl mx-auto">

            <div class="text-center mb-12">

                <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary mb-3">
                    Pertanyaan Umum
                </p>

                <h2 class="font-serif text-3xl md:text-4xl font-semibold">
                    Yang sering ditanyakan
                    tentang SiLapor
                </h2>

            </div>


            <div class="space-y-3">

                <details class="group rounded-xl border border-slate-200 dark:border-slate-800
                        bg-white dark:bg-slate-900 p-5 open:pb-5">
                    <summary class="flex items-center justify-between cursor-pointer
                          font-semibold text-slate-900 dark:text-white list-none">
                        Apakah identitas saya akan diketahui saat melapor?
                        <svg class="w-4 h-4 text-slate-400 transition-transform
                        group-open:rotate-45 shrink-0 ml-3" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Untuk kategori sensitif seperti bullying, identitas pelapor
                        dijaga kerahasiaannya dan hanya dapat diakses oleh guru BK
                        atau pihak yang berwenang menangani kasus tersebut.
                    </p>
                </details>

                <details class="group rounded-xl border border-slate-200 dark:border-slate-800
                        bg-white dark:bg-slate-900 p-5 open:pb-5">
                    <summary class="flex items-center justify-between cursor-pointer
                          font-semibold text-slate-900 dark:text-white list-none">
                        Berapa lama laporan biasanya ditindaklanjuti?
                        <svg class="w-4 h-4 text-slate-400 transition-transform
                        group-open:rotate-45 shrink-0 ml-3" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Waktu penanganan tergantung pada kategori dan tingkat urgensi
                        laporan. Setiap perubahan status akan terlihat langsung di
                        dashboard, sehingga siswa dapat memantau perkembangannya.
                    </p>
                </details>

                <details class="group rounded-xl border border-slate-200 dark:border-slate-800
                        bg-white dark:bg-slate-900 p-5 open:pb-5">
                    <summary class="flex items-center justify-between cursor-pointer
                          font-semibold text-slate-900 dark:text-white list-none">
                        Apakah saya bisa melampirkan foto sebagai bukti?
                        <svg class="w-4 h-4 text-slate-400 transition-transform
                        group-open:rotate-45 shrink-0 ml-3" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Bisa. Melampirkan foto sangat disarankan, terutama untuk
                        laporan fasilitas rusak atau kebersihan, agar pihak sekolah
                        dapat lebih cepat memahami kondisi sebenarnya.
                    </p>
                </details>

                <details class="group rounded-xl border border-slate-200 dark:border-slate-800
                        bg-white dark:bg-slate-900 p-5 open:pb-5">
                    <summary class="flex items-center justify-between cursor-pointer
                          font-semibold text-slate-900 dark:text-white list-none">
                        Apa yang terjadi setelah laporan berstatus "Selesai"?
                        <svg class="w-4 h-4 text-slate-400 transition-transform
                        group-open:rotate-45 shrink-0 ml-3" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Laporan akan tersimpan sebagai riwayat dan tetap dapat dibuka
                        kembali kapan saja. Jika masalah muncul lagi, siswa disarankan
                        membuat laporan baru agar tercatat sebagai kasus terpisah.
                    </p>
                </details>

            </div>

        </div>

    </section>



    <!-- =========================================================
     CTA
  ========================================================= -->

    <section class="px-6 pb-20">

        <div class="max-w-4xl mx-auto rounded-2xl bg-primary
                px-7 py-12 md:px-12 md:py-14 text-center">

            <h2 class="font-serif text-3xl md:text-4xl font-semibold text-white">
                Ada masalah di sekolah?
                Jangan dipendam sendiri.
            </h2>

            <p class="max-w-xl mx-auto mt-4 text-blue-100 leading-relaxed">
                Laporkan lewat SiLapor dan biarkan pihak sekolah membantu
                menyelesaikannya.
            </p>

            <a
                href="<?= htmlspecialchars($baseUrl . 'silapor/buat.php') ?>"
                class="inline-flex items-center gap-2 mt-7 rounded-lg bg-white
               px-5 py-2.5 text-sm font-semibold text-primary
               hover:bg-blue-50 transition">
                Buat Laporan Sekarang

                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14" />
                    <path d="m13 6 6 6-6 6" />
                </svg>
            </a>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>


    <script
        type="module"
        src="<?= htmlspecialchars($baseUrl) ?>assets/js/app.js"
        defer></script>

</body>

</html>