/* ============================================================
   YAWW — Rooms pricing: the stacked room list and the room modal

   Reads the page's own data, written by retreats/_tools/build_page.py:
     TIERS        one entry per room type: name, priceUSD, priceUnit,
                  desc, pills, gallery
     ROOM_VENUES  the hotels, in the order of the stay
     YAWW_HOTELS  the accommodations module's hotels (name, gallery, text),
                  so the modal can show each hotel as well as each room

   Layout (css/retreat.css, "ROOMS · stacked list"): the "How to book"
   rail keeps the first four columns; the eight beside it hold one
   horizontal card per room type: a small photo, the name, pills for the
   facts that decide (private or shared, bed, bath, view), the old site's
   own description, the price and two actions.

   "Reserve" on a card carries the room. Until the WeTravel trip page is
   wired (window.YAWW_WETRAVEL = { url: "…" }), it opens the lead form in
   its "reserve" wording with the room filled in, so the intent is never
   lost. docs/wetravel-cta.md has the reasoning.
   ============================================================ */
(function (window, document) {
  "use strict";

  var tiers = typeof window.TIERS !== "undefined" ? window.TIERS : (typeof TIERS !== "undefined" ? TIERS : []);
  var venues = typeof ROOM_VENUES !== "undefined" ? ROOM_VENUES : [];
  var list = document.getElementById("rooms-grid");
  if (!list || !tiers.length) return;

  var WETRAVEL = window.YAWW_WETRAVEL && window.YAWW_WETRAVEL.url ? window.YAWW_WETRAVEL : null;
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)");

  function esc(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }
  function price(t) {
    return t.priceUSD == null ? "Coming soon" : t.priceUSD.toLocaleString("en-US") + " USD";
  }
  function reserveAttrs(room, source) {
    if (WETRAVEL) {
      var url = (WETRAVEL.rooms && WETRAVEL.rooms[room]) || WETRAVEL.url;
      return 'href="' + esc(url) + '" target="_blank" rel="noopener"';
    }
    return 'href="contact.html#general-inquiry" data-lead-open data-lead-intent="reserve" data-lead-source="' + source +
           '" data-lead-room="' + esc(room) + '"';
  }
  var ARROW = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M%s"/></svg>';

  // The section's own "Reserve your spot" goes straight to WeTravel once
  // there is a trip URL; until then it stays on the lead form.
  if (WETRAVEL) {
    document.querySelectorAll("[data-wetravel]").forEach(function (a) {
      a.removeAttribute("data-lead-open");
      a.href = WETRAVEL.url;
      a.target = "_blank";
      a.rel = "noopener";
    });
  }

  /* ------------------------------------------------------------- the list */
  // Where the rooms are, said once above the list instead of on every card.
  var head = document.createElement("p");
  head.className = "rooms__where";
  if (venues.length > 1) {
    head.textContent = "One price covers the whole stay: " + venues.slice(0, -1).join(", ") + " and " + venues[venues.length - 1] + ".";
  } else if (venues.length === 1) {
    head.textContent = "Where you stay: " + venues[0] + ".";
  }
  if (head.textContent) list.parentNode.insertBefore(head, list);

  list.innerHTML = tiers.map(function (t, i) {
    var photo = t.gallery && t.gallery.length
      ? '<img src="' + esc(t.gallery[0]) + '" alt="" loading="lazy">' +
        (t.gallery.length > 1 ? '<span class="room-card__count">' + t.gallery.length + ' photos</span>' : "")
      : '<span class="room-card__missing">[missing photo: ' + esc(t.name) + ']</span>';
    return '<article class="room-card" data-room="' + i + '">' +
        '<button class="room-card__media" type="button" data-room-open="' + i + '" tabindex="-1" aria-hidden="true">' + photo + '</button>' +
        '<div class="room-card__body">' +
          '<h3 class="room-card__name"><button type="button" data-room-open="' + i + '">' + esc(t.name) + '</button></h3>' +
          (t.pills && t.pills.length ? '<ul class="room-card__pills">' + t.pills.map(function (p) { return '<li>' + esc(p) + '</li>'; }).join("") + '</ul>' : "") +
          '<p class="room-card__desc">' + (t.desc ? esc(t.desc) : '[missing data: room description]') + '</p>' +
        '</div>' +
        '<div class="room-card__side">' +
          '<p class="room-card__price">' + price(t) + '</p>' +
          (t.priceUSD != null && t.priceUnit ? '<p class="room-card__unit">' + esc(t.priceUnit) + '</p>' : "") +
          '<div class="room-card__actions">' +
            '<a class="room-card__reserve" ' + reserveAttrs(t.name, "room-card") + '>Reserve</a>' +
            '<button class="room-card__details" type="button" data-room-open="' + i + '">Details</button>' +
          '</div>' +
        '</div>' +
      '</article>';
  }).join("");

  /* ------------------------------------------------------------ the modal */
  var hotels = (window.YAWW_HOTELS || []).filter(function (h) { return h && h.gallery; });
  var showHotels = hotels.length > 1;
  var dlg, opener, current = { kind: "room", i: 0 }, slide = 0;

  function build() {
    dlg = document.createElement("dialog");
    dlg.className = "room-modal";
    dlg.setAttribute("aria-label", "Rooms");
    dlg.setAttribute("data-lenis-prevent", "");
    var tabs = tiers.map(function (t, i) {
      return '<button class="room-modal__tab" type="button" role="tab" data-kind="room" data-i="' + i + '">' + esc(t.name) + '</button>';
    }).join("");
    if (showHotels) {
      tabs += '<span class="room-modal__tabsep" aria-hidden="true">Hotels</span>' + hotels.map(function (h, i) {
        return '<button class="room-modal__tab room-modal__tab--hotel" type="button" role="tab" data-kind="hotel" data-i="' + i + '">' + esc(h.tab || h.name) + '</button>';
      }).join("");
    }
    dlg.innerHTML =
      '<div class="room-modal__sheet">' +
        '<div class="room-modal__bar">' +
          '<div class="room-modal__tabs" role="tablist" aria-label="Rooms' + (showHotels ? ' and hotels' : '') + '">' + tabs + '</div>' +
          '<button class="room-modal__close" type="button" aria-label="Close">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>' +
          '</button>' +
        '</div>' +
        '<div class="room-modal__panel" role="tabpanel" tabindex="-1">' +
          '<div class="room-modal__gallery">' +
            '<div class="room-modal__frame" data-frame></div>' +
            '<button class="room-modal__arrow room-modal__arrow--prev" type="button" aria-label="Previous photo">' + ARROW.replace("%s", "15 18l-6-6 6-6") + '</button>' +
            '<button class="room-modal__arrow room-modal__arrow--next" type="button" aria-label="Next photo">' + ARROW.replace("%s", "9 18l6-6-6-6") + '</button>' +
            '<p class="room-modal__count" aria-live="polite" data-count></p>' +
          '</div>' +
          '<div class="room-modal__info" data-info></div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(dlg);

    dlg.querySelector(".room-modal__close").addEventListener("click", close);
    dlg.addEventListener("click", function (e) {
      if (e.target === dlg) return close();
      var tab = e.target.closest(".room-modal__tab");
      if (tab) return show(tab.getAttribute("data-kind"), +tab.getAttribute("data-i"), true);
      var ask = e.target.closest("[data-room-lead]");
      if (ask) {
        e.preventDefault();
        // Hand over to the lead form: close this one first, and let focus
        // come back to the card's own Reserve link afterwards.
        var room = tiers[current.i] && current.kind === "room" ? tiers[current.i].name : "";
        var proxy = list.querySelector('.room-card[data-room="' + current.i + '"] .room-card__reserve');
        var intent = ask.getAttribute("data-room-lead");
        closeNow(false);
        if (window.YAWW && window.YAWW.openLead) {
          var t = document.createElement("a");
          t.setAttribute("data-lead-source", "room-modal");
          if (room) t.setAttribute("data-lead-room", room);
          if (intent === "reserve") t.setAttribute("data-lead-intent", "reserve");
          t.focus = function (o) { if (proxy) proxy.focus(o); };
          window.YAWW.openLead(t);
        }
      }
    });
    dlg.querySelector(".room-modal__arrow--prev").addEventListener("click", function () { go(slide - 1); });
    dlg.querySelector(".room-modal__arrow--next").addEventListener("click", function () { go(slide + 1); });
    dlg.addEventListener("cancel", function (e) { e.preventDefault(); close(); });
    dlg.addEventListener("keydown", function (e) {
      var tab = e.target.closest && e.target.closest(".room-modal__tab");
      if (tab && (e.key === "ArrowRight" || e.key === "ArrowLeft")) {
        var all = Array.prototype.slice.call(dlg.querySelectorAll(".room-modal__tab"));
        var n = all[(all.indexOf(tab) + (e.key === "ArrowRight" ? 1 : -1) + all.length) % all.length];
        e.preventDefault();
        n.focus();
        n.click();
        return;
      }
      if (!tab && (e.key === "ArrowRight" || e.key === "ArrowLeft") && !/INPUT|TEXTAREA/.test(e.target.tagName)) {
        go(slide + (e.key === "ArrowRight" ? 1 : -1));
      }
      if (e.key === "Tab") trap(e);
    });
    // Swipe between photos on a phone.
    var x0 = null;
    var frame = dlg.querySelector("[data-frame]");
    frame.addEventListener("touchstart", function (e) { x0 = e.touches[0].clientX; }, { passive: true });
    frame.addEventListener("touchend", function (e) {
      if (x0 == null) return;
      var dx = e.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 40) go(slide + (dx < 0 ? 1 : -1));
      x0 = null;
    });
  }

  function trap(e) {
    var f = Array.prototype.filter.call(dlg.querySelectorAll("button, [href], [tabindex]:not([tabindex='-1'])"),
      function (el) { return !el.disabled && el.offsetParent !== null; });
    if (!f.length) return;
    if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
    else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
  }

  function photos() {
    return current.kind === "room" ? (tiers[current.i].gallery || []) : (hotels[current.i].gallery || []);
  }

  function go(n) {
    var list = photos();
    var frame = dlg.querySelector("[data-frame]");
    var count = dlg.querySelector("[data-count]");
    dlg.classList.toggle("is-single", list.length < 2);
    if (!list.length) {
      frame.innerHTML = '<span class="room-modal__missing">[missing photo: ' + esc(current.kind === "room" ? tiers[current.i].name : hotels[current.i].name) + ']</span>';
      count.textContent = "";
      return;
    }
    slide = (n + list.length) % list.length;
    var name = current.kind === "room" ? tiers[current.i].name : (hotels[current.i].tab || hotels[current.i].name);
    frame.innerHTML = '<img src="' + esc(list[slide]) + '" alt="' + esc(name) + ', photo ' + (slide + 1) + ' of ' + list.length + '">';
    count.textContent = (slide + 1) + " / " + list.length;
    // Warm the next photo so the arrow never waits on the network.
    var pre = new Image();
    pre.src = list[(slide + 1) % list.length];
  }

  function show(kind, i, fromTab) {
    current = { kind: kind, i: i };
    dlg.querySelectorAll(".room-modal__tab").forEach(function (t) {
      var on = t.getAttribute("data-kind") === kind && +t.getAttribute("data-i") === i;
      t.setAttribute("aria-selected", on ? "true" : "false");
      t.tabIndex = on ? 0 : -1;
      t.classList.toggle("is-active", on);
      if (on && t.scrollIntoView) {
        var bar = t.parentNode;
        bar.scrollTo({ left: t.offsetLeft - (bar.clientWidth - t.offsetWidth) / 2, behavior: fromTab && !reduce.matches ? "smooth" : "auto" });
      }
    });
    var info = dlg.querySelector("[data-info]");
    if (kind === "room") {
      var t = tiers[i];
      info.innerHTML =
        '<h2 class="room-modal__name">' + esc(t.name) + '</h2>' +
        '<p class="room-modal__price">' + price(t) + (t.priceUSD != null && t.priceUnit ? ' <span>' + esc(t.priceUnit) + '</span>' : "") + '</p>' +
        (t.pills && t.pills.length ? '<ul class="room-card__pills room-modal__pills">' + t.pills.map(function (p) { return '<li>' + esc(p) + '</li>'; }).join("") + '</ul>' : "") +
        '<p class="room-modal__desc">' + (t.desc ? esc(t.desc) : "[missing data: room description]") + '</p>' +
        (venues.length ? '<p class="room-modal__where">' + (venues.length > 1 ? "Stays: " + esc(venues.join(" · ")) : esc(venues[0])) + '</p>' : "") +
        '<div class="room-modal__actions">' +
          (WETRAVEL
            ? '<a class="btn btn--primary" ' + reserveAttrs(t.name, "room-modal") + '>Reserve this room</a>'
            : '<a class="btn btn--primary" href="contact.html#general-inquiry" data-room-lead="reserve">Reserve this room</a>') +
          '<a class="btn btn--secondary" href="contact.html#general-inquiry" data-room-lead="info">Ask about this room</a>' +
        '</div>';
    } else {
      var h = hotels[i];
      info.innerHTML =
        '<p class="room-modal__eyebrow">Where you stay</p>' +
        '<h2 class="room-modal__name">' + esc(h.name) + '</h2>' +
        (h.text || []).slice(0, 2).map(function (p) { return '<p class="room-modal__desc">' + p + '</p>'; }).join("") +
        '<p class="room-modal__where"><a href="#" data-to-accom>Read more in Accommodations</a></p>';
      var more = info.querySelector("[data-to-accom]");
      if (more) more.addEventListener("click", function (e) {
        e.preventDefault();
        closeNow(false);
        var sec = document.querySelector(".retreat-accom");
        if (sec) sec.scrollIntoView({ behavior: reduce.matches ? "auto" : "smooth" });
      });
    }
    go(0);
    var panel = dlg.querySelector(".room-modal__panel");
    panel.classList.remove("is-swapping");
    void panel.offsetWidth;
    panel.classList.add("is-swapping");
  }

  function lock(on) {
    var html = document.documentElement;
    if (on) {
      html.style.setProperty("--lead-scrollbar", (window.innerWidth - html.clientWidth) + "px");
      html.classList.add("has-lead-modal");
      if (window.yawwLenis) window.yawwLenis.stop();
    } else {
      html.classList.remove("has-lead-modal");
      html.style.removeProperty("--lead-scrollbar");
      if (window.yawwLenis) window.yawwLenis.start();
    }
  }

  function open(i, trigger) {
    if (!dlg) build();
    opener = trigger;
    dlg.classList.remove("is-closing");
    lock(true);
    dlg.showModal();
    show("room", i, false);
    var tab = dlg.querySelector('.room-modal__tab[aria-selected="true"]');
    if (tab) tab.focus({ preventScroll: true });
  }

  function closeNow(restore) {
    dlg.classList.remove("is-closing");
    dlg.close();
    lock(false);
    if (restore !== false && opener && opener.focus) opener.focus({ preventScroll: true });
  }
  function close() {
    if (!dlg || !dlg.open || dlg.classList.contains("is-closing")) return;
    if (reduce.matches) return closeNow();
    dlg.classList.add("is-closing");
    setTimeout(closeNow, 180);
  }

  list.addEventListener("click", function (e) {
    var b = e.target.closest("[data-room-open]");
    if (!b) return;
    var card = b.closest(".room-card");
    open(+b.getAttribute("data-room-open"), card.querySelector(".room-card__name button"));
  });
})(window, document);
