/* Location pages: in-page anchors (#retreats, #waitlist) glide with Lenis, clear of the sticky nav
   (build_location.py adds this to the static Lenis init). */
(function () {
  var lenis = window.yawwLenis;
  if (!lenis) return;
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('a[href^="#"]');
    if (!a || a.getAttribute('href').length < 2 || a.classList.contains('skip-link')) return;
    var to = document.querySelector(a.getAttribute('href'));
    if (!to) return;
    e.preventDefault();
    lenis.scrollTo(to, { offset: -56 });
  });
})();
