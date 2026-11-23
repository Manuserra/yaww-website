/* CTA band: background parallax, copied from the static pages' inline script
   (same rig as .retreat-includes__bg on retreat pages, same SPEED / MAX_OFFSET_RATIO). */
(function () {
  var section = document.querySelector('.home-cta');
  var bg = document.querySelector('.home-cta__bg');
  if (!section || !bg) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  var SPEED = 0.3;
  var MAX_OFFSET_RATIO = 0.2;
  var ticking = false;
  var listening = false;

  function update() {
    ticking = false;
    var rect = section.getBoundingClientRect();
    var raw = (window.innerHeight / 2 - (rect.top + rect.height / 2)) * SPEED;
    var max = rect.height * MAX_OFFSET_RATIO;
    var offset = Math.max(-max, Math.min(max, raw));
    bg.style.transform = 'translate3d(0,' + offset.toFixed(1) + 'px,0)';
  }

  function onScroll() {
    if (!ticking) { ticking = true; requestAnimationFrame(update); }
  }

  new IntersectionObserver(function (entries) {
    var visible = entries[0].isIntersecting;
    if (visible && !listening) {
      listening = true;
      window.addEventListener('scroll', onScroll, { passive: true });
      update();
    } else if (!visible && listening) {
      listening = false;
      window.removeEventListener('scroll', onScroll);
    }
  }, { rootMargin: '20% 0px' }).observe(section);
})();
