/* Bullet leading: before / now switch on a copy of the El Cuyo retreat page.
   1. The page is a copy of the real one, two folders down: static paths were
      rebased when copying; this rebases the ones scripts build at runtime.
   2. A fixed switch (top right, or press T) shows the bullets as they were
      (16/24, dot at 9.5px) or as they are now on the site (16/26.4, dot at
      10.7px). "Now" is just the real css/type.css; "Before" adds html.bl-before. */
(function () {
  var PREFIX = "../../";
  var SKIP = /^(#|\/|\.\.\/|[a-z][a-z0-9+.-]*:|\?|$)/i;
  var ATTRS = ["src", "href", "poster", "srcset"];
  function rebase(v) { return SKIP.test(v) ? v : PREFIX + v; }
  function fixEl(el) {
    if (el.nodeType !== 1 || (el.closest && el.closest(".tx-toggle"))) return;
    for (var i = 0; i < ATTRS.length; i++) {
      var a = ATTRS[i], v = el.getAttribute(a);
      if (v == null) continue;
      var nv = a === "srcset" ? v.split(",").map(function (s) { return rebase(s.trim()); }).join(", ") : rebase(v.trim());
      if (nv !== v) el.setAttribute(a, nv);
    }
    var st = el.getAttribute("style");
    if (st && st.indexOf("url(") > -1) {
      var ns = st.replace(/url\((['"]?)([^'")]+)\1\)/g, function (m, q, u) { return "url(" + q + rebase(u) + q + ")"; });
      if (ns !== st) el.setAttribute("style", ns);
    }
  }
  function fixTree(n) { if (n.nodeType !== 1) return; fixEl(n); n.querySelectorAll("[src],[href],[poster],[srcset],[style]").forEach(fixEl); }
  new MutationObserver(function (list) {
    list.forEach(function (m) { if (m.type === "attributes") fixEl(m.target); else m.addedNodes.forEach(fixTree); });
  }).observe(document.documentElement, { subtree: true, childList: true, attributes: true, attributeFilter: ATTRS.concat("style") });

  var KEY = "yaww-bullet-leading";
  function read() { try { return localStorage.getItem(KEY) !== "before"; } catch (e) { return true; } }
  function apply(now, btn) {
    document.documentElement.classList.toggle("bl-before", !now);
    if (btn) { btn.setAttribute("aria-pressed", String(now)); btn.querySelector(".tx-toggle__state").textContent = now ? "Now · 26.4" : "Before · 24"; }
  }
  apply(read());
  document.addEventListener("DOMContentLoaded", function () {
    var css = document.createElement("style");
    css.textContent =
      "html.bl-before .retreat-itinerary li,html.bl-before .retreat-includes li{line-height:24px}" +
      "html.bl-before .retreat-includes__list li::before,html.bl-before .retreat-itinerary__bullets li::before{top:9.5px}" +
      ".tx-toggle{position:fixed;top:150px;right:16px;z-index:2147483000;display:flex;align-items:center;gap:10px;" +
      "padding:8px 12px 8px 14px;background:#283139;color:#F7F2F0;border:0;border-radius:0;cursor:pointer;" +
      "font:600 12px/16px Raleway,system-ui,sans-serif;letter-spacing:.08em;text-transform:uppercase;box-shadow:0 4px 16px rgba(0,0,0,.18)}" +
      ".tx-toggle__label{opacity:.65}" +
      ".tx-toggle__sw{position:relative;width:28px;height:16px;border:1px solid currentColor;flex:none}" +
      ".tx-toggle__sw::after{content:'';position:absolute;top:2px;left:2px;width:10px;height:10px;background:currentColor;transition:transform .2s cubic-bezier(.23,1,.32,1)}" +
      ".tx-toggle[aria-pressed=true] .tx-toggle__sw::after{transform:translateX(12px)}" +
      ".tx-toggle:focus-visible{outline:2px solid #F7F2F0;outline-offset:2px}" +
      "@media (max-width:767px){.tx-toggle{top:auto;bottom:96px;right:12px}}" +
      "@media (prefers-reduced-motion:reduce){.tx-toggle__sw::after{transition:none}}";
    document.head.appendChild(css);
    var btn = document.createElement("button");
    btn.type = "button"; btn.className = "tx-toggle";
    btn.title = "Bullets: before / now (T)";
    btn.innerHTML = '<span class="tx-toggle__label">Bullets</span><span class="tx-toggle__state"></span><span class="tx-toggle__sw" aria-hidden="true"></span>';
    document.body.appendChild(btn);
    var now = read(); apply(now, btn);
    function flip() { now = !now; apply(now, btn); try { localStorage.setItem(KEY, now ? "now" : "before"); } catch (e) {} }
    btn.addEventListener("click", flip);
    document.addEventListener("keydown", function (e) {
      if ((e.key === "t" || e.key === "T") && !e.metaKey && !e.ctrlKey && !e.altKey &&
          !/^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && !e.target.isContentEditable) flip();
    });
    var it = document.querySelector(".retreat-includes");
    if (it && !location.hash) setTimeout(function () { it.scrollIntoView({ block: "start" }); }, 300);
  });
})();
