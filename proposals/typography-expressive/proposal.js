/* Typography proposal: helpers for the copied pages in this folder.
   1. The pages are copies of the official ones, two folders down. Static
      paths were rebased when copying; this rebases the ones the page
      scripts build at runtime (cards from data/*.js, galleries, links).
   2. A fixed toggle (top right) turns type.css on and off. Also: press T,
      or open the page with ?type=off. */
(function () {
  var PREFIX = "../../";
  var SKIP = /^(#|\/|\.\.\/|[a-z][a-z0-9+.-]*:|\?|$)/i;
  var ATTRS = ["src", "href", "poster", "srcset"];

  function rebase(v) { return SKIP.test(v) ? v : PREFIX + v; }
  function fixEl(el) {
    if (el.nodeType !== 1) return;
    if (el.id === "type-expressive" || el.closest && el.closest(".tx-toggle")) return;
    for (var i = 0; i < ATTRS.length; i++) {
      var a = ATTRS[i], v = el.getAttribute(a);
      if (v == null) continue;
      var nv = a === "srcset"
        ? v.split(",").map(function (s) { return rebase(s.trim()); }).join(", ")
        : rebase(v.trim());
      if (nv !== v) el.setAttribute(a, nv);
    }
    var st = el.getAttribute("style");
    if (st && st.indexOf("url(") > -1) {
      var ns = st.replace(/url\((['"]?)([^'")]+)\1\)/g, function (m, q, u) { return "url(" + q + rebase(u) + q + ")"; });
      if (ns !== st) el.setAttribute("style", ns);
    }
  }
  function fixTree(n) {
    if (n.nodeType !== 1) return;
    fixEl(n);
    var all = n.querySelectorAll("[src],[href],[poster],[srcset],[style]");
    for (var i = 0; i < all.length; i++) fixEl(all[i]);
  }
  new MutationObserver(function (list) {
    list.forEach(function (m) {
      if (m.type === "attributes") fixEl(m.target);
      else m.addedNodes.forEach(fixTree);
    });
  }).observe(document.documentElement, { subtree: true, childList: true, attributes: true, attributeFilter: ATTRS.concat("style") });

  /* ---- On/off toggle ---- */
  var KEY = "yaww-type-expressive";
  function read() {
    if (/[?&]type=off\b/.test(location.search)) return false;
    if (/[?&]type=on\b/.test(location.search)) return true;
    try { return localStorage.getItem(KEY) !== "off"; } catch (e) { return true; }
  }
  function apply(on, btn) {
    var link = document.getElementById("type-expressive");
    if (link) link.disabled = !on;
    document.documentElement.classList.toggle("tx-on", on);
    if (btn) {
      btn.setAttribute("aria-pressed", String(on));
      btn.querySelector(".tx-toggle__state").textContent = on ? "Proposal" : "Current site";
    }
  }
  document.addEventListener("DOMContentLoaded", function () {
    var css = document.createElement("style");
    css.textContent =
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
    btn.type = "button";
    btn.className = "tx-toggle";
    btn.title = "Switch the typography proposal on or off (T)";
    btn.innerHTML = '<span class="tx-toggle__label">Type</span><span class="tx-toggle__state"></span><span class="tx-toggle__sw" aria-hidden="true"></span>';
    document.body.appendChild(btn);
    var on = read();
    apply(on, btn);
    function flip() {
      on = !on; apply(on, btn);
      try { localStorage.setItem(KEY, on ? "on" : "off"); } catch (e) {}
    }
    btn.addEventListener("click", flip);
    document.addEventListener("keydown", function (e) {
      if ((e.key === "t" || e.key === "T") && !e.metaKey && !e.ctrlKey && !e.altKey &&
          !/^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && !e.target.isContentEditable) flip();
    });
  });
  /* Apply the stored state as early as possible to avoid a flash. */
  if (!read()) document.addEventListener("readystatechange", function once() {
    var l = document.getElementById("type-expressive"); if (l) l.disabled = true;
  });
})();
