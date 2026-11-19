/* YAWW · PREMIUM SKIN — shared behaviour for every page in premium/
   Header (solid / hidden on scroll down), mobile menu, and soft reveals.
   The page's own scripts (Lenis, filters, sliders…) keep running as-is. */
(function () {
  var root = document.documentElement;
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var header = document.getElementById('pHeader');
  if (!header) return;

  /* --- Header ----------------------------------------------------- */
  var overHero = document.body.classList.contains('p-over-hero');
  var hero = overHero ? document.querySelector('main > section, main > header, main > div') : null;
  var lastY = window.scrollY;
  function onScroll() {
    var y = window.scrollY;
    var limit = hero ? Math.max(40, hero.offsetHeight - header.offsetHeight) : 40;
    header.classList.toggle('is-solid', !overHero || y > limit);
    var goingDown = y > lastY + 2;
    if (goingDown && y > 600) header.classList.add('is-hidden');
    if (y < lastY - 2 || y < 600) header.classList.remove('is-hidden');
    lastY = y;
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* --- Mobile menu ------------------------------------------------ */
  var menu = document.getElementById('pMenu');
  var openBtn = header.querySelector('.p-menu-btn');
  var closeBtn = menu && menu.querySelector('[data-close]');
  function setMenu(open) {
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.style.overflow = open ? 'hidden' : '';
    (open ? closeBtn : openBtn).focus();
  }
  if (menu && openBtn && closeBtn) {
    openBtn.addEventListener('click', function () { setMenu(true); });
    closeBtn.addEventListener('click', function () { setMenu(false); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('is-open')) setMenu(false);
    });
  }

  /* --- Reveals ---------------------------------------------------- *
     Elements opt in with [data-reveal] (family skins may add more via
     window.P_REVEAL, a selector string). Only things that start below
     the fold are hidden, so nothing visible on load ever blinks. */
  if (reduced || !('IntersectionObserver' in window)) return;
  root.classList.add('p-js');
  function collect() {
    /* A family skin opts elements in from CSS:
         body.p-fam-about { --p-reveal: ".about-card, .about-quote"; } */
    var fromCss = getComputedStyle(document.body).getPropertyValue('--p-reveal').trim().replace(/^["']|["']$/g, '');
    var sel = '[data-reveal]' + (fromCss ? ',' + fromCss : '') + (window.P_REVEAL ? ',' + window.P_REVEAL : '');
    return Array.prototype.slice.call(document.querySelectorAll(sel));
  }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (en) {
      if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.04 });
  function arm() {
    var vh = window.innerHeight;
    collect().forEach(function (el) {
      if (el.dataset.pArmed) return;
      el.dataset.pArmed = '1';
      if (!el.hasAttribute('data-reveal')) el.setAttribute('data-reveal', '');
      if (el.getBoundingClientRect().top < vh) { el.classList.add('is-in'); return; }
      io.observe(el);
    });
  }
  /* Arm after the page's own scripts have rendered their content, and
     again for content they render later (filters, tabs). */
  window.addEventListener('load', function () { setTimeout(arm, 60); });
  if ('MutationObserver' in window) {
    var t = null;
    new MutationObserver(function () { clearTimeout(t); t = setTimeout(arm, 120); })
      .observe(document.querySelector('main') || document.body, { childList: true, subtree: true });
  }
})();
