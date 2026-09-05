/* ==========================================================================
   EduCare — Fitur Bahasa (i18n) untuk Landing Page
   Mendukung Bahasa Indonesia (id) & English (en).
   Pilihan bahasa disimpan di localStorage supaya konsisten antar halaman.
   ========================================================================== */
(function () {
  "use strict";

  var STORAGE_KEY = "educare-lang";
  var DEFAULT_LANG = "id";

  // Base path project (folder dua tingkat di atas assets/js/i18n.js), dipakai
  // untuk mengarahkan ajax-set-lang.php ke lokasi yang benar dari subfolder
  // mana pun (belajar/, dashboard-guru/, dst).
  var I18N_BASE = "/";
  (function () {
    var src = (document.currentScript && document.currentScript.src) || "";
    var m = src.match(/^(.*)\/assets\/js\/i18n\.js/);
    if (m && m[1]) {
      I18N_BASE = m[1] + "/";
    } else {
      var db = document.documentElement.getAttribute("data-base");
      if (db) I18N_BASE = db;
    }
  })();

  var translations = {
    id: {
      nav: {
        home: "Beranda",
        about: "Tentang",
        edukasi: "Edukasi",
        silapor: "Silapor",
        cara_kerja: "Cara Kerja",
        testimoni: "Testimoni",
        faq: "FAQ",
        contact: "Contact",
        login: "Masuk",
        register: "Daftar",
        logout: "Keluar",
        menu: "Menu",
        courses: "Kursus",
      },
      hero: {
        badge: "Platform Digital Sekolah",
        title:
          'Belajar dan melapor,<br class="hidden sm:block">\n          <span class="text-blue-600">satu platform</span> sekolah.',
        desc: 'EduCare menggabungkan <strong class="text-slate-700 font-semibold">Belajar Online</strong> — mata pelajaran umum seperti Matematika dan IPA, sampai bidang IT seperti Pemrograman dan AI — dengan <strong class="text-slate-700 font-semibold">SiLapor Sekolah</strong>, sistem pelaporan masalah lingkungan sekolah yang cepat dan transparan.',
        cta1: "Mulai Belajar",
        cta2: "Buat Laporan",
        stat1: "Materi",
        stat2: "Video",
        stat3: "Siswa aktif",
      },
      mockup: {
        greeting: "Halo, Nadia 👋",
        class_school: "Kelas 9A · SMKN 1 Cikarang",
        quiz_score: "Nilai kuis: 90/100",
        progress_label: "Progres Belajar",
        subject1: "Matematika",
        subject2: "IPA",
        subject3: "Pemrograman",
        webdev: "🌐 Web Development",
        uiux: "🎨 UI/UX & Desain",
        report: "Laporan #042 — Fasilitas Rusak",
      },
      trust: {
        label: "Dipercaya sekolah-sekolah berikut",
        school1: "SMKN 1 Cikarang",
        school2: "SMK Telkom",
        school3: "SMKN 2 Bekasi",
        school4: "SMK Wikrama",
        school5: "SMKN 3 Karawang",
      },
      forwhom: {
        badge: "Untuk Siapa",
        role1: {
          label: "01 • Siswa",
          title: "Siswa",
          desc: "Belajar materi, kerjakan kuis, pantau XP & peringkat, dan laporkan masalah sekolah lewat SiLapor.",
          feat1: "Akses materi belajar sesuai kategori",
          feat2: "Kuis online untuk menguji pemahaman",
          feat3: "Lapor masalah sekolah secara langsung",
          cta: "Mulai Belajar",
        },
        role2: {
          label: "02 • Guru",
          title: "Guru",
          desc: "Buat & kelola materi, pantau progres siswa, dan tindak lanjuti laporan SiLapor.",
          feat1: "Kelola materi & kategori pelajaran",
          feat2: "Pantau nilai, XP & aktivitas siswa",
          feat3: "Tindak lanjuti laporan pengaduan",
          cta: "Mulai Mengajar",
        },
      },
      edukasi: {
        badge: "Belajar Online",
        title: "Dua jalur edukasi, satu dashboard",
        desc: "Materi terstruktur, video pembelajaran, dan kuis interaktif untuk mata pelajaran umum maupun bidang IT.",
        umum: {
          title: "Edukasi Umum",
          item1: "Aljabar Dasar",
          item2: "Interaksi Makhluk Hidup",
          item3: "Keragaman Sosial dan Budaya Indonesia",
          item4: " Bahasa Indonesia & Bahasa Inggris",
          item5: " Manajemen Waktu dan Kebiasaan Belajar Efektif",
        },
        it: {
          title: "Edukasi IT",
          item1: "Dasar-Dasar HTML",
          item2: "CSS Dasar",
          item3: "JavaScript Dasar",
          item4: "PHP Dasar",
          item5: "Python Dasar",
          item6: "Basis Data & SQL",
          item7: "Git & GitHub",
          item8: "UI/UX Design & Figma",
        },
      },
      features: {
        quiz: {
          title: "Kuis Online",
          desc: "Setiap materi ditutup kuis singkat untuk memastikan konsep melekat.",
        },
        grade: {
          title: "Nilai & Progres",
          desc: "Nilai dan persentase penyelesaian tiap topik terekam otomatis.",
        },
        dashboard: {
          title: "Dashboard Guru",
          desc: "Guru mengelola materi dan memantau progres tiap kelas dari satu layar.",
        },
      },
      silapor: {
        badge: "SiLapor Sekolah",
        title: "Lapor masalah sekolah, secepat kirim pesan",
        desc: "Siswa dapat melaporkan masalah lingkungan sekolah secara langsung, dan memantau status penanganannya seperti melacak tiket.",
        cat1: {
          title: "Fasilitas Rusak",
          desc: "Kelas, toilet, atau perlengkapan sekolah yang rusak.",
        },
        cat2: {
          title: "Bullying",
          desc: "Laporan perundungan ditangani secara rahasia dan cepat.",
        },
        cat3: {
          title: "Kebersihan",
          desc: "Area sekolah yang butuh perhatian kebersihan.",
        },
        cat4: {
          title: "Kehilangan Barang",
          desc: "Laporkan barang hilang agar mudah dilacak kembali.",
        },
        issue1: {
          title: "#042 Kran air rusak — Kelas 9A",
          meta: "kategori: fasilitas · dilaporkan 2 jam lalu",
        },
        issue2: {
          title: "#041 Tas hilang di kantin",
          meta: "kategori: kehilangan barang · 1 hari lalu",
        },
        issue3: {
          title: "#039 Sampah menumpuk — Lab Komputer",
          meta: "kategori: kebersihan · 3 hari lalu",
        },
        status: {
          in_review: "Diperiksa",
          open: "Belum diproses",
          resolved: "Selesai",
        },
        issues_label: "Laporan Masuk",
        header_open_count: "3 belum selesai",
        admin_note:
          "Dashboard admin memantau seluruh laporan hingga selesai ditangani.",

        // ===== TAMBAHAN UNTUK SILAPOR =====
        howitworks: {
          badge: "Cara Kerja",
          title: "Tiga langkah sederhana, dari lapor sampai selesai.",
          desc: "SiLapor dirancang agar siswa tidak perlu bingung harus lapor ke siapa. Cukup buat laporan, dan sistem akan meneruskannya ke pihak yang tepat.",
          step1: {
            title: "Pilih kategori & tulis laporan",
            desc: "Pilih kategori yang sesuai — fasilitas rusak, bullying, kebersihan, atau kehilangan barang — lalu jelaskan situasinya secara singkat. Foto bisa dilampirkan sebagai bukti pendukung.",
          },
          step2: {
            title: "Laporan diteruskan otomatis",
            desc: "Sistem memberi nomor tiket dan meneruskan laporan ke guru atau admin sekolah yang berwenang menangani kategori tersebut.",
          },
          step3: {
            title: "Pantau status seperti tiket",
            desc: "Status laporan berubah dari Belum diproses → Diperiksa → Selesai, dan siswa bisa memantaunya kapan saja lewat dashboard.",
          },
        },
        categories: {
          badge: "Kategori Laporan",
          title: "Setiap jenis masalah, punya jalur penanganannya sendiri.",
          desc: "Kategori membantu laporan langsung sampai ke pihak yang tepat, tanpa perlu bolak-balik menjelaskan ke siapa harus melapor.",
        },
        status_legend: {
          title: "Arti status laporan",
          open_desc:
            "Laporan baru masuk dan belum ditinjau oleh pihak sekolah.",
          in_review_desc:
            "Laporan sedang ditinjau atau ditindaklanjuti oleh pihak yang berwenang.",
          resolved_desc:
            "Masalah sudah ditangani dan laporan dinyatakan tuntas.",
        },
        faq: {
          badge: "Pertanyaan Umum",
          title: "Yang sering ditanyakan tentang SiLapor",
          q1: "Apakah identitas saya akan diketahui saat melapor?",
          a1: "Untuk kategori sensitif seperti bullying, identitas pelapor dijaga kerahasiaannya dan hanya dapat diakses oleh guru BK atau pihak yang berwenang menangani kasus tersebut.",
          q2: "Berapa lama laporan biasanya ditindaklanjuti?",
          a2: "Waktu penanganan tergantung pada kategori dan tingkat urgensi laporan. Setiap perubahan status akan terlihat langsung di dashboard, sehingga siswa dapat memantau perkembangannya.",
          q3: "Apakah saya bisa melampirkan foto sebagai bukti?",
          a3: "Bisa. Melampirkan foto sangat disarankan, terutama untuk laporan fasilitas rusak atau kebersihan, agar pihak sekolah dapat lebih cepat memahami kondisi sebenarnya.",
          q4: "Apa yang terjadi setelah laporan berstatus 'Selesai'?",
          a4: "Laporan akan tersimpan sebagai riwayat dan tetap dapat dibuka kembali kapan saja. Jika masalah muncul lagi, siswa disarankan membuat laporan baru agar tercatat sebagai kasus terpisah.",
        },
        cta: {
          title: "Ada masalah di sekolah? Jangan dipendam sendiri.",
          desc: "Laporkan lewat SiLapor dan biarkan pihak sekolah membantu menyelesaikannya.",
          button: "Buat Laporan Sekarang",
        },
      },
      alur: {
        badge: "Cara Kerja",
        title: "Empat langkah, dua tujuan",
        desc: "Satu login, dua pilihan: belajar atau melapor — semua tercatat dan terkelola otomatis.",
        step1: {
          label: "Langkah 01",
          title: "Login",
          desc: "Siswa, guru, atau admin masuk sesuai perannya masing-masing.",
        },
        step2: {
          label: "Langkah 02",
          title: "Pilih Menu",
          desc: "Masuk ke Belajar Online atau SiLapor Sekolah.",
        },
        step3: {
          label: "Langkah 03",
          title: "Belajar / Lapor",
          desc: "Ikuti materi & kuis, atau buat laporan bila ada masalah.",
        },
        step4: {
          label: "Langkah 04",
          title: "Guru & Admin Kelola",
          desc: "Materi maupun laporan dikelola melalui dashboard masing-masing.",
        },
      },
      testimoni: {
        badge: "Testimoni",
        title: "Kata mereka yang sudah pakai EduCare",
        t1: {
          handle: "@ahmadrizki · siswa",
          quote:
            "Belajar Web Development-nya runtut, dari dasar sampai bikin project nyata. Kuisnya bantu ukur pemahaman sebelum lanjut.",
        },
        t2: {
          handle: "@sitinur · siswa",
          quote:
            "Waktu ada fasilitas rusak, saya lapor lewat SiLapor dan bisa langsung lihat statusnya. Nggak perlu nunggu tanpa kejelasan lagi.",
        },
        t3: {
          handle: "@budiharto · guru",
          quote:
            "Dashboard guru memudahkan saya memantau progres kelas sekaligus laporan yang perlu ditindaklanjuti, semua dalam satu tempat.",
        },
      },
      cta: {
        badge: "Gratis untuk sekolah",
        title: "Siap wujudkan sekolah digital yang lebih baik?",
        desc: "Belajar lebih terarah dan lapor masalah sekolah lebih transparan — mulai bersama EduCare.",
        button: "Mulai Sekarang",
      },
      faq: {
        badge: "Pusat Bantuan",
        title: "Pertanyaan yang Sering Diajukan",
        desc: "Semua yang perlu kamu tahu soal Belajar Online dan SiLapor dalam satu platform EduCare.",
        q1: "Apa itu platform EduCare?",
        a1: 'EduCare adalah satu platform terintegrasi yang menjawab dua kebutuhan sekolah sekaligus: ruang kelas digital (<strong class="text-blue-600 font-semibold">Belajar Online</strong>) dan sistem pengaduan sekolah (<strong class="text-amber-600 font-semibold">SiLapor</strong>). Kami menyatukan pengelolaan pembelajaran dan keluhan fasilitas ke dalam satu alur dashboard yang sama.',
        q2: "Mata pelajaran apa saja yang tersedia di Belajar Online EduCare?",
        a2: "Kami menyediakan materi pembelajaran komprehensif, mulai dari mata pelajaran umum sekolah seperti Matematika dan IPA, hingga bidang keahlian teknologi (IT) mutakhir seperti Pemrograman dan Artificial Intelligence (AI).",
        q3: "Apa itu fitur SiLapor Sekolah dan bagaimana cara kerjanya?",
        a3: '<strong class="font-semibold text-slate-800">SiLapor</strong> adalah sistem pelaporan infrastruktur sekolah. Melalui modul ini, siswa dan guru dapat mengadukan kerusakan fasilitas fisik atau kendala lingkungan belajar. Setiap laporan otomatis tercatat secara sistematis untuk diteruskan langsung ke bagian sarana prasarana sekolah.',
        q4: "Bagaimana alur pelacakan status pengaduan di SiLapor?",
        a4: 'Transparansi adalah fokus kami. Setiap aduan yang kamu kirim memiliki status penanganan real-time yang terpampang di dashboard, mulai dari status <span class="font-semibold text-blue-600">Diterima</span>, <span class="font-semibold text-amber-600">Diproses</span> oleh teknisi, hingga laporan dinyatakan <span class="font-semibold text-emerald-600">Selesai Ditangani</span>.',
        q5: "Apakah siswa memerlukan banyak akun berbeda untuk mengakses fitur ini?",
        a5: 'Tidak perlu repot. Siswa hanya menggunakan <strong class="font-semibold text-slate-800">satu akun tunggal (single account)</strong> untuk masuk ke dashboard utama. Dari akun tersebut, siswa sudah bisa belajar, mengerjakan tugas kelas digital, sekaligus memantau tindak lanjut laporan infrastruktur secara bersamaan.',
      },
      footer: {
        desc: "Platform pembelajaran digital untuk siswa yang ingin menguasai skill teknologi masa depan.",
        nav_heading: "Navigasi",
        nav: {
          item1: "Beranda",
          item2: "Edukasi",
          item4: "Cara Kerja",
          item5: "Testimoni",
        },
        company_heading: "Perusahaan",
        company: { item2: "Kontak" },
        social_heading: "Ikuti Kami",
        copyright: "© 2026 EduCare. Seluruh hak cipta dilindungi.",
        privacy: "Kebijakan Privasi",
        terms: "Syarat & Ketentuan",
      },
      about: {
        badge: "Profil",
        crumb: "Tentang EduCare",
        title: "Satu Platform untuk Menjawab Dua Kebutuhan Sekolah Sekaligus",
        deck: "Di tengah sekolah yang masih mengelola pembelajaran dan pengaduan secara terpisah, EduCare hadir menyatukan keduanya — dari ruang kelas digital sampai laporan fasilitas rusak, dalam satu alur yang sama.",
        byline_name: "Tim EduCare",
        byline_meta: "Diperbarui 20 Jul 2026 · 4 menit baca",
        figcaption:
          "Ilustrasi — tampilan Belajar Online dan SiLapor dalam satu dashboard EduCare.",
        p1: "EduCare dimulai dari pengamatan sederhana: siswa belajar lewat satu aplikasi, sementara keluhan tentang fasilitas atau lingkungan sekolah dilaporkan lewat cara yang berbeda-beda — dari kertas, grup chat, sampai kotak saran yang jarang dibuka. Dua kebutuhan yang sebenarnya berjalan setiap hari, tapi tidak pernah benar-benar terhubung.",
        p2_html:
          'Dari situ EduCare dibangun sebagai satu platform yang menggabungkan <strong class="font-semibold text-slate-900 dark:text-white">Belajar Online</strong> — mulai dari mata pelajaran umum seperti Matematika dan IPA, sampai bidang IT seperti Pemrograman dan AI — dengan <strong class="font-semibold text-slate-900 dark:text-white">SiLapor Sekolah</strong>, sistem pelaporan yang membuat setiap laporan tercatat, terlacak statusnya, dan sampai ke pihak yang tepat.',
        quote:
          "\u201cSekolah yang responsif bukan cuma soal mengajar dengan baik, tapi juga mendengar dengan cepat.\u201d",
        p3: "Bagi siswa, ini berarti satu akun untuk mengakses materi, mengerjakan kuis, dan memantau progres belajar dari mata pelajaran apa pun. Bagi guru dan pihak sekolah, setiap laporan yang masuk melalui SiLapor punya status yang jelas — mulai dari diterima, diproses, hingga selesai ditangani — sehingga tidak ada laporan yang hilang begitu saja.",
        p4: "Hari ini, EduCare digunakan oleh guru dan siswa untuk menjalankan dua sisi kehidupan sekolah yang sama pentingnya: belajar setiap hari, dan memastikan lingkungan belajar itu sendiri tetap layak digunakan.",
        tag1: "Pendidikan",
        tag2: "Digitalisasi Sekolah",
        tag3: "SiLapor",
        tag4: "Belajar Online",
        facts_heading: "Fakta Singkat",
        fact1_label: "Materi tersedia",
        fact2_label: "Video pembelajaran",
        fact3_label: "Siswa aktif",
        fact4_label: "Sekolah mitra",
        related_heading: "Baca Juga",
        related1_title: "Belajar Pemrograman dari Nol lewat EduCare",
        related1_cat: "Edukasi IT",
        related2_title: "Cara Kerja SiLapor: dari Laporan Masuk sampai Selesai",
        related2_cat: "SiLapor",
        related3_title: "Memantau Progres Belajar Siswa secara Real-Time",
        related3_cat: "Belajar Online",
        cta_text: "Mau lihat sendiri bagaimana EduCare bekerja?",
        cta_button: "Mulai Belajar",
      },
      page: {
        index: "EduCare - Platform Belajar Digital",
        login: "Masuk Akun — EduCare",
        register: "Daftar Akun Baru — EduCare",
        kontak: "Kontak Kami - EduCare",
        about: "Tentang EduCare — Profil",
        dashboard_siswa: "Dashboard Siswa • EduCare",
        dashboard_guru: "Dashboard Guru • EduCare",
      },
      kontak: {
        badge: "Hubungi Kami",
        title_html:
          'Kami Siap Membantu <span class="text-gradient">Anda</span>',
        desc: "Punya pertanyaan seputar platform EduCare atau ingin memberikan saran untuk sekolah? Hubungi kami langsung melalui formulir di bawah ini.",
        form_title: "Kirim Pesan via WA",
        label_nama: "Nama Lengkap",
        label_email: "Alamat Email",
        label_pesan: "Isi Pesan",
        ph_nama: "Masukkan nama Anda",
        ph_email: "nama@email.com",
        ph_pesan: "Tuliskan pertanyaan atau saran Anda di sini...",
        loading: "Menghubungkan...",
        submit_button: "Kirim ke WhatsApp",
        info_title: "Informasi Sekolah",
        info_desc:
          "Anda juga dapat mengunjungi instansi kami secara langsung atau menghubungi kami melalui saluran resmi berikut:",
        addr_label: "Alamat SMK Telekomunikasi Telesandi Bekasi",
        phone_label: "Telepon / Fax",
        email_label: "Surel Resmi",
        map_title: "Peta Interaktif Lokasi",
        map_sub:
          "SMK Telekomunikasi Telesandi Bekasi \u2022 Tambun Selatan, Bekasi",
      },
      auth: {
        login: {
          title: "Masuk ke Akun Anda",
          subtitle: "Masuk untuk melanjutkan belajar dari nol.",
          email_label: "Email",
          email_placeholder: "nama@email.com",
          password_label: "Kata Sandi",
          password_placeholder: "Masukkan kata sandi Anda",
          remember: "Ingat saya",
          forgot: "Lupa kata sandi?",
          submit: "Masuk",
          no_account: "Belum memiliki akun?",
          signup_link: "Daftar sekarang",
          copyright: "Hak cipta dilindungi undang-undang.",
        },
        register: {
          title: "Buat Akun Baru",
          subtitle: "Isi formulir di bawah untuk mendaftar sebagai siswa.",
          nama_label: "Nama Lengkap",
          nama_placeholder: "Masukkan nama lengkap Anda",
          email_label: "Email",
          email_placeholder: "contoh@gmail.com",
          password_label: "Kata Sandi",
          password_placeholder: "Minimal 6 karakter",
          confirm_label: "Konfirmasi Kata Sandi",
          confirm_placeholder: "Ulangi kata sandi Anda",
          role_label: "Daftar Sebagai",
          role_placeholder: "Pilih Akses Peran",
          role_siswa: "Siswa",
          submit: "Daftar Sekarang",
          have_account: "Sudah memiliki akun?",
          login_link: "Masuk di sini",
          match_ok: "\u2713 Kata sandi cocok",
          match_fail: "\u2717 Kata sandi tidak cocok",
          copyright: "Hak cipta dilindungi undang-undang.",
        },
      },

      siswa: {
        sidebar: {
          section_utama: "Utama",
          section_akademik: "Akademik",
          section_layanan: "Layanan",
          section_komunitas: "Komunitas",
          nav_dashboard: "Dashboard",
          nav_courses: "Kursus Saya",
          nav_materi: "Materi Pembelajaran",
          nav_continue: "Lanjut Belajar",
          nav_progress: "Progress",
          nav_quiz: "Quiz & Latihan",
          nav_laporan: "Laporan Siswa",
          nav_leaderboard: "Papan Peringkat",
          brand_tag: "Platform Sekolah",
        },
        header: {
          breadcrumb_dashboard: "Dashboard",
          breadcrumb_overview: "Overview",
          role: "Siswa",
          notif_title: "Notifikasi",
          notif_mark_all: "Tandai semua dibaca",
          notif_settings: "Pengaturan Notifikasi →",
          profile_role: "Siswa · EduCare",
          menu_profile: "👤 Profil Saya",
          menu_settings: "⚙️ Pengaturan",
          menu_logout: "🚪 Keluar",
        },
        overview: {
          greeting: "Selamat datang,",
          subtitle:
            "Mulai perjalanan belajarmu hari ini — dari nol, satu langkah sekarang!",
          cta_start: "+ Mulai Kursus",
          card_progress_title: "📖 Progres Kursus",
          card_progress_link: "Detail →",
          card_activity_title: "⚡ Aktivitas Terbaru",
          card_xp_title: "⚡ XP & Level",
          card_leaderboard_title: "🏆 Top Pelajar",
          card_leaderboard_link: "Lihat →",
        },
        dynamic: {
          active_courses: "Kursus Aktif",
          learning: "Aktif belajar",
          no_courses: "Belum ada kursus",
          quizzes_done: "Quiz Dikerjakan",
          great: "Keren!",
          first_quiz: "Kerjakan quiz pertamamu",
          day_streak: "Streak Hari",
          keep_going: "Pertahankan!",
          start_streak: "Mulai streak hari ini",
          start_course_desc:
            "Mulai perjalananmu! Daftar kursus pertamamu dan mulai belajar dari nol.",
          view_materials: "Lihat Materi",
          modules: "modul",
          view_course: "Lihat Kursus →",
          continue_learning: "Lanjutkan Belajar →",
          no_activity:
            "Belum ada aktivitas. Mulai belajar untuk melihat aktivitasmu di sini.",
          quizzes_available: "quiz tersedia",
          material_quiz: "Quiz Materi",
          completed: "Selesai",
          do_quiz: "Kerjakan",
          questions: "soal",
          deadline: "deadline",
          cancel_report_confirm: "Batalkan laporan ini?",
          category_general: "Umum",
          category_facility: "Fasilitas",
          category_academic: "Akademik",
          category_discipline: "Kedisiplinan",
          category_other: "Lainnya",
          week_days: ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
          this_week: "Minggu ini",
          total: "Total",
          courses: "Kursus",
          lessons_done: "pelajaran selesai",
          no_skill: "Belum ada skill yang dipelajari. Mulai kursus dulu!",
          progress_detail_empty: "Daftar kursus untuk melihat detail progress.",
        },
        dashboard: {
          level: "Level",
          active_student: "Pelajar Aktif",
          streak: "Streak",
          completed: "Selesai",
        },
        my_courses: {
          title: "Kursus Saya 📚",
          subtitle:
            'Kursus yang sudah kamu selesaikan 100%. Masih ada yang berjalan? Klik "Lanjut Belajar" di menu.',
          cta_continue: "▶️ Lanjut Belajar",
        },
        materi: {
          title: "Materi Pembelajaran 📘",
          subtitle:
            "Materi asli buatan guru, lengkap dengan video dan kuis — semua di dashboard ini.",
          tab_all: "Semua Materi",
          tab_it: "IT / Teknologi",
          tab_umum: "Umum",
          search_placeholder: "🔎 Cari materi...",
          no_quiz_category:
            "Kuis untuk kategori materi ini belum tersedia. Silakan hubungi guru Anda.",
        },
        quiz: {
          title: "Quiz & Latihan ❓",
          subtitle:
            "Uji pemahamanmu dengan soal interaktif. Setiap materi punya quiz sendiri, minimal 15 soal.",
          all_quiz_title: "🧑‍🏫 Semua Quiz",
          manage_questions: "Kelola Soal",
          no_questions_title: "Belum Ada Soal",
          no_questions_desc:
            'Silakan tambahkan soal pertama untuk kuis ini dengan mengklik tombol "Tambah Soal".',
          submit_button: "Kirim",
          prev_question: "← Sebelumnya",
          next_question: "Berikutnya →",
          submit_quiz: "📤 Selesai & Kirim",
          time_up: "Waktu habis! Jawaban akan dikirim otomatis.",
          confirm_submit: "Yakin ingin mengumpulkan jawaban?",
          confirm_leave_progress:
            "Progres jawabanmu belum dikirim dan akan hilang jika keluar sekarang. Yakin ingin kembali ke dashboard?",
          confirm_leave: "Yakin ingin keluar dari quiz ini?",
          back_to_dashboard: "← Kembali ke Dashboard",
        },
        progress: {
          title: "Progress Belajar 📊",
          subtitle: "Pantau perkembangan belajarmu secara detail.",
          weekly_title: "📈 Aktivitas Mingguan",
          skill_title: "🎯 Skill Progress",
          detail_title: "📚 Detail Per Kursus",
        },
        laporan: {
          title: "Laporan Siswa 📮",
          subtitle:
            "Kirim laporan atau keluhan terkait sekolah, dan pantau statusnya di sini.",
          form_title: "📝 Buat Laporan Baru",
          label_title: "Judul Laporan",
          placeholder_title: "Contoh: Kerusakan fasilitas kelas",
          label_category: "Kategori",
          label_desc: "Deskripsi",
          placeholder_desc: "Jelaskan laporanmu secara detail...",
          submit: "📤 Kirim Laporan",
          summary_title: "📊 Ringkasan Status",
          status_pending: "⏳ Menunggu",
          status_process: "🔧 Diproses",
          status_done: "✅ Selesai",
          history_title: "📋 Riwayat Laporan Saya",
          empty_title: "Belum ada laporan",
          empty_desc: "Laporan yang kamu kirim akan muncul di sini.",
          cancel: "🗑 Batalkan",
        },
        leaderboard: {
          title: "Papan Peringkat 🏆",
          subtitle: "Kompetisi sehat mendorong semangat belajar.",
          tab_monthly: "Bulanan",
          tab_weekly: "Mingguan",
          tab_alltime: "Sepanjang Waktu",
        },
        profile: {
          title: "Profil Saya 👤",
          subtitle: "Kelola informasi profil dan lihat pencapaianmu.",
          edit: "✏️ Edit Profil",
          achievements_title: "🏅 Pencapaian",
        },
        settings: {
          title: "Pengaturan ⚙️",
          subtitle: "Kelola preferensi dan keamanan akunmu.",
          tab_profile: "👤 Profil",
          tab_notif: "🔔 Notifikasi",
          tab_privacy: "🔐 Privasi",
          tab_appearance: "🎨 Tampilan",
        },
        chrome: {
          completed: "✓ Selesai",
          xp: "XP",
          overview: {
            totalXp: "TOTAL XP",
            levelCurrent: "Level {level} — {name}",
            xpNext: "{n} XP lagi menuju Level {level}",
            xpFraction: "{have} / {need} XP",
            calTitle: "📅 Kalender",
            months: [
              "Januari",
              "Februari",
              "Maret",
              "April",
              "Mei",
              "Juni",
              "Juli",
              "Agustus",
              "September",
              "Oktober",
              "November",
              "Desember",
            ],
            days: ["S", "S", "R", "K", "J", "S", "M"],
          },
          levelNames: [
            "Pemula",
            "Explorer",
            "Pelajar",
            "Intermediate",
            "Advanced",
            "Pro",
            "Expert",
            "Master",
            "Elite",
            "Legend",
          ],
          levelBadges: ["🌱 Pemula", "🌿 Menengah", "🌳 Lanjutan"],
          lbYou: "(Kamu)",
          time: {
            justNow: "Baru saja",
            minutesAgo: "{n} menit lalu",
            hoursAgo: "{n} jam lalu",
            yesterday: "Kemarin",
            daysAgo: "{n} hari lalu",
          },
          enroll: {
            activity: 'Mendaftar kursus "{title}"',
            activityAlt: 'Mendaftar ke kursus "{title}"',
            toast: "Berhasil mendaftar!",
            notifTitle: "Kursus Baru!",
            notifMsg: 'Kamu berhasil mendaftar ke "{title}". Selamat belajar!',
          },
          mycourses: {
            emptyTitle: "Belum ada kursus yang selesai",
            emptyDesc:
              "Kursus akan muncul di sini setelah kamu menyelesaikannya 100%. Yuk lanjutkan belajar dulu!",
            continue: "▶️ Lanjut Belajar",
            progress100: "100% selesai",
            lessonsCount: "{n} pelajaran",
            review: "🔁 Tinjau Ulang",
          },
          lesson: {
            summary: "{pct}% selesai · {n}/{total} pelajaran",
            active: "Aktif",
            lessonLabel: "LESSON {cur}/{total}",
            scriptPlaceholder: "📄 Script Langkah",
            close: "Tutup ✕",
            scriptHeading: "📖 Script Praktik: {title}",
            stepsIntro:
              "Berikut adalah langkah-langkah praktik untuk memahami materi ini:",
            step1: "Buka <strong>Coding Playground</strong> (tombol di bawah).",
            step2: "Salin kode contoh dan modifikasi sesuai petunjuk.",
            step3: "Jalankan kode dan amati hasilnya.",
            step4: "Coba variasikan nilai atau struktur kode.",
            tip: "Tips:",
            tipText:
              "Gunakan <code>console.log()</code> untuk debug di playground. Jalankan kode di atas untuk melihat output simulasi!",
            playgroundBtn: "💻 Coding Playground",
            aiBtn: "🤖 Tanya AI Tutor",
            noteBtn: "🔖 Simpan Catatan",
            noteSaved: "Catatan disimpan! 📝",
            prev: "← Sebelumnya",
            quizBtn: "❓ Kerjakan Quiz",
            doneNext: "✓ Selesai & Lanjut →",
            forumTitle: "💬 Forum Diskusi — {title}",
            forumNew: "+ Buat Topik Baru",
            forumEmpty:
              "Belum ada diskusi. Jadilah yang pertama bertanya atau berbagi!",
            like: "Suka",
            reply: "Balas",
            noActiveDesc: "Daftar kursus dulu untuk mulai belajar!",
            noLessonsTitle: "Kursus belum punya pelajaran",
            noLessonsDesc:
              'Kursus "{title}" belum memiliki pelajaran. Silakan hubungi guru Anda atau cek materi lain.',
          },
          complete: {
            bonus: "Bonus chapter selesai!",
<<<<<<< HEAD
            courseDone: "🎉 Selamat! Kursus selesai! +500 XP!",
=======
            courseDone:
              "🎉 Selamat! Kursus selesai! +500 XP & Sertifikat diraih!",
>>>>>>> fix-=web
            lessonDone: "✅ Pelajaran selesai! +30 XP",
            allDone: "🎉 Kamu sudah menyelesaikan semua pelajaran!",
            allDoneToast: "🎉 Semua materi & kursus sudah kamu selesaikan!",
            lessonReason: 'Menyelesaikan pelajaran di "{title}"',
            courseReason: 'Menyelesaikan kursus "{title}"',
            chapterTitle: "Chapter Selesai!",
            chapterMsg:
              'Kamu telah menyelesaikan chapter di "{title}". Lanjut terus!',
            courseTitle: "Kursus Selesai!",
<<<<<<< HEAD
            courseMsg: "Selamat! Kamu telah menyelesaikan kursus \"{title}\".",
=======
            courseMsg:
              'Selamat! Kamu telah menyelesaikan "{title}". Sertifikatmu sudah tersedia!',
>>>>>>> fix-=web
          },
          playground: {
            onlyIT:
              "Coding Playground hanya tersedia untuk kursus IT/Pemrograman.",
            noLesson:
              "Kursus ini belum punya pelajaran untuk dipraktikkan. Tambahkan pelajaran dari dashboard guru.",
            placeholder: "// Tulis kode di sini...",
            title: "💻 Coding Playground: {title}",
            hint: "Tulis atau modifikasi kode di bawah, lalu klik Jalankan.",
            run: "🚀 Jalankan Kode",
            close: "Tutup",
            compiling: "[Compiling & Executing {label} script...]",
            output: "▶ Output:",
            noOutput: "(tidak ada output)",
            error: "⚠️ Error:",
          },
          ai: {
            prompt: "Tanyakan sesuatu tentang materi ini (AI Tutor):",
            thinking: "AI Tutor sedang memproses...",
            reply:
              "🤖 AI: Terima kasih atas pertanyaanmu! Pastikan untuk mempraktikkan kode setiap selesai membaca.",
          },
          forum: {
            postTitle: "Judul topik diskusi:",
            postBody: "Isi diskusi:",
            postXp: "Posting di forum",
            newTopicActivity: "Membuat topik forum: {title}",
            postDone: "Topik berhasil dibuat! +10 XP",
            liked: "👍 Disukai!",
            replyPrompt: "Tulis balasanmu:",
            replyDone: "Balasan ditambahkan!",
          },
          quiz: {
            noActiveTitle: "Belum ada kursus aktif",
            noActiveDesc: "Daftar kursus dulu untuk bisa mengerjakan quiz-nya!",
            viewMateri: "📘 Lihat Materi",
            guruSoal: "{n} soal · Quiz Guru",
            guruRetake:
              "Quiz ini sudah kamu kerjakan. Kerjakan ulang untuk meningkatkan skor.",
            guruAvailable:
              "Quiz ini dibuat oleh guru. Kerjakan di halaman khusus untuk mengumpulkan skor.",
            retake: "Kerjakan Ulang →",
            start: "Mulai Kerjakan →",
            gnotAvailableTitle: "Quiz belum tersedia",
            gnotAvailableDesc: "Quiz untuk kursus ini belum disiapkan.",
            estTime: "{n} soal · estimasi 10 menit",
            timer: "WAKTU",
            reset: "🔄 Reset",
            submit: "Periksa Jawaban →",
            resultScore: "Skor: {score}/{total} ({pct}%) — {feedback}",
            resultGreat: "Luar biasa!",
            resultRetry: "Coba lagi untuk meningkatkan pemahamanmu.",
            savingMsg: "Menyimpan ke server…",
            passXp: "Lulus Quiz",
            doneToast: "Quiz selesai! Skor {pct}% +{xp} XP 🏅",
            activity: "Mengerjakan quiz: skor {pct}%",
          },
          progress: {
            enrolledAt: "Didaftar: {date}",
            lessonsDone: "{n} pelajaran selesai",
          },
<<<<<<< HEAD
=======
          certs: {
            emptyTitle: "Belum ada sertifikat",
            emptyDesc:
              "Selesaikan kursus untuk mendapatkan sertifikat resmi EduCare yang diakui.",
            viewMyCourses: "📚 Lihat Kursus Saya",
            stillNeeds: "Selesaikan {n}% lagi untuk sertifikat ini",
            stillNeedsShort: "Selesaikan {n}% lagi",
            official: "SERTIFIKAT RESMI",
            earnedBy: "Diraih oleh <strong>{name}</strong>",
            view: "👁 Lihat",
            download: "⬇ Unduh PDF",
            viewToast: "Membuka sertifikat...",
            downloadToast: "Mengunduh PDF...",
          },
>>>>>>> fix-=web
          notifs: {
            emptyTitle: "Tidak ada notifikasi",
            emptyDesc: "Mulai belajar untuk mendapatkan notifikasi progress!",
            allRead: "Semua notifikasi ditandai dibaca.",
            welcomeTitle: "Selamat bergabung!",
            welcomeMsg:
              "Selamat datang di EduCare! Mulai perjalanan belajarmu dari nol hari ini.",
          },
          profile: {
            roleFallback: "Pelajar Aktif",
            statHours: "Jam Belajar",
            statCourses: "Kursus",
            statStreak: "Streak",
            statXp: "XP",
            ach: [
              ["First Blood", "Selesaikan pelajaran pertama"],
              ["Streak 3 Hari", "Belajar 3 hari berturut"],
              ["Komunitas", "Post pertama di forum"],
              ["100 XP", "Kumpulkan 100 XP"],
              ["Enrolled", "Daftar kursus pertama"],
              ["Speed Learner", "Selesaikan kursus <30 hari"],
              ["Top Scorer", "Skor quiz 100%"],
            ],
            earned: "✓ DIRAIH",
          },
          settings: {
            profile: "Informasi Profil",
            firstName: "Nama Depan",
            lastName: "Nama Belakang",
            email: "Email",
            username: "Username",
            institution: "Institusi",
            save: "💾 Simpan Perubahan",
            notif: "Pengaturan Notifikasi",
            emailNotif: "Notifikasi Email",
            emailNotifDesc: "Terima update via email",
            courseNotif: "Pemberitahuan Kursus",
            courseNotifDesc: "Info kursus sesuai minatmu",
            reminder: "Pengingat Belajar Harian",
            reminderDesc: "Pengingat untuk menjaga streak",
            privacy: "Pengaturan Privasi",
            publicProfile: "Profil Publik",
            publicProfileDesc: "Orang lain dapat melihat profilmu",
            lbVisible: "Tampilkan di Leaderboard",
            lbVisibleDesc: "Masuk dalam papan peringkat",
            appearance: "Tampilan & Tema",
            mode: "Mode Tampilan",
            dark: "Tema gelap",
            light: "Tema terang",
            darkColor: "Warna untuk Dark Mode",
            lightColor: "Warna untuk Light Mode",
            colorBlue: "Biru",
            colorPurple: "Ungu",
            colorGreen: "Hijau",
            colorAmber: "Amber",
            colorPink: "Merah Muda",
            colorCyan: "Cyan",
            saved:
              "Profil berhasil diperbarui secara lokal! Hubungi admin untuk perubahan data resmi. ✅",
            themeChanged: "Tema diubah!",
          },
          materi: {
            emptyTitle: "Materi tidak ditemukan",
            emptyDesc:
              "Belum ada materi yang cocok dengan pencarian atau kategori ini.",
            notOpened: "Belum Dibuka",
            hasVideo: "🎬 Ada Video",
            textModule: "📄 Modul Teks",
            videoTitle: "🎬 Video Penjelasan",
            videoFallback: "Putar video eksternal melalui link berikut:",
            openVideo: "Buka Link Video",
            prev: "← Materi Sebelumnya",
            next: "Materi Selanjutnya →",
            quizCta: "🎯 Siap Menguji Pemahamanmu?",
            quizCtaDesc:
              "Kerjakan <strong>{quiz}</strong> untuk menguji materi ini dan mengumpulkan skor.",
            takeQuiz: "Kerjakan Kuis →",
            back: "← Kembali ke Materi",
            studied: "✓ Selesai Dipelajari",
            openedXp: 'Membuka materi "{title}"',
            openedNotifTitle: "Materi Dipelajari",
            openedNotifMsg: 'Kamu membuka materi "{title}".',
            published: "Diterbitkan: {date}",
          },
          levelup: {
            title: "Level Up!",
            msg: "Selamat! Kamu naik ke Level {level} — {name}! XP: {xp}",
          },
        },
      },

      belajar: {
        materi: {
          page_title: "Materi Pembelajaran",
<<<<<<< HEAD
          page_subtitle: "Jelajahi materi, tonton modul video, dan selesaikan quiz untuk menguji pemahamanmu.",
=======
          page_subtitle:
            "Jelajahi materi, tonton modul video, dan selesaikan quiz untuk mendapatkan sertifikat.",
>>>>>>> fix-=web
          class_label: "Kelas 9A • Siswa",
          tab_mtk: "Matematika",
          search_ph: "Cari materi...",
          all_categories: "Semua Kategori",
          has_video: "Ada Video",
          text_module: "Modul Teks",
          start_learning: "Mulai Belajar",
          empty_title: "Materi Belajar Kosong",
          empty_desc:
            "Belum ada materi pelajaran yang diunggah oleh guru saat ini.",
        },
        detail: {
          back_to_materi: "Kembali ke Materi",
          breadcrumb: "Detail Materi",
          studied: "Selesai Dipelajari",
          published_prefix: "Diterbitkan pada:",
          video_title: "Video Penjelasan",
          video_active: "Modul Video Aktif",
          video_fallback: "Putar video eksternal melalui link:",
          prev_material: "Materi Sebelumnya",
          next_material: "Materi Selanjutnya",
          quiz_badge: "Uji Kompetensi",
          quiz_cta_title: "Siap Menguji Pemahaman Anda?",
          quiz_cta_desc_prefix: "Selesaikan kuis interaktif",
          quiz_cta_desc_min: "(minimal",
          quiz_cta_desc_questions: "soal)",
<<<<<<< HEAD
          quiz_cta_desc_suffix: "untuk menguji pemahamanmu terhadap materi ini.",
=======
          quiz_cta_desc_suffix:
            "untuk menguji materi ini dan klaim sertifikat belajar Anda.",
>>>>>>> fix-=web
          take_quiz_now: "Kerjakan Kuis Sekarang",
          copied: "Copied!",
        },
      },

      msg: {
        guru: {
          quiz: {
            name_required: "Nama quiz wajib diisi!",
            add_success: "Quiz baru berhasil ditambahkan!",
            update_success: "Quiz berhasil diperbarui!",
            delete_success: "Quiz berhasil dihapus!",
            question_add_success: "Soal berhasil ditambahkan!",
            question_update_success: "Soal berhasil diperbarui!",
            question_delete_success: "Soal berhasil dihapus!",
          },
          materi: {
            required: "Judul, Kategori, dan Isi Materi wajib diisi!",
            add_success: "Materi belajar berhasil ditambahkan!",
            update_success: "Materi belajar berhasil diperbarui!",
            delete_success: "Materi belajar berhasil dihapus!",
          },
          kategori: {
            add_success: "Kategori baru berhasil ditambahkan!",
            name_required: "Nama kategori tidak boleh kosong!",
            update_success: "Kategori berhasil diperbarui!",
            delete_success: "Kategori berhasil dihapus!",
          },
          user: {
            required: "Semua field wajib diisi!",
            invalid_email: "Format email tidak valid!",
            email_exists: "Email sudah digunakan!",
            add_success: "User baru berhasil ditambahkan!",
            required_fields: "Field Nama, Email, dan Role wajib diisi!",
            email_exists_other: "Email sudah digunakan oleh user lain!",
            update_success: "Data user berhasil diperbarui!",
            cannot_delete_self:
              "Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!",
            delete_success: "User berhasil dihapus!",
          },
          laporan: {
            status_updated: "Status laporan berhasil diperbarui!",
            invalid_status: "Status tidak valid!",
          },
          pengaturan: {
            required: "Nama dan Email wajib diisi!",
            password_min: "Password minimal 6 karakter!",
            password_mismatch: "Konfirmasi password tidak cocok!",
            update_success: "Pengaturan akun berhasil diperbarui!",
          },
        },
        quiz: {
          no_questions:
            "Quiz ini belum memiliki soal. Silakan hubungi guru Anda.",
          no_valid_questions:
            "Quiz ini belum memiliki soal yang valid. Silakan hubungi guru Anda.",
        },
      },

      guru: {
        sidebar: {
          section_utama: "Utama",
          section_pembelajaran: "Pembelajaran",
          section_siswa: "Siswa",
          section_silapor: "SiLapor",
          section_akun: "Akun",
          nav_dashboard: "Dashboard",
          nav_analitik: "Analitik",
          nav_materi: "Kelola Kursus",
          nav_quiz: "Kelola Quiz",
          nav_nilai: "Nilai Siswa",
          nav_manage_users: "Kelola User",
          nav_kategori: "Kategori Materi",
          nav_pengaturan: "Pengaturan",
          nav_data_siswa: "Data Siswa",
          nav_aktivitas: "Aktivitas Siswa",
          nav_laporan_masuk: "Laporan Masuk",
          brand_tag: "Platform Sekolah",
        },
        header: {
          breadcrumb_dashboard: "Dashboard Guru",
          breadcrumb_overview: "Overview",
          role: "Guru",
          notif_title: "Notifikasi",
          menu_logout: "🚪 Keluar",
          profile_role: "Guru · EduCare",
        },
        overview: {
          greeting: "Selamat datang,",
          subtitle: "Kelola kursus, quiz, dan pantau perkembangan siswa.",
          stat_courses: "Total Kursus",
          stat_students: "Total Siswa",
          stat_quiz: "Total Quiz",
          stat_reports: "Laporan Baru",
          recent_courses_title: "📚 Kursus Terbaru",
          link_manage: "Kelola →",
          recent_activity_title: "📝 Aktivitas Siswa",
          link_detail: "Detail →",
          quiz_title: "❓ Kelola Quiz",
          reports_title: "📮 Laporan Masuk",
          top_students_title: "🏆 Top Siswa",
          link_view: "Lihat →",
          leaderboard_title: "🏆 Leaderboard",
        },
        analitik: {
          title: "Analitik Pembelajaran 📊",
          subtitle: "Pantau performa belajar siswa secara real-time.",
          chart_kelas: "Rata-rata Nilai per Kelas",
          chart_laporan: "Status Laporan Masuk",
          chart_kategori: "Kursus per Kategori",
          chart_mapel: "Rata-rata Pemahaman per Mapel",
          average_score: "Rata-rata Nilai",
          average_understanding: "Rata-rata Pemahaman",
          no_data: "Belum ada data",
          no_courses: "Belum ada kursus",
        },
        materi: {
          title: "Kelola Kursus 📚",
          subtitle: "Tambah, lihat, dan kelola kursus untuk siswa.",
          add_materi: "Tambah Materi",
          search_placeholder: "Cari judul materi...",
          all_categories: "Semua Kategori",
          video_active: "Video Aktif",
          text_only: "Teks Saja",
          empty_title: "Belum Ada Materi",
          empty_desc:
            'Materi pembelajaran digital belum tersedia. Silakan klik tombol "Tambah Materi" untuk mengunggah.',
          add_modal_title: "Tambah Materi Baru",
          title_label: "Judul Materi",
          title_placeholder: "Contoh: Dasar HTML & Tag",
          category_label: "Kategori",
          select_category: "Pilih Kategori",
          card_emoji_label: "Emoji Kartu",
          optional: "(Opsional)",
          card_color_label: "Warna Kartu",
          content_label: "Isi Materi Pembelajaran",
          video_label: "Tautan Video Pembelajaran",
          video_placeholder: "Contoh: https://www.youtube.com/watch?v=...",
          upload: "Unggah",
          edit_modal_title: "Edit Materi",
          delete_confirm_title: "Hapus Materi?",
          delete_confirm_desc: "Apakah Anda yakin ingin menghapus materi",
          delete_confirm_warning: "Tindakan ini tidak dapat dibatalkan.",
          helper_code_subject:
            'Gunakan heading <code class="materi-inline-code">## Judul Bagian</code> untuk memisahkan bagian (contoh: Pengertian, Tujuan Pembelajaran, Contoh, Kesimpulan), dan blok <code class="materi-inline-code">&#x60;&#x60;&#x60;html ... &#x60;&#x60;&#x60;</code> untuk kode yang otomatis diberi syntax highlighting. Materi tanpa format ini tetap bisa disimpan &mdash; akan ditampilkan sebagai paragraf biasa.',
          helper_general_subject:
            'Kategori ini adalah mapel Umum (bukan IT/Pemrograman), jadi <strong>tidak perlu</strong> menyertakan blok kode. Gunakan heading <code class="materi-inline-code">## Judul Bagian</code> untuk memisahkan bagian seperti Pengertian, Contoh Soal, Penyelesaian, dan Kesimpulan &mdash; materi akan ditampilkan sebagai teks/paragraf biasa.',
          edit_helper_code_subject:
            'Gunakan heading <code class="materi-inline-code">## Judul Bagian</code> untuk memisahkan bagian, dan blok <code class="materi-inline-code">&#x60;&#x60;&#x60;html ... &#x60;&#x60;&#x60;</code> untuk kode.',
          edit_helper_general_subject:
            'Kategori ini adalah mapel Umum (bukan IT/Pemrograman) &mdash; sebaiknya isi materi tanpa blok kode, cukup heading <code class="materi-inline-code">## Judul Bagian</code> dan penjelasan/contoh soal dalam bentuk teks biasa.',
        },
        quiz: {
          title: "Kelola Quiz ❓",
          subtitle: "Buat dan kelola quiz untuk siswa.",
          questions_title: "Soal:",
          questions_subtitle:
            "Kelola pertanyaan pilihan ganda dan kunci jawaban kuis ini.",
          add_question_button: "Tambah Soal",
          answer_key: "Kunci",
          questions_count: "Jumlah Soal",
          no_quiz_empty_title: "Belum ada quiz yang dibuat.",
          no_quiz_empty_desc:
            "Buat quiz pertama untuk mulai menguji pemahaman siswa.",
          add_quiz_button: "Tambah Quiz",
          add_quiz_modal_title: "Buat Quiz Baru",
          not_linked_option: "— Tidak ditautkan (quiz berdiri sendiri) —",
          link_materi_helper:
            "Jika dipilih, kuis ini tampil di detail materi tersebut dan di daftar Quiz & Latihan siswa dengan judul yang sama.",
          status_label: "Status Kuis",
          edit_modal_title: "Edit Informasi Kuis",
          add_question_modal_title: "Tambah Soal Kuis",
          edit_question_modal_title: "Edit Soal Kuis",
          question_label: "Pertanyaan / Soal",
          question_placeholder: "Tuliskan teks pertanyaan...",
          options_label: "Opsi Pilihan Jawaban",
          answer_a: "Jawaban A",
          answer_b: "Jawaban B",
          answer_c: "Jawaban C",
          answer_d: "Jawaban D",
          correct_answer_label: "Jawaban yang Benar (Kunci)",
          option_a: "Pilihan A",
          option_b: "Pilihan B",
          option_c: "Pilihan C",
          option_d: "Pilihan D",
          delete_confirm_title: "Hapus Quiz?",
          delete_confirm_desc: "Apakah Anda yakin ingin menghapus kuis",
          delete_confirm_warning:
            "Semua data nilai progres siswa yang berkaitan juga terpengaruh.",
          delete_question_confirm_title: "Hapus Soal Kuis?",
          delete_question_confirm_desc:
            "Apakah Anda yakin ingin menghapus soal ini dari kuis? Tindakan ini tidak dapat dibatalkan.",
        },
        kategori: {
          subtitle:
            "Kelola kategori mata pelajaran untuk struktur pembelajaran.",
          add_kategori: "Tambah Kategori",
          search_placeholder: "Cari nama kategori...",
          add_modal_title: "Tambah Kategori",
          name_label: "Nama Kategori",
          name_placeholder: "Contoh: Pemrograman Web",
          desc_label: "Deskripsi",
          desc_placeholder: "Tuliskan deskripsi singkat kategori ini...",
          group_label: "Grup Kategori",
          group_it: "IT / Teknologi (menampilkan penjelasan + code + praktik)",
          group_mtk: "Matematika (menampilkan rumus + contoh soal)",
          group_umum: "Umum (penjelasan biasa)",
          group_helper:
            "Menentukan bagaimana materi pada kategori ini akan ditampilkan ke siswa.",
          edit_modal_title: "Edit Kategori",
          delete_confirm_title: "Hapus Kategori?",
          delete_confirm_desc: "Apakah Anda yakin ingin menghapus kategori",
          delete_confirm_warning: "Tindakan ini tidak dapat dibatalkan.",
          empty_title: "Belum Ada Kategori",
          empty_desc:
            'Kategori baru belum ditambahkan. Silakan klik tombol "Tambah Kategori" di pojok kanan atas.',
        },
        user: {
          subtitle: "Kelola hak akses dan profil akun Siswa dan Guru.",
          add_user: "Tambah User",
          search_placeholder: "Cari nama atau email...",
          all_roles: "Semua Role",
          role: "Role",
          role_student: "Siswa",
          role_teacher: "Guru",
          empty_title: "Belum Ada User",
          empty_desc: "Belum ada user yang terdaftar dalam database JSON.",
          add_modal_title: "Tambah User Baru",
          full_name: "Nama Lengkap",
          name_placeholder: "Contoh: Nadia Safitri",
          email_label: "Alamat Email",
          email_placeholder: "nama@email.com",
          password: "Password",
          password_placeholder: "Minimal 6 karakter",
          role_access: "Role Akses",
          edit_modal_title: "Edit User",
          new_password: "Password Baru",
          leave_blank: "(Kosongkan jika tidak diubah)",
          new_password_placeholder: "Masukkan password baru jika ingin diganti",
          delete_confirm_title: "Hapus User?",
          delete_confirm_desc: "Apakah Anda yakin ingin menghapus user",
          delete_confirm_warning: "Tindakan ini tidak dapat dibatalkan.",
        },
        laporan: {
          title: "Laporan Pengaduan Siswa (SiLapor)",
          subtitle:
            "Tindak lanjuti laporan, keluhan, dan saran fasilitas dari siswa secara real-time.",
          print_pdf: "Cetak PDF",
          export_csv: "Ekspor CSV",
          search_placeholder: "Cari judul, pelapor, atau isi deskripsi...",
          all_status: "Semua Status",
          status_pending: "Menunggu",
          status_process: "Diproses",
          status_done: "Selesai",
          category_label: "Kategori:",
          follow_up: "Tindak Lanjut",
          empty_title: "Tidak Ada Laporan",
          empty_desc: "Belum ada laporan pengaduan masuk dari siswa saat ini.",
          follow_up_modal_title: "Tindak Lanjut Laporan",
          report_title_label: "Judul Laporan",
          change_status_label: "Ubah Status Laporan",
          update_status_button: "Perbarui Status",
        },
        pengaturan: {
          title: "Pengaturan Akun",
          subtitle:
            "Perbarui profil pribadi, alamat email, dan kata sandi Anda.",
          my_profile: "Profil Saya",
          change_password_title: "Ubah Kata Sandi",
          new_password: "Kata Sandi Baru",
          password_placeholder: "Min. 6 karakter",
          confirm_password: "Konfirmasi Kata Sandi Baru",
          confirm_password_placeholder: "Ulangi kata sandi baru",
        },
        nilai: {
          title: "Nilai Siswa 🏅",
          subtitle: "Rekap nilai dan progress belajar siswa.",
        },
        data_siswa: {
          title: "Data Siswa 👥",
          subtitle: "Daftar seluruh siswa yang terdaftar di EduCare.",
        },
        aktivitas: {
          title: "Aktivitas Siswa 📝",
          subtitle: "Riwayat aktivitas belajar siswa terbaru.",
        },
        laporan_masuk: {
          title: "Laporan Masuk 📮",
          subtitle: "Kelola laporan yang dikirim oleh siswa.",
        },
        dynamic: {
          no_notifications: "Belum ada notifikasi.",
          no_courses: "Belum ada kursus",
          no_activity: "Belum ada aktivitas siswa",
          no_data: "Belum ada data",
          no_students: "Belum ada siswa terdaftar",
          no_reports: "Belum ada laporan masuk",
          no_quizzes: "Belum ada quiz",
          no_grades: "Belum ada rekap nilai",
          no_progress: "Belum ada progress belajar tercatat.",
          no_match: "Tidak ada siswa yang cocok.",
          search_students: "Cari nama atau email siswa...",
          search_grades: "Cari nama siswa atau kelas...",
          save_course: "Simpan Kursus",
          save_quiz: "Simpan Quiz",
          save: "Simpan",
          save_changes: "Simpan Perubahan",
          confirm_delete: "Ya, Hapus",
          action: "Aksi",
          link_materi: "Tautkan ke Materi/Kursus (opsional)",
          link_materi_desc:
            "Jika dipilih, quiz ini akan otomatis muncul di detail materi tersebut DAN di daftar Quiz & Latihan siswa dengan judul yang sama.",
          linked_to: 'tertaut ke "{title}"',
          not_linked: "belum tertaut",
          edit: "Edit",
          edit_quiz_title: "Edit Quiz",
          cancel: "Batal",
          delete_course: "Hapus",
          delete_quiz: "Hapus",
          delete_course_confirm:
            "Hapus kursus ini? Progress siswa yang sudah mendaftar tidak akan hilang otomatis.",
          delete_quiz_confirm: "Hapus quiz?",
          incoming_reports: "Laporan Masuk",
          recent_activity: "Aktivitas Siswa Terbaru",
          student_data: "Data Siswa",
          student_grades: "Nilai Siswa",
          manage_course_desc:
            "Kursus yang kamu buat di sini langsung tampil di halaman Jelajahi Kursus milik siswa.",
          course_title: "Judul Kursus",
          course_title_placeholder: "Contoh: Belajar HTML Dasar",
          category: "Kategori",
          level: "Level",
          level_beginner: "Pemula",
          level_intermediate: "Menengah",
          level_advanced: "Lanjutan",
          lessons: "pelajaran",
          short_description: "Deskripsi Singkat",
          course_description_placeholder:
            "Deskripsi kursus untuk ditampilkan ke siswa...",
          theme_color: "Warna Tema",
          chapters_lessons: "Chapter & Pelajaran",
          add_chapter: "+ Tambah Chapter",
          update: "Update",
          name: "Nama",
          email: "Email",
          status: "Status",
          active: "Aktif",
          draft: "Draft",
          quiz_name: "Nama Quiz",
          quiz_name_placeholder: "Contoh: Quiz HTML Dasar",
          deadline_label: "Deadline",
          active_visible: "Aktif (tampil ke siswa)",
          draft_hidden: "Draft (belum tampil)",
          questions_label: "Soal",
          add_question: "+ Tambah Soal",
          chapter_placeholder: "Judul Chapter, contoh: Chapter 1: HTML Dasar",
          lesson_placeholder: "Judul pelajaran",
          question_placeholder: "Tulis pertanyaan...",
          option_placeholder: "Pilihan",
          chapter_unit: "chapter",
          class_word: "Kelas",
          reporter_label: "Pelapor",
          questions_word: "soal",
          add_lesson: "+ Tambah Pelajaran",
          question_word: "SOAL",
          correct_answer_hint: "Pilih radio di samping jawaban yang benar",
          min_chapter_error: "Tambahkan minimal 1 chapter dengan 1 pelajaran.",
          footer_rights: "EduCare. Seluruh hak cipta dilindungi.",
        },
      },
    },

    en: {
      nav: {
        home: "Home",
        about: "About",
        edukasi: "Learning",
        silapor: "Reports",
        cara_kerja: "How it Works",
        testimoni: "Testimonials",
        faq: "FAQ",
        contact: "Contact",
        login: "Log In",
        register: "Sign Up",
        logout: "Log Out",
        menu: "Menu",
        courses: "Courses",
      },

      site: {
        title: "EduCare",
      },
      hero: {
        badge: "Digital School Platform",
        title:
          'Learn and report,<br class="hidden sm:block">\n          <span class="text-blue-600">one platform</span> for schools.',
        desc: 'EduCare combines <strong class="text-slate-700 font-semibold">Online Learning</strong> — general subjects like Math and Science, up to IT fields like Programming and AI — with <strong class="text-slate-700 font-semibold">SiLapor School Reporting</strong>, a fast and transparent system for reporting school facility issues.',
        cta1: "Start Learning",
        cta2: "Submit a Report",
        stat1: "Materials",
        stat2: "Videos",
        stat3: "Active students",
      },
      mockup: {
        greeting: "Hi, Nadia 👋",
        class_school: "Grade 9A · SMKN 1 Cikarang",
        quiz_score: "Quiz score: 90/100",
        progress_label: "Learning Progress",
        subject1: "Mathematics",
        subject2: "Science",
        subject3: "Programming",
        webdev: "🌐 Web Development",
        uiux: "🎨 UI/UX & Design",
        report: "Report #042 — Damaged Facility",
      },
      trust: {
        label: "Trusted by the following schools",
        school1: "SMKN 1 Cikarang",
        school2: "SMK Telkom",
        school3: "SMKN 2 Bekasi",
        school4: "SMK Wikrama",
        school5: "SMKN 3 Karawang",
      },
      forwhom: {
        badge: "For Whom",
        role1: {
          label: "01 • Student",
          title: "Student",
          desc: "Study materials, take quizzes, track your XP & ranking, and report school issues via SiLapor.",
          feat1: "Access learning materials by category",
          feat2: "Online quizzes to test your understanding",
          feat3: "Report school issues directly",
          cta: "Start Learning",
        },
        role2: {
          label: "02 • Teacher",
          title: "Teacher",
          desc: "Create & manage materials, monitor student progress, and follow up on SiLapor reports.",
          feat1: "Manage lessons & subject categories",
          feat2: "Monitor student grades, XP & activity",
          feat3: "Follow up on incident reports",
          cta: "Start Teaching",
        },
      },
      edukasi: {
        badge: "Online Learning",
        title: "Two learning tracks, one dashboard",
        desc: "Structured materials, learning videos, and interactive quizzes for both general subjects and IT.",
        umum: {
          title: "General Education",
          item1: "Basic Algebra",
          item2: "Interaction of Living Things",
          item3: "Indonesia's Social & Cultural Diversity",
          item4: " Indonesian & English Language",
          item5: " Time Management & Effective Study Habits",
        },
        it: {
          title: "IT Education",
          item1: "HTML Basics",
          item2: "CSS Basics",
          item3: "JavaScript Basics",
          item4: "PHP Basics",
          item5: "Python Basics",
          item6: "Databases & SQL",
          item7: "Git & GitHub",
          item8: "UI/UX Design & Figma",
        },
      },
      features: {
        quiz: {
          title: "Online Quizzes",
          desc: "Every material ends with a short quiz to make sure the concept sticks.",
        },
        grade: {
          title: "Scores & Progress",
          desc: "Scores and completion percentage for every topic are recorded automatically.",
        },
        dashboard: {
          title: "Teacher Dashboard",
          desc: "Teachers manage materials and monitor each class's progress from one screen.",
        },
      },
      silapor: {
        badge: "SiLapor School Reports",
        title: "Report school issues as fast as sending a message",
        desc: "Students can report school facility problems directly, and track the handling status just like tracking a ticket.",
        cat1: {
          title: "Damaged Facility",
          desc: "Classrooms, restrooms, or school equipment that are damaged.",
        },
        cat2: {
          title: "Bullying",
          desc: "Bullying reports are handled confidentially and quickly.",
        },
        cat3: {
          title: "Cleanliness",
          desc: "School areas that need cleaning attention.",
        },
        cat4: {
          title: "Lost Items",
          desc: "Report lost items so they're easier to track down.",
        },
        issue1: {
          title: "#042 Broken water tap — Class 9A",
          meta: "category: facility · reported 2 hours ago",
        },
        issue2: {
          title: "#041 Bag lost in the canteen",
          meta: "category: lost item · 1 day ago",
        },
        issue3: {
          title: "#039 Trash piling up — Computer Lab",
          meta: "category: cleanliness · 3 days ago",
        },
        status: {
          in_review: "In review",
          open: "Open",
          resolved: "Resolved",
        },
        issues_label: "Incoming Reports",
        header_open_count: "3 open",
        admin_note:
          "The admin dashboard monitors every report until it's fully resolved.",

        // ===== ADDITIONS FOR SILAPOR (EN) =====
        howitworks: {
          badge: "How It Works",
          title: "Three simple steps, from reporting to resolution.",
          desc: "SiLapor is designed so students don't need to wonder who to report to. Just create a report, and the system will forward it to the right people.",
          step1: {
            title: "Choose category & write your report",
            desc: "Select the appropriate category — damaged facility, bullying, cleanliness, or lost items — then briefly describe the situation. Photos can be attached as supporting evidence.",
          },
          step2: {
            title: "Report is automatically forwarded",
            desc: "The system assigns a ticket number and forwards the report to the teacher or school admin responsible for that category.",
          },
          step3: {
            title: "Track status like a ticket",
            desc: "Report status changes from Open → In review → Resolved, and students can track it anytime via the dashboard.",
          },
        },
        categories: {
          badge: "Report Categories",
          title: "Every type of issue has its own handling path.",
          desc: "Categories ensure reports go directly to the right person, without needing to explain who to contact.",
        },
        status_legend: {
          title: "Report status meanings",
          open_desc: "New report just submitted and not yet reviewed.",
          in_review_desc:
            "Report is being reviewed or followed up by the authorized party.",
          resolved_desc: "The issue has been handled and the report is closed.",
        },
        faq: {
          badge: "FAQ",
          title: "Frequently asked questions about SiLapor",
          q1: "Will my identity be known when I report?",
          a1: "For sensitive categories like bullying, the reporter's identity is kept confidential and only accessible to the guidance counselor or authorized personnel handling the case.",
          q2: "How long does it usually take for a report to be followed up?",
          a2: "Handling time depends on the category and urgency. Any status change will be visible on the dashboard, so students can track progress.",
          q3: "Can I attach a photo as evidence?",
          a3: "Yes. Attaching photos is highly recommended, especially for facility damage or cleanliness reports, so the school can better understand the actual condition.",
          q4: "What happens after the report status is 'Resolved'?",
          a4: "The report will be saved in your history and can be reopened anytime. If the issue recurs, students are advised to create a new report so it's recorded as a separate case.",
        },
        cta: {
          title: "Have a problem at school? Don't keep it to yourself.",
          desc: "Report it via SiLapor and let the school help resolve it.",
          button: "Submit a Report Now",
        },
      },
      alur: {
        badge: "How It Works",
        title: "Four steps, two goals",
        desc: "One login, two choices: learn or report — everything is logged and managed automatically.",
        step1: {
          label: "Step 01",
          title: "Log In",
          desc: "Students, teachers, or admins log in according to their role.",
        },
        step2: {
          label: "Step 02",
          title: "Choose a Menu",
          desc: "Go to Online Learning or School Reports.",
        },
        step3: {
          label: "Step 03",
          title: "Learn / Report",
          desc: "Follow materials & quizzes, or submit a report if there's an issue.",
        },
        step4: {
          label: "Step 04",
          title: "Teachers & Admins Manage",
          desc: "Materials and reports are managed through their respective dashboards.",
        },
      },
      testimoni: {
        badge: "Testimonials",
        title: "What people say about EduCare",
        t1: {
          handle: "@ahmadrizki · student",
          quote:
            "The Web Development lessons are well-structured, from the basics to building real projects. The quizzes help gauge understanding before moving on.",
        },
        t2: {
          handle: "@sitinur · student",
          quote:
            "When a facility was damaged, I reported it through SiLapor and could see its status right away. No more waiting around with no clarity.",
        },
        t3: {
          handle: "@budiharto · teacher",
          quote:
            "The teacher dashboard makes it easy for me to monitor class progress and follow up on reports, all in one place.",
        },
      },
      cta: {
        badge: "Free for schools",
        title: "Ready to build a better digital school?",
        desc: "Learn in a more focused way and report school issues more transparently — get started with EduCare.",
        button: "Get Started",
      },
      faq: {
        badge: "Help Center",
        title: "Frequently Asked Questions",
        desc: "Everything you need to know about Online Learning and SiLapor in one EduCare platform.",
        q1: "What is the EduCare platform?",
        a1: 'EduCare is a single integrated platform that addresses two school needs at once: a digital classroom (<strong class="text-blue-600 font-semibold">Online Learning</strong>) and a school reporting system (<strong class="text-amber-600 font-semibold">SiLapor</strong>). We bring learning management and facility complaints together into the same dashboard flow.',
        q2: "What subjects are available in EduCare's Online Learning?",
        a2: "We provide comprehensive learning materials, from general school subjects like Math and Science, to cutting-edge IT fields like Programming and Artificial Intelligence (AI).",
        q3: "What is the SiLapor School Reporting feature and how does it work?",
        a3: '<strong class="font-semibold text-slate-800">SiLapor</strong> is a school infrastructure reporting system. Through this module, students and teachers can report physical facility damage or learning-environment issues. Every report is automatically logged and forwarded straight to the school\'s facilities team.',
        q4: "How does report status tracking work in SiLapor?",
        a4: 'Transparency is our focus. Every report you submit has a real-time status shown on the dashboard, from <span class="font-semibold text-blue-600">Received</span>, to <span class="font-semibold text-amber-600">In Progress</span> with a technician, until the report is marked <span class="font-semibold text-emerald-600">Resolved</span>.',
        q5: "Do students need multiple different accounts to access these features?",
        a5: 'No need to worry. Students only use <strong class="font-semibold text-slate-800">a single account</strong> to log into the main dashboard. From that account, students can study, complete digital classroom assignments, and track infrastructure report follow-ups all at once.',
      },
      footer: {
        desc: "A digital learning platform for students who want to master the tech skills of the future.",
        nav_heading: "Navigation",
        nav: {
          item1: "Home",
          item2: "Learning",
          item4: "How it Works",
          item5: "Testimonials",
        },
        company_heading: "Company",
        company: { item2: "Contact" },
        social_heading: "Follow Us",
        copyright: "© 2026 EduCare. All rights reserved.",
        privacy: "Privacy Policy",
        terms: "Terms & Conditions",
      },
      about: {
        badge: "Profile",
        crumb: "About EduCare",
        title: "One Platform to Answer Two School Needs at Once",
        deck: "Amid schools that still manage learning and complaints separately, EduCare unifies both — from the digital classroom to facility damage reports, in one and the same flow.",
        byline_name: "The EduCare Team",
        byline_meta: "Updated Jul 20, 2026 · 4 min read",
        figcaption:
          "Illustration — Online Learning and SiLapor views in one EduCare dashboard.",
        p1: "EduCare started from a simple observation: students learn through one app, while complaints about school facilities or the environment get reported in many different ways — from paper, to chat groups, to a suggestion box that's rarely opened. Two needs that run every day, but were never really connected.",
        p2_html:
          'From there, EduCare was built as a single platform that combines <strong class="font-semibold text-slate-900 dark:text-white">Online Learning</strong> — from general subjects like Math and Science, to IT fields like Programming and AI — with <strong class="font-semibold text-slate-900 dark:text-white">SiLapor School Reporting</strong>, a reporting system that keeps every report logged, tracked, and delivered to the right party.',
        quote:
          "\u201cA responsive school isn't just about teaching well, it's also about listening quickly.\u201d",
        p3: "For students, this means one account to access materials, take quizzes, and track learning progress across any subject. For teachers and school staff, every report submitted through SiLapor has a clear status — from received, to in progress, to resolved — so no report ever gets lost.",
        p4: "Today, EduCare is used by teachers and students to run two equally important sides of school life: learning every day, and making sure the learning environment itself stays fit for use.",
        tag1: "Education",
        tag2: "School Digitalization",
        tag3: "SiLapor",
        tag4: "Online Learning",
        facts_heading: "Quick Facts",
        fact1_label: "Materials available",
        fact2_label: "Learning videos",
        fact3_label: "Active students",
        fact4_label: "Partner school",
        related_heading: "Read Also",
        related1_title: "Learning Programming from Scratch with EduCare",
        related1_cat: "IT Education",
        related2_title: "How SiLapor Works: From Report to Resolution",
        related2_cat: "SiLapor",
        related3_title: "Tracking Student Learning Progress in Real Time",
        related3_cat: "Online Learning",
        cta_text: "Want to see for yourself how EduCare works?",
        cta_button: "Start Learning",
      },
      page: {
        index: "EduCare - Digital School Platform",
        login: "Sign In — EduCare",
        register: "Create Account — EduCare",
        kontak: "Contact Us - EduCare",
        about: "About EduCare — Profile",
        dashboard_siswa: "Student Dashboard • EduCare",
        dashboard_guru: "Teacher Dashboard • EduCare",
      },
      kontak: {
        badge: "Contact Us",
        title_html:
          'We\u2019re Ready to Help <span class="text-gradient">You</span>',
        desc: "Have a question about the EduCare platform or want to give feedback for your school? Reach us directly through the form below.",
        form_title: "Send a Message via WA",
        label_nama: "Full Name",
        label_email: "Email Address",
        label_pesan: "Message",
        ph_nama: "Enter your name",
        ph_email: "name@email.com",
        ph_pesan: "Write your question or suggestion here...",
        loading: "Connecting...",
        submit_button: "Send to WhatsApp",
        info_title: "School Information",
        info_desc:
          "You can also visit us directly or reach us through the official channels below:",
        addr_label: "SMK Telekomunikasi Telesandi Bekasi Address",
        phone_label: "Phone / Fax",
        email_label: "Official Email",
        map_title: "Interactive Location Map",
        map_sub:
          "SMK Telekomunikasi Telesandi Bekasi \u2022 Tambun Selatan, Bekasi",
      },
      auth: {
        login: {
          title: "Sign In to Your Account",
          subtitle: "Sign in to continue learning from scratch.",
          email_label: "Email",
          email_placeholder: "name@email.com",
          password_label: "Password",
          password_placeholder: "Enter your password",
          remember: "Remember me",
          forgot: "Forgot password?",
          submit: "Sign In",
          no_account: "Don't have an account?",
          signup_link: "Sign up now",
          copyright: "All rights reserved.",
        },
        register: {
          title: "Create a New Account",
          subtitle: "Fill in the form below to register as a student.",
          nama_label: "Full Name",
          nama_placeholder: "Enter your full name",
          email_label: "Email",
          email_placeholder: "example@gmail.com",
          password_label: "Password",
          password_placeholder: "Minimum 6 characters",
          confirm_label: "Confirm Password",
          confirm_placeholder: "Repeat your password",
          role_label: "Register As",
          role_placeholder: "Choose Account Type",
          role_siswa: "Student",
          submit: "Register Now",
          have_account: "Already have an account?",
          login_link: "Sign in here",
          match_ok: "\u2713 Passwords match",
          match_fail: "\u2717 Passwords do not match",
          copyright: "All rights reserved.",
        },
      },

      siswa: {
        sidebar: {
          section_utama: "Main",
          section_akademik: "Academic",
          section_layanan: "Services",
          section_komunitas: "Community",
          nav_dashboard: "Dashboard",
          nav_courses: "My Courses",
          nav_materi: "Learning Materials",
          nav_continue: "Continue Learning",
          nav_progress: "Progress",
          nav_quiz: "Quiz & Practice",
          nav_laporan: "Student Reports",
          nav_leaderboard: "Leaderboard",
          brand_tag: "School Platform",
        },
        header: {
          breadcrumb_dashboard: "Dashboard",
          breadcrumb_overview: "Overview",
          role: "Student",
          notif_title: "Notifications",
          notif_mark_all: "Mark all as read",
          notif_settings: "Notification Settings →",
          profile_role: "Student · EduCare",
          menu_profile: "👤 My Profile",
          menu_settings: "⚙️ Settings",
          menu_logout: "🚪 Log Out",
        },
        overview: {
          greeting: "Welcome,",
          subtitle: "Start your learning journey today — one step at a time!",
          cta_start: "+ Start a Course",
          card_progress_title: "📖 Course Progress",
          card_progress_link: "Details →",
          card_activity_title: "⚡ Recent Activity",
          card_xp_title: "⚡ XP & Level",
          card_leaderboard_title: "🏆 Top Learners",
          card_leaderboard_link: "View →",
        },
        dynamic: {
          active_courses: "Active Courses",
          learning: "Currently learning",
          no_courses: "No courses yet",
          quizzes_done: "Quizzes Completed",
          great: "Great!",
          first_quiz: "Take your first quiz",
          day_streak: "Day Streak",
          keep_going: "Keep it up!",
          start_streak: "Start a streak today",
          start_course_desc:
            "Start your journey! Enroll in your first course and begin learning from scratch.",
          view_materials: "View Materials",
          modules: "modules",
          view_course: "View Course →",
          continue_learning: "Continue Learning →",
          no_activity:
            "No activity yet. Start learning to see your activity here.",
          quizzes_available: "quizzes available",
          material_quiz: "Material Quiz",
          completed: "Completed",
          do_quiz: "Take Quiz",
          questions: "questions",
          deadline: "deadline",
          cancel_report_confirm: "Cancel this report?",
          category_general: "General",
          category_facility: "Facility",
          category_academic: "Academic",
          category_discipline: "Discipline",
          category_other: "Other",
          week_days: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
          this_week: "This week",
          total: "Total",
          courses: "Courses",
          lessons_done: "lessons completed",
          no_skill: "No skills learned yet. Start a course first!",
          progress_detail_empty: "Enroll in a course to see progress details.",
        },
        dashboard: {
          level: "Level",
          active_student: "Active Student",
          streak: "Streak",
          completed: "Completed",
        },
        my_courses: {
          title: "My Courses 📚",
          subtitle:
            'Courses you have completed 100%. Still working on one? Click "Continue Learning" in the menu.',
          cta_continue: "▶️ Continue Learning",
        },
        materi: {
          title: "Learning Materials 📘",
          subtitle:
            "Original materials made by teachers, complete with videos and quizzes — all in this dashboard.",
          tab_all: "All Materials",
          tab_it: "IT / Technology",
          tab_umum: "General",
          search_placeholder: "🔎 Search materials...",
          no_quiz_category:
            "Quizzes for this material category are not available yet. Please contact your teacher.",
        },
        quiz: {
          title: "Quiz & Practice ❓",
          subtitle:
            "Test your understanding with interactive questions. Each material has its own quiz, at least 15 questions.",
          all_quiz_title: "🧑‍🏫 All Quizzes",
          manage_questions: "Manage Questions",
          no_questions_title: "No Questions Yet",
          no_questions_desc:
            'Please add the first question for this quiz by clicking the "Add Question" button.',
          submit_button: "Submit",
          prev_question: "← Previous",
          next_question: "Next →",
          submit_quiz: "📤 Finish & Submit",
          time_up: "Time's up! Your answers will be submitted automatically.",
          confirm_submit: "Are you sure you want to submit your answers?",
          confirm_leave_progress:
            "Your answer progress hasn't been submitted and will be lost if you leave now. Are you sure you want to return to the dashboard?",
          confirm_leave: "Are you sure you want to leave this quiz?",
          back_to_dashboard: "← Back to Dashboard",
        },
        progress: {
          title: "Learning Progress 📊",
          subtitle: "Track your learning progress in detail.",
          weekly_title: "📈 Weekly Activity",
          skill_title: "🎯 Skill Progress",
          detail_title: "📚 Per-Course Details",
        },
        laporan: {
          title: "Student Reports 📮",
          subtitle:
            "Submit a report or complaint about the school, and track its status here.",
          form_title: "📝 Create New Report",
          label_title: "Report Title",
          placeholder_title: "Example: Damaged classroom facility",
          label_category: "Category",
          label_desc: "Description",
          placeholder_desc: "Describe your report in detail...",
          submit: "📤 Submit Report",
          summary_title: "📊 Status Summary",
          status_pending: "⏳ Pending",
          status_process: "🔧 In Progress",
          status_done: "✅ Resolved",
          history_title: "📋 My Report History",
          empty_title: "No reports yet",
          empty_desc: "Reports you submit will appear here.",
          cancel: "🗑 Cancel",
        },
        leaderboard: {
          title: "Leaderboard 🏆",
          subtitle: "Healthy competition fuels the spirit to learn.",
          tab_monthly: "Monthly",
          tab_weekly: "Weekly",
          tab_alltime: "All Time",
        },
        profile: {
          title: "My Profile 👤",
          subtitle: "Manage your profile info and see your achievements.",
          edit: "✏️ Edit Profile",
          achievements_title: "🏅 Achievements",
        },
        settings: {
          title: "Settings ⚙️",
          subtitle: "Manage your account preferences and security.",
          tab_profile: "👤 Profile",
          tab_notif: "🔔 Notifications",
          tab_privacy: "🔐 Privacy",
          tab_appearance: "🎨 Appearance",
        },
        chrome: {
          completed: "✓ Completed",
          xp: "XP",
          overview: {
            totalXp: "TOTAL XP",
            levelCurrent: "Level {level} — {name}",
            xpNext: "{n} XP to reach Level {level}",
            xpFraction: "{have} / {need} XP",
            calTitle: "📅 Calendar",
            months: [
              "January",
              "February",
              "March",
              "April",
              "May",
              "June",
              "July",
              "August",
              "September",
              "October",
              "November",
              "December",
            ],
            days: ["S", "M", "T", "W", "T", "F", "S"],
          },
          levelNames: [
            "Beginner",
            "Explorer",
            "Learner",
            "Intermediate",
            "Advanced",
            "Pro",
            "Expert",
            "Master",
            "Elite",
            "Legend",
          ],
          levelBadges: ["🌱 Beginner", "🌿 Intermediate", "🌳 Advanced"],
          lbYou: "(You)",
          time: {
            justNow: "Just now",
            minutesAgo: "{n} minutes ago",
            hoursAgo: "{n} hours ago",
            yesterday: "Yesterday",
            daysAgo: "{n} days ago",
          },
          enroll: {
            activity: 'Enrolled in "{title}"',
            activityAlt: 'Enrolled in "{title}"',
            toast: "Successfully enrolled!",
            notifTitle: "New Course!",
            notifMsg: 'You\'ve enrolled in "{title}". Happy learning!',
          },
          mycourses: {
            emptyTitle: "No completed courses yet",
            emptyDesc:
              "Courses appear here after you complete them 100%. Keep learning first!",
            continue: "▶️ Continue Learning",
            progress100: "100% complete",
            lessonsCount: "{n} lessons",
            review: "🔁 Review Again",
          },
          lesson: {
            summary: "{pct}% complete · {n}/{total} lessons",
            active: "Active",
            lessonLabel: "LESSON {cur}/{total}",
            scriptPlaceholder: "📄 Step Script",
            close: "Close ✕",
            scriptHeading: "📖 Practice Script: {title}",
            stepsIntro:
              "Here are the practice steps to understand this material:",
            step1:
              "Open the <strong>Coding Playground</strong> (button below).",
            step2: "Copy the example code and modify it as directed.",
            step3: "Run the code and observe the output.",
            step4: "Try varying the values or the code structure.",
            tip: "Tip:",
            tipText:
              "Use <code>console.log()</code> to debug in the playground. Run the code above to see the simulated output!",
            playgroundBtn: "💻 Coding Playground",
            aiBtn: "🤖 Ask AI Tutor",
            noteBtn: "🔖 Save Note",
            noteSaved: "Note saved! 📝",
            prev: "← Previous",
            quizBtn: "❓ Take Quiz",
            doneNext: "✓ Complete & Next →",
            forumTitle: "💬 Discussion Forum — {title}",
            forumNew: "+ Create New Topic",
            forumEmpty: "No discussions yet. Be the first to ask or share!",
            like: "Like",
            reply: "Reply",
            noActiveDesc: "Enroll in a course first to start learning!",
            noLessonsTitle: "This course has no lessons yet",
            noLessonsDesc:
              'Course "{title}" has no lessons yet. Please contact your teacher or check other materials.',
          },
          complete: {
            bonus: "Bonus chapter complete!",
<<<<<<< HEAD
            courseDone: "🎉 Congrats! Course complete! +500 XP!",
=======
            courseDone:
              "🎉 Congrats! Course complete! +500 XP & Certificate earned!",
>>>>>>> fix-=web
            lessonDone: "✅ Lesson complete! +30 XP",
            allDone: "🎉 You've finished every lesson!",
            allDoneToast: "🎉 All materials & courses are done!",
            lessonReason: 'Completed a lesson in "{title}"',
            courseReason: 'Completed the course "{title}"',
            chapterTitle: "Chapter Complete!",
            chapterMsg: 'You completed a chapter in "{title}". Keep going!',
            courseTitle: "Course Complete!",
<<<<<<< HEAD
            courseMsg: "Congrats! You completed the course \"{title}\".",
=======
            courseMsg:
              'Congrats! You completed "{title}". Your certificate is ready!',
>>>>>>> fix-=web
          },
          playground: {
            onlyIT:
              "Coding Playground is only available for IT/Programming courses.",
            noLesson:
              "This course has no lessons to practice yet. Add lessons from the teacher dashboard.",
            placeholder: "// Write code here...",
            title: "💻 Coding Playground: {title}",
            hint: "Write or modify the code below, then click Run.",
            run: "🚀 Run Code",
            close: "Close",
            compiling: "[Compiling & Executing {label} script...]",
            output: "▶ Output:",
            noOutput: "(no output)",
            error: "⚠️ Error:",
          },
          ai: {
            prompt: "Ask something about this material (AI Tutor):",
            thinking: "AI Tutor is processing...",
            reply:
              "🤖 AI: Thanks for your question! Make sure to practice the code every time you finish reading.",
          },
          forum: {
            postTitle: "Discussion topic title:",
            postBody: "Discussion body:",
            postXp: "Posting on the forum",
            newTopicActivity: "Created forum topic: {title}",
            postDone: "Topic created! +10 XP",
            liked: "👍 Liked!",
            replyPrompt: "Write your reply:",
            replyDone: "Reply added!",
          },
          quiz: {
            noActiveTitle: "No active course",
            noActiveDesc: "Enroll in a course first to take its quiz!",
            viewMateri: "📘 View Materials",
            guruSoal: "{n} questions · Teacher Quiz",
            guruRetake:
              "You've taken this quiz. Retake it to improve your score.",
            guruAvailable:
              "Created by your teacher. Take it on the dedicated page to submit your score.",
            retake: "Retake →",
            start: "Start →",
            gnotAvailableTitle: "Quiz not available",
            gnotAvailableDesc:
              "A quiz for this course hasn't been prepared yet.",
            estTime: "{n} questions · about 10 minutes",
            timer: "TIME",
            reset: "🔄 Reset",
            submit: "Check Answers →",
            resultScore: "Score: {score}/{total} ({pct}%) — {feedback}",
            resultGreat: "Excellent!",
            resultRetry: "Try again to improve your understanding.",
            savingMsg: "Saving to server…",
            passXp: "Quiz passed",
            doneToast: "Quiz complete! Score {pct}% +{xp} XP 🏅",
            activity: "Taking quiz: score {pct}%",
          },
          progress: {
            enrolledAt: "Enrolled: {date}",
            lessonsDone: "{n} lessons completed",
          },
<<<<<<< HEAD
=======
          certs: {
            emptyTitle: "No certificates yet",
            emptyDesc:
              "Complete a course to earn an official EduCare certificate.",
            viewMyCourses: "📚 View My Courses",
            stillNeeds: "Complete {n}% more for this certificate",
            stillNeedsShort: "Complete {n}% more",
            official: "OFFICIAL CERTIFICATE",
            earnedBy: "Awarded to <strong>{name}</strong>",
            view: "👁 View",
            download: "⬇ Download PDF",
            viewToast: "Opening certificate...",
            downloadToast: "Downloading PDF...",
          },
>>>>>>> fix-=web
          notifs: {
            emptyTitle: "No notifications",
            emptyDesc: "Start learning to get progress notifications!",
            allRead: "All notifications marked as read.",
            welcomeTitle: "Welcome!",
            welcomeMsg:
              "Welcome to EduCare! Start your learning journey from scratch today.",
          },
          profile: {
            roleFallback: "Active Student",
            statHours: "Study Hours",
            statCourses: "Courses",
            statStreak: "Streak",
            statXp: "XP",
            ach: [
              ["First Blood", "Complete your first lesson"],
              ["3-Day Streak", "Study 3 days in a row"],
              ["Community", "First forum post"],
              ["100 XP", "Collect 100 XP"],
              ["Enrolled", "Enroll in your first course"],
              ["Speed Learner", "Finish a course in <30 days"],
              ["Top Scorer", "100% quiz score"],
            ],
            earned: "✓ EARNED",
          },
          settings: {
            profile: "Profile Info",
            firstName: "First Name",
            lastName: "Last Name",
            email: "Email",
            username: "Username",
            institution: "Institution",
            save: "💾 Save Changes",
            notif: "Notification Settings",
            emailNotif: "Email Notifications",
            emailNotifDesc: "Receive updates via email",
            courseNotif: "Course Notifications",
            courseNotifDesc: "Course info that matches your interests",
            reminder: "Daily Study Reminder",
            reminderDesc: "Reminders to keep your streak",
            privacy: "Privacy Settings",
            publicProfile: "Public Profile",
            publicProfileDesc: "Others can see your profile",
            lbVisible: "Show on Leaderboard",
            lbVisibleDesc: "Be included in the leaderboard",
            appearance: "Appearance & Theme",
            mode: "Display Mode",
            dark: "Dark theme",
            light: "Light theme",
            darkColor: "Color for Dark Mode",
            lightColor: "Color for Light Mode",
            colorBlue: "Blue",
            colorPurple: "Purple",
            colorGreen: "Green",
            colorAmber: "Amber",
            colorPink: "Pink",
            colorCyan: "Cyan",
            saved:
              "Profile updated locally! Contact admin for official data changes. ✅",
            themeChanged: "Theme changed!",
          },
          materi: {
            emptyTitle: "No materials found",
            emptyDesc: "No materials match this search or category.",
            notOpened: "Not Opened",
            hasVideo: "🎬 Has Video",
            textModule: "📄 Text Module",
            videoTitle: "🎬 Explanation Video",
            videoFallback: "Play the external video via the following link:",
            openVideo: "Open Video Link",
            prev: "← Previous Material",
            next: "Next Material →",
            quizCta: "🎯 Ready to Test Yourself?",
            quizCtaDesc:
              "Take <strong>{quiz}</strong> to test this material and earn your score.",
            takeQuiz: "Take Quiz →",
            back: "← Back to Materials",
            studied: "✓ Studied",
            openedXp: 'Opened material "{title}"',
            openedNotifTitle: "Material Studied",
            openedNotifMsg: 'You opened the material "{title}".',
            published: "Published: {date}",
          },
          levelup: {
            title: "Level Up!",
            msg: "Congrats! You reached Level {level} — {name}! XP: {xp}",
          },
        },
      },

      belajar: {
        materi: {
          page_title: "Learning Materials",
<<<<<<< HEAD
          page_subtitle: "Explore materials, watch video modules, and complete quizzes to test your understanding.",
=======
          page_subtitle:
            "Explore materials, watch video modules, and complete quizzes to earn a certificate.",
>>>>>>> fix-=web
          class_label: "Class 9A • Student",
          tab_mtk: "Mathematics",
          search_ph: "Search materials...",
          all_categories: "All Categories",
          has_video: "Has Video",
          text_module: "Text Module",
          start_learning: "Start Learning",
          empty_title: "No Learning Materials",
          empty_desc:
            "No learning materials have been uploaded by teachers yet.",
        },
        detail: {
          back_to_materi: "Back to Materials",
          breadcrumb: "Material Details",
          studied: "Studied",
          published_prefix: "Published on:",
          video_title: "Explanation Video",
          video_active: "Active Video Module",
          video_fallback: "Play the external video via the link:",
          prev_material: "Previous Material",
          next_material: "Next Material",
          quiz_badge: "Competency Test",
          quiz_cta_title: "Ready to Test Your Understanding?",
          quiz_cta_desc_prefix: "Complete the interactive quiz",
          quiz_cta_desc_min: "(at least",
          quiz_cta_desc_questions: "questions)",
<<<<<<< HEAD
          quiz_cta_desc_suffix: "to test your understanding of this material.",
=======
          quiz_cta_desc_suffix:
            "to test this material and claim your learning certificate.",
>>>>>>> fix-=web
          take_quiz_now: "Take the Quiz Now",
          copied: "Copied!",
        },
      },

      msg: {
        guru: {
          quiz: {
            name_required: "Quiz name is required!",
            add_success: "New quiz successfully added!",
            update_success: "Quiz successfully updated!",
            delete_success: "Quiz successfully deleted!",
            question_add_success: "Question successfully added!",
            question_update_success: "Question successfully updated!",
            question_delete_success: "Question successfully deleted!",
          },
          materi: {
            required: "Title, Category, and Material Content are required!",
            add_success: "Learning material successfully added!",
            update_success: "Learning material successfully updated!",
            delete_success: "Learning material successfully deleted!",
          },
          kategori: {
            add_success: "New category successfully added!",
            name_required: "Category name cannot be empty!",
            update_success: "Category successfully updated!",
            delete_success: "Category successfully deleted!",
          },
          user: {
            required: "All fields are required!",
            invalid_email: "Invalid email format!",
            email_exists: "Email is already in use!",
            add_success: "New user successfully added!",
            required_fields: "The Name, Email, and Role fields are required!",
            email_exists_other: "Email is already in use by another user!",
            update_success: "User data successfully updated!",
            cannot_delete_self: "You cannot delete your own active account!",
            delete_success: "User successfully deleted!",
          },
          laporan: {
            status_updated: "Report status successfully updated!",
            invalid_status: "Invalid status!",
          },
          pengaturan: {
            required: "Name and Email are required!",
            password_min: "Password must be at least 6 characters!",
            password_mismatch: "Password confirmation does not match!",
            update_success: "Account settings successfully updated!",
          },
        },
        quiz: {
          no_questions:
            "This quiz does not have questions yet. Please contact your teacher.",
          no_valid_questions:
            "This quiz has no valid questions yet. Please contact your teacher.",
        },
      },

      guru: {
        sidebar: {
          section_utama: "Main",
          section_pembelajaran: "Learning",
          section_siswa: "Students",
          section_silapor: "SiLapor",
          section_akun: "Account",
          nav_dashboard: "Dashboard",
          nav_analitik: "Analytics",
          nav_materi: "Manage Courses",
          nav_quiz: "Manage Quizzes",
          nav_nilai: "Student Grades",
          nav_manage_users: "Manage Users",
          nav_kategori: "Categories",
          nav_pengaturan: "Settings",
          nav_data_siswa: "Student Data",
          nav_aktivitas: "Student Activity",
          nav_laporan_masuk: "Incoming Reports",
          brand_tag: "School Platform",
        },
        header: {
          breadcrumb_dashboard: "Teacher Dashboard",
          breadcrumb_overview: "Overview",
          role: "Teacher",
          notif_title: "Notifications",
          menu_logout: "🚪 Log Out",
          profile_role: "Teacher · EduCare",
        },
        overview: {
          greeting: "Welcome,",
          subtitle: "Manage courses, quizzes, and monitor student progress.",
          stat_courses: "Total Courses",
          stat_students: "Total Students",
          stat_quiz: "Total Quizzes",
          stat_reports: "New Reports",
          recent_courses_title: "📚 Recent Courses",
          link_manage: "Manage →",
          recent_activity_title: "📝 Student Activity",
          link_detail: "Details →",
          quiz_title: "❓ Manage Quizzes",
          reports_title: "📮 Incoming Reports",
          top_students_title: "🏆 Top Students",
          link_view: "View →",
          leaderboard_title: "🏆 Leaderboard",
        },
        analitik: {
          title: "Learning Analytics 📊",
          subtitle: "Monitor student learning performance in real time.",
          chart_kelas: "Average Grade per Class",
          chart_laporan: "Incoming Report Status",
          chart_kategori: "Courses per Category",
          chart_mapel: "Average Understanding per Subject",
          average_score: "Average Grade",
          average_understanding: "Average Understanding",
          no_data: "No data yet",
          no_courses: "No courses yet",
        },
        materi: {
          title: "Manage Courses 📚",
          subtitle: "Add, view, and manage courses for students.",
          add_materi: "Add Material",
          search_placeholder: "Search material title...",
          all_categories: "All Categories",
          video_active: "Video Active",
          text_only: "Text Only",
          empty_title: "No Materials Yet",
          empty_desc:
            'Digital learning materials are not yet available. Please click the "Add Material" button to upload.',
          add_modal_title: "Add New Material",
          title_label: "Material Title",
          title_placeholder: "Example: HTML Basics & Tags",
          category_label: "Category",
          select_category: "Select Category",
          card_emoji_label: "Card Emoji",
          optional: "(Optional)",
          card_color_label: "Card Color",
          content_label: "Learning Material Content",
          video_label: "Learning Video Link",
          video_placeholder: "Example: https://www.youtube.com/watch?v=...",
          upload: "Upload",
          edit_modal_title: "Edit Material",
          delete_confirm_title: "Delete Material?",
          delete_confirm_desc: "Are you sure you want to delete this material",
          delete_confirm_warning: "This action cannot be undone.",
          helper_code_subject:
            'Use heading <code class="materi-inline-code">## Section Title</code> to separate sections (e.g., Definition, Learning Objectives, Examples, Conclusion), and block <code class="materi-inline-code">&#x60;&#x60;&#x60;html ... &#x60;&#x60;&#x60;</code> for code with automatic syntax highlighting. Materials without this format can still be saved and will be displayed as plain paragraphs.',
          helper_general_subject:
            'This category is a General subject (not IT/Programming), so there\'s <strong>no need</strong> to include code blocks. Use heading <code class="materi-inline-code">## Section Title</code> to separate sections like Definition, Practice Problems, Solutions, and Conclusion — materials will be displayed as plain text/paragraphs.',
          edit_helper_code_subject:
            'Use heading <code class="materi-inline-code">## Section Title</code> to separate sections, and block <code class="materi-inline-code">&#x60;&#x60;&#x60;html ... &#x60;&#x60;&#x60;</code> for code.',
          edit_helper_general_subject:
            'This category is a General subject (not IT/Programming) — you should fill the material without code blocks, just use heading <code class="materi-inline-code">## Section Title</code> and explanations/practice problems in plain text format.',
        },
        quiz: {
          title: "Manage Quizzes ❓",
          subtitle: "Create and manage quizzes for students.",
          questions_title: "Questions:",
          questions_subtitle:
            "Manage multiple-choice questions and answer keys for this quiz.",
          add_question_button: "Add Question",
          answer_key: "Key",
          questions_count: "Question Count",
          no_quiz_empty_title: "No quizzes have been created yet.",
          no_quiz_empty_desc:
            "Create your first quiz to start testing student understanding.",
          add_quiz_button: "Add Quiz",
          add_quiz_modal_title: "Create New Quiz",
          not_linked_option: "— Not linked (standalone quiz) —",
          link_materi_helper:
            "If selected, this quiz appears in that material's detail page and in the student's Quiz & Practice list with the same title.",
          status_label: "Quiz Status",
          edit_modal_title: "Edit Quiz Information",
          add_question_modal_title: "Add Quiz Question",
          edit_question_modal_title: "Edit Quiz Question",
          question_label: "Question",
          question_placeholder: "Write the question text...",
          options_label: "Answer Options",
          answer_a: "Answer A",
          answer_b: "Answer B",
          answer_c: "Answer C",
          answer_d: "Answer D",
          correct_answer_label: "Correct Answer (Key)",
          option_a: "Option A",
          option_b: "Option B",
          option_c: "Option C",
          option_d: "Option D",
          delete_confirm_title: "Delete Quiz?",
          delete_confirm_desc: "Are you sure you want to delete this quiz",
          delete_confirm_warning:
            "All related student progress and grade data will also be affected.",
          delete_question_confirm_title: "Delete Quiz Question?",
          delete_question_confirm_desc:
            "Are you sure you want to delete this question from the quiz? This action cannot be undone.",
        },
        kategori: {
          subtitle: "Manage subject categories for learning structure.",
          add_kategori: "Add Category",
          search_placeholder: "Search category name...",
          add_modal_title: "Add Category",
          name_label: "Category Name",
          name_placeholder: "Example: Web Programming",
          desc_label: "Description",
          desc_placeholder: "Write a short description of this category...",
          group_label: "Category Group",
          group_it: "IT / Technology (shows explanation + code + practice)",
          group_mtk: "Mathematics (shows formulas + example problems)",
          group_umum: "General (normal explanation)",
          group_helper:
            "Determines how materials in this category are displayed to students.",
          edit_modal_title: "Edit Category",
          delete_confirm_title: "Delete Category?",
          delete_confirm_desc: "Are you sure you want to delete this category",
          delete_confirm_warning: "This action cannot be undone.",
          empty_title: "No Categories Yet",
          empty_desc:
            'No new categories have been added. Please click the "Add Category" button in the top right corner.',
        },
        user: {
          subtitle:
            "Manage access rights and account profiles for Students and Teachers.",
          add_user: "Add User",
          search_placeholder: "Search name or email...",
          all_roles: "All Roles",
          role: "Role",
          role_student: "Student",
          role_teacher: "Teacher",
          empty_title: "No Users Yet",
          empty_desc: "No users are registered in the JSON database yet.",
          add_modal_title: "Add New User",
          full_name: "Full Name",
          name_placeholder: "Example: Nadia Safitri",
          email_label: "Email Address",
          email_placeholder: "name@email.com",
          password: "Password",
          password_placeholder: "Minimum 6 characters",
          role_access: "Access Role",
          edit_modal_title: "Edit User",
          new_password: "New Password",
          leave_blank: "(Leave blank if unchanged)",
          new_password_placeholder:
            "Enter a new password if you want to change it",
          delete_confirm_title: "Delete User?",
          delete_confirm_desc: "Are you sure you want to delete this user",
          delete_confirm_warning: "This action cannot be undone.",
        },
        laporan: {
          title: "Student Complaint Reports (SiLapor)",
          subtitle:
            "Follow up on student reports, complaints, and facility suggestions in real time.",
          print_pdf: "Print PDF",
          export_csv: "Export CSV",
          search_placeholder:
            "Search title, reporter, or description content...",
          all_status: "All Statuses",
          status_pending: "Pending",
          status_process: "In Progress",
          status_done: "Done",
          category_label: "Category:",
          follow_up: "Follow Up",
          empty_title: "No Reports",
          empty_desc:
            "No complaint reports have been submitted by students yet.",
          follow_up_modal_title: "Report Follow Up",
          report_title_label: "Report Title",
          change_status_label: "Change Report Status",
          update_status_button: "Update Status",
        },
        pengaturan: {
          title: "Account Settings",
          subtitle:
            "Update your personal profile, email address, and password.",
          my_profile: "My Profile",
          change_password_title: "Change Password",
          new_password: "New Password",
          password_placeholder: "Min. 6 characters",
          confirm_password: "Confirm New Password",
          confirm_password_placeholder: "Repeat the new password",
        },
        nilai: {
          title: "Student Grades 🏅",
          subtitle: "A summary of student grades and learning progress.",
        },
        data_siswa: {
          title: "Student Data 👥",
          subtitle: "A list of all students registered on EduCare.",
        },
        aktivitas: {
          title: "Student Activity 📝",
          subtitle: "Recent history of student learning activity.",
        },
        laporan_masuk: {
          title: "Incoming Reports 📮",
          subtitle: "Manage reports submitted by students.",
        },
        dynamic: {
          no_notifications: "No notifications yet.",
          no_courses: "No courses yet",
          no_activity: "No student activity yet",
          no_data: "No data yet",
          no_students: "No registered students yet",
          no_reports: "No incoming reports yet",
          no_quizzes: "No quizzes yet",
          no_grades: "No grade summary yet",
          no_progress: "No recorded learning progress yet.",
          no_match: "No matching students.",
          search_students: "Search student name or email...",
          search_grades: "Search student name or class...",
          save_course: "Save Course",
          save_quiz: "Save Quiz",
          save: "Save",
          save_changes: "Save Changes",
          confirm_delete: "Yes, Delete",
          action: "Action",
          link_materi: "Link to Materi/Course (optional)",
          link_materi_desc:
            "If selected, this quiz will automatically appear in that materi's detail page AND in the student's Quiz & Latihan list with the same title.",
          linked_to: 'linked to "{title}"',
          not_linked: "not linked",
          edit: "Edit",
          edit_quiz_title: "Edit Quiz",
          cancel: "Cancel",
          delete_course: "Delete",
          delete_quiz: "Delete",
          delete_course_confirm:
            "Delete this course? Progress of enrolled students will not be removed automatically.",
          delete_quiz_confirm: "Delete quiz?",
          incoming_reports: "Incoming Reports",
          recent_activity: "Recent Student Activity",
          student_data: "Student Data",
          student_grades: "Student Grades",
          manage_course_desc:
            "Courses you create here appear directly on the students' Explore Courses page.",
          course_title: "Course Title",
          course_title_placeholder: "Example: Learn HTML Basics",
          category: "Category",
          level: "Level",
          level_beginner: "Beginner",
          level_intermediate: "Intermediate",
          level_advanced: "Advanced",
          lessons: "lessons",
          short_description: "Short Description",
          course_description_placeholder:
            "Course description shown to students...",
          theme_color: "Theme Color",
          chapters_lessons: "Chapters & Lessons",
          add_chapter: "+ Add Chapter",
          update: "Update",
          name: "Name",
          email: "Email",
          status: "Status",
          active: "Active",
          draft: "Draft",
          quiz_name: "Quiz Name",
          quiz_name_placeholder: "Example: HTML Basics Quiz",
          deadline_label: "Deadline",
          active_visible: "Active (visible to students)",
          draft_hidden: "Draft (hidden)",
          questions_label: "Questions",
          add_question: "+ Add Question",
          chapter_placeholder: "Chapter title, e.g. Chapter 1: HTML Basics",
          lesson_placeholder: "Lesson title",
          question_placeholder: "Write a question...",
          option_placeholder: "Option",
          chapter_unit: "chapters",
          class_word: "Class",
          reporter_label: "Reporter",
          questions_word: "questions",
          add_lesson: "+ Add Lesson",
          question_word: "QUESTION",
          correct_answer_hint:
            "Select the radio button next to the correct answer",
          min_chapter_error: "Add at least 1 chapter with 1 lesson.",
          footer_rights: "EduCare. All rights reserved.",
        },
      },
    },
  };

  function getStoredLang() {
    try {
      var stored = localStorage.getItem(STORAGE_KEY);
      if (stored === "id" || stored === "en") return stored;
    } catch (e) {
      /* localStorage tidak tersedia, abaikan */
    }
    return DEFAULT_LANG;
  }

  function storeLang(lang) {
    try {
      localStorage.setItem(STORAGE_KEY, lang);
    } catch (e) {
      /* localStorage tidak tersedia, abaikan */
    }
  }

  function resolveKey(dict, key) {
    var parts = key.split(".");
    var cur = dict;
    for (var i = 0; i < parts.length; i++) {
      if (cur == null || typeof cur !== "object") return undefined;
      cur = cur[parts[i]];
    }
    return cur;
  }

  function translate(key, lang) {
    var selectedLang = lang || getStoredLang();
    var value = resolveKey(translations[selectedLang], key);
    if (value === undefined)
      value = resolveKey(translations[DEFAULT_LANG], key);
    return value === undefined ? key : value;
  }

  function syncSwitcherUI(lang) {
    document.querySelectorAll(".lang-current-label").forEach(function (el) {
      el.textContent = lang.toUpperCase();
    });
    document.querySelectorAll(".lang-option").forEach(function (btn) {
      var isActive = btn.getAttribute("data-lang-set") === lang;
      btn.classList.toggle("text-blue-600", isActive);
      btn.classList.toggle("font-semibold", isActive);
      btn.classList.toggle("bg-blue-50", isActive);
      btn.classList.toggle("text-gray-500", !isActive);
    });
  }

  function applyLang(lang) {
    if (lang !== "id" && lang !== "en") lang = DEFAULT_LANG;
    var dict = translations[lang];

    document.querySelectorAll("[data-i18n]").forEach(function (el) {
      var key = el.getAttribute("data-i18n");
      var value = translate(key, lang);
      if (value === undefined) return;
      el.textContent = value;
    });

    document.querySelectorAll("[data-i18n-html]").forEach(function (el) {
      var key = el.getAttribute("data-i18n-html");
      var value = translate(key, lang);
      if (value === undefined) return;
      el.innerHTML = value;
    });

    document.querySelectorAll("[data-i18n-placeholder]").forEach(function (el) {
      var key = el.getAttribute("data-i18n-placeholder");
      var value = translate(key, lang);
      if (value !== undefined) el.setAttribute("placeholder", value);
    });

    document.querySelectorAll("[data-i18n-title]").forEach(function (el) {
      var key = el.getAttribute("data-i18n-title");
      var value = translate(key, lang);
      if (value !== undefined) el.setAttribute("title", value);
    });

    document.documentElement.setAttribute("lang", lang);
    syncSwitcherUI(lang);
    document.dispatchEvent(
      new CustomEvent("educare:languagechange", {
        detail: { lang: lang },
      }),
    );
  }

  function setLang(lang) {
    storeLang(lang);
    applyLang(lang);
    syncLangToServer(lang);
  }

  // Kirim pilihan bahasa ke server (session PHP) agar halaman yang dirender
  // server-side bisa ikut menerjemahkan konten dinamis (t_dynamic).
  function syncLangToServer(lang) {
    try {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", I18N_BASE + "ajax-set-lang.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send("lang=" + encodeURIComponent(lang));
    } catch (e) {
      /* jika gagal, abaikan (halaman tetap berfungsi di mode non-EN) */
    }
  }

  function initI18n() {
    try {
      console.debug("i18n: init start, stored lang=", getStoredLang());
    } catch (e) {}
    applyLang(getStoredLang());
    syncLangToServer(getStoredLang());

    var buttons = document.querySelectorAll("[data-lang-set]");
    try {
      console.debug("i18n: found lang buttons", buttons.length);
    } catch (e) {}

    buttons.forEach(function (btn) {
      (function (b) {
        try {
          console.debug(
            "i18n: registering lang button",
            b.getAttribute("data-lang-set"),
          );
        } catch (e) {}
        b.addEventListener("click", function () {
          var langTo = b.getAttribute("data-lang-set");
          try {
            console.debug("i18n: lang button clicked", langTo);
          } catch (e) {}
          setLang(langTo);
        });
      })(btn);
    });

    // Fallback: event delegation so clicks on any element with `data-lang-set`
    // are handled even if buttons are moved/hidden or other scripts close the menu.
    document.addEventListener("click", function (ev) {
      try {
        var el = ev.target.closest && ev.target.closest("[data-lang-set]");
        if (!el) return;
        var langTo = el.getAttribute("data-lang-set");
        if (!langTo) return;
        console.debug("i18n: delegated lang click", langTo);
        setLang(langTo);
      } catch (e) {
        // ignore
      }
    });

    try {
      console.debug("i18n: init done");
    } catch (e) {}
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initI18n);
  } else {
    initI18n();
  }

  // Ekspos ke global scope kalau dibutuhkan dari script lain.
  window.EduCareI18n = {
    setLang: setLang,
    applyLang: applyLang,
    getLang: getStoredLang,
    t: translate,
    refresh: function () {
      applyLang(getStoredLang());
    },
  };
})();
