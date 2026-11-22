/* ============================================================
   YAWW · NAV — desktop dropdown panels + the mobile overlay
   Loaded (defer) on every real page after the site scripts.

   Progressive enhancement: the header markup in each page is left
   alone and keeps working without this file (plain links, the old
   :hover sub-list, the inline mobile toggle). With it:

   DESKTOP (≥768)  Retreats, Locations, About and Contact each get a
     full-width editorial panel under the nav row: a lead (the page's
     own heading and lede, existing copy only), links in columns, and
     a photo that crossfades to whatever link is under the pointer.
     The top link still navigates; a small caret button beside it is
     the disclosure (aria-expanded) for keyboard and touch. Hover
     intent: 120ms to open, 200ms grace to close, 90ms to switch
     between open panels (so a diagonal sweep doesn't flicker).
     Esc closes and returns focus; focus leaving an item closes it.

   MOBILE (≤767)   The hamburger opens a full-screen overlay in brand
     blue (--sea-blue, the logo's colour), revealed top-down like the
     premium draft, with the items set big in Libre Baskerville in
     stone (the sand secondary). Items with more options expand in
     place. Scroll lock, focus trap, Esc, focus returns to the toggle.

   Data (locations, upcoming retreats) is read from data/*.js. Pages
   that don't already include those files get them injected on first
   intent (pointer near the nav, focus, or idle), from the site root
   worked out from this script's own URL, so journal/ pages resolve
   ../ correctly.
   ============================================================ */
(function () {
  "use strict";

  var nav = document.querySelector(".site-header__nav");
  var list = nav && nav.querySelector(".nav-list");
  if (!nav || !list) return;

  /* ---------- paths ---------------------------------------------------- */
  var me = document.currentScript;
  var meSrc = (me && me.src) || "";
  var ROOT = meSrc ? new URL("../", meSrc).href : new URL("./", location.href).href;
  var VER = (meSrc.match(/[?&]v=([^&#]+)/) || [])[1] || "";
  function url(p) { return new URL(p, ROOT).href; }

  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)");
  var mobileMQ = window.matchMedia("(max-width: 767px)");

  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;")
      .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }
  function el(tag, cls, html) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (html != null) n.innerHTML = html;
    return n;
  }

  /* ---------- where are we? -------------------------------------------- */
  var path = location.pathname;
  var file = path.split("/").pop() || "index.html";
  function sectionOf(f, inJournal) {
    if (inJournal || f === "journal.html") return "journal";
    if (/^retreats?(page)?[-.]|^retreats-full/.test(f) || /^retreatpage-/.test(f)) return "retreats";
    if (/^locations?(page)?[-.]|^locationpage-/.test(f)) return "locations";
    if (/^(about|teachers|facilitators|team)\.html$|^(teacher|facilitator|team)-/.test(f)) return "about";
    if (/^(contact|teach-with-us)\.html$/.test(f)) return "contact";
    return "";
  }
  var CURRENT = sectionOf(file, /\/journal\//.test(path));
  function here(href) {
    var u = new URL(href, location.href), h = location;
    return u.pathname === h.pathname && u.search === h.search && u.hash === h.hash;
  }

  /* ---------- copy (all of it lifted from the pages themselves) -------- */
  var COPY = {
    retreats: {
      eyebrow: "Retreats",
      title: "Your next week away starts here",          // home / people pages CTA band
      lede: "Pick a place you've always wanted to know. We'll take care of the rest.",
      links: [                                            // retreats.html tabs + COLLECTION_COPY
        { href: "retreats.html", label: "All Retreats", desc: "Every journey we run, in one place." },
        { href: "retreats.html?tier=deep-dives", label: "Deep Dives", desc: "Grounded, intimate retreats in beautiful places." },
        { href: "retreats.html?tier=signature-collection", label: "Signature Collection", desc: "Our most exclusive journeys, in rare places and exceptional venues." }
      ],
      sideLabel: "Upcoming Dates"                         // footer
    },
    locations: {
      eyebrow: "Locations",
      title: "Where we go",                               // locations.html h1 + lede
      lede: "Fourteen destinations we return to year after year, each with the hotels, teachers and local guides we have come to trust.",
      all: { href: "locations.html", label: "All locations" }
    },
    about: {
      eyebrow: "About",
      title: "Small-group yoga journeys, hosted by people who know the place",   // about.html h1 + lede
      lede: "Since 2008, Christina Thomas and her team have been taking small groups inside the places they love, with a hand-picked teacher and yoga woven into every day.",
      links: [
        { href: "about.html", label: "About Us", img: "images/about-hero.jpg" },
        { href: "teachers.html", label: "Teachers", img: "images/about-how-teachers.jpg" },
        { href: "facilitators.html", label: "Facilitators", img: "images/about-how-team.jpg" }
      ]
    },
    contact: {
      eyebrow: "Contact",
      title: "We would love to hear from you",           // contact.html h1 + lede
      lede: "Ask us anything about a retreat, plan a trip for your own group, or just say hello.",
      links: [
        { href: "contact.html", label: "Ask a question", img: "images/about-how-people.jpg" },
        { href: "contact.html#private-groups", label: "Private groups", img: "images/about-how-places.jpg" },
        { href: "teach-with-us.html", label: "Teach with us", img: "images/lead-group-terrace.jpg" }
      ]
    }
  };
  var MOBILE_SUBS = {
    retreats: COPY.retreats.links,
    about: COPY.about.links,
    contact: COPY.contact.links
  };
  var ORDER = { "retreats.html": "retreats", "locations.html": "locations", "about.html": "about",
                "journal.html": "journal", "contact.html": "contact" };

  /* ---------- data ----------------------------------------------------- */
  function loadScript(p) {
    return new Promise(function (resolve) {
      var s = document.createElement("script");
      s.src = url(p) + (VER ? "?v=" + VER : "");
      s.onload = s.onerror = function () { resolve(); };
      document.head.appendChild(s);
    });
  }
  var dataPromise = null;
  function ensureData() {
    if (dataPromise) return dataPromise;
    var p = Promise.resolve();
    if (!window.YAWW_LOCATIONS) p = p.then(function () { return loadScript("data/locations.js"); });
    if (!window.YAWW_RETREATS) p = p.then(function () { return loadScript("data/retreats.js"); });
    if (!(window.YAWW && window.YAWW.getUpcomingRetreats)) p = p.then(function () { return loadScript("js/retreat-cards.js"); });
    dataPromise = p.then(function () { fillData(); });
    return dataPromise;
  }
  function locationGroups() {
    var locs = window.YAWW_LOCATIONS || [];
    var regions = (window.YAWW_REGIONS || []).slice();
    locs.forEach(function (l) { if (regions.indexOf(l.region) < 0) regions.push(l.region); });
    return regions.map(function (r) {
      return { region: r, items: locs.filter(function (l) { return l.region === r && l.pageUrl; }) };
    }).filter(function (g) { return g.items.length; });
  }
  function upcoming(n) {
    var Y = window.YAWW;
    if (!Y || !Y.getUpcomingRetreats) return [];
    return Y.getUpcomingRetreats().filter(function (r) {
      return r.status !== "sold_out" && !r.hidden && !r.unlisted;
    }).slice(0, n);
  }

  /* ======================================================================
     DESKTOP PANELS
     ====================================================================== */
  var CARET = '<svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true" focusable="false">' +
              '<path d="M2 3.75 5 6.75 8 3.75" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

  function linkHTML(l, cls, extra) {
    var cur = here(url(l.href));
    return '<a class="' + cls + (cur ? " is-current" : "") + '" href="' + esc(url(l.href)) + '"' +
           (cur ? ' aria-current="page"' : "") + (extra || "") + ">";
  }
  function leadHTML(c, i) {
    return '<div class="mnav-col mnav-lead" style="--i:' + i + '">' +
      '<p class="mnav-eyebrow">' + esc(c.eyebrow) + "</p>" +
      '<p class="mnav-title">' + esc(c.title) + "</p>" +
      '<p class="mnav-lede">' + esc(c.lede) + "</p>" +
      (c.all ? linkHTML(c.all, "mnav-all") + '<span>' + esc(c.all.label) + '</span>' +
        '<svg width="16" height="10" viewBox="0 0 16 10" aria-hidden="true" focusable="false"><path d="M1 5h13M10 1.5 14 5l-4 3.5" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg></a>' : "") +
      "</div>";
  }
  function mediaHTML(items, i) {
    // data-src: nothing is fetched until the panel first opens.
    return '<div class="mnav-col mnav-media" style="--i:' + i + '" aria-hidden="true">' +
      '<div class="mnav-media__frame">' +
      items.map(function (m, k) {
        return '<img alt="" decoding="async" data-src="' + esc(url(m.img)) + '" data-key="' + esc(m.key) + '"' +
               (k === 0 ? ' class="is-on"' : "") + ">";
      }).join("") +
      "</div>" +
      (items.some(function (m) { return m.caption; })
        ? '<p class="mnav-media__cap">' + esc(items[0].caption || "") + "</p>" : "") +
      "</div>";
  }

  function buildRetreats() {
    var c = COPY.retreats;
    return '<div class="container mnav-grid mnav-grid--retreats">' + leadHTML(c, 0) +
      '<ul class="mnav-col mnav-links mnav-links--desc" style="--i:1">' +
      c.links.map(function (l) {
        return "<li>" + linkHTML(l, "mnav-link") + '<span class="mnav-link__t">' + esc(l.label) + "</span>" +
               '<span class="mnav-link__d">' + esc(l.desc) + "</span></a></li>";
      }).join("") + "</ul>" +
      '<div class="mnav-col mnav-side" style="--i:2" data-fill="upcoming" hidden>' +
      '<p class="mnav-eyebrow">' + esc(c.sideLabel) + "</p><ul class=\"mnav-dates\"></ul></div>" +
      "</div>";
  }
  function fillUpcoming(panel) {
    var box = panel.querySelector('[data-fill="upcoming"]');
    if (!box || box.dataset.done) return;
    var rows = upcoming(3), Y = window.YAWW;
    if (!rows.length) return;
    box.dataset.done = "1";
    box.querySelector(".mnav-dates").innerHTML = rows.map(function (r) {
      var loc = Y.getLocation ? Y.getLocation(r.locationId) : null;
      var img = r.image || (loc && loc.image);
      var href = url(Y.retreatHref ? Y.retreatHref(r) : (r.pageUrl || "retreats.html"));
      return '<li><a class="mnav-date' + (here(href) ? " is-current" : "") + '" href="' + esc(href) + '">' +
        '<span class="mnav-date__img">' + (img ? '<img alt="" decoding="async" data-src="' + esc(url(img)) + '">' : "") + "</span>" +
        '<span class="mnav-date__txt"><span class="mnav-date__when">' + esc(Y.retreatDateLabel ? Y.retreatDateLabel(r) : r.startDate) + "</span>" +
        '<span class="mnav-date__name">' + esc(r.officialName) + "</span>" +
        (loc ? '<span class="mnav-date__where">' + esc(loc.displayName || loc.name) + "</span>" : "") +
        "</span></a></li>";
    }).join("");
    box.hidden = false;
    if (panel.dataset.seen) wakeImages(box);
  }

  function buildLocations() {
    return '<div class="container mnav-grid mnav-grid--locations">' + leadHTML(COPY.locations, 0) +
      '<div class="mnav-col mnav-regions" style="--i:1" data-fill="locations"></div>' +
      '<div class="mnav-col mnav-media" style="--i:2" aria-hidden="true" data-fill="loc-media"></div>' +
      "</div>";
  }
  function fillLocations(panel) {
    var box = panel.querySelector('[data-fill="locations"]');
    if (!box || box.dataset.done) return;
    var groups = locationGroups();
    if (!groups.length) return;
    box.dataset.done = "1";
    // Europe gets a column to itself; the two smaller regions share the second.
    var cols = groups.length > 2 ? [[groups[0]], groups.slice(1)] : groups.map(function (g) { return [g]; });
    box.innerHTML = cols.map(function (col) {
      return '<div class="mnav-regions__col">' + col.map(function (g) {
        return '<div class="mnav-region"><p class="mnav-eyebrow">' + esc(g.region) + "</p><ul>" +
          g.items.map(function (l) {
            return "<li>" + linkHTML({ href: l.pageUrl }, "mnav-place", ' data-key="' + esc(l.id) + '"') + esc(l.name) + "</a></li>";
          }).join("") + "</ul></div>";
      }).join("") + "</div>";
    }).join("");
    var all = groups.reduce(function (o, g) { return o.concat(g.items); }, []);
    var cur = all.filter(function (l) { return here(url(l.pageUrl)); })[0];
    var media = all.filter(function (l) { return l.image; }).map(function (l) {
      return { key: l.id, img: l.image, caption: l.displayName || l.name };
    });
    if (cur) media.sort(function (a, b) { return (b.key === cur.id) - (a.key === cur.id); });
    var slot = panel.querySelector('[data-fill="loc-media"]');
    var tmp = el("div", "", mediaHTML(media, 2));
    slot.replaceWith(tmp.firstChild);
    if (panel.dataset.seen) wakeImages(panel);
    wireMedia(panel);
  }

  function buildSimple(key) {
    var c = COPY[key];
    return '<div class="container mnav-grid">' + leadHTML(c, 0) +
      '<ul class="mnav-col mnav-links" style="--i:1">' +
      c.links.map(function (l, k) {
        return "<li>" + linkHTML(l, "mnav-link", ' data-key="' + key + k + '"') +
          '<span class="mnav-link__t">' + esc(l.label) + "</span></a></li>";
      }).join("") + "</ul>" +
      mediaHTML(c.links.map(function (l, k) { return { key: key + k, img: l.img }; }), 2) +
      "</div>";
  }

  function wakeImages(scope) {
    scope.querySelectorAll("img[data-src]").forEach(function (img) {
      img.src = img.getAttribute("data-src");
      img.removeAttribute("data-src");
    });
  }
  // Pointer over a link → its photo comes up; leaving the list keeps the
  // last one (a photo snapping back on every gap between rows is noise).
  function wireMedia(panel) {
    var frame = panel.querySelector(".mnav-media__frame");
    if (!frame || frame.dataset.wired) return;
    frame.dataset.wired = "1";
    var cap = panel.querySelector(".mnav-media__cap");
    function show(key) {
      var hit = null;
      frame.querySelectorAll("img").forEach(function (img) {
        var on = img.getAttribute("data-key") === key;
        if (on) hit = img;
        img.classList.toggle("is-on", on);
      });
      if (hit && cap) {
        var link = panel.querySelector('[data-key="' + key + '"]:not(img)');
        var locs = window.YAWW_LOCATIONS || [];
        var l = locs.filter(function (x) { return x.id === key; })[0];
        cap.textContent = l ? (l.displayName || l.name) : (link ? link.textContent : "");
      }
    }
    panel.addEventListener("pointerover", function (e) {
      var a = e.target.closest && e.target.closest("a[data-key]");
      if (a && panel.contains(a)) show(a.getAttribute("data-key"));
    });
    var cur = panel.querySelector("a.is-current[data-key]");
    if (cur) show(cur.getAttribute("data-key"));
    panel.addEventListener("focusin", function (e) {
      var a = e.target.closest && e.target.closest("a[data-key]");
      if (a) show(a.getAttribute("data-key"));
    });
  }

  /* ---------- wire the top row ----------------------------------------- */
  var items = {};          // key -> { li, link, btn, panel }
  var openKey = null, tOpen = 0, tClose = 0, lastPointer = "mouse";
  var scrim = el("div", "mnav-scrim");
  scrim.setAttribute("aria-hidden", "true");

  Array.prototype.forEach.call(list.children, function (li) {
    var a = li.querySelector(":scope > a");
    if (!a) return;
    var key = ORDER[(a.getAttribute("href") || "").split("/").pop().split(/[?#]/)[0]];
    if (!key) return;
    a.removeAttribute("aria-haspopup");
    a.removeAttribute("aria-expanded");
    li.classList.add("mnav-top");
    if (key === CURRENT) { li.classList.add("is-current"); a.classList.add("is-current"); }
    if (!COPY[key]) return;          // Journal: a plain link

    var id = "mnav-" + key;
    var btn = el("button", "mnav-caret", CARET);
    btn.type = "button";
    btn.setAttribute("aria-expanded", "false");
    btn.setAttribute("aria-controls", id);
    btn.setAttribute("aria-label", a.textContent.trim() + " menu");
    var panel = el("div", "mnav-panel",
      key === "retreats" ? buildRetreats() : key === "locations" ? buildLocations() : buildSimple(key));
    panel.id = id;
    a.after(btn);
    // The panel lives at the end of the nav, not inside the <ul>, so the
    // row's own link styles never reach it; focus is routed by hand below
    // so Tab still goes caret → panel → next item.
    nav.appendChild(panel);
    li.classList.add("mnav-has-panel");
    items[key] = { li: li, link: a, btn: btn, panel: panel };
    wireMedia(panel);

    li.addEventListener("pointerenter", function (e) {
      if (e.pointerType !== "mouse" || mobileMQ.matches) return;
      ensureData();
      clearTimeout(tClose); clearTimeout(tOpen);
      if (openKey === key) return;
      tOpen = setTimeout(function () { open(key); }, openKey ? 90 : 120);
    });
    function leave(e) {
      if (e.pointerType !== "mouse") return;
      clearTimeout(tOpen);
      clearTimeout(tClose);
      tClose = setTimeout(close, 200);
    }
    li.addEventListener("pointerleave", leave);
    panel.addEventListener("pointerleave", leave);
    panel.addEventListener("pointerenter", function (e) {
      if (e.pointerType !== "mouse") return;
      if (openKey === key) { clearTimeout(tClose); clearTimeout(tOpen); }
    });
    btn.addEventListener("click", function () {
      ensureData();
      clearTimeout(tOpen); clearTimeout(tClose);
      if (openKey === key) close(); else open(key);
    });
    function panelLinks() { return Array.prototype.slice.call(panel.querySelectorAll("a[href]")); }
    function nextTopLink() {
      var n = li.nextElementSibling;
      return n && n.querySelector("a, button");
    }
    btn.addEventListener("keydown", function (e) {
      var tabIn = e.key === "Tab" && !e.shiftKey && openKey === key;
      if (e.key === "ArrowDown" || tabIn) {
        e.preventDefault();
        open(key);
        var first = panelLinks()[0];
        if (first) first.focus();
      }
    });
    panel.addEventListener("keydown", function (e) {
      if (e.key !== "Tab") return;
      var links = panelLinks();
      if (e.shiftKey && document.activeElement === links[0]) { e.preventDefault(); btn.focus(); }
      else if (!e.shiftKey && document.activeElement === links[links.length - 1]) {
        var nx = nextTopLink();
        e.preventDefault();
        close();
        if (nx) nx.focus(); else btn.focus();
      }
    });
    // Touch / pen on a tablet: the first tap on the word opens the panel,
    // a second tap (or any link inside) navigates.
    a.addEventListener("click", function (e) {
      if (lastPointer === "mouse" || mobileMQ.matches) return;
      if (openKey !== key) { e.preventDefault(); ensureData(); open(key); }
    });
    function focusOut(e) {
      var t = e.relatedTarget;
      if (openKey === key && !li.contains(t) && !panel.contains(t)) close();
    }
    li.addEventListener("focusout", focusOut);
    panel.addEventListener("focusout", focusOut);
    panel.addEventListener("click", function (e) {
      if (e.target.closest("a")) close(true);
    });
  });

  // Plain items (Journal) close an open panel after the same grace.
  Array.prototype.forEach.call(list.children, function (li) {
    if (li.classList.contains("mnav-has-panel")) return;
    li.addEventListener("pointerenter", function (e) {
      if (e.pointerType !== "mouse") return;
      clearTimeout(tOpen);
    });
  });

  document.addEventListener("pointerdown", function (e) {
    lastPointer = e.pointerType || "mouse";
    if (openKey && !nav.contains(e.target)) close();
  }, true);

  function placeScrim() {
    scrim.style.transform = "translateY(" + Math.max(0, nav.getBoundingClientRect().bottom) + "px)";
  }
  var raf = 0;
  window.addEventListener("scroll", function () {
    if (!openKey || raf) return;
    raf = requestAnimationFrame(function () { raf = 0; placeScrim(); });
  }, { passive: true });

  function open(key) {
    var it = items[key];
    if (!it || openKey === key) return;
    var prev = openKey && items[openKey];
    if (!it.panel.dataset.seen) { it.panel.dataset.seen = "1"; wakeImages(it.panel); }
    nav.classList.toggle("mnav-switching", !!prev);
    if (prev) {
      prev.li.classList.remove("is-open");
      prev.panel.classList.remove("is-open");
      prev.btn.setAttribute("aria-expanded", "false");
    }
    it.li.classList.add("is-open");
    it.panel.classList.add("is-open");
    it.btn.setAttribute("aria-expanded", "true");
    nav.classList.add("mnav-open");
    if (!scrim.parentNode) document.body.appendChild(scrim);
    placeScrim();
    scrim.classList.add("is-on");
    openKey = key;
  }
  function close(instant) {
    clearTimeout(tOpen); clearTimeout(tClose);
    if (!openKey) return;
    var it = items[openKey];
    it.li.classList.remove("is-open");
    it.panel.classList.remove("is-open");
    it.btn.setAttribute("aria-expanded", "false");
    nav.classList.remove("mnav-open", "mnav-switching");
    nav.classList.toggle("mnav-instant", !!instant);
    scrim.classList.remove("is-on");
    openKey = null;
    if (instant) setTimeout(function () { nav.classList.remove("mnav-instant"); }, 50);
  }

  document.addEventListener("keydown", function (e) {
    if (e.key !== "Escape" || !openKey) return;
    var it = items[openKey];
    var inside = it.li.contains(document.activeElement) || it.panel.contains(document.activeElement);
    close();
    if (inside) it.btn.focus();
  });

  function fillData() {
    if (items.retreats) fillUpcoming(items.retreats.panel);
    if (items.locations) fillLocations(items.locations.panel);
    fillSheetLocations();
  }

  /* ======================================================================
     MOBILE OVERLAY
     ====================================================================== */
  var oldToggle = nav.querySelector(".nav-toggle");
  var sheet = null, toggle = null, sheetOpen = false;

  function sheetSubHTML(key, id) {
    var subs = MOBILE_SUBS[key];
    var inner = key === "locations"
      ? '<div class="msheet__regions" data-fill="sheet-locations"></div>'
      : "<ul>" + subs.map(function (l) {
          return "<li>" + linkHTML(l, "msheet__sublink") + esc(l.label) + "</a></li>";
        }).join("") + "</ul>";
    return '<div class="msheet__sub" id="' + id + '"><div class="msheet__subinner">' + inner + "</div></div>";
  }
  function fillSheetLocations() {
    var box = sheet && sheet.querySelector('[data-fill="sheet-locations"]');
    if (!box || box.dataset.done) return;
    var groups = locationGroups();
    if (!groups.length) return;
    box.dataset.done = "1";
    box.innerHTML = groups.map(function (g) {
      return '<div class="msheet__region"><p class="msheet__eyebrow">' + esc(g.region) + "</p><ul>" +
        g.items.map(function (l) {
          return "<li>" + linkHTML({ href: l.pageUrl }, "msheet__sublink") + esc(l.name) + "</a></li>";
        }).join("") + "</ul></div>";
    }).join("") +
      '<p class="msheet__all">' + linkHTML(COPY.locations.all, "msheet__sublink") + esc(COPY.locations.all.label) + "</a></p>";
  }

  function buildSheet() {
    var logo = nav.querySelector(".site-header__logo");
    sheet = el("div", "msheet");
    sheet.id = "mnav-sheet";
    sheet.setAttribute("role", "dialog");
    sheet.setAttribute("aria-modal", "true");
    sheet.setAttribute("aria-label", "Menu");
    sheet.setAttribute("data-lenis-prevent", "");
    var rows = Array.prototype.map.call(list.children, function (li, i) {
      var a = li.querySelector(":scope > a");
      if (!a) return "";
      var key = ORDER[(a.getAttribute("href") || "").split("/").pop().split(/[?#]/)[0]];
      var label = a.textContent.trim();
      var cur = key && key === CURRENT;
      var hasSub = key && (MOBILE_SUBS[key] || key === "locations");
      var id = "msheet-" + key;
      return '<li class="msheet__item' + (cur ? " is-current" : "") + '" style="--i:' + i + '">' +
        '<div class="msheet__row"><a class="msheet__link" href="' + esc(a.href) + '"' + (cur ? ' aria-current="true"' : "") + ">" + esc(label) + "</a>" +
        (hasSub ? '<button type="button" class="msheet__more" aria-expanded="false" aria-controls="' + id + '" aria-label="' + esc(label) + ', more links"><span aria-hidden="true"></span></button>' : "") +
        "</div>" + (hasSub ? sheetSubHTML(key, id) : "") + "</li>";
    }).join("");
    sheet.innerHTML =
      '<div class="msheet__top">' +
        '<a class="msheet__logo" href="' + esc(logo ? logo.href : url("index.html")) + '" aria-label="Yoga Adventures Worldwide — home">' +
          '<svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg></a>' +
        '<button type="button" class="msheet__close" data-close>Close</button>' +
      "</div>" +
      '<nav class="msheet__body" aria-label="Primary"><ul class="msheet__list">' + rows + "</ul></nav>" +
      '<div class="msheet__foot"><span>Since 2008</span><span>Yoga Adventures Worldwide</span></div>';
    document.body.appendChild(sheet);

    sheet.querySelector("[data-close]").addEventListener("click", function () { setSheet(false); });
    sheet.querySelectorAll(".msheet__more").forEach(function (b) {
      b.addEventListener("click", function () {
        ensureData();
        var sub = document.getElementById(b.getAttribute("aria-controls"));
        var on = b.getAttribute("aria-expanded") !== "true";
        // One open at a time keeps the big type list readable.
        sheet.querySelectorAll(".msheet__more[aria-expanded=true]").forEach(function (o) {
          if (o === b) return;
          o.setAttribute("aria-expanded", "false");
          document.getElementById(o.getAttribute("aria-controls")).classList.remove("is-open");
          o.closest(".msheet__item").classList.remove("is-expanded");
        });
        b.setAttribute("aria-expanded", on ? "true" : "false");
        sub.classList.toggle("is-open", on);
        b.closest(".msheet__item").classList.toggle("is-expanded", on);
      });
    });
    sheet.addEventListener("click", function (e) {
      var a = e.target.closest("a");
      if (!a) return;
      // Same-page hash link (Private groups on contact): just close.
      if (here(a.href.split("#")[0]) && a.hash) setSheet(false);
    });
    sheet.addEventListener("keydown", function (e) {
      if (e.key === "Escape") { e.preventDefault(); setSheet(false); return; }
      if (e.key !== "Tab") return;
      var f = Array.prototype.filter.call(
        sheet.querySelectorAll("a[href], button"),
        function (n) { return n.offsetParent !== null && getComputedStyle(n).visibility !== "hidden"; });
      if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });
    fillSheetLocations();
  }

  function setSheet(on) {
    if (!sheet || sheetOpen === on) return;
    sheetOpen = on;
    sheet.classList.toggle("is-open", on);
    toggle.setAttribute("aria-expanded", on ? "true" : "false");
    document.documentElement.classList.toggle("mnav-locked", on);
    if (on) {
      ensureData();
      // Focus after the reveal has started, so the ring doesn't flash on a
      // still-clipped button.
      setTimeout(function () { var c = sheet.querySelector("[data-close]"); if (sheetOpen && c) c.focus({ preventScroll: true }); }, reduced.matches ? 0 : 120);
    } else {
      toggle.focus({ preventScroll: true });
    }
  }

  if (oldToggle) {
    // Cloning drops the inline is-open listener every page wires up, so the
    // old drop-down list never opens underneath the overlay.
    toggle = oldToggle.cloneNode(true);
    oldToggle.replaceWith(toggle);
    toggle.setAttribute("aria-controls", "mnav-sheet");
    toggle.setAttribute("aria-expanded", "false");
    toggle.setAttribute("aria-haspopup", "dialog");
    buildSheet();
    toggle.addEventListener("click", function () { setSheet(!sheetOpen); });
  }

  function onBreakpoint() {
    if (!mobileMQ.matches) setSheet(false);
    else close(true);
  }
  if (mobileMQ.addEventListener) mobileMQ.addEventListener("change", onBreakpoint);
  else if (mobileMQ.addListener) mobileMQ.addListener(onBreakpoint);

  nav.classList.add("mnav-ready");
  document.documentElement.classList.add("mnav-js");

  /* ---------- warm the data on first intent ----------------------------- */
  nav.addEventListener("pointerenter", ensureData, { once: true });
  nav.addEventListener("focusin", ensureData, { once: true });
  window.addEventListener("load", function () {
    var idle = window.requestIdleCallback || function (fn) { return setTimeout(fn, 1200); };
    idle(function () { ensureData(); }, { timeout: 3000 });
  });
  if (window.YAWW_LOCATIONS) fillData();
})();
