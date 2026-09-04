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
          Tentang EduCare
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
                   dark:text-white">
        Satu Platform untuk
        <span class="text-primary dark:text-blue-400">
          Sekolah yang Lebih Baik
        </span>
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
                   dark:text-slate-400">
        EduCare adalah platform digital sekolah yang menghubungkan
        proses pembelajaran, komunikasi, dan pelaporan dalam satu
        sistem yang sederhana dan mudah digunakan.
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
                       transition">
          Mulai Menggunakan EduCare

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
                           text-primary">
            Tentang Kami
          </p>

        </div>


        <!-- Content -->

        <div class="space-y-6">

          <h2
            class="font-serif
                           text-3xl
                           md:text-4xl
                           font-semibold
                           leading-tight">
            Pendidikan tidak hanya
            tentang belajar.
          </h2>

          <p
            class="text-base
                           md:text-lg
                           leading-relaxed
                           text-slate-600
                           dark:text-slate-400">
            EduCare dibuat dari sebuah kebutuhan sederhana:
            bagaimana membuat aktivitas sekolah menjadi lebih
            terhubung secara digital.
          </p>

          <p
            class="text-base
                           leading-relaxed
                           text-slate-600
                           dark:text-slate-400">
            Dalam satu platform, siswa dapat mengakses materi,
            mengikuti pembelajaran, mengerjakan kuis, dan
            memantau perkembangan mereka. Di sisi lain, guru
            dan pihak sekolah dapat mengelola informasi serta
            menerima laporan dari siswa dengan lebih terstruktur.
          </p>

          <p
            class="text-base
                           leading-relaxed
                           text-slate-600
                           dark:text-slate-400">
            Dengan pendekatan tersebut, EduCare ingin membantu
            menciptakan lingkungan sekolah yang lebih terbuka,
            responsif, dan siap menghadapi kebutuhan pendidikan
            di era digital.
          </p>

        </div>

      </div>

    </div>

  </section>



  <!-- =========================================================
     VISI & MISI
========================================================= -->

  <section class="px-6 py-20">

    <div class="max-w-5xl mx-auto">

      <div class="text-center mb-12">

        <p
          class="text-xs
                       font-bold
                       uppercase
                       tracking-[0.18em]
                       text-primary
                       mb-3">
          Visi & Tujuan
        </p>

        <h2
          class="font-serif
                       text-3xl
                       md:text-4xl
                       font-semibold">
          Membangun sekolah yang lebih terhubung
        </h2>

      </div>


      <div class="grid md:grid-cols-2 gap-5">


        <!-- Vision -->

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7
                       md:p-8">

          <div
            class="w-11 h-11
                           rounded-xl
                           bg-blue-50
                           dark:bg-blue-500/10
                           text-primary
                           flex items-center
                           justify-center
                           mb-6">

            <svg
              width="21"
              height="21"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8">
              <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
              <circle cx="12" cy="12" r="3" />
            </svg>

          </div>

          <h3
            class="text-xl
                           font-semibold
                           mb-3">
            Visi
          </h3>

          <p
            class="leading-relaxed
                           text-slate-600
                           dark:text-slate-400">
            Menjadi platform digital yang membantu sekolah
            menciptakan proses belajar dan komunikasi yang
            lebih mudah, transparan, dan terintegrasi.
          </p>

        </div>



        <!-- Mission -->

        <div
          class="about-card
                       rounded-2xl
                       border
                       border-slate-200
                       dark:border-slate-800
                       bg-white
                       dark:bg-slate-900
                       p-7
                       md:p-8">

          <div
            class="w-11 h-11
                           rounded-xl
                           bg-blue-50
                           dark:bg-blue-500/10
                           text-primary
                           flex items-center
                           justify-center
                           mb-6">

            <svg
              width="21"
              height="21"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8">
              <path d="M12 2v20" />
              <path d="m5 9 7-7 7 7" />
              <path d="M5 15h14" />
            </svg>

          </div>

          <h3
            class="text-xl
                           font-semibold
                           mb-3">
            Tujuan
          </h3>

          <p
            class="leading-relaxed
                           text-slate-600
                           dark:text-slate-400">
            Menghadirkan teknologi yang sederhana dan bermanfaat
            agar siswa, guru, dan sekolah dapat beraktivitas
            dengan lebih efektif.
          </p>

        </div>

      </div>

    </div>

  </section>



  <!-- =========================================================
     PERAN PENGGUNA (Siswa / Guru / Sekolah)
========================================================= -->

  <section
    class="px-6 py-20
           bg-slate-50
           dark:bg-slate-900/40
           border-y
           border-slate-100
           dark:border-slate-900">

    <div class="max-w-5xl mx-auto">

      <!-- Heading -->
      <div class="grid md:grid-cols-[1fr_320px] gap-8 items-start
                  border-b border-slate-200 dark:border-slate-800 pb-10 mb-2">

        <h2 class="font-serif text-3xl md:text-4xl font-semibold leading-tight">
          Satu login, tiga
          pengalaman yang fokus.
        </h2>

        <p class="text-slate-600 dark:text-slate-400 leading-relaxed md:pt-2">
          Siswa, guru, dan pihak sekolah masing-masing punya ruang kerja
          sendiri, sementara seluruh sekolah berjalan di atas data yang
          sama dan terhubung.
        </p>

      </div>


      <!-- Row: Siswa -->
      <div class="grid md:grid-cols-[80px_260px_1fr] gap-6 md:gap-10
                  py-10 border-b border-slate-200 dark:border-slate-800">

        <p class="font-serif text-xl text-primary dark:text-blue-400">01</p>

        <div>
          <h3 class="text-xl font-semibold mb-1">Siswa</h3>
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500 mb-3">
            Belajar
          </p>
          <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
            Satu tempat untuk materi, tugas, dan hasil belajar, lengkap
            dengan kuis dan pemantauan progress.
          </p>
        </div>

        <div class="grid sm:grid-cols-2 gap-x-8 gap-y-3 content-start">
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Akses materi dan tugas pembelajaran
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Kuis dan evaluasi terstruktur
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Pantau progress belajar
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Sampaikan laporan lewat SiLapor
          </p>
        </div>

      </div>


      <!-- Row: Guru -->
      <div class="grid md:grid-cols-[80px_260px_1fr] gap-6 md:gap-10
                  py-10 border-b border-slate-200 dark:border-slate-800">

        <p class="font-serif text-xl text-primary dark:text-blue-400">02</p>

        <div>
          <h3 class="text-xl font-semibold mb-1">Guru</h3>
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500 mb-3">
            Mengajar
          </p>
          <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
            Kelola aktivitas pembelajaran dan informasi siswa dengan
            lebih terstruktur, tanpa proses yang berbelit.
          </p>
        </div>

        <div class="grid sm:grid-cols-2 gap-x-8 gap-y-3 content-start">
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Kelola aktivitas pembelajaran
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Buat dan nilai kuis siswa
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Pantau perkembangan siswa
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Terima dan tindak lanjuti laporan SiLapor
          </p>
        </div>

      </div>


      <!-- Row: Sekolah -->
      <div class="grid md:grid-cols-[80px_260px_1fr] gap-6 md:gap-10 py-10">

        <p class="font-serif text-xl text-primary dark:text-blue-400">03</p>

        <div>
          <h3 class="text-xl font-semibold mb-1">Sekolah</h3>
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500 mb-3">
            Kelola
          </p>
          <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
            Dorong pengelolaan sekolah yang lebih modern lewat data
            yang terintegrasi dalam satu ekosistem.
          </p>
        </div>

        <div class="grid sm:grid-cols-2 gap-x-8 gap-y-3 content-start">
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Kelola data siswa dan guru terpusat
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Pantau seluruh aktivitas sekolah
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Kelola laporan SiLapor secara terstruktur
          </p>
          <p class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
            <span class="text-primary dark:text-blue-400 mt-0.5">✓</span>
            Satu ekosistem untuk seluruh sekolah
          </p>
        </div>

      </div>

    </div>

  </section>



  <!-- =========================================================
     SIMPLE STAT
========================================================= -->

  <section class="px-6 py-20">

    <div class="max-w-4xl mx-auto">

      <div
        class="rounded-2xl
                   border
                   border-slate-200
                   dark:border-slate-800
                   bg-white
                   dark:bg-slate-900
                   p-8
                   md:p-10">

        <div
          class="grid
                       sm:grid-cols-3
                       gap-8
                       text-center">

          <div>

            <p
              class="font-serif
                               text-3xl
                               font-semibold
                               text-primary">
              <?= (int) $totalMateri ?>
            </p>

            <p
              class="text-sm
                               text-slate-500
                               dark:text-slate-400
                               mt-1">
              Materi Pembelajaran
            </p>

          </div>


          <div>

            <p
              class="font-serif
                               text-3xl
                               font-semibold
                               text-primary">
              <?= (int) $totalVideo ?>
            </p>

            <p
              class="text-sm
                               text-slate-500
                               dark:text-slate-400
                               mt-1">
              Video Pembelajaran
            </p>

          </div>


          <div>

            <p
              class="font-serif
                               text-3xl
                               font-semibold
                               text-primary">
              1 Platform
            </p>

            <p
              class="text-sm
                               text-slate-500
                               dark:text-slate-400
                               mt-1">
              Untuk Ekosistem Sekolah
            </p>

          </div>

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
                   text-white">
        Mari mulai perjalanan belajar
        yang lebih baik.
      </h2>

      <p
        class="max-w-xl
                   mx-auto
                   mt-4
                   text-blue-100
                   leading-relaxed">
        Bergabung dengan EduCare dan nikmati pengalaman
        pembelajaran sekolah yang lebih sederhana dan terhubung.
      </p>

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
                   transition">
        Mulai Sekarang

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