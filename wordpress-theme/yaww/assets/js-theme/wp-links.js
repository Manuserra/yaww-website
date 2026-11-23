/* ============================================================
   YAWW (WordPress) · static links -> WordPress URLs

   The site's scripts (js/nav.js, js/retreat-rooms.js, js/booking-bar.js,
   the listing) are reused unchanged, and they name pages the static way:
   "retreats.html", "contact.html#general-inquiry", and nav.js resolves
   them against its own folder. window.YAWW_WP_LINKS (inc/views.php) maps
   every static file name to its WordPress URL; this file rewrites links
   as they appear.

   js/nav.js also recognises the top-level nav items by their href's file
   name (ORDER["retreats.html"]). So, just before it runs (both deferred,
   this one first), the five top links get their static names back for a
   moment; on DOMContentLoaded (after every deferred script) they and
   everything nav.js built are pointed at WordPress again, and the current
   section, which nav.js reads from a .html path, is marked from the
   server's aria-current.
   ============================================================ */
(function () {
  "use strict";
  var MAP = window.YAWW_WP_LINKS || {};
  var ASSETS = window.YAWW_ASSETS || "";
  var REV = {};
  Object.keys(MAP).forEach(function (k) { if (k) REV[norm(MAP[k])] = k; });

  function norm(u) {
    try { var x = new URL(u, location.href); return x.origin + x.pathname.replace(/\/?$/, "/"); }
    catch (e) { return u; }
  }

  /* "<anything>/retreats.html?x#y" -> WP URL + ?x#y, or null when not a static page link. */
  function wp(href) {
    if (!href || /^(#|mailto:|tel:|javascript:)/i.test(href)) return null;
    var m = href.match(/^([^?#]*?)([^\/?#]+\.html)([?#].*)?$/);
    if (!m) return null;
    var base = m[1], file = m[2], rest = m[3] || "";
    if (!(file in MAP)) return null;
    if (/^[a-z]+:\/\//i.test(base)) {
      var u;
      try { u = new URL(href); } catch (e) { return null; }
      if (u.origin !== location.origin) return null;
    }
    return MAP[file] + rest;
  }

  function fix(root) {
    var as = root.querySelectorAll ? root.querySelectorAll("a[href]") : [];
    if (root.tagName === "A" && root.hasAttribute("href")) as = [root].concat([].slice.call(as));
    Array.prototype.forEach.call(as, function (a) {
      var to = wp(a.getAttribute("href"));
      if (to) a.setAttribute("href", to);
    });
  }

  /* Before js/nav.js: the top links carry their static names for it to key on. */
  var tops = [];
  var list = document.querySelector(".site-header__nav .nav-list");
  if (list) {
    Array.prototype.forEach.call(list.children, function (li) {
      var a = li.querySelector(":scope > a");
      if (!a) return;
      var stat = REV[norm(a.getAttribute("href"))];
      if (!stat) return;
      tops.push({ a: a, href: a.getAttribute("href"), cur: a.hasAttribute("data-current") });
      a.setAttribute("href", stat);
    });
  }

  function after() {
    tops.forEach(function (t) {
      t.a.setAttribute("href", t.href);
      if (t.cur) {
        t.a.classList.add("is-current");
        var li = t.a.closest("li");
        if (li) li.classList.add("is-current");
      }
    });
    fix(document);
    new MutationObserver(function (muts) {
      muts.forEach(function (m) {
        Array.prototype.forEach.call(m.addedNodes, function (n) { if (n.nodeType === 1) fix(n); });
        if (m.type === "attributes" && m.target.tagName === "A") {
          var to = wp(m.target.getAttribute("href"));
          if (to) m.target.setAttribute("href", to);
        }
      });
      markSheet();
    }).observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ["href"] });
  }

  /* The mobile sheet nav.js builds marks the current item from its own path check. */
  function markSheet() {
    var cur = tops.filter(function (t) { return t.cur; })[0];
    if (!cur) return;
    var target = norm(cur.href);
    document.querySelectorAll(".msheet__item:not(.is-current) > .msheet__row > .msheet__link").forEach(function (a) {
      if (norm(a.getAttribute("href")) === target) {
        a.closest(".msheet__item").classList.add("is-current");
        a.setAttribute("aria-current", "true");
      }
    });
  }

  /* Deferred scripts run while readyState is already "interactive" but
     before DOMContentLoaded, so wait for the event unless the page is
     fully loaded (the load fallback covers a late, non-deferred load). */
  var done = false;
  function once() { if (!done) { done = true; after(); } }
  if (document.readyState === "complete") once();
  else {
    document.addEventListener("DOMContentLoaded", once);
    window.addEventListener("load", once);
  }

  window.YAWW_WP = { url: wp, fix: fix, assets: ASSETS };
})();
