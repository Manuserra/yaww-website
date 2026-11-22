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
  var STATES = ["now", "try"];  /* 26.4 is decided; only the gap is on trial */
  var LABEL = { before: "Before · 24 · gap 8", now: "Now · 26.4 · gap 8", "try": "Try · 26.4 · gap 6" };
  function read() { try { var v = localStorage.getItem(KEY); return STATES.indexOf(v) > -1 ? v : "try"; } catch (e) { return "try"; } }
  function apply(st, btn) {
    var h = document.documentElement;
    
    h.classList.toggle("bl-try", st === "try");
    if (btn) { btn.setAttribute("aria-pressed", String(st === "try")); btn.querySelector(".tx-toggle__state").textContent = LABEL[st]; }
  }
  apply(read());
  document.addEventListener("DOMContentLoaded", function () {
    var css = document.createElement("style");
    css.textContent =
      "html.bl-before .retreat-itinerary li,html.bl-before .retreat-includes li{line-height:24px}" +
      "html.bl-before .retreat-includes__list li::before,html.bl-before .retreat-itinerary__bullets li::before{top:9.5px}" +
      "html.bl-try .retreat-includes__list,html.bl-try .retreat-itinerary__bullets{gap:6px}" +
      "@media (min-width:947px){html.ph-grow .retreat-itinerary__slider{align-self:stretch}}" +
      ".tx-toggle.tx-toggle--photo{top:196px}" +
      "@media (min-width:768px){html.rm-552 .room-card__body{grid-column:6 / span 5}html.rm-552 .room-card__side{grid-column:11 / span 2}}" +
      ".tx-toggle.tx-toggle--rooms{top:242px}" +
      "@media (max-width:767px){.tx-toggle.tx-toggle--rooms{bottom:184px;top:auto}}" +
      "@media (max-width:767px){.tx-toggle.tx-toggle--photo{bottom:140px;top:auto}}" +
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
    btn.title = "Bullets: gap 8 / gap 6 (T)";
    btn.innerHTML = '<span class="tx-toggle__label">Bullets</span><span class="tx-toggle__state"></span><span class="tx-toggle__sw" aria-hidden="true"></span>';
    document.body.appendChild(btn);
    var st = read(); apply(st, btn);
    function flip() { st = STATES[(STATES.indexOf(st) + 1) % STATES.length]; apply(st, btn); try { localStorage.setItem(KEY, st); } catch (e) {} }
    btn.addEventListener("click", flip);

    /* Second switch: on laptops and tablets a long day's bullets can run
       taller than its photo; "grows" stretches the photo to the row (it
       crops a little at the sides instead of leaving a gap below). */
    var PKEY = "yaww-photo-grow";
    var b2 = document.createElement("button");
    b2.type = "button"; b2.className = "tx-toggle tx-toggle--photo";
    b2.title = "Itinerary photo: as now / grows with the text (P)";
    b2.innerHTML = '<span class="tx-toggle__label">Photo</span><span class="tx-toggle__state"></span><span class="tx-toggle__sw" aria-hidden="true"></span>';
    document.body.appendChild(b2);
    var grow; try { grow = localStorage.getItem(PKEY) !== "now"; } catch (e) { grow = true; }
    function applyP() {
      document.documentElement.classList.toggle("ph-grow", grow);
      b2.setAttribute("aria-pressed", String(grow));
      b2.querySelector(".tx-toggle__state").textContent = grow ? "Grows with text" : "As now";
    }
    function flipP() { grow = !grow; applyP(); try { localStorage.setItem(PKEY, grow ? "grow" : "now"); } catch (e) {} }
    applyP();
    b2.addEventListener("click", flipP);

    /* Third switch: the room rows' columns. Now photo 5 · text 4 · price 3;
       try photo 5 · text 5 · price 2 (the price only uses ~118px). */
    var RKEY = "yaww-rooms-cols";
    var b3 = document.createElement("button");
    b3.type = "button"; b3.className = "tx-toggle tx-toggle--rooms";
    b3.title = "Room rows: text 4 · price 3 / text 5 · price 2 (R)";
    b3.innerHTML = '<span class="tx-toggle__label">Rooms</span><span class="tx-toggle__state"></span><span class="tx-toggle__sw" aria-hidden="true"></span>';
    document.body.appendChild(b3);
    var wide; try { wide = localStorage.getItem(RKEY) !== "now"; } catch (e) { wide = true; }
    function applyR() {
      document.documentElement.classList.toggle("rm-552", wide);
      b3.setAttribute("aria-pressed", String(wide));
      b3.querySelector(".tx-toggle__state").textContent = wide ? "Try · text 5 · price 2" : "Now · text 4 · price 3";
    }
    function flipR() { wide = !wide; applyR(); try { localStorage.setItem(RKEY, wide ? "try" : "now"); } catch (e) {} }
    applyR();
    b3.addEventListener("click", flipR);
    document.addEventListener("keydown", function (e) {
      if ((e.key === "r" || e.key === "R") && !e.metaKey && !e.ctrlKey && !e.altKey &&
          !/^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && !e.target.isContentEditable) flipR();
    });
    document.addEventListener("keydown", function (e) {
      if ((e.key === "p" || e.key === "P") && !e.metaKey && !e.ctrlKey && !e.altKey &&
          !/^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && !e.target.isContentEditable) flipP();
    });
    document.addEventListener("keydown", function (e) {
      if ((e.key === "t" || e.key === "T") && !e.metaKey && !e.ctrlKey && !e.altKey &&
          !/^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName) && !e.target.isContentEditable) flip();
    });
    var it = document.querySelector(/camino/.test(location.pathname) ? ".retreat-itinerary__day:nth-child(8)" : "#rooms, .rooms");
    if (it && !location.hash) setTimeout(function () { it.scrollIntoView({ block: "start" }); }, 300);
  });
})();
