<!-- ===================== HERO ===================== -->
<section id="home" class="relative pt-32 sm:pt-40 pb-24 px-4 sm:px-6 overflow-hidden">
  <div class="max-w-6xl mx-auto">
    <div class="grid lg:grid-cols-[1.05fr_1fr] gap-14 items-center">
      <div class="reveal" data-reveal="up">
        <h1 class="font-display text-[2.5rem] sm:text-[3.1rem] font-bold leading-[1.08] tracking-tight text-slate-900 dark:text-white"
          data-i18n-html="hero.title">
          Belajar dan melapor<br class="hidden sm:block">
          <span class="text-blue-600">satu platform</span> sekolah.
        </h1>
        <p class="mt-6 text-[16.5px] text-slate-500 dark:text-slate-400 leading-relaxed max-w-lg"
          data-i18n-html="hero.desc">
          EduCare menggabungkan <strong class="text-slate-700 dark:text-slate-300 font-semibold">Belajar Online</strong> — mata pelajaran umum seperti Matematika dan IPA, sampai bidang IT seperti Pemrograman dan AI — dengan <strong class="text-slate-700 dark:text-slate-300 font-semibold">SiLapor Sekolah</strong>, sistem pelaporan masalah lingkungan sekolah yang cepat dan transparan.
        </p>

        <div class="mt-9 flex flex-wrap items-center gap-3">
          <a href="auth/register.php" class="group px-6 py-3 rounded-lg text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors duration-200 inline-flex items-center gap-2">
            <span data-i18n="hero.cta1">Mulai Belajar</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform duration-200 group-hover:translate-x-0.5">
              <path d="M5 12h14M13 6l6 6-6 6" />
            </svg>
          </a>
          <a href="#silapor" class="px-6 py-3 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
            <span data-i18n="hero.cta2">Buat Laporan</span>
          </a>
        </div>

        <div class="mt-14 flex items-center gap-10 max-w-md border-t border-slate-200 dark:border-slate-800 pt-8">
          <div>
            <p class="font-display text-[26px] font-bold text-slate-900 dark:text-white"><span class="counter" data-target="13">0</span>+</p>
            <p class="text-[12.5px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="hero.stat1">Materi</p>
          </div>
          <div>
            <p class="font-display text-[26px] font-bold text-slate-900 dark:text-white"><span class="counter" data-target="8">0</span>+</p>
            <p class="text-[12.5px] text-slate-500 dark:text-slate-400 mt-1" data-i18n="hero.stat2">Video</p>
          </div>
        </div>
      </div>

      <!-- Dashboard Mockup (teks di dalam mockup juga bisa diterjemahkan dengan data-i18n) -->
      <div class="reveal float-card rounded-xl w-full max-w-full" data-reveal="scale" data-delay="150">
        <div class="rounded-xl border border-slate-200 dark:border-slate-700 shadow-[0_20px_50px_-25px_rgba(11,18,32,0.3)] dark:shadow-[0_20px_50px_-25px_rgba(0,0,0,0.6)] overflow-hidden bg-white dark:bg-slate-900">
          <div class="flex items-center gap-1.5 px-4 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
            <span class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-600 inline-block"></span>
            <span class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-600 inline-block"></span>
            <span class="w-3 h-3 rounded-full bg-slate-300 dark:bg-slate-600 inline-block"></span>
            <span class="ml-3 text-[11px] font-mono text-slate-400 dark:text-slate-500 truncate">educare.id/dashboard</span>
          </div>
          <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
              <div class="min-w-0">
                <p class="font-display font-semibold text-slate-900 dark:text-white" data-i18n="mockup.greeting">Halo, Nadia 👋</p>
                <p class="text-[13px] text-slate-400 dark:text-slate-500 truncate" data-i18n="mockup.class_school">Kelas 9A · SMKN 1 Cikarang</p>
              </div>
              <span class="text-[12px] font-mono text-slate-400 dark:text-slate-500 shrink-0" data-i18n="mockup.quiz_score">Nilai kuis: 90/100</span>
            </div>

            <p class="text-[13px] font-medium text-slate-500 dark:text-slate-400 mb-2.5" data-i18n="mockup.progress_label">Progres belajar</p>
            <div class="grid grid-cols-3 gap-2.5 mb-5">
              <div class="rounded-lg border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-3 py-2.5">
                <p class="text-[16px] leading-none">📖</p>
                <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-1.5" data-i18n="mockup.subject1">Matematika</p>
                <p class="font-mono text-[12px] font-semibold text-slate-800 dark:text-slate-200 mt-0.5">78%</p>
              </div>
              <div class="rounded-lg border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-3 py-2.5">
                <p class="text-[16px] leading-none">🧪</p>
                <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-1.5" data-i18n="mockup.subject2">IPA</p>
                <p class="font-mono text-[12px] font-semibold text-slate-800 dark:text-slate-200 mt-0.5">64%</p>
              </div>
              <div class="rounded-lg border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-3 py-2.5">
                <p class="text-[16px] leading-none">💻</p>
                <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-1.5" data-i18n="mockup.subject3">Pemrograman</p>
                <p class="font-mono text-[12px] font-semibold text-slate-800 dark:text-slate-200 mt-0.5">82%</p>
              </div>
            </div>

            <div class="space-y-3 mb-5">
              <div>
                <div class="flex justify-between text-[12.5px] text-slate-500 dark:text-slate-400 mb-1.5">
                  <span data-i18n="mockup.webdev">🌐 Web Development</span>
                  <span class="font-mono">82%</span>
                </div>
                <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                  <div class="h-1.5 rounded-full bg-blue-700 dark:bg-blue-500 bar-fill" data-width="82" style="width:0%"></div>
                </div>
              </div>
              <div>
                <div class="flex justify-between text-[12.5px] text-slate-500 dark:text-slate-400 mb-1.5">
                  <span data-i18n="mockup.uiux">🎨 UI/UX &amp; Desain</span>
                  <span class="font-mono">40%</span>
                </div>
                <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                  <div class="h-1.5 rounded-full bg-blue-700 dark:bg-blue-500 bar-fill" data-width="40" style="width:0%"></div>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-3 sm:px-4 py-3">
              <div class="flex items-center gap-3 min-w-0">
                <span class="text-[17px] shrink-0">🏚️</span>
                <p class="text-[13px] font-medium text-slate-700 dark:text-slate-300 truncate" data-i18n="mockup.report">Laporan #042 — Fasilitas Rusak</p>
              </div>
              <span class="px-2.5 py-1 text-[11px] font-medium rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 shrink-0">Diperiksa</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== UNTUK SIAPA ===================== -->
<section class="px-6 py-24">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-16 reveal" data-reveal="up">
      <h2 class="font-display text-[1.9rem] sm:text-3xl font-bold text-slate-900 dark:text-white leading-tight" data-i18n="forwhom.role1.title">Untuk siapa EduCare</h2>
    </div>

    <div class="grid lg:grid-cols-2 gap-px bg-slate-200 dark:bg-slate-700 max-w-5xl mx-auto rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
      <!-- Card Siswa -->
      <div class="reveal bg-white dark:bg-slate-900 p-8 sm:p-10 flex flex-col" data-reveal="up" data-delay="0">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 mb-6">
          <svg class="w-6 h-6 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747 0-6.002-4.5-10.747-10-10.747z" />
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3" data-i18n="forwhom.role1.title">Siswa</h3>
        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6 flex-grow" data-i18n="forwhom.role1.desc">Belajar materi, kerjakan kuis, pantau nilai dan progres, lalu laporkan masalah sekolah lewat SiLapor bila diperlukan.</p>
        <ul class="space-y-3 mb-8">
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-700 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-slate-700 dark:text-slate-300" data-i18n="forwhom.role1.feat1">Akses materi belajar sesuai kategori</span>
          </li>
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-700 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-slate-700 dark:text-slate-300" data-i18n="forwhom.role1.feat2">Kuis online untuk menguji pemahaman</span>
          </li>
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-700 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-slate-700 dark:text-slate-300" data-i18n="forwhom.role1.feat3">Lapor masalah sekolah secara langsung</span>
          </li>
        </ul>
        <a href="auth/register.php" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold text-sm transition-colors duration-200 mt-auto" data-i18n="forwhom.role1.cta">
          Mulai Belajar
        </a>
      </div>

      <!-- Card Guru -->
      <div class="reveal bg-white dark:bg-slate-900 p-8 sm:p-10 flex flex-col" data-reveal="up" data-delay="100">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 mb-6">
          <svg class="w-6 h-6 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5-4a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3" data-i18n="forwhom.role2.title">Guru</h3>
        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6 flex-grow" data-i18n="forwhom.role2.desc">Buat dan kelola materi, pantau progres siswa per kelas, dan tindak lanjuti laporan yang masuk lewat SiLapor.</p>
        <ul class="space-y-3 mb-8">
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-700 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-slate-700 dark:text-slate-300" data-i18n="forwhom.role2.feat1">Kelola materi dan kategori pelajaran</span>
          </li>
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-700 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-slate-700 dark:text-slate-300" data-i18n="forwhom.role2.feat2">Pantau nilai dan aktivitas siswa</span>
          </li>
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-700 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-slate-700 dark:text-slate-300" data-i18n="forwhom.role2.feat3">Tindak lanjuti laporan pengaduan</span>
          </li>
        </ul>
        <a href="auth/login.php" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-blue-700 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold text-sm transition-colors duration-200 mt-auto" data-i18n="forwhom.role2.cta">
          Mulai Belajar
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ===================== TEMA 1: BELAJAR ONLINE ===================== -->
<section id="edukasi" class="py-24 px-6 border-t border-slate-100 dark:border-slate-800">
  <div class="max-w-6xl mx-auto">
    <div class="max-w-lg mb-14 reveal" data-reveal="up">
      <p class="text-[13px] font-semibold text-blue-700 dark:text-blue-400 mb-3" data-i18n="edukasi.badge">Belajar Online</p>
      <h2 class="font-display text-[1.9rem] font-bold text-slate-900 dark:text-white leading-tight" data-i18n="edukasi.title">Dua jalur edukasi, satu dashboard</h2>
      <p class="text-slate-500 dark:text-slate-400 mt-4 text-[14.5px] leading-relaxed" data-i18n="edukasi.desc">Materi terstruktur, video pembelajaran, dan kuis interaktif untuk mata pelajaran umum maupun bidang IT.</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
      <!-- Edukasi Umum -->
      <div class="reveal rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900" data-reveal="left">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
          <span class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 flex items-center justify-center text-[16px] shrink-0">📚</span>
          <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="edukasi.umum.title">Edukasi Umum</h3>
        </div>
        <ul class="divide-y divide-slate-100 dark:divide-slate-800">
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">📖</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.umum.item1">Aljabar Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🧪</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.umum.item2">Interaksi Makhluk Hidup</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🌍</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.umum.item3">Keragaman Sosial dan Budaya Indonesia</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🌐</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.umum.item4">Bahasa Indonesia &amp; Bahasa Inggris</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🌱</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.umum.item5">Manajemen Waktu dan Kebiasaan Belajar Efektif</span>
          </li>
        </ul>
      </div>

      <!-- Edukasi IT -->
      <div class="reveal rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900" data-reveal="right">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
          <span class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 flex items-center justify-center text-[16px] shrink-0">💻</span>
          <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="edukasi.it.title">Edukasi IT</h3>
        </div>
        <ul class="divide-y divide-slate-100 dark:divide-slate-800">
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">💻</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item1">Dasar-Dasar HTML</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🎨</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item2">CSS Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">⚡</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item3">JavaScript Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🐘</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item4">PHP Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🐍</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item5">Python Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🗄️</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item6">Basis Data &amp; SQL</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🌿</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item7">Git &amp; GitHub</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-700 dark:hover:border-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[13px] shrink-0">🎯</span>
            <span class="text-[13.5px] text-slate-600 dark:text-slate-300 flex-1" data-i18n="edukasi.it.item8">UI/UX Design &amp; Figma</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- fitur ringkas -->
    <div id="fitur" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-6">
      <div class="reveal bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6" data-reveal="up" data-delay="100">
        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mb-4">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2">
            <path d="M9 11l3 3L22 4" />
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
          </svg>
        </div>
        <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="features.quiz.title">Kuis Online</h3>
        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 leading-relaxed mt-2" data-i18n="features.quiz.desc">Setiap materi ditutup kuis singkat untuk memastikan konsep melekat.</p>
      </div>
      <div class="reveal bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6" data-reveal="up" data-delay="200">
        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mb-4">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2">
            <path d="M3 3v18h18" />
            <path d="M7 15l4-5 3 3 5-7" />
          </svg>
        </div>
        <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="features.grade.title">Nilai &amp; Progres</h3>
        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 leading-relaxed mt-2" data-i18n="features.grade.desc">Nilai dan persentase penyelesaian tiap topik terekam otomatis.</p>
      </div>
      <div class="reveal bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6" data-reveal="up" data-delay="300">
        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center mb-4">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2">
            <path d="M4 4h16v16H4z" />
            <path d="M4 9h16M9 9v11" />
          </svg>
        </div>
        <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="features.dashboard.title">Dashboard Guru</h3>
        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 leading-relaxed mt-2" data-i18n="features.dashboard.desc">Guru mengelola materi dan memantau progres tiap kelas dari satu layar.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===================== ALUR PENGGUNAAN ===================== -->
<section id="alur" class="py-24 px-6 border-t border-slate-100 dark:border-slate-800">
  <div class="max-w-6xl mx-auto">
    <div class="max-w-lg mb-16 reveal" data-reveal="up">
      <p class="text-[13px] font-semibold text-blue-700 dark:text-blue-400 mb-3" data-i18n="alur.badge">Cara Kerja</p>
      <h2 class="font-display text-[1.9rem] font-bold text-slate-900 dark:text-white leading-tight" data-i18n="alur.title">Empat langkah, dua tujuan</h2>
      <p class="text-slate-500 dark:text-slate-400 mt-4 text-[14.5px] leading-relaxed" data-i18n="alur.desc">Satu login, dua pilihan: belajar atau melapor — semua tercatat dan terkelola otomatis.</p>
    </div>

    <div class="grid md:grid-cols-4 gap-8 md:gap-6">
      <div class="reveal" data-reveal="up" data-delay="0">
        <div class="w-9 h-9 rounded-full border-2 border-blue-700 dark:border-blue-400 text-blue-700 dark:text-blue-400 font-display font-semibold text-[14px] flex items-center justify-center mb-4">1</div>
        <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="alur.step1.title">Login</h3>
        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed" data-i18n="alur.step1.desc">Siswa, guru, atau admin masuk sesuai perannya masing-masing.</p>
      </div>

      <div class="reveal" data-reveal="up" data-delay="150">
        <div class="w-9 h-9 rounded-full border-2 border-blue-700 dark:border-blue-400 text-blue-700 dark:text-blue-400 font-display font-semibold text-[14px] flex items-center justify-center mb-4">2</div>
        <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="alur.step2.title">Pilih Menu</h3>
        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed" data-i18n="alur.step2.desc">Masuk ke Belajar Online atau SiLapor Sekolah.</p>
      </div>

      <div class="reveal" data-reveal="up" data-delay="300">
        <div class="w-9 h-9 rounded-full border-2 border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 font-display font-semibold text-[14px] flex items-center justify-center mb-4">3</div>
        <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="alur.step3.title">Belajar / Lapor</h3>
        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed" data-i18n="alur.step3.desc">Ikuti materi &amp; kuis, atau buat laporan bila ada masalah.</p>
      </div>

      <div class="reveal" data-reveal="up" data-delay="450">
        <div class="w-9 h-9 rounded-full border-2 border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 font-display font-semibold text-[14px] flex items-center justify-center mb-4">4</div>
        <h3 class="font-display font-semibold text-slate-900 dark:text-white text-[15px]" data-i18n="alur.step4.title">Guru &amp; Admin Kelola</h3>
        <p class="text-[13.5px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed" data-i18n="alur.step4.desc">Materi maupun laporan dikelola melalui dashboard masing-masing.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===================== TESTIMONIAL ===================== -->
<section id="testimoni" class="py-24 px-6 bg-[#f8fafc] dark:bg-slate-900/50">
  <div class="max-w-6xl mx-auto">
    <div class="max-w-lg mb-14 reveal" data-reveal="up">
      <p class="text-[13px] font-semibold text-blue-700 dark:text-blue-400 mb-3" data-i18n="testimoni.badge">Testimoni</p>
      <h2 class="font-display text-[1.9rem] font-bold text-slate-900 dark:text-white leading-tight" data-i18n="testimoni.title">Kata mereka yang sudah pakai EduCare</h2>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
      <div class="reveal bg-white dark:bg-slate-900 rounded-xl p-7 border border-slate-200 dark:border-slate-700 flex flex-col" data-reveal="up" data-delay="0">
        <p class="text-[14.5px] text-slate-700 dark:text-slate-300 leading-relaxed flex-1" data-i18n="testimoni.t1.quote">Belajar Web Development-nya runtut, dari dasar sampai bikin project nyata. Kuisnya bantu ukur pemahaman sebelum lanjut.</p>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[12px] font-semibold bg-blue-700">AR</div>
          <div>
            <p class="text-[13.5px] font-semibold text-slate-900 dark:text-white" data-i18n="testimoni.t1.handle">@ahmadrizki · siswa</p>
            <p class="text-[12px] text-slate-500 dark:text-slate-400">Siswa</p>
          </div>
        </div>
      </div>
      <div class="reveal bg-white dark:bg-slate-900 rounded-xl p-7 border border-slate-200 dark:border-slate-700 flex flex-col" data-reveal="up" data-delay="150">
        <p class="text-[14.5px] text-slate-700 dark:text-slate-300 leading-relaxed flex-1" data-i18n="testimoni.t2.quote">Waktu ada fasilitas rusak, saya lapor lewat SiLapor dan bisa langsung lihat statusnya. Nggak perlu menunggu tanpa kejelasan lagi.</p>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[12px] font-semibold bg-blue-700">SN</div>
          <div>
            <p class="text-[13.5px] font-semibold text-slate-900 dark:text-white" data-i18n="testimoni.t2.handle">@sitinur · siswa</p>
            <p class="text-[12px] text-slate-500 dark:text-slate-400">Siswa</p>
          </div>
        </div>
      </div>
      <div class="reveal bg-white dark:bg-slate-900 rounded-xl p-7 border border-slate-200 dark:border-slate-700 flex flex-col" data-reveal="up" data-delay="300">
        <p class="text-[14.5px] text-slate-700 dark:text-slate-300 leading-relaxed flex-1" data-i18n="testimoni.t3.quote">Dashboard guru memudahkan saya memantau progres kelas sekaligus laporan yang perlu ditindaklanjuti, semua dalam satu tempat.</p>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[12px] font-semibold bg-blue-700">BH</div>
          <div>
            <p class="text-[13.5px] font-semibold text-slate-900 dark:text-white" data-i18n="testimoni.t3.handle">@budiharto · guru</p>
            <p class="text-[12px] text-slate-500 dark:text-slate-400">Guru</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== CTA ===================== -->
<section class="py-24 px-6">
  <div class="max-w-6xl mx-auto">
    <div class="reveal rounded-xl px-5 sm:px-8 py-14 sm:py-20 text-center bg-slate-900 dark:bg-slate-800" data-reveal="scale">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/5 border border-white/10 text-[12px] font-semibold text-blue-300 dark:text-blue-400 mb-5" data-i18n="cta.badge">
        Gratis untuk sekolah
      </span>
      <h2 class="font-display text-[1.9rem] md:text-3xl font-bold text-white" data-i18n="cta.title">Siap wujudkan sekolah digital yang lebih baik?</h2>
      <p class="text-slate-400 dark:text-slate-300 mt-4 max-w-md mx-auto text-[14.5px]" data-i18n="cta.desc">Belajar lebih terarah dan lapor masalah sekolah lebih transparan — mulai bersama EduCare.</p>
      <a href="auth/register.php" class="inline-block mt-8 px-7 py-3 rounded-lg text-sm font-semibold text-slate-900 bg-white hover:bg-slate-100 transition-colors duration-200" data-i18n="cta.button">
        Mulai Sekarang
      </a>
    </div>
  </div>
</section>

<!-- Faq -->
<section id="faq">
  <?php include __DIR__ . '/../includes/Faq.php'; ?>
</section>