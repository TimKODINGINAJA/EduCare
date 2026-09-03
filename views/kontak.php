<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../function.php';
// Set page title
$pageTitle = 'Kontak Kami - EduCare Platform';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n="page.kontak"><?= htmlspecialchars($pageTitle); ?></title>

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

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <!-- Override dark mode global (navbar, footer, warna umum) -->
    <link rel="stylesheet" href="<?= htmlspecialchars(assetUrl('assets/css/style.css')) ?>">

    <!-- Alpine.js (Diperlukan untuk validasi & interaktivitas Form) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f8fafc;
            color: #0f172a;
        }

        html.dark body {
            background: #0b1120;
            color: #e2e8f0;
        }

        .text-gradient {
            background: linear-gradient(to right, #2563eb, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glassmorphism {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        html.dark .glassmorphism {
            background: rgba(15, 23, 42, 0.55);
        }
    </style>
</head>

<body class="antialiased bg-[#f8fafc] dark:bg-[#0b1120] transition-colors duration-300">

    <!-- Include navbar (Hanya dipanggil sekali di sini) -->
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <!-- Hero Header -->
    <header class="relative pt-40 pb-16 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 mb-6">
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 tracking-wide uppercase" data-i18n="kontak.badge">Hubungi Kami</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white max-w-3xl mx-auto leading-[1.1]" data-i18n-html="kontak.title_html">
                Kami Siap Membantu <span class="text-gradient">Anda</span>
            </h1>
            <p class="mt-6 text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto font-normal leading-relaxed" data-i18n="kontak.desc">
                Punya pertanyaan seputar platform EduCare atau ingin memberikan saran untuk sekolah? Hubungi kami langsung melalui formulir di bawah ini.
            </p>
        </div>
        <!-- Decorative background mesh lines -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px)] bg-[size:6rem] opacity-20 [mask-image:radial-gradient(ellipse_at_center,white,transparent)]"></div>
    </header>

    <!-- Main Section -->
    <section class="max-w-6xl mx-auto px-6 pb-24 relative z-10" x-data="{
    nama: '',
    email: '',
    pesan: '',
    submitted: false,
    loading: false,
    errors: {},
    validate() {
        this.errors = {};
        if (!this.nama.trim()) this.errors.nama = 'Nama wajib diisi';
        if (!this.email.trim()) {
            this.errors.email = 'Email wajib diisi';
        } else if (!/\S+@\S+\.\S+/.test(this.email)) {
            this.errors.email = 'Format email tidak valid';
        }
        if (!this.pesan.trim()) this.errors.pesan = 'Pesan wajib diisi';
        return Object.keys(this.errors).length === 0;
    },
    submitForm() {
        if (!this.validate()) return;
        this.loading = true;
        
        // Nomor WhatsApp tujuan (Otomatis dibersihkan dari simbol + atau spasi)
        let noWhatsApp = '6281295431853'.replace(/[^0-9]/g, ''); 
        
        // Menyusun format pesan teks agar rapi saat dibaca di WA
        const textMessage = `*Halo Admin EduCare!*%0A` +
                            `Ada pesan baru dari halaman kontak resmi:%0A%0A` +
                            `*Nama:* ${encodeURIComponent(this.nama)}%0A` +
                            `*Email:* ${encodeURIComponent(this.email)}%0A` +
                            `*Pesan:*%0A${encodeURIComponent(this.pesan)}`;
        
        // URL WhatsApp API
        const waUrl = `https://api.whatsapp.com/send?phone=${noWhatsApp}&text=${textMessage}`;
        
        // Simulasi efek loading premium sebelum redirect
        setTimeout(() => {
            this.loading = false;
            this.submitted = true;
            
            // Membuka WhatsApp di tab baru
            window.open(waUrl, '_blank');
            
            // Reset Form setelah berhasil
            this.nama = '';
            this.email = '';
            this.pesan = '';
            
            setTimeout(() => this.submitted = false, 5000);
        }, 1200);
    }
}">

        <!-- Success Toast -->
        <div x-show="submitted"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed bottom-5 right-5 z-50 flex items-center gap-3 p-4 bg-emerald-600 text-white rounded-2xl shadow-xl max-w-sm"
            style="display: none;">
            <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold">✓</div>
            <div>
                <div class="font-bold text-sm">Membuka WhatsApp...</div>
                <div class="text-xs text-emerald-100">Menghubungkan Anda ke chat admin resmi kami.</div>
            </div>
            <button @click="submitted = false" class="text-white hover:text-slate-200 text-lg font-bold ml-auto">&times;</button>
        </div>

        <div class="grid md:grid-cols-[1.1fr_1fr] gap-12 items-start">

            <!-- Form Kontak -->
            <div class="glassmorphism rounded-3xl p-8 border border-white/60 dark:border-slate-700/60 shadow-xl bg-white/40 dark:bg-slate-900/40">
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-6" data-i18n="kontak.form_title">Kirim Pesan via WA</h3>

                <form @submit.prevent="submitForm" class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2" data-i18n="kontak.label_nama">Nama Lengkap</label>
                        <input type="text" x-model="nama"
                            :class="errors.nama ? 'border-red-300 focus:ring-red-200' : 'border-slate-200 dark:border-slate-700 focus:ring-blue-100'"
                            class="w-full px-4 py-3 border rounded-2xl focus:outline-none focus:ring-4 transition text-sm bg-white dark:bg-slate-800 dark:text-slate-100" placeholder="Masukkan nama Anda" data-i18n-placeholder="kontak.ph_nama">
                        <p x-show="errors.nama" x-text="errors.nama" class="text-xs text-red-500 mt-1.5" style="display: none;"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2" data-i18n="kontak.label_email">Alamat Email</label>
                        <input type="email" x-model="email"
                            :class="errors.email ? 'border-red-300 focus:ring-red-200' : 'border-slate-200 dark:border-slate-700 focus:ring-blue-100'"
                            class="w-full px-4 py-3 border rounded-2xl focus:outline-none focus:ring-4 transition text-sm bg-white dark:bg-slate-800 dark:text-slate-100" placeholder="nama@email.com" data-i18n-placeholder="kontak.ph_email">
                        <p x-show="errors.email" x-text="errors.email" class="text-xs text-red-500 mt-1.5" style="display: none;"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2" data-i18n="kontak.label_pesan">Isi Pesan</label>
                        <textarea x-model="pesan" rows="4"
                            :class="errors.pesan ? 'border-red-300 focus:ring-red-200' : 'border-slate-200 dark:border-slate-700 focus:ring-blue-100'"
                            class="w-full px-4 py-3 border rounded-2xl focus:outline-none focus:ring-4 transition text-sm bg-white dark:bg-slate-800 dark:text-slate-100" placeholder="Tuliskan pertanyaan atau saran Anda di sini..." data-i18n-placeholder="kontak.ph_pesan"></textarea>
                        <p x-show="errors.pesan" x-text="errors.pesan" class="text-xs text-red-500 mt-1.5" style="display: none;"></p>
                    </div>

                    <button type="submit" :disabled="loading"
                        class="w-full py-4 bg-gradient-to-r from-emerald-600 to-green-600 text-white font-bold rounded-2xl transition hover:opacity-95 shadow-md flex items-center justify-center gap-2 text-sm disabled:opacity-50">
                        <span x-show="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" style="display: none;"></span>
                        <span x-show="loading" data-i18n="kontak.loading" style="display:none">Menghubungkan...</span>
                        <span x-show="!loading" data-i18n="kontak.submit_button" style="display:none">Kirim ke WhatsApp</span>
                    </button>
                </form>
            </div>

            <!-- Info Kontak Sekolah -->
            <div class="space-y-8">
                <div class="glassmorphism rounded-3xl p-8 border border-white/60 dark:border-slate-700/60 shadow-xl bg-white/40 dark:bg-slate-900/40 space-y-6">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white" data-i18n="kontak.info_title">Informasi Sekolah</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed" data-i18n="kontak.info_desc">
                        Anda juga dapat mengunjungi instansi kami secara langsung atau menghubungi kami melalui saluran resmi berikut:
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm" data-i18n="kontak.addr_label">Alamat SMK Telekomunikasi Telesandi Bekasi</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Mekarsari Raya Jl. KH. Mochammad, Mekarsari, Kec. Tambun Selatan, Kab. Bekasi, Jawa Barat 17510, Indonesia.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm" data-i18n="kontak.phone_label">Telepon / Fax</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">(021) 8901-2345 / (021) 8901-2346</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm" data-i18n="kontak.email_label">Surel Resmi</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">info@smkeducare.sch.id</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Map Placeholder -->
                <div class="rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-lg h-64 bg-slate-100 dark:bg-slate-900 relative flex items-center justify-center">
                    <div class="absolute inset-0 bg-[linear-gradient(to_right,#cbd5e1_1px,transparent_1px),linear-gradient(to_bottom,#cbd5e1_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#334155_1px,transparent_1px),linear-gradient(to_bottom,#334155_1px,transparent_1px)] bg-[size:1.5rem] opacity-30"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-slate-900 dark:to-slate-800 flex flex-col items-center justify-center p-6 text-center z-10">
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-3">
                            <i data-lucide="map" class="w-6 h-6"></i>
                        </div>
                        <div class="font-bold text-slate-800 dark:text-slate-200 text-sm" data-i18n="kontak.map_title">Peta Interaktif Lokasi</div>
                        <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1" data-i18n="kontak.map_sub">SMK Telekomunikasi Telesandi Bekasi • Tambun Selatan, Bekasi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <!-- Javascript: wajib dimuat supaya tombol menu mobile (hamburger) bisa
     membuka drawer -- termasuk lang switcher versi mobile yang ada di
     dalam drawer tersebut. Tanpa ini, di layar mobile drawer tidak akan
     pernah terbuka. -->
    <script type="module" src="<?= htmlspecialchars($baseUrl) ?>assets/js/app.js" defer></script>

</body>

</html>