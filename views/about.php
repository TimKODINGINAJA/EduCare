<?php

require_once __DIR__ . '/../function.php';

$pageTitle = 'Tentang EduCare — Profil';

$materiData = readJSON('materi.json');
$totalMateri = count($materiData);
$totalVideo = 0;
foreach ($materiData as $m) {
  if (!empty($m['video_url'])) {
    $totalVideo++;
  }
}

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
  </style>
</head>

<body class="bg-white text-slate-900 dark:bg-slate-950 dark:text-white antialiased transition-colors duration-200">

  <?php include __DIR__ . '/../includes/navbar.php'; ?>


  <!-- =========================================================
     HERO
========================================================= -->

  <section class="px-6 pt-20 pb-16 md:pt-28 md:pb-20">

    <div class="max-w-5xl mx-auto text-center">

      <!-- Badge -->
      <div class="fade-up inline-flex items-center gap-2
                    rounded-full border border-slate-200
                    dark:border-slate-800
                    px-3 py-1.5 mb-7">

        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>

        <span
          class="text-xs font-semibold tracking-wide
                       text-slate-500 dark:text-slate-400"
          data-i18n="about.badge">
          Profil
        </span>

      </div>


      <!-- Heading -->

      <h1
        class="fade-up delay-1
                   font-serif
                   text-4xl
                   sm:text-5xl
                   md:text-6xl
                   font-semibold
                   leading-tight
                   tracking-tight
                   text-slate-900
                   dark:text-white"
        data-i18n="about.title">
        Satu Platform untuk Menjawab Dua Kebutuhan Sekolah Sekaligus
      </h1>


      <!-- Description -->

      <p
        class="fade-up delay-2
                   max-w-2xl
                   mx-auto
                   mt-6
                   text-base
                   md:text-lg
                   leading-relaxed
                   text-slate-600
                   dark:text-slate-400"
        data-i18n="about.deck">
        Di tengah sekolah yang masih mengelola pembelajaran dan pengaduan secara terpisah, EduCare hadir menyatukan keduanya — dari ruang kelas digital sampai laporan fasilitas rusak, dalam satu alur yang sama.
      </p>


      <!-- CTA -->

      <div class="fade-up delay-3 flex flex-wrap justify-center gap-3 mt-8">

        <a
          href="<?= htmlspecialchars($baseUrl . 'auth/register.php') ?>"
          class="inline-flex items-center gap-2
                       rounded-lg
                       bg-primary
                       px-5 py-2.5
                       text-sm
                       font-semibold
                       text-white
                       hover:bg-blue-800
                       transition"
          data-i18n="about.cta_button">
          Mulai Belajar

          <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2">
            <path d="M5 12h14" />
            <path d="m13 6 6 6-6 6" />
          </svg>
        </a>

        <a
          href="#tentang"
          class="inline-flex items-center
                       rounded-lg
                       border
                       border-slate-200
                       dark:border-slate-800
                       px-5 py-2.5
                       text-sm
                       font-semibold
                       text-slate-700
                       dark:text-slate-300
                       hover:bg-slate-50
                       dark:hover:bg-slate-900
                       transition">
          Pelajari lebih lanjut
        </a>

      </div>

    </div>

  </section>



  <!-- =========================================================
     INTRO
========================================================= -->

  <section
    id="tentang"
    class="px-6 py-16 md:py-20
           border-y
           border-slate-100
           dark:border-slate-900
           bg-slate-50/70
           dark:bg-slate-900/30">

    <div class="max-w-4xl mx-auto">

      <div class="grid md:grid-cols-[180px_1fr] gap-8 md:gap-14">

        <!-- Label -->

        <div>

          <p
            class="text-xs
                           font-bold
                           uppercase
                           tracking-[0.18em]
                           text-primary"
            data-i18n="about.crumb">
            Tentang EduCare
          </p>

        </div>


        <!-- Content -->

        <div class="space-y-6">

          <h2
            class="font-serif
                           text-3xl
                           md:text-4xl
                           font-semibold
                           leading-tight"
            data-i18n="about.title">
            Satu Platform untuk Menjawab Dua Kebutuhan Sekolah Sekaligus
          </h2>

          <p
            class="text-base
                           md:text-lg
                           leading-relaxed
                           text-slate-600
                           dark:text-slate-400"
            data-i18n="about.p1">
            EduCare dimulai dari pengamatan sederhana: siswa belajar lewat satu aplikasi, sementara keluhan tentang fasilitas atau lingkungan sekolah dilaporkan lewat cara yang berbeda-beda — dari kertas, grup chat, sampai kotak saran yang jarang dibuka. Dua kebutuhan yang sebenarnya berjalan setiap hari, tapi tidak pernah benar-benar terhubung.
          </p>

          <p
            class="text-base
                           leading-relaxed
                           text-slate-600
                           dark:text-slate-400"
            data-i18n-html="about.p2_html">
            Dari situ EduCare dibangun sebagai satu platform yang menggabungkan <strong class="font-semibold text-slate-900 dark:text-white">Belajar Online</strong> — mulai dari mata pelajaran umum seperti Matematika dan IPA, sampai bidang IT seperti Pemrograman dan AI — dengan <strong class="font-semibold text-slate-900 dark:text-white">SiLapor Sekolah</strong>, sistem pelaporan yang membuat setiap laporan tercatat, terlacak statusnya, dan sampai ke pihak yang tepat.
          </p>

          <blockquote
            class="border-l-4 border-primary pl-6 italic text-slate-700 dark:text-slate-300 text-lg"
            data-i18n="about.quote">
            “Sekolah yang responsif bukan cuma soal mengajar dengan baik, tapi juga mendengar dengan cepat.”
          </blockquote>

          <p
            class="text-base
                           leading-relaxed
                           text-slate-600
                           dark:text-slate-400"
            data-i18n="about.p3">
            Bagi siswa, ini berarti satu akun untuk mengakses materi, mengerjakan kuis, dan memantau progres belajar dari mata pelajaran apa pun. Bagi guru dan pihak sekolah, setiap laporan yang masuk melalui SiLapor punya status yang jelas — mulai dari diterima, diproses, hingga selesai ditangani — sehingga tidak ada laporan yang hilang begitu saja.
          </p>

          <p
            class="text-base
                           leading-relaxed
                           text-slate-600
                           dark:text-slate-400"
            data-i18n="about.p4">
            Hari ini, EduCare digunakan oleh guru dan siswa untuk menjalankan dua sisi kehidupan sekolah yang sama pentingnya: belajar setiap hari, dan memastikan lingkungan belajar itu sendiri tetap layak digunakan.
          </p>

        </div>

      </div>

    </div>

  </section>



  <!-- =========================================================
     FAKTA SINGKAT
========================================================= -->

  <section class="px-6 py-20">

    <div class="max-w-5xl mx-auto">

      <div class="text-center mb-12">

        <h2
          class="font-serif
                       text-3xl
                       md:text-4xl
                       font-semibold"
          data-i18n="about.facts_heading">
          Fakta Singkat
        </h2>

      </div>


      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7
                       text-center">

          <p class="text-4xl font-bold text-primary dark:text-blue-400"><?= $totalMateri ?>+</p>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400" data-i18n="about.fact1_label">Materi tersedia</p>

        </div>

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7
                       text-center">

          <p class="text-4xl font-bold text-primary dark:text-blue-400"><?= $totalVideo ?>+</p>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400" data-i18n="about.fact2_label">Video pembelajaran</p>

        </div>

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7
                       text-center">

          <p class="text-4xl font-bold text-primary dark:text-blue-400">50+</p>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400" data-i18n="about.fact3_label">Siswa aktif</p>

        </div>

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7
                       text-center">

          <p class="text-4xl font-bold text-primary dark:text-blue-400">5</p>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400" data-i18n="about.fact4_label">Sekolah mitra</p>

        </div>

      </div>

    </div>

  </section>



  <!-- =========================================================
     BACA JUGA
========================================================= -->

  <section
    class="px-6 py-20
           bg-slate-50
           dark:bg-slate-900/40
           border-y
           border-slate-100
           dark:border-slate-900">

    <div class="max-w-5xl mx-auto">

      <div class="text-center mb-12">

        <h2
          class="font-serif
                       text-3xl
                       md:text-4xl
                       font-semibold"
          data-i18n="about.related_heading">
          Baca Juga
        </h2>

      </div>

      <div class="grid md:grid-cols-3 gap-5">

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7">

          <h3 class="text-lg font-semibold" data-i18n="about.related1_title">Belajar Pemrograman dari Nol lewat EduCare</h3>
          <p class="mt-1 text-sm text-primary dark:text-blue-400" data-i18n="about.related1_cat">Edukasi IT</p>
          <a href="#" class="mt-4 inline-block text-sm font-medium text-slate-600 hover:text-primary dark:text-slate-400 dark:hover:text-blue-400 transition">Baca →</a>

        </div>

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7">

          <h3 class="text-lg font-semibold" data-i18n="about.related2_title">Cara Kerja SiLapor: dari Laporan Masuk sampai Selesai</h3>
          <p class="mt-1 text-sm text-primary dark:text-blue-400" data-i18n="about.related2_cat">SiLapor</p>
          <a href="#" class="mt-4 inline-block text-sm font-medium text-slate-600 hover:text-primary dark:text-slate-400 dark:hover:text-blue-400 transition">Baca →</a>

        </div>

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7">

          <h3 class="text-lg font-semibold" data-i18n="about.related3_title">Memantau Progres Belajar Siswa secara Real-Time</h3>
          <p class="mt-1 text-sm text-primary dark:text-blue-400" data-i18n="about.related3_cat">Belajar Online</p>
          <a href="#" class="mt-4 inline-block text-sm font-medium text-slate-600 hover:text-primary dark:text-slate-400 dark:hover:text-blue-400 transition">Baca →</a>

        </div>

      </div>

    </div>

  </section>



  <!-- =========================================================
     CTA
========================================================= -->

  <section class="px-6 pb-20">

    <div
      class="max-w-4xl
               mx-auto
               rounded-2xl
               bg-primary
               px-7
               py-12
               md:px-12
               md:py-14
               text-center">

      <h2
        class="font-serif
                   text-3xl
                   md:text-4xl
                   font-semibold
                   text-white"
        data-i18n="about.cta_text">
        Mau lihat sendiri bagaimana EduCare bekerja?
      </h2>

      <a
        href="<?= htmlspecialchars($baseUrl . 'auth/register.php') ?>"
        class="inline-flex
                   items-center
                   gap-2
                   mt-7
                   rounded-lg
                   bg-white
                   px-5
                   py-2.5
                   text-sm
                   font-semibold
                   text-primary
                   hover:bg-blue-50
                   transition"
        data-i18n="about.cta_button">
        Mulai Belajar

        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2">
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