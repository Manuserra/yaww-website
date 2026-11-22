/* Retreats page past 1440: capped / fluid switch on a copy of retreats.html.
   Capped = today (container stops at 1440, cards stay 312). Fluid = above 1440 the container
   grows to 1920 and the same 12-col grid scales: season label = 3 cols, cards = 3 x 3 cols.
   The page is a copy of the real one, two folders down: static paths were
   rebased when copying; this rebases the ones scripts build at runtime
   (card images, links) and adds a fixed switch top right (or press W). */
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

  var KEY = "yaww-retreats-wide";
  var STATES = ["capped", "fluid"];
  var LABEL = {"capped": "Capped 1440", "fluid": "Fluid"};
  var CLASS = {"capped": "", "fluid": "rw-fluid"};
  var CSS = "@media (min-width:1441px){html.rw-fluid .container.retreats{max-width:1920px}html.rw-fluid .season{grid-template-columns:calc((100% - 144px) / 4) 1fr}}";
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
    btn.title = "Width: capped 1440 / fluid (W)";
    btn.innerHTML = '<span class="tx-toggle__label">Width</span><span class="tx-toggle__state"></span><span class="tx-toggle__sw" aria-hidden="true"></span>';
    document.body.appendChild(btn);
    apply();
    function flip() { st = STATES[(STATES.indexOf(st) + 1) % STATES.length]; apply(); try { localStorage.setItem(KEY, st); } catch (e) {} }
    btn.addEventListener("click", flip);
    document.addEventListener("keydown", function (e) {
      if (e.key.toLowerCase() === "w" && !e.metaKey && !e.ctrlKey && !e.altKey &&
          !/^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && !e.target.isContentEditable) flip();
    });
  });
})();
