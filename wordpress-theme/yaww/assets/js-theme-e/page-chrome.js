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

// Image reveal: fade in once loaded, skip images already decoded (cached / above the fold)
document.querySelectorAll('img').forEach(function (img) {
  if (img.complete) return;
  img.classList.add('img-reveal');
  function done() { img.classList.add('is-loaded'); }
  img.addEventListener('load', done);
  img.addEventListener('error', done);
});
