/* ============================================================
   YAWW — Sticky booking bar (retreat pages)

   A second home for the hero's two CTAs while the hero is off screen:
   the retreat's name and dates on the left, the lowest price and two
   actions on the right. "Ask for info" opens the same lead form as
   "I want more info" (js/lead-modal.js); "Book now" goes to the rooms.
   On a waitlist page (no prices) the primary action is "Join the
   waitlist" instead.

   Shown only while BOTH the hero and the booking block (rooms or the
   waitlist form) are off screen: at the top the hero's own buttons are
   right there, over the rooms their own Reserve is. Two observers'
   worth of state, no scroll handler.

   Desktop: across the top, over the sticky nav, frosted stone.
   Phone: along the bottom edge, where the thumb is; price on the left,
   the two buttons side by side.
   ============================================================ */
(function (window, document) {
  "use strict";

  var hero = document.querySelector(".retreat-hero");
  var block = document.querySelector(".rooms") || document.querySelector(".waitlist");
  if (!hero || !block || !window.IntersectionObserver) return;

  var tiers = typeof TIERS !== "undefined" ? TIERS : [];
  var priced = tiers.filter(function (t) { return t.priceUSD != null; });
  var cheapest = priced.reduce(function (a, t) { return !a || t.priceUSD < a.priceUSD ? t : a; }, null);
  var waitlist = !!document.querySelector(".waitlist");

  var name = hero.querySelector(".retreat-hero__title");
  var date = hero.querySelector(".retreat-hero__meta span");

  function esc(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  var price = cheapest
    ? '<p class="booking-bar__price"><span class="booking-bar__from">From</span> ' +
        cheapest.priceUSD.toLocaleString("en-US") + ' USD' +
        (cheapest.priceUnit ? '<span class="booking-bar__price-unit">' + esc(cheapest.priceUnit) + '</span>' : "") +
      '</p>'
    : '<p class="booking-bar__price booking-bar__price--soft">' + (waitlist ? "Dates to be announced" : "Prices coming soon") + '</p>';

  var bar = document.createElement("div");
  bar.className = "booking-bar";
  bar.setAttribute("role", "region");
  bar.setAttribute("aria-label", "Book this retreat");
  bar.innerHTML =
    '<div class="booking-bar__inner">' +
      '<div class="booking-bar__id">' +
        '<p class="booking-bar__name">' + esc(name ? name.textContent : "") + '</p>' +
        (date ? '<p class="booking-bar__date">' + esc(date.textContent) + '</p>' : "") +
      '</div>' +
      '<div class="booking-bar__act">' +
        price +
        '<div class="booking-bar__btns">' +
          '<a class="btn btn--secondary booking-bar__ask" href="contact.html#general-inquiry" data-lead-open data-lead-source="sticky-bar">Ask for info</a>' +
          (waitlist
            ? '<a class="btn btn--primary" href="#waitlist">Join the waitlist</a>'
            : '<a class="btn btn--primary" href="#rooms">Book now</a>') +
        '</div>' +
      '</div>' +
    '</div>';
  document.body.appendChild(bar);
  // Hidden bar: out of the tab order and the accessibility tree.
  bar.inert = true;

  var heroIn = true, blockIn = false;
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.target === hero) heroIn = e.isIntersecting;
      else blockIn = e.isIntersecting;
    });
    var show = !heroIn && !blockIn;
    bar.classList.toggle("is-visible", show);
    bar.inert = !show;
  });
  io.observe(hero);
  io.observe(block);
})(window, document);
