<?php
require_once __DIR__ . '/../function.php';
$baseUrl = rtrim(BASE_URL, '/') . '/';
?>
<footer class="px-6 pb-10">
    <div class="max-w-6xl mx-auto pt-16 border-t border-slate-100">
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-10 pb-12">
            <div>
                <a href="<?= htmlspecialchars(pageUrl('index.php') . '#home') ?>" class="flex items-center gap-2.5 mb-4">
                    <img src="<?= htmlspecialchars(assetUrl('assets/img/EduCare-logo.png')) ?>" alt="Logo EduCare" width="28" height="28" class="w-7 h-7 object-contain rounded-md">
                    <span class="font-display font-bold text-slate-900">EduCare</span>
                </a>
                <p class="text-[13.5px] text-slate-500 leading-relaxed" data-i18n="footer.desc">Platform pembelajaran digital untuk siswa yang ingin menguasai skill teknologi masa depan.</p>
            </div>

            <div>
                <p class="text-[13px] font-semibold text-slate-900 mb-4" data-i18n="footer.nav_heading">Navigasi</p>
                <ul class="space-y-2.5 text-[13.5px] text-slate-500">
                    <li><a href="<?= htmlspecialchars(pageUrl('index.php') . '#home') ?>" class="hover:text-slate-900 transition-colors" data-i18n="footer.nav.item1">Beranda</a></li>
                    <li><a href="<?= htmlspecialchars(pageUrl('index.php') . '#edukasi') ?>" class="hover:text-slate-900 transition-colors" data-i18n="footer.nav.item2">Edukasi</a></li>
                    <li><a href="<?= htmlspecialchars(pageUrl('index.php') . '#silapor') ?>" class="hover:text-slate-900 transition-colors">Silapor</a></li>
                    <li><a href="<?= htmlspecialchars(pageUrl('index.php') . '#alur') ?>" class="hover:text-slate-900 transition-colors" data-i18n="footer.nav.item4">Cara Kerja</a></li>
                    <li><a href="<?= htmlspecialchars(pageUrl('index.php') . '#testimoni') ?>" class="hover:text-slate-900 transition-colors" data-i18n="footer.nav.item5">Testimoni</a></li>
                    <li><a href="<?= htmlspecialchars(pageUrl('index.php') . '#faq') ?>" class="hover:text-slate-900 transition-colors">FAQ</a></li>


                </ul>
            </div>

            <div>
                <p class="text-[13px] font-semibold text-slate-900 mb-4" data-i18n="footer.company_heading">Perusahaan</p>
                <ul class="space-y-2.5 text-[13.5px] text-slate-500">
                    <li><a href="<?= htmlspecialchars(pageUrl('views/about.php')) ?>" class="hover:text-slate-900 transition-colors">About</a></li>
                    <li><a href="<?= htmlspecialchars(pageUrl('views/kontak.php')) ?>" class="hover:text-slate-900 transition-colors" data-i18n="footer.company.item2">Kontak</a></li>
                </ul>
            </div>

            <div>
                <p class="text-[13px] font-semibold text-slate-900 mb-4" data-i18n="footer.social_heading">Ikuti Kami</p>
                <div class="flex items-center gap-3">
                    <a href="#" class="w-8 h-8 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center hover:border-slate-300 transition-colors text-slate-500">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z" />
                        </svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center hover:border-slate-300 transition-colors text-slate-500">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.2c2.7 0 3 0 4.1.1 1 .1 1.7.2 2.3.5.6.2 1.1.6 1.6 1.1.5.5.8.9 1.1 1.6.2.6.4 1.3.5 2.3.1 1 .1 1.4.1 4.1s0 3-.1 4.1c-.1 1-.2 1.7-.5 2.3-.2.6-.6 1.1-1.1 1.6-.5.5-.9.8-1.6 1.1-.6.2-1.3.4-2.3.5-1 .1-1.4.1-4.1.1s-3 0-4.1-.1c-1-.1-1.7-.2-2.3-.5-.6-.2-1.1-.6-1.6-1.1-.5-.5-.8-.9-1.1-1.6-.2-.6-.4-1.3-.5-2.3-.1-1-.1-1.4-.1-4.1s0-3 .1-4.1c.1-1 .2-1.7.5-2.3.2-.6.6-1.1 1.1-1.6.5-.5.9-.8 1.6-1.1.6-.2 1.3-.4 2.3-.5C9 2.2 9.3 2.2 12 2.2zm0 1.8c-2.6 0-2.9 0-4 .1-.8.1-1.3.2-1.6.3-.4.2-.7.3-1 .6-.3.3-.5.6-.6 1-.1.3-.3.8-.3 1.6-.1 1.1-.1 1.4-.1 4 s0 2.9.1 4c.1.8.2 1.3.3 1.6.2.4.3.7.6 1 .3.3.6.5 1 .6.3.1.8.3 1.6.3 1.1.1 1.4.1 4 .1s2.9 0 4-.1c.8-.1 1.3-.2 1.6-.3.4-.2.7-.3 1-.6.3-.3.5-.6.6-1 .1-.3.3-.8.3-1.6.1-1.1.1-1.4.1-4s0-2.9-.1-4c-.1-.8-.2-1.3-.3-1.6-.2-.4-.3-.7-.6-1-.3-.3-.6-.5-1-.6-.3-.1-.8-.3-1.6-.3-1.1-.1-1.4-.1-4-.1zm0 3.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 1.8a2.7 2.7 0 1 0 0 5.4 2.7 2.7 0 0 0 0-5.4zm5.7-2a1 1 0 1 1-2.1 0 1 1 0 0 1 2.1 0z" />
                        </svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center hover:border-slate-300 transition-colors text-slate-500">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 4.5s-.9.5-1.9.7c.7-.6 1.2-1.4 1.4-2.2-.7.4-1.4.7-2.2.9A3.5 3.5 0 0 0 14 6.8c0 .3 0 .5.1.8-3-.2-5.6-1.6-7.3-3.8-.3.5-.5 1.1-.5 1.8 0 1.2.6 2.2 1.6 2.9-.6 0-1.1-.2-1.6-.4v.1c0 1.7 1.2 3.1 2.8 3.4-.3.1-.6.1-.9.1-.2 0-.4 0-.6-.1.4 1.4 1.7 2.4 3.2 2.4A7 7 0 0 1 1 17.5a10 10 0 0 0 5.3 1.5c6.4 0 9.9-5.3 9.9-9.9v-.5c.7-.5 1.3-1.1 1.8-1.9z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-[12px] text-slate-400" data-i18n="footer.copyright">© 2026 EduCare. Seluruh hak cipta dilindungi.</p>
            <div class="flex items-center gap-5 text-[12px] text-slate-400">
                <a href="#" class="hover:text-slate-900 transition-colors" data-i18n="footer.privacy">Kebijakan Privasi</a>
                <a href="#" class="hover:text-slate-900 transition-colors" data-i18n="footer.terms">Syarat &amp; Ketentuan</a>
            </div>
        </div>
    </div>
</footer>