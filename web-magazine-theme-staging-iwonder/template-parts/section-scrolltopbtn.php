
<button id="scrollTopBtn" aria-label="<?php echo esc_attr( t('iw_scroll_to_top') ); ?>"><i class="fa-solid fa-arrow-up"></i></button>

<script>
  const scrollBtn = document.getElementById("scrollTopBtn");

  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      scrollBtn.classList.add("show");
    } else {
      scrollBtn.classList.remove("show");
    }
  });

  scrollBtn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
</script>
