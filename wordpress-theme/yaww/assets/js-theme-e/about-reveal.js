/* About: split image reveal, copied from about.html's inline script. The figure lifts and fades
   as it enters view; the pre-reveal class is applied here, not in the markup, so the figures stay
   visible if this never runs. */
(function () {
  var figures = document.querySelectorAll('.about-split__media');
  if (!figures.length || !('IntersectionObserver' in window)) return;

  Array.prototype.forEach.call(figures, function (f) { f.classList.add('js-reveal'); });

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-revealed');
      io.unobserve(entry.target);
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });

  Array.prototype.forEach.call(figures, function (f) { io.observe(f); });
})();
