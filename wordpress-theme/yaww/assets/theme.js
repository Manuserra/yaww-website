/* YAWW theme glue: the small behaviours the static pages carry inline.
   Nav toggle, tab groups, FAQ accordion, Lenis smooth scroll. Gaps: itinerary progress rail,
   gallery slider and lightbox still live in the static pages' inline scripts (see README). */
(function () {
  "use strict";
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  var toggle = $(".nav-toggle"), list = $("#primary-nav");
  if (toggle && list) toggle.addEventListener("click", function () {
    var open = toggle.getAttribute("aria-expanded") !== "true";
    toggle.setAttribute("aria-expanded", String(open));
    list.classList.toggle("is-open", open);
  });

  $$("[role=tablist]").forEach(function (tl) {
    var tabs = $$("[data-panel]", tl);
    var scope = tl.parentNode;
    tabs.forEach(function (t) {
      t.addEventListener("click", function () {
        tabs.forEach(function (o) { var on = o === t; o.classList.toggle("is-active", on); o.setAttribute("aria-selected", String(on)); });
        $$("[role=tabpanel][data-panel]", scope).forEach(function (p) { p.hidden = p.getAttribute("data-panel") !== t.getAttribute("data-panel"); });
      });
    });
  });

  $$(".faq__trigger").forEach(function (b) {
    b.addEventListener("click", function () {
      var open = b.getAttribute("aria-expanded") === "true";
      b.setAttribute("aria-expanded", String(!open));
      var p = b.closest(".faq");
      if (p) p.classList.toggle("is-open", !open);
    });
  });

  if (window.Lenis && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    var lenis = new window.Lenis();
    (function raf(t) { lenis.raf(t); requestAnimationFrame(raf); })(0);
  }
})();
