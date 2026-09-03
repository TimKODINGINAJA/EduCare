(function () {
  // Cek preferensi aksesibilitas pengguna untuk mengurangi animasi berlebih
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ---- 1. Scroll Reveal ----
  var revealEls = document.querySelectorAll('.reveal');
  revealEls.forEach(function (el) {
    var delay = el.getAttribute('data-delay');
    if (delay) el.style.setProperty('--reveal-delay', delay + 'ms');
  });

  if (reduceMotion || !('IntersectionObserver' in window)) {
    revealEls.forEach(function (el) { el.classList.add('is-visible'); });
  } else {
    var revealObserver = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    revealEls.forEach(function (el) { revealObserver.observe(el); });
  }

  // ---- 2. Counter Angka Statis (100+, 50+, 1000+) ----
  var counters = document.querySelectorAll('.counter');
  function animateCounter(el) {
    var target = parseInt(el.getAttribute('data-target'), 10) || 0;
    if (reduceMotion) { el.textContent = target; return; }
    var duration = 1400;
    var startTime = null;
    function step(ts) {
      if (!startTime) startTime = ts;
      var progress = Math.min((ts - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
      el.textContent = Math.floor(eased * target);
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = target;
      }
    }
    requestAnimationFrame(step);
  }

  if (counters.length) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      counters.forEach(animateCounter);
    } else {
      var counterObserver = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.6 });
      counters.forEach(function (el) { counterObserver.observe(el); });
    }
  }

  // ---- 3. Progress Bar Fill (Hero Mockup) ----
  var bars = document.querySelectorAll('.bar-fill');
  if (bars.length) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      bars.forEach(function (el) { el.style.width = el.getAttribute('data-width') + '%'; });
    } else {
      var barObserver = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var el = entry.target;
            el.style.width = el.getAttribute('data-width') + '%';
            obs.unobserve(el);
          }
        });
      }, { threshold: 0.4 });
      bars.forEach(function (el) { barObserver.observe(el); });
    }
  }

  // ---- 4. Pipeline Garis Penghubung (Section #alur) ----
  var pipelineFill = document.querySelector('.pipeline-fill');
  if (pipelineFill) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      pipelineFill.style.width = '100%';
    } else {
      var pipeObserver = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            pipelineFill.style.width = '100%';
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.3 });
      pipeObserver.observe(pipelineFill.closest('#alur'));
    }
  }

  // ---- 5. FAQ Accordion (EduCare) ----
  var faqTriggers = document.querySelectorAll(".faq-trigger");
  if (faqTriggers.length) {
    faqTriggers.forEach(function (button) {
      button.addEventListener("click", function () {
        var content = button.nextElementSibling;
        var icon = button.querySelector(".faq-icon");
        var item = button.closest(".faq-item");

        if (!content || !icon) return;

        // Toggle kelas hidden pada konten
        content.classList.toggle("hidden");
        
        // Cek status terbaru apakah class 'hidden' ada atau tidak
        var isHidden = content.classList.contains("hidden");

        // Ubah teks indikator (+ / −) secara dinamis
        icon.textContent = isHidden ? "+" : "−";

        // Sinkronisasi kelas 'open' pada pembungkus komponen utama
        if (item) {
          if (!isHidden) {
            item.classList.add("open");
          } else {
            item.classList.remove("open");
          }
        }

        // Modifikasi warna teks ikon berbasis Tailwind Utility custom property
        if (!isHidden) {
          icon.classList.add("text-[color:var(--primary,#3D5AFE)]");
        } else {
          icon.classList.remove("text-[color:var(--primary,#3D5AFE)]");
        }
      });
    });
  }
})();