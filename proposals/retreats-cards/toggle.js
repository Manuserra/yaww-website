/* Retreat cards: ours / premium-style switch on a copy of retreats.html.
   ON restyles the narrow cards: left-aligned; the date first, in premium's date style
   (Raleway 11/600, tracked .18em, caps, #877C71); name as now; location in our
   Libre Baskerville italic but premium's colour; teacher in our type, primary blue.
   The page is a copy of the real one, two folders down: static paths were
   rebased when copying; this rebases the ones scripts build at runtime
   (card images, links) and adds a fixed switch top right (or press C). */
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

  var KEY = "yaww-retreats-cards";
  var STATES = ["ours", "premium"];
  var LABEL = {"ours": "Ours", "premium": "Premium-style"};
  var CLASS = {"ours": "rc-ours", "premium": ""};  /* premium-style is live now (css/cards.css); "ours" switches it off */
  var CSS = "";
  function read() { try { var v = localStorage.getItem(KEY); return STATES.indexOf(v) > -1 ? v : STATES[1]; } catch (e) { return STATES[1]; } }
  var st = read(), btn;
  function apply() {
    var h = document.documentElement;
    STATES.forEach(function (s) { if (CLASS[s]) h.classList.remove(CLASS[s]); });
    if (CLASS[st]) h.classList.add(CLASS[st]);
    if (btn) { btn.setAttribute("aria-pressed", String(st !== STATES[0])); btn.querySelector(".tx-toggle__state").textContent = LABEL[st]; }
  }
  apply();
  document.addEventListener("DOMContentLoaded", function () {
    var css = document.createElement("style");
    css.textContent = CSS +
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
    btn = document.createElement("button");
    btn.type = "button"; btn.className = "tx-toggle";
    btn.title = "Cards: ours / premium-style (C)";
    btn.innerHTML = '<span class="tx-toggle__label">Cards</span><span class="tx-toggle__state"></span><span class="tx-toggle__sw" aria-hidden="true"></span>';
    document.body.appendChild(btn);
    apply();
    function flip() { st = STATES[(STATES.indexOf(st) + 1) % STATES.length]; apply(); try { localStorage.setItem(KEY, st); } catch (e) {} }
    btn.addEventListener("click", flip);
    document.addEventListener("keydown", function (e) {
      if (e.key.toLowerCase() === "c" && !e.metaKey && !e.ctrlKey && !e.altKey &&
          !/^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && !e.target.isContentEditable) flip();
    });
  });
})();
