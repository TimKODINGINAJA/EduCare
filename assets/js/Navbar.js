document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("navbar-toggle");
  const closeBtn = document.getElementById("navbar-close");
  const mobileMenu = document.getElementById("navbar-mobile-menu");
  const overlay = document.getElementById("navbar-overlay");

  // === 1. Drawer open/close ===
  function openMenu() {
    toggleBtn?.classList.add("active");
    toggleBtn?.setAttribute("aria-expanded", "true");
    mobileMenu?.classList.add("open");
    overlay?.classList.add("open");
    document.body.classList.add("navbar-drawer-open");
  }

  function closeMenu() {
    toggleBtn?.classList.remove("active");
    toggleBtn?.setAttribute("aria-expanded", "false");
    mobileMenu?.classList.remove("open");
    overlay?.classList.remove("open");
    document.body.classList.remove("navbar-drawer-open");
  }

  function isMenuOpen() {
    return !!mobileMenu?.classList.contains("open");
  }

  toggleBtn?.addEventListener("click", function () {
    isMenuOpen() ? closeMenu() : openMenu();
  });

  closeBtn?.addEventListener("click", closeMenu);

  // Klik area gelap (overlay) untuk menutup drawer
  overlay?.addEventListener("click", closeMenu);

  // Tutup drawer otomatis saat salah satu link di dalam drawer diklik
  mobileMenu?.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", closeMenu);
  });

  // Tutup drawer dengan tombol Escape
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeMenu();
  });

  // Tutup drawer kalau layar di-resize ke ukuran desktop (md ke atas)
  window.addEventListener("resize", function () {
    if (window.innerWidth >= 1024) closeMenu();
  });

  // === 2. Smooth scroll untuk link ber-anchor (#) ===
  document.querySelectorAll('nav a[href*="#"]').forEach((a) => {
    a.addEventListener("click", function (e) {
      const href = a.getAttribute("href");
      if (!href) return;

      if (href.startsWith("#")) {
        const el = document.querySelector(href);
        if (el) {
          e.preventDefault();
          const offset =
            document.getElementById("site-navbar")?.offsetHeight || 0;
          const top =
            el.getBoundingClientRect().top + window.pageYOffset - offset - 8;
          window.scrollTo({
            top,
            behavior: "smooth",
          });
          if (window.innerWidth < 1024 && isMenuOpen()) closeMenu();
        }
        return;
      }

      if (href.indexOf("#") !== -1) {
        try {
          const url = new URL(href, window.location.href);
          const targetPath = url.pathname.replace(/\/$/, "");
          const currentPath = window.location.pathname.replace(/\/$/, "");
          const hash = url.hash;
          if (
            hash &&
            (targetPath === currentPath ||
              targetPath.endsWith(currentPath) ||
              currentPath.endsWith(targetPath))
          ) {
            const el = document.querySelector(hash);
            if (el) {
              e.preventDefault();
              const offset =
                document.getElementById("site-navbar")?.offsetHeight || 0;
              const top =
                el.getBoundingClientRect().top +
                window.pageYOffset -
                offset -
                8;
              window.scrollTo({
                top,
                behavior: "smooth",
              });
              if (window.innerWidth < 1024 && isMenuOpen()) closeMenu();
            }
          }
        } catch (err) {}
      }
    });
  });

  // === 3. Scrollspy: tandai nav-link aktif sesuai section yang sedang dilihat ===
  const navLinks = document.querySelectorAll(".nav-link");
  const sections = [];

  navLinks.forEach((link) => {
    const anchor = link.getAttribute("data-anchor");
    if (anchor && anchor.startsWith("#")) {
      const sectionEl = document.querySelector(anchor);
      if (sectionEl) {
        sections.push({
          id: anchor,
          element: sectionEl,
          link: link,
        });
      }
    }
  });

  function updateActiveState() {
    const scrollPosition =
      window.scrollY +
      (document.getElementById("site-navbar")?.offsetHeight || 80) +
      100;
    let currentActiveSection = null;

    sections.forEach((sec) => {
      const top = sec.element.offsetTop;
      const height = sec.element.offsetHeight;
      if (scrollPosition >= top && scrollPosition < top + height) {
        currentActiveSection = sec;
      }
    });

    navLinks.forEach((link) => {
      const anchor = link.getAttribute("data-anchor");
      const path = link.getAttribute("data-path");
      const indicator = link.querySelector(".line-indicator");

      if (currentActiveSection && anchor === currentActiveSection.id) {
        link.classList.add("text-blue-600");
        link.classList.remove("text-gray-600");
        indicator?.classList.replace("w-0", "w-full");
      } else if (
        !currentActiveSection &&
        !anchor &&
        path === "EduCare/index.php"
      ) {
        link.classList.add("text-blue-600");
        link.classList.remove("text-gray-600");
        indicator?.classList.replace("w-0", "w-full");
      } else {
        if (anchor || path === "EduCare/index.php") {
          link.classList.remove("text-blue-600");
          link.classList.add("text-gray-600");
          indicator?.classList.replace("w-full", "w-0");
        }
      }
    });
  }

  if (sections.length > 0) {
    window.addEventListener("scroll", updateActiveState);
    updateActiveState();
  }
});
