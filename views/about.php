<?php
require_once __DIR__ . '/../function.php';
$pageTitle = 'Tentang EduCare — Profil';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title data-i18n="page.about"><?= htmlspecialchars($pageTitle) ?></title>

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

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

  <!-- Override dark mode global (navbar, footer, warna umum) -->
  <link rel="stylesheet" href="<?= htmlspecialchars(assetUrl('assets/css/style.css')) ?>">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#2563EB',
            'primary-deep': '#1D4ED8',
          },
          fontFamily: {
            serif: ['"Source Serif 4"', 'Georgia', 'serif'],
            body: ['"Inter"', 'sans-serif'],
            mono: ['"JetBrains Mono"', 'monospace'],
          }
        }
      }
    }
  </script>

  <style>
    * {
      font-family: 'Inter', sans-serif;
    }

    .font-serif {
      font-family: 'Source Serif 4', Georgia, serif;
    }

    .font-mono-tech {
      font-family: 'JetBrains Mono', monospace;
    }

    body {
      background: #ffffff;
      color: #0f172a;
    }

    html.dark body {
      background: #0b1120;
      color: #e2e8f0;
    }

    .drop-cap::first-letter {
      font-family: 'Source Serif 4', Georgia, serif;
      font-size: 3.6rem;
      font-weight: 700;
      float: left;
      line-height: .8;
      padding-right: .55rem;
      padding-top: .35rem;
      color: #2563EB;
    }

    .pull-quote {
      border-left: 3px solid #2563EB;
    }

    .related-item {
      transition: background-color .2s ease;
    }

    .related-item:hover {
      background-color: #F8FAFC;
    }

    .tag-pill {
      transition: border-color .2s ease, color .2s ease;
    }

    .tag-pill:hover {
      border-color: #2563EB;
      color: #2563EB;
    }

    @keyframes riseIn {
      from {
        opacity: 0;
        transform: translateY(16px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .rise {
      animation: riseIn .6s cubic-bezier(.16, 1, .3, 1) both;
    }

    .d1 {
      animation-delay: .05s
    }

    .d2 {
      animation-delay: .12s
    }

    .d3 {
      animation-delay: .2s
    }

    .d4 {
      animation-delay: .28s
    }
  </style>
</head>

<body class="antialiased bg-white dark:bg-[#0b1120] transition-colors duration-300">


  <?php include __DIR__ . '/../includes/navbar.php'; ?>

  <section id="about" class="px-6 py-20 md:py-24">
    <div class="max-w-6xl mx-auto">

      <!-- ===== Section label ===== -->
      <div class="rise flex items-center gap-2 mb-6">
        <span class="w-2.5 h-2.5 bg-primary rounded-sm"></span>
        <p class="text-[12.5px] font-bold tracking-[0.14em] text-primary uppercase" data-i18n="about.badge">Profil</p>
        <span class="text-slate-300 dark:text-slate-600">/</span>
        <p class="text-[12.5px] font-semibold tracking-[0.1em] text-slate-400 dark:text-slate-500 uppercase" data-i18n="about.crumb">Tentang EduCare</p>
      </div>

      <!-- ===== Headline + deck ===== -->
      <div class="max-w-3xl mb-8">
        <h2 class="rise d1 font-serif text-[2.15rem] sm:text-[2.75rem] font-bold leading-[1.15] text-slate-900 dark:text-white" data-i18n="about.title">
          Satu Platform untuk Menjawab Dua Kebutuhan Sekolah Sekaligus
        </h2>
        <p class="rise d2 mt-4 text-[17px] text-slate-500 dark:text-slate-400 leading-relaxed font-serif" data-i18n="about.deck">
          Di tengah sekolah yang masih mengelola pembelajaran dan pengaduan secara terpisah, EduCare hadir menyatukan keduanya — dari ruang kelas digital sampai laporan fasilitas rusak, dalam satu alur yang sama.
        </p>
      </div>

      <!-- ===== Byline row ===== -->
      <div class="rise d3 flex flex-wrap items-center gap-4 pb-6 mb-10 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-primary flex items-center justify-center text-white text-[13px] font-bold font-serif">E</div>
          <div>
            <p class="text-[13.5px] font-semibold text-slate-800 dark:text-slate-200" data-i18n="about.byline_name">Tim EduCare</p>
            <p class="text-[12px] text-slate-400 dark:text-slate-500 font-mono-tech" data-i18n="about.byline_meta">Diperbarui 20 Jul 2026 · 4 menit baca</p>
          </div>
        </div>
        <div class="ml-auto flex items-center gap-2">
          <button class="w-8 h-8 rounded-full border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-primary hover:border-primary transition-colors" aria-label="Bagikan">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 12v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7M16 6l-4-4-4 4M12 2v14" />
            </svg>
          </button>
          <button class="w-8 h-8 rounded-full border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-primary hover:border-primary transition-colors" aria-label="Simpan">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- ===== Main grid: article column + sidebar ===== -->
      <div class="grid lg:grid-cols-[1fr_320px] gap-12">

        <!-- ===================== ARTICLE COLUMN ===================== -->
        <div class="rise d4 min-w-0">

          <!-- Feature visual styled like a news lead image -->
          <figure class="mb-8">
            <div class="rounded-lg overflow-hidden border border-slate-200 dark:border-slate-800 bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-900 dark:to-slate-800 aspect-[16/9] flex items-center justify-center relative">
              <div class="grid grid-cols-3 gap-3 w-full max-w-md px-6">
                <div class="col-span-2 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-3">
                  <p class="text-[10px] font-mono-tech text-slate-400 dark:text-slate-500 mb-1.5">BELAJAR ONLINE</p>
                  <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 mb-1.5">
                    <div class="h-1.5 rounded-full bg-primary w-3/4"></div>
                  </div>
                  <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 w-2/3"></div>
                </div>
                <div class="rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-3 flex flex-col items-center justify-center">
                  <span class="text-[18px]">📚</span>
                </div>
                <div class="rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-3 flex flex-col items-center justify-center">
                  <span class="text-[18px]">📢</span>
                </div>
                <div class="col-span-2 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-3">
                  <p class="text-[10px] font-mono-tech text-slate-400 dark:text-slate-500 mb-1.5">SILAPOR</p>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[9px] font-medium border border-amber-200 dark:border-amber-500/30">in-review</span>
                </div>
              </div>
            </div>
            <figcaption class="text-[12px] text-slate-400 dark:text-slate-500 mt-2.5 font-mono-tech" data-i18n="about.figcaption">Ilustrasi — tampilan Belajar Online dan SiLapor dalam satu dashboard EduCare.</figcaption>
          </figure>

          <p class="drop-cap text-[16.5px] text-slate-700 dark:text-slate-300 leading-[1.85] mb-6" data-i18n="about.p1">
            EduCare dimulai dari pengamatan sederhana: siswa belajar lewat satu aplikasi, sementara keluhan tentang fasilitas atau lingkungan sekolah dilaporkan lewat cara yang berbeda-beda — dari kertas, grup chat, sampai kotak saran yang jarang dibuka. Dua kebutuhan yang sebenarnya berjalan setiap hari, tapi tidak pernah benar-benar terhubung.
          </p>

          <p class="text-[16.5px] text-slate-700 dark:text-slate-300 leading-[1.85] mb-6" data-i18n-html="about.p2_html">
            Dari situ EduCare dibangun sebagai satu platform yang menggabungkan <strong class="font-semibold text-slate-900">Belajar Online</strong> — mulai dari mata pelajaran umum seperti Matematika dan IPA, sampai bidang IT seperti Pemrograman dan AI — dengan <strong class="font-semibold text-slate-900">SiLapor Sekolah</strong>, sistem pelaporan yang membuat setiap laporan tercatat, terlacak statusnya, dan sampai ke pihak yang tepat.
          </p>

          <blockquote class="pull-quote pl-5 py-1 my-8">
            <p class="font-serif text-[20px] leading-relaxed text-slate-800 dark:text-slate-200 italic" data-i18n="about.quote">
              "Sekolah yang responsif bukan cuma soal mengajar dengan baik, tapi juga mendengar dengan cepat."
            </p>
          </blockquote>

          <p class="text-[16.5px] text-slate-700 dark:text-slate-300 leading-[1.85] mb-6" data-i18n="about.p3">
            Bagi siswa, ini berarti satu akun untuk mengakses materi, mengerjakan kuis, dan memantau progres belajar dari mata pelajaran apa pun. Bagi guru dan pihak sekolah, setiap laporan yang masuk melalui SiLapor punya status yang jelas — mulai dari diterima, diproses, hingga selesai ditangani — sehingga tidak ada laporan yang hilang begitu saja.
          </p>

          <p class="text-[16.5px] text-slate-700 dark:text-slate-300 leading-[1.85] mb-8" data-i18n="about.p4">
            Hari ini, EduCare digunakan oleh guru dan siswa untuk menjalankan dua sisi kehidupan sekolah yang sama pentingnya: belajar setiap hari, dan memastikan lingkungan belajar itu sendiri tetap layak digunakan.
          </p>

          <!-- Tags -->
          <div class="flex flex-wrap gap-2 pt-6 border-t border-slate-200 dark:border-slate-800">
            <span class="tag-pill px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 text-[12px] font-medium text-slate-500 dark:text-slate-400" data-i18n="about.tag1">Pendidikan</span>
            <span class="tag-pill px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 text-[12px] font-medium text-slate-500 dark:text-slate-400" data-i18n="about.tag2">Digitalisasi Sekolah</span>
            <span class="tag-pill px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 text-[12px] font-medium text-slate-500 dark:text-slate-400" data-i18n="about.tag3">SiLapor</span>
            <span class="tag-pill px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 text-[12px] font-medium text-slate-500 dark:text-slate-400" data-i18n="about.tag4">Belajar Online</span>
          </div>
        </div>

        <!-- ===================== SIDEBAR ===================== -->
        <aside class="space-y-8">

          <!-- Quick facts box -->
          <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 p-5">
            <p class="text-[11px] font-bold tracking-[0.1em] text-slate-400 dark:text-slate-500 uppercase mb-4" data-i18n="about.facts_heading">Fakta Singkat</p>
            <dl class="space-y-3.5">
              <div class="flex items-center justify-between">
                <dt class="text-[13px] text-slate-500 dark:text-slate-400" data-i18n="about.fact1_label">Materi tersedia</dt>
                <dd class="font-mono-tech text-[13px] font-semibold text-slate-800 dark:text-slate-200">100+</dd>
              </div>
              <div class="flex items-center justify-between">
                <dt class="text-[13px] text-slate-500 dark:text-slate-400" data-i18n="about.fact2_label">Video pembelajaran</dt>
                <dd class="font-mono-tech text-[13px] font-semibold text-slate-800 dark:text-slate-200">50+</dd>
              </div>
              <div class="flex items-center justify-between">
                <dt class="text-[13px] text-slate-500 dark:text-slate-400" data-i18n="about.fact3_label">Siswa aktif</dt>
                <dd class="font-mono-tech text-[13px] font-semibold text-slate-800 dark:text-slate-200">1.000+</dd>
              </div>
              <div class="flex items-center justify-between">
                <dt class="text-[13px] text-slate-500 dark:text-slate-400" data-i18n="about.fact4_label">Sekolah mitra</dt>
                <dd class="font-mono-tech text-[13px] font-semibold text-slate-800 dark:text-slate-200">SMK Telekomunikasi Telesandi Bekasi</dd>
              </div>
            </dl>
          </div>

          <!-- Related list -->
          <div>
            <p class="text-[11px] font-bold tracking-[0.1em] text-slate-400 dark:text-slate-500 uppercase mb-3 px-1" data-i18n="about.related_heading">Baca Juga</p>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 border-t border-b border-slate-100 dark:border-slate-800">
              <a href="#" class="related-item flex gap-3 py-3.5 px-1">
                <div class="w-16 h-16 shrink-0 rounded-md bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-[20px]">💻</div>
                <div class="min-w-0">
                  <p class="text-[13.5px] font-semibold text-slate-800 dark:text-slate-200 leading-snug line-clamp-2" data-i18n="about.related1_title">Belajar Pemrograman dari Nol lewat EduCare</p>
                  <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-mono-tech" data-i18n="about.related1_cat">Edukasi IT</p>
                </div>
              </a>
              <a href="#" class="related-item flex gap-3 py-3.5 px-1">
                <div class="w-16 h-16 shrink-0 rounded-md bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-[20px]">📢</div>
                <div class="min-w-0">
                  <p class="text-[13.5px] font-semibold text-slate-800 dark:text-slate-200 leading-snug line-clamp-2" data-i18n="about.related2_title">Cara Kerja SiLapor: dari Laporan Masuk sampai Selesai</p>
                  <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-mono-tech" data-i18n="about.related2_cat">SiLapor</p>
                </div>
              </a>
              <a href="#" class="related-item flex gap-3 py-3.5 px-1">
                <div class="w-16 h-16 shrink-0 rounded-md bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-[20px]">📈</div>
                <div class="min-w-0">
                  <p class="text-[13.5px] font-semibold text-slate-800 dark:text-slate-200 leading-snug line-clamp-2" data-i18n="about.related3_title">Memantau Progres Belajar Siswa secara Real-Time</p>
                  <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-mono-tech" data-i18n="about.related3_cat">Belajar Online</p>
                </div>
              </a>
            </div>
          </div>

          <!-- CTA box - FIXED PATH TO REGISTER -->
          <div class="rounded-lg bg-slate-900 dark:bg-slate-950 dark:border dark:border-slate-800 p-5">
            <p class="font-serif text-[15px] text-white leading-snug mb-3" data-i18n="about.cta_text">Mau lihat sendiri bagaimana EduCare bekerja?</p>
            <a href="<?= htmlspecialchars($baseUrl . 'auth/register.php') ?>" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-white bg-primary hover:bg-primary-deep px-4 py-2.5 rounded-md transition-colors">
              <span data-i18n="about.cta_button">Mulai Belajar</span>
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M5 12h14M13 6l6 6-6 6" />
              </svg>
            </a>
          </div>
        </aside>

      </div>
    </div>
  </section>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <!-- Javascript: wajib dimuat supaya tombol menu mobile (hamburger) bisa
     membuka drawer -- termasuk lang switcher versi mobile yang ada di
     dalam drawer tersebut. Tanpa ini, di layar mobile drawer tidak akan
     pernah terbuka. -->
  <script type="module" src="<?= htmlspecialchars($baseUrl) ?>assets/js/app.js" defer></script>

</body>

</html>