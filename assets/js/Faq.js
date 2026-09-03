document.querySelectorAll(".faq-toggle").forEach((button) => {
  button.addEventListener("click", () => {
    const faqItem = button.closest(".faq-item");
    const content = faqItem.querySelector(".faq-content");
    const isOpen = content.classList.contains("is-open");

    // Tutup semua FAQ lain (single accordion)
    document
      .querySelectorAll(".faq-content")
      .forEach((c) => c.classList.remove("is-open"));
    document
      .querySelectorAll(".faq-item")
      .forEach((item) => item.classList.remove("is-open"));
    document
      .querySelectorAll(".faq-toggle")
      .forEach((b) => b.setAttribute("aria-expanded", "false"));

    // Buka FAQ yang diklik (jika sebelumnya tertutup)
    if (!isOpen) {
      content.classList.add("is-open");
      faqItem.classList.add("is-open");
      button.setAttribute("aria-expanded", "true");
    }
  });
});
