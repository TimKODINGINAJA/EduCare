<!-- ===================== HERO ===================== -->
<section id="home" class="pt-32 sm:pt-40 pb-24 px-4 sm:px-6 overflow-hidden">
  <div class="max-w-6xl mx-auto">
    <div class="grid lg:grid-cols-[1.05fr_1fr] gap-14 items-center">
      <div class="reveal" data-reveal="up">
        <h1 class="font-display text-[2.5rem] sm:text-[3.1rem] font-bold leading-[1.08] tracking-tight text-slate-900" data-i18n-html="hero.title">
          Belajar dan melapor,<br class="hidden sm:block">
          <span class="text-blue-600">satu platform</span> sekolah.
        </h1>
        <p class="mt-6 text-[16.5px] text-slate-500 leading-relaxed max-w-lg" data-i18n-html="hero.desc">
          EduCare menggabungkan <strong class="text-slate-700 font-semibold">Belajar Online</strong> — mata pelajaran umum seperti Matematika dan IPA, sampai bidang IT seperti Pemrograman dan AI — dengan <strong class="text-slate-700 font-semibold">SiLapor Sekolah</strong>, sistem pelaporan masalah lingkungan sekolah yang cepat dan transparan.
        </p>

        <div class="mt-9 flex flex-wrap items-center gap-3">
          <a href="auth/register.php" class="group px-6 py-3 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all duration-300 inline-flex items-center gap-2 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-600/20">
            <span data-i18n="hero.cta1">Mulai Belajar</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform duration-300 group-hover:translate-x-1">
              <path d="M5 12h14M13 6l6 6-6 6" />
            </svg>
          </a>
          <a href="#silapor" class="px-6 py-3 rounded-lg text-sm font-semibold text-slate-700 border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-all duration-300 hover:-translate-y-0.5">
            <span data-i18n="hero.cta2">Buat Laporan</span>
          </a>
        </div>

        <div class="mt-14 grid grid-cols-3 gap-6 max-w-md">
          <div>
            <p class="font-mono text-[26px] font-bold text-slate-900"><span class="counter" data-target="100">0</span>+</p>
            <p class="text-[12.5px] text-slate-500 mt-1" data-i18n="hero.stat1">Materi</p>
          </div>
          <div>
            <p class="font-mono text-[26px] font-bold text-slate-900"><span class="counter" data-target="50">0</span>+</p>
            <p class="text-[12.5px] text-slate-500 mt-1" data-i18n="hero.stat2">Video</p>
          </div>
          <div>
            <p class="font-mono text-[26px] font-bold text-slate-900"><span class="counter" data-target="1000">0</span>+</p>
            <p class="text-[12.5px] text-slate-500 mt-1" data-i18n="hero.stat3">Siswa aktif</p>
          </div>
        </div>
      </div>

      <!-- Signature: dashboard mockup that visibly spans Edukasi Umum, Edukasi IT, dan SiLapor -->
      <div class="reveal float-card glow-card rounded-xl w-full max-w-full" data-reveal="scale" data-delay="150">
        <div class="rounded-xl border border-slate-200 shadow-[0_24px_60px_-30px_rgba(11,18,32,0.35)] overflow-hidden bg-white">
        <div class="flex items-center gap-1.5 px-4 py-3 bg-slate-50 border-b border-slate-200">
          <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
          <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
          <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
          <span class="ml-3 text-[11px] font-mono text-slate-400 truncate">educare.id/dashboard</span>
        </div>
        <div class="p-4 sm:p-6">
          <div class="flex items-center justify-between gap-3 mb-5">
            <div class="min-w-0">
              <p class="font-display font-semibold text-slate-900" data-i18n="mockup.greeting">Halo, Nadia 👋</p>
              <p class="text-[13px] text-slate-400 truncate" data-i18n="mockup.class_school">Kelas 9A · SMKN 1 Cikarang</p>
            </div>
            <span class="text-[12px] font-mono text-slate-400 shrink-0" data-i18n="mockup.quiz_score">Nilai kuis: 90/100</span>
          </div>

          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mb-2.5" data-i18n="mockup.progress_label">Progres Belajar</p>
          <div class="grid grid-cols-3 gap-2.5 mb-5">
            <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2.5 mini-card">
              <p class="text-[16px] leading-none">📖</p>
              <p class="text-[11.5px] text-slate-500 mt-1.5" data-i18n="mockup.subject1">Matematika</p>
              <p class="font-mono text-[12px] font-semibold text-slate-800 mt-0.5">78%</p>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2.5 mini-card">
              <p class="text-[16px] leading-none">🧪</p>
              <p class="text-[11.5px] text-slate-500 mt-1.5" data-i18n="mockup.subject2">IPA</p>
              <p class="font-mono text-[12px] font-semibold text-slate-800 mt-0.5">64%</p>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2.5 mini-card">
              <p class="text-[16px] leading-none">💻</p>
              <p class="text-[11.5px] text-slate-500 mt-1.5" data-i18n="mockup.subject3">Pemrograman</p>
              <p class="font-mono text-[12px] font-semibold text-slate-800 mt-0.5">82%</p>
            </div>
          </div>

          <div class="space-y-3 mb-5">
            <div>
              <div class="flex justify-between text-[12.5px] text-slate-500 mb-1.5"><span data-i18n="mockup.webdev">🌐 Web Development</span><span class="font-mono">82%</span></div>
              <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-1.5 rounded-full bg-blue-600 bar-fill" data-width="82" style="width:0%"></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between text-[12.5px] text-slate-500 mb-1.5"><span data-i18n="mockup.uiux">🎨 UI/UX &amp; Desain</span><span class="font-mono">40%</span></div>
              <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-1.5 rounded-full bg-blue-600 bar-fill" data-width="40" style="width:0%"></div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-100 bg-slate-50 px-3 sm:px-4 py-3">
            <div class="flex items-center gap-3 min-w-0">
              <span class="text-[17px] shrink-0">🏚️</span>
              <p class="text-[13px] font-medium text-slate-700 truncate" data-i18n="mockup.report">Laporan #042 — Fasilitas Rusak</p>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-200 shrink-0" data-i18n="silapor.status.in_review">in-review</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== TRUST BAR ===================== -->
<section id="trust-bar" class="relative px-6 py-10 border-y border-slate-100">
  <div class="max-w-6xl mx-auto reveal" data-reveal="up">
    <p class="trust-label text-center text-[11px] font-semibold text-slate-400 tracking-[0.12em] uppercase mb-7 font-mono" data-i18n="trust.label">// dipercaya sekolah-sekolah berikut</p>
    <div class="flex flex-wrap items-center justify-center gap-x-14 gap-y-5 text-slate-400">
      <span class="font-display font-semibold text-[15px] tracking-tight trust-item" data-i18n="trust.school1">SMKN 1 Cikarang</span>
      <span class="font-display font-semibold text-[15px] tracking-tight trust-item" data-i18n="trust.school2">SMK Telkom</span>
      <span class="font-display font-semibold text-[15px] tracking-tight trust-item" data-i18n="trust.school3">SMKN 2 Bekasi</span>
      <span class="font-display font-semibold text-[15px] tracking-tight trust-item" data-i18n="trust.school4">SMK Wikrama</span>
      <span class="font-display font-semibold text-[15px] tracking-tight trust-item" data-i18n="trust.school5">SMKN 3 Karawang</span>
    </div>
  </div>
</section>

<!-- ===================== UNTUK SIAPA ===================== -->
<section class="px-6 py-24">
  <div class="max-w-6xl mx-auto">
    <!-- Section Header -->
    <div class="text-center mb-16 reveal" data-reveal="up">
      <h2 class="font-display text-[1.9rem] sm:text-3xl font-bold text-slate-900 dark:text-white leading-tight" data-i18n="forwhom.badge">Untuk Siapa</h2>
    </div>

    <!-- Cards Grid -->
    <div class="grid lg:grid-cols-2 gap-8 max-w-5xl mx-auto">

      <!-- Card 1: Siswa/Learner -->
      <div class="reveal group relative" data-reveal="up" data-delay="0">
        <!-- Background Gradient Blob -->
        <div class="absolute -inset-1 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-2xl opacity-0 group-hover:opacity-20 blur-2xl transition-opacity duration-500 pointer-events-none"></div>

        <!-- Card Container -->
        <div class="relative h-full bg-white rounded-2xl border border-slate-200 overflow-hidden transition-all duration-300 hover:border-blue-300 hover:shadow-xl hover:shadow-blue-500/10">

          <!-- Top Gradient Bar -->
          <div class="h-1 bg-gradient-to-r from-blue-500 to-cyan-500"></div>

          <!-- Card Content -->
          <div class="p-8 flex flex-col h-full">

            <!-- Icon Container -->
            <div class="mb-6">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-200 shadow-sm group-hover:shadow-md transition-all duration-300 group-hover:scale-110">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747 0-6.002-4.5-10.747-10-10.747z" />
                </svg>
              </div>
            </div>

            <!-- Role Label -->
            <p class="text-xs font-mono font-semibold text-blue-600 tracking-widest uppercase mb-2" data-i18n="forwhom.role1.label">01 • Siswa</p>

            <!-- Title -->
            <h3 class="text-2xl font-bold text-slate-900 mb-3" data-i18n="forwhom.role1.title">Siswa</h3>

            <!-- Description -->
            <p class="text-slate-600 leading-relaxed mb-6 flex-grow" data-i18n="forwhom.role1.desc">Belajar materi, kerjakan kuis, pantau XP & peringkat, dan laporkan masalah sekolah lewat SiLapor.</p>

            <!-- Features List -->
            <ul class="space-y-3 mb-8">
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-slate-700" data-i18n="forwhom.role1.feat1">Akses materi belajar sesuai kategori</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-slate-700" data-i18n="forwhom.role1.feat2">Kuis online untuk menguji pemahaman</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-slate-700" data-i18n="forwhom.role1.feat3">Lapor masalah sekolah secara langsung</span>
              </li>
            </ul>

            <!-- CTA Button -->
            <a href="auth/register.php" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold text-sm transition-all duration-300 hover:shadow-lg hover:shadow-blue-600/30 hover:-translate-y-0.5 group/btn">
              <span data-i18n="forwhom.role1.cta">Mulai Belajar</span>
              <svg class="w-4 h-4 ml-2 transition-transform duration-300 group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Card 2: Guru/Instructor -->
      <div class="reveal group relative" data-reveal="up" data-delay="100">
        <!-- Background Gradient Blob -->
        <div class="absolute -inset-1 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl opacity-0 group-hover:opacity-20 blur-2xl transition-opacity duration-500 pointer-events-none"></div>

        <!-- Card Container -->
        <div class="relative h-full bg-white rounded-2xl border border-slate-200 overflow-hidden transition-all duration-300 hover:border-purple-300 hover:shadow-xl hover:shadow-purple-500/10">

          <!-- Top Gradient Bar -->
          <div class="h-1 bg-gradient-to-r from-purple-500 to-pink-500"></div>

          <!-- Card Content -->
          <div class="p-8 flex flex-col h-full">

            <!-- Icon Container -->
            <div class="mb-6">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 shadow-sm group-hover:shadow-md transition-all duration-300 group-hover:scale-110">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5-4a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>

            <!-- Role Label -->
            <p class="text-xs font-mono font-semibold text-purple-600 tracking-widest uppercase mb-2" data-i18n="forwhom.role2.label">02 • Guru</p>

            <!-- Title -->
            <h3 class="text-2xl font-bold text-slate-900 mb-3" data-i18n="forwhom.role2.title">Guru</h3>

            <!-- Description -->
            <p class="text-slate-600 leading-relaxed mb-6 flex-grow" data-i18n="forwhom.role2.desc">Buat & kelola materi, pantau progres siswa, dan tindak lanjuti laporan SiLapor.</p>

            <!-- Features List -->
            <ul class="space-y-3 mb-8">
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-slate-700" data-i18n="forwhom.role2.feat1">Kelola materi & kategori pelajaran</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-slate-700" data-i18n="forwhom.role2.feat2">Pantau nilai, XP & aktivitas siswa</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-slate-700" data-i18n="forwhom.role2.feat3">Tindak lanjuti laporan pengaduan</span>
              </li>
            </ul>

            <!-- CTA Button -->
            <a href="auth/login.php" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold text-sm transition-all duration-300 hover:shadow-lg hover:shadow-purple-600/30 hover:-translate-y-0.5 group/btn">
              <span data-i18n="forwhom.role2.cta">Mulai Mengajar</span>
              <svg class="w-4 h-4 ml-2 transition-transform duration-300 group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== TEMA 1: BELAJAR ONLINE ===================== -->
<section id="edukasi" class="py-24 px-6 border-t border-slate-100">
  <div class="max-w-6xl mx-auto">
    <div class="max-w-lg mb-14 reveal" data-reveal="up">
      <p class="text-[13px] font-mono font-semibold text-blue-600 mb-3 uppercase tracking-wide" data-i18n="edukasi.badge">Belajar Online</p>
      <h2 class="font-display text-[1.9rem] font-bold text-slate-900 leading-tight" data-i18n="edukasi.title">Dua jalur edukasi, satu dashboard</h2>
      <p class="text-slate-500 mt-4 text-[14.5px] leading-relaxed" data-i18n="edukasi.desc">Materi terstruktur, video pembelajaran, dan kuis interaktif untuk mata pelajaran umum maupun bidang IT.</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
      <!-- Edukasi Umum -->
      <div class="reveal rounded-xl border border-slate-200 overflow-hidden bg-white" data-reveal="left">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-2.5">
          <span class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-[16px] shrink-0">📚</span>
          <div>
            <p class="font-mono text-[11px] text-slate-400">folder/</p>
            <h3 class="font-display font-semibold text-slate-900 text-[15px]" data-i18n="edukasi.umum.title">Edukasi Umum</h3>
          </div>
        </div>
        <ul class="divide-y divide-slate-100">
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">📖</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.umum.item1">Aljabar Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🧪</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.umum.item2">Interaksi Makhluk Hidup</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🌍</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.umum.item3">Keragaman Sosial dan Budaya Indonesia</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🌐</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.umum.item4"> Bahasa Indonesia & Bahasa Inggris</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🌱</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.umum.item5"> Manajemen Waktu dan Kebiasaan Belajar Efektif</span>
          </li>
        </ul>
      </div>

      <!-- Edukasi IT -->
      <div class="reveal rounded-xl border border-slate-200 overflow-hidden bg-white" data-reveal="right">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-2.5">
          <span class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-[16px] shrink-0">💻</span>
          <div>
            <p class="font-mono text-[11px] text-slate-400">folder/</p>
            <h3 class="font-display font-semibold text-slate-900 text-[15px]" data-i18n="edukasi.it.title">Edukasi IT</h3>
          </div>
        </div>
        <ul class="divide-y divide-slate-100">
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">💻</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item1">Dasar-Dasar HTML</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🎨</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item2">CSS Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">⚡</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item3">JavaScript Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🐘</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item4">PHP Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🐍</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item5">Python Dasar</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🗄️</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item6">Basis Data & SQL</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🌿</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item7">Git & GitHub</span>
          </li>
          <li class="subj-chip flex items-center gap-3 px-6 py-3.5 border-l-2 border-transparent hover:border-blue-600 hover:bg-slate-50 hover:pl-7 transition-all duration-300 cursor-pointer">
            <span class="w-7 h-7 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[13px] shrink-0 group-hover:border-blue-200">🎯</span><span class="text-[13.5px] text-slate-600 flex-1" data-i18n="edukasi.it.item8">UI/UX Design & Figma</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- fitur ringkas belajar online -->
    <div id="fitur" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-6">
      <div class="reveal hover-card feat-card bg-white rounded-xl border border-slate-200 p-6" data-reveal="up" data-delay="100">
        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mb-4 icon-pop">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
            <path d="M9 11l3 3L22 4" />
            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
          </svg>
        </div>
        <h3 class="font-display font-semibold text-slate-900 text-[15px]" data-i18n="features.quiz.title">Kuis Online</h3>
        <p class="text-[13.5px] text-slate-500 leading-relaxed mt-2" data-i18n="features.quiz.desc">Setiap materi ditutup kuis singkat untuk memastikan konsep melekat.</p>
      </div>
      <div class="reveal hover-card feat-card bg-white rounded-xl border border-slate-200 p-6" data-reveal="up" data-delay="200">
        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mb-4 icon-pop">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
            <path d="M3 3v18h18" />
            <path d="M7 15l4-5 3 3 5-7" />
          </svg>
        </div>
        <h3 class="font-display font-semibold text-slate-900 text-[15px]" data-i18n="features.grade.title">Nilai &amp; Progres</h3>
        <p class="text-[13.5px] text-slate-500 leading-relaxed mt-2" data-i18n="features.grade.desc">Nilai dan persentase penyelesaian tiap topik terekam otomatis.</p>
      </div>
      <div class="reveal hover-card feat-card bg-white rounded-xl border border-slate-200 p-6" data-reveal="up" data-delay="300">
        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mb-4 icon-pop">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
            <path d="M4 4h16v16H4z" />
            <path d="M4 9h16M9 9v11" />
          </svg>
        </div>
        <h3 class="font-display font-semibold text-slate-900 text-[15px]" data-i18n="features.dashboard.title">Dashboard Guru</h3>
        <p class="text-[13.5px] text-slate-500 leading-relaxed mt-2" data-i18n="features.dashboard.desc">Guru mengelola materi dan memantau progres tiap kelas dari satu layar.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===================== TEMA 2: SILAPOR SEKOLAH ===================== -->
<section id="silapor" class="py-24 px-6 bg-[#f8fafc]">
  <div class="max-w-6xl mx-auto">
    <div class="max-w-lg mb-14 reveal" data-reveal="up">
      <p class="text-[13px] font-mono font-semibold text-amber-600 uppercase tracking-wide" data-i18n="silapor.badge">SiLapor Sekolah</p>
      <h2 class="font-display text-[1.9rem] font-bold text-slate-900 leading-tight mt-3" data-i18n="silapor.title">Lapor masalah sekolah, secepat kirim pesan</h2>
      <p class="text-slate-500 mt-4 text-[14.5px] leading-relaxed" data-i18n="silapor.desc">Siswa dapat melaporkan masalah lingkungan sekolah secara langsung, dan memantau status penanganannya seperti melacak tiket.</p>
    </div>

    <div class="grid lg:grid-cols-[1fr_1.1fr] gap-6">
      <!-- kategori laporan -->
      <div class="grid grid-cols-2 gap-4 content-start">
        <div class="reveal hover-card bg-white rounded-xl border border-slate-200 p-6" data-reveal="up" data-delay="0">
          <span class="w-11 h-11 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-[19px]">🏚️</span>
          <h3 class="font-display font-semibold text-slate-900 text-[14px] mt-3" data-i18n="silapor.cat1.title">Fasilitas Rusak</h3>
          <p class="text-[12.5px] text-slate-500 mt-1.5 leading-relaxed" data-i18n="silapor.cat1.desc">Kelas, toilet, atau perlengkapan sekolah yang rusak.</p>
        </div>
        <div class="reveal hover-card bg-white rounded-xl border border-slate-200 p-6" data-reveal="up" data-delay="100">
          <span class="w-11 h-11 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-[19px]">🚫</span>
          <h3 class="font-display font-semibold text-slate-900 text-[14px] mt-3" data-i18n="silapor.cat2.title">Bullying</h3>
          <p class="text-[12.5px] text-slate-500 mt-1.5 leading-relaxed" data-i18n="silapor.cat2.desc">Laporan perundungan ditangani secara rahasia dan cepat.</p>
        </div>
        <div class="reveal hover-card bg-white rounded-xl border border-slate-200 p-6" data-reveal="up" data-delay="200">
          <span class="w-11 h-11 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-[19px]">🧹</span>
          <h3 class="font-display font-semibold text-slate-900 text-[14px] mt-3" data-i18n="silapor.cat3.title">Kebersihan</h3>
          <p class="text-[12.5px] text-slate-500 mt-1.5 leading-relaxed" data-i18n="silapor.cat3.desc">Area sekolah yang butuh perhatian kebersihan.</p>
        </div>
        <div class="reveal hover-card bg-white rounded-xl border border-slate-200 p-6" data-reveal="up" data-delay="300">
          <span class="w-11 h-11 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-[19px]">🎒</span>
          <h3 class="font-display font-semibold text-slate-900 text-[14px] mt-3" data-i18n="silapor.cat4.title">Kehilangan Barang</h3>
          <p class="text-[12.5px] text-slate-500 mt-1.5 leading-relaxed" data-i18n="silapor.cat4.desc">Laporkan barang hilang agar mudah dilacak kembali.</p>
        </div>
      </div>

      <!-- mockup issue tracker -->
      <div class="reveal rounded-xl border border-slate-200 bg-white overflow-hidden" data-reveal="right" data-delay="150">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
          <p class="font-mono text-[12px] text-slate-400" data-i18n="silapor.issues_label">Laporan Masuk</p>
          <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-red-50 text-red-600 border border-red-100" data-i18n="silapor.header_open_count">3 open</span>
        </div>
        <div class="divide-y divide-slate-100">
          <div class="issue-row px-4 sm:px-5 py-4 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
            <div class="min-w-0">
              <p class="text-[13.5px] font-medium text-slate-800 truncate" data-i18n="silapor.issue1.title">#042 Kran air rusak — Kelas 9A</p>
              <p class="text-[11.5px] text-slate-400 font-mono mt-1 truncate" data-i18n="silapor.issue1.meta">kategori: fasilitas · dilaporkan 2 jam lalu</p>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-200 shrink-0" data-i18n="silapor.status.in_review">in-review</span>
          </div>
          <div class="issue-row px-4 sm:px-5 py-4 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
            <div class="min-w-0">
              <p class="text-[13.5px] font-medium text-slate-800 truncate" data-i18n="silapor.issue2.title">#041 Tas hilang di kantin</p>
              <p class="text-[11.5px] text-slate-400 font-mono mt-1 truncate" data-i18n="silapor.issue2.meta">kategori: kehilangan barang · 1 hari lalu</p>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-medium rounded-full bg-red-50 text-red-700 border border-red-200 shrink-0" data-i18n="silapor.status.open">open</span>
          </div>
          <div class="issue-row px-4 sm:px-5 py-4 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
            <div class="min-w-0">
              <p class="text-[13.5px] font-medium text-slate-800 truncate" data-i18n="silapor.issue3.title">#039 Sampah menumpuk — Lab Komputer</p>
              <p class="text-[11.5px] text-slate-400 font-mono mt-1 truncate" data-i18n="silapor.issue3.meta">kategori: kebersihan · 3 hari lalu</p>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-medium rounded-full bg-green-50 text-green-700 border border-green-200 shrink-0" data-i18n="silapor.status.resolved">resolved</span>
          </div>
        </div>
        <div class="px-5 py-4 bg-slate-50 text-[12px] text-slate-500 border-t border-slate-100" data-i18n="silapor.admin_note">
          Dashboard admin memantau seluruh laporan hingga selesai ditangani.
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== ALUR PENGGUNAAN (pipeline) ===================== -->
<section id="alur" class="py-24 px-6 border-t border-slate-100">
  <div class="max-w-6xl mx-auto">
    <div class="max-w-lg mb-16 reveal" data-reveal="up">
      <p class="text-[13px] font-mono font-semibold text-blue-600 mb-3 uppercase tracking-wide" data-i18n="alur.badge">Cara Kerja</p>
      <h2 class="font-display text-[1.9rem] font-bold text-slate-900 leading-tight" data-i18n="alur.title">Empat langkah, dua tujuan</h2>
      <p class="text-slate-500 mt-4 text-[14.5px] leading-relaxed" data-i18n="alur.desc">Satu login, dua pilihan: belajar atau melapor — semua tercatat dan terkelola otomatis.</p>
    </div>

    <div class="grid md:grid-cols-4 gap-6 md:gap-0 relative">
      <div class="hidden md:block absolute top-[22px] left-[12.5%] right-[12.5%] h-[2px] bg-slate-200 overflow-hidden">
        <div class="h-full bg-blue-600 pipeline-fill"></div>
      </div>

      <div class="reveal relative pt-0 md:px-4 text-center md:text-left" data-reveal="up" data-delay="0">
        <div class="flex md:flex-col items-center gap-3 justify-center md:justify-start">
          <span class="w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow inline-block relative z-10 step-dot"></span>
          <p class="font-mono text-[11.5px] text-slate-400 md:mt-3" data-i18n="alur.step1.label">Langkah 01</p>
        </div>
        <h3 class="font-display font-semibold text-slate-900 mt-2 text-[15px]" data-i18n="alur.step1.title">Login</h3>
        <p class="text-[13.5px] text-slate-500 mt-2 leading-relaxed" data-i18n="alur.step1.desc">Siswa, guru, atau admin masuk sesuai perannya masing-masing.</p>
      </div>

      <div class="reveal relative pt-0 md:px-4 text-center md:text-left mt-6 md:mt-0" data-reveal="up" data-delay="150">
        <div class="flex md:flex-col items-center gap-3 justify-center md:justify-start">
          <span class="w-4 h-4 rounded-full bg-blue-600 border-4 border-white shadow inline-block relative z-10 step-dot"></span>
          <p class="font-mono text-[11.5px] text-slate-400 md:mt-3" data-i18n="alur.step2.label">Langkah 02</p>
        </div>
        <h3 class="font-display font-semibold text-slate-900 mt-2 text-[15px]" data-i18n="alur.step2.title">Pilih Menu</h3>
        <p class="text-[13.5px] text-slate-500 mt-2 leading-relaxed" data-i18n="alur.step2.desc">Masuk ke Belajar Online atau SiLapor Sekolah.</p>
      </div>

      <div class="reveal relative pt-0 md:px-4 text-center md:text-left mt-6 md:mt-0" data-reveal="up" data-delay="300">
        <div class="flex md:flex-col items-center gap-3 justify-center md:justify-start">
          <span class="w-4 h-4 rounded-full bg-slate-300 border-4 border-white shadow inline-block relative z-10 step-dot"></span>
          <p class="font-mono text-[11.5px] text-slate-400 md:mt-3" data-i18n="alur.step3.label">Langkah 03</p>
        </div>
        <h3 class="font-display font-semibold text-slate-900 mt-2 text-[15px]" data-i18n="alur.step3.title">Belajar / Lapor</h3>
        <p class="text-[13.5px] text-slate-500 mt-2 leading-relaxed" data-i18n="alur.step3.desc">Ikuti materi &amp; kuis, atau buat laporan bila ada masalah.</p>
      </div>

      <div class="reveal relative pt-0 md:px-4 text-center md:text-left mt-6 md:mt-0" data-reveal="up" data-delay="450">
        <div class="flex md:flex-col items-center gap-3 justify-center md:justify-start">
          <span class="w-4 h-4 rounded-full bg-slate-300 border-4 border-white shadow inline-block relative z-10 step-dot"></span>
          <p class="font-mono text-[11.5px] text-slate-400 md:mt-3" data-i18n="alur.step4.label">Langkah 04</p>
        </div>
        <h3 class="font-display font-semibold text-slate-900 mt-2 text-[15px]" data-i18n="alur.step4.title">Guru &amp; Admin Kelola</h3>
        <p class="text-[13.5px] text-slate-500 mt-2 leading-relaxed" data-i18n="alur.step4.desc">Materi maupun laporan dikelola melalui dashboard masing-masing.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===================== TESTIMONIAL ===================== -->
<section id="testimoni" class="py-24 px-6 bg-[#f8fafc]">
  <div class="max-w-6xl mx-auto">
    <div class="max-w-lg mb-14 reveal" data-reveal="up">
      <p class="text-[13px] font-mono font-semibold text-blue-600 mb-3 uppercase tracking-wide" data-i18n="testimoni.badge">Testimoni</p>
      <h2 class="font-display text-[1.9rem] font-bold text-slate-900 leading-tight" data-i18n="testimoni.title">Kata mereka yang sudah pakai EduCare</h2>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
      <div class="reveal hover-card bg-white rounded-xl p-7 border border-slate-200 flex flex-col" data-reveal="up" data-delay="0">
        <p class="font-mono text-[11px] text-slate-400 mb-3" data-i18n="testimoni.t1.handle">@ahmadrizki · siswa</p>
        <p class="text-[14.5px] text-slate-700 leading-relaxed flex-1" data-i18n="testimoni.t1.quote">Belajar Web Development-nya runtut, dari dasar sampai bikin project nyata. Kuisnya bantu ukur pemahaman sebelum lanjut.</p>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100">
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[12px] font-semibold bg-blue-600">AR</div>
          <p class="text-[13.5px] font-semibold text-slate-900">Ahmad Rizki</p>
        </div>
      </div>
      <div class="reveal hover-card bg-white rounded-xl p-7 border border-slate-200 flex flex-col" data-reveal="up" data-delay="150">
        <p class="font-mono text-[11px] text-slate-400 mb-3" data-i18n="testimoni.t2.handle">@sitinur · siswa</p>
        <p class="text-[14.5px] text-slate-700 leading-relaxed flex-1" data-i18n="testimoni.t2.quote">Waktu ada fasilitas rusak, saya lapor lewat SiLapor dan bisa langsung lihat statusnya. Nggak perlu nunggu tanpa kejelasan lagi.</p>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100">
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[12px] font-semibold bg-purple-600">SN</div>
          <p class="text-[13.5px] font-semibold text-slate-900">Siti Nuraini</p>
        </div>
      </div>
      <div class="reveal hover-card bg-white rounded-xl p-7 border border-slate-200 flex flex-col" data-reveal="up" data-delay="300">
        <p class="font-mono text-[11px] text-slate-400 mb-3" data-i18n="testimoni.t3.handle">@budiharto · guru</p>
        <p class="text-[14.5px] text-slate-700 leading-relaxed flex-1" data-i18n="testimoni.t3.quote">Dashboard guru memudahkan saya memantau progres kelas sekaligus laporan yang perlu ditindaklanjuti, semua dalam satu tempat.</p>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100">
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[12px] font-semibold bg-teal-600">BH</div>
          <p class="text-[13.5px] font-semibold text-slate-900">Budi Hartono</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== CTA ===================== -->
<section class="py-24 px-6">
  <div class="max-w-6xl mx-auto">
    <div class="reveal rounded-xl px-5 sm:px-8 py-14 sm:py-20 text-center bg-slate-900 cta-glow" data-reveal="scale">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/5 border border-white/10 text-[12px] font-semibold text-blue-300 mb-5">
        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
        <span data-i18n="cta.badge">Gratis untuk sekolah</span>
      </span>
      <h2 class="font-display text-[1.9rem] md:text-3xl font-bold text-white" data-i18n="cta.title">Siap wujudkan sekolah digital yang lebih baik?</h2>
      <p class="text-slate-400 mt-4 max-w-md mx-auto text-[14.5px]" data-i18n="cta.desc">Belajar lebih terarah dan lapor masalah sekolah lebih transparan — mulai bersama EduCare.</p>
      <a href="auth/register.php" class="inline-block mt-8 px-7 py-3 rounded-lg text-sm font-semibold text-slate-900 bg-white hover:bg-slate-100 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-white/10">
        <span data-i18n="cta.button">Mulai Sekarang</span>
      </a>
    </div>
  </div>
</section>

<!-- Faq -->
<section id="faq">
  <?php include __DIR__ . '/../includes/Faq.php'; ?>
</section>