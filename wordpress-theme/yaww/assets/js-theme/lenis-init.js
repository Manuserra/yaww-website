/* Smooth scroll (Lenis): the static pages' inline init, verbatim
   (retreatpage-*.html; index.html and retreats.html are the same minus
   window.yawwLenis, which only the lead modal reads). */
(function () {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (typeof Lenis === 'undefined') return;

  var lenis = new Lenis({
    lerp: 0.1,
    wheelMultiplier: 0.7,
    infinite: false,
    gestureOrientation: 'vertical',
    normalizeWheel: false,
    smoothTouch: false
  });

  // The lead modal stops and restarts it while it is open.
  window.yawwLenis = lenis;

  function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  }
  requestAnimationFrame(raf);
})();
