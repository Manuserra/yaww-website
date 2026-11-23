/* What every static location / people / About / Journal page runs inline at the foot,
   verbatim: mobile nav toggle, the newsletter submit guard, image reveal. */
(function () {
  var nav = document.querySelector('.site-header__nav');
  var btn = document.querySelector('.nav-toggle');
  if (!nav || !btn) return;
  btn.addEventListener('click', function () {
    var open = nav.classList.toggle('is-open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
})();

// Newsletter form: no endpoint yet, swallow the submit
document.querySelectorAll('.footer-news__form').forEach(function (form) {
  form.addEventListener('submit', function (e) { e.preventDefault(); });
});

// Image reveal: fade in once loaded, skip images already decoded (cached / above the fold).
// Skipped too where the static page draws the markup in JS after this ran (people grids, rails,
// profiles, location dates, the journal article), so those images never faded there either.
var NO_REVEAL = '[data-people-grid], [data-rail-track], .person-page, [data-person-retreats-grid], [data-loc-grid], [data-loc-wait-card], [data-journal-post]';
document.querySelectorAll('img').forEach(function (img) {
  if (img.complete || img.closest(NO_REVEAL)) return;
  img.classList.add('img-reveal');
  function done() { img.classList.add('is-loaded'); }
  img.addEventListener('load', done);
  img.addEventListener('error', done);
});
