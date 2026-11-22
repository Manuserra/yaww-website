/* ============================================================
   YAWW · attribution.js · where a visit came from, for the forms

   Loaded on every page (retreats/_tools/chrome.py, LAYERS_JS).

   On the first page of a browser session it remembers:
     utm_source, utm_medium, utm_campaign, utm_term, utm_content
                  from the address, if the link carried them
     referrer     the site that sent the visitor (origin + path, no
                  query string), only when it is another site
     landing_page the path of that first page (no query string)
   in sessionStorage ("yaww_attr"). A later page in the same session
   with its own utm_* in the address starts a new record (a second
   campaign link counts); other pages leave it alone.

   The very first visit on this browser is also kept in localStorage
   ("yaww_attr_first", with the date), because a retreat is a slow
   decision: the person who books in March often first arrived from an
   Instagram post in January. It is sent as first_utm_source, …,
   first_referrer, first_landing_page, first_seen.

   On submit, every <form data-attribution> gets these as hidden
   inputs (created or refreshed in the capture phase, so they exist
   before the form's own submit handler reads the fields). Both forms
   use it: contact.html (js/contact.js) and the retreat pop-up
   (js/lead-modal.js). Anything else can read YAWW_ATTRIBUTION.get().

   Nothing here goes into a URL, and storage that is blocked (private
   mode, cookies off) simply means the fields are sent empty.
   Docs: docs/lead-form.md, "Attribution".
   ============================================================ */
(function (window, document) {
  "use strict";

  var KEYS = ["utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content"];
  var FIELDS = KEYS.concat(["referrer", "landing_page"]);
  var SESSION = "yaww_attr", FIRST = "yaww_attr_first", MAX = 200;

  function read(store, key) {
    try { var v = window[store].getItem(key); return v ? JSON.parse(v) : null; }
    catch (e) { return null; }
  }
  function write(store, key, val) {
    try { window[store].setItem(key, JSON.stringify(val)); } catch (e) { /* blocked: send empty */ }
  }
  function clip(s) { return String(s || "").slice(0, MAX); }

  function externalReferrer() {
    var r = document.referrer;
    if (!r) return "";
    try {
      var u = new URL(r);
      if (u.hostname === location.hostname) return "";
      return clip(u.origin + u.pathname);
    } catch (e) { return ""; }
  }

  function current() {
    var q = {};
    try { new URLSearchParams(location.search).forEach(function (v, k) { q[k] = v; }); } catch (e) {}
    var rec = { landing_page: clip(location.pathname), referrer: externalReferrer() };
    var tagged = false;
    KEYS.forEach(function (k) { rec[k] = clip(q[k]); if (q[k]) tagged = true; });
    return { rec: rec, tagged: tagged };
  }

  var now = current();
  var session = read("sessionStorage", SESSION);
  if (!session || now.tagged) {
    session = now.rec;
    write("sessionStorage", SESSION, session);
  }
  var first = read("localStorage", FIRST);
  if (!first) {
    first = {};
    FIELDS.forEach(function (k) { first[k] = now.rec[k]; });
    first.first_seen = new Date().toISOString().slice(0, 10);
    write("localStorage", FIRST, first);
  }

  function get() {
    var out = {};
    FIELDS.forEach(function (k) { out[k] = (session && session[k]) || ""; });
    FIELDS.forEach(function (k) { out["first_" + k] = (first && first[k]) || ""; });
    out.first_seen = (first && first.first_seen) || "";
    return out;
  }

  function fill(form) {
    var data = get();
    Object.keys(data).forEach(function (name) {
      var el = form.querySelector('input[type="hidden"][name="' + name + '"]');
      if (!el) {
        el = document.createElement("input");
        el.type = "hidden";
        el.name = name;
        form.appendChild(el);
      }
      el.value = data[name];
    });
  }

  document.addEventListener("submit", function (e) {
    var f = e.target;
    if (f && f.matches && f.matches("form[data-attribution]")) fill(f);
  }, true);

  window.YAWW_ATTRIBUTION = { get: get, fill: fill };
})(window, document);
