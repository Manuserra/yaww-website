/* ============================================================
   YAWW — CONTACT PAGE
   Renders the tab bar and the three forms from data/contact.js,
   switches tabs, and validates on submit.

   No backend yet — same shape as the location-page waitlist:
   validate, show the success state, leave one place to wire the
   endpoint.
   ============================================================ */
(function (window, document) {
  "use strict";

  var YAWW = window.YAWW || (window.YAWW = {});
  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;")
      .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  // TODO(backend): point this at the Zapier catch hook or the WP REST
  // route and post the payload built in submit() below.
  YAWW.CONTACT_ENDPOINT = null;

  function forms() { return window.YAWW_CONTACT_FORMS || []; }

  /* Labels are visible and tied to their input by id. The source hides
     them and leaves the placeholder as the only name a field has,
     which disappears the moment someone types. */
  function fieldHTML(formId, f) {
    var id = formId + "--" + f.name;
    var common =
      ' id="' + esc(id) + '" name="' + esc(f.name) + '"' +
      (f.required ? ' required aria-required="true"' : "") +
      (f.autocomplete ? ' autocomplete="' + esc(f.autocomplete) + '"' : "");
    var control = f.type === "textarea"
      ? '<textarea class="cf__input cf__input--area" rows="6"' + common + '></textarea>'
      : '<input class="cf__input" type="text"' + common + '>';
    return '<div class="cf__field">' +
             '<label class="cf__label" for="' + esc(id) + '">' + esc(f.label) +
               (f.required ? ' <span class="cf__req" aria-hidden="true">*</span>' : "") +
             '</label>' +
             control +
           '</div>';
  }

  function formHTML(def) {
    return '<div class="cf__panel" id="' + esc(def.id) + '" data-cf-panel="' + esc(def.id) + '" role="tabpanel" hidden>' +
             '<p class="cf__title">' + esc(def.title) + '</p>' +
             '<form class="cf__form" data-cf-form="' + esc(def.id) + '" novalidate>' +
               def.fields.map(function (f) { return fieldHTML(def.id, f); }).join("") +
               '<p class="cf__error" data-cf-error role="alert" hidden></p>' +
               '<button class="btn btn--primary cf__submit" type="submit">Submit</button>' +
             '</form>' +
             '<div class="cf__done" data-cf-done hidden tabindex="-1">' +
               '<p class="cf__title">Thank you</p>' +
               '<p class="cf__done-body">We have your message and will write back shortly.</p>' +
             '</div>' +
           '</div>';
  }

  function tabsHTML(defs, active) {
    return defs.map(function (d) {
      var on = d.id === active;
      return '<a class="cf__tab' + (on ? ' is-active' : '') + '" href="#' + esc(d.id) + '"' +
               ' role="tab" aria-selected="' + on + '" data-cf-tab="' + esc(d.id) + '">' +
               esc(d.label) +
             '</a>';
    }).join("");
  }

  function show(root, id) {
    root.querySelectorAll("[data-cf-panel]").forEach(function (p) {
      p.hidden = p.getAttribute("data-cf-panel") !== id;
    });
    root.querySelectorAll("[data-cf-tab]").forEach(function (t) {
      var on = t.getAttribute("data-cf-tab") === id;
      t.classList.toggle("is-active", on);
      t.setAttribute("aria-selected", on);
    });
  }

  function submit(def, form) {
    var errorEl = form.querySelector("[data-cf-error]");
    var done = form.parentNode.querySelector("[data-cf-done]");
    var bad = null, msg = "";

    for (var i = 0; i < def.fields.length; i++) {
      var f = def.fields[i];
      if (!f.required) continue;
      var el = form.elements[f.name];
      if (!el || !el.value.trim()) { bad = el; msg = "Please fill in " + f.label.toLowerCase() + "."; break; }
      // One combined "Email & phone" field, so an address is only
      // checked when someone actually typed one.
      if (f.name === "contact-info" && el.value.indexOf("@") > -1 &&
          !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value.trim().split(/[\s,;]+/).filter(function (p) { return p.indexOf("@") > -1; })[0] || "")) {
        bad = el; msg = "That email address does not look right."; break;
      }
    }
    if (bad) {
      if (errorEl) { errorEl.textContent = msg; errorEl.hidden = false; }
      bad.focus();
      return;
    }
    if (errorEl) { errorEl.textContent = ""; errorEl.hidden = true; }

    var payload = { form: def.id };
    def.fields.forEach(function (f) {
      var el = form.elements[f.name];
      if (el) payload[f.name] = el.value.trim();
    });
    document.dispatchEvent(new CustomEvent("yaww:track", {
      detail: { event: "contact_submit", form: def.id }
    }));
    // TODO(backend): POST `payload` to YAWW.CONTACT_ENDPOINT and only
    // then reveal the success state.
    if (done) {
      form.hidden = true;
      done.hidden = false;
      done.focus();
    }
  }

  function init() {
    var root = document.querySelector("[data-contact]");
    if (!root) return;
    var defs = forms();
    if (!defs.length) return;

    var hash = (window.location.hash || "").replace("#", "");
    var active = defs.some(function (d) { return d.id === hash; }) ? hash : defs[0].id;

    root.querySelector("[data-cf-tabs]").innerHTML = tabsHTML(defs, active);
    root.querySelector("[data-cf-panels]").innerHTML = defs.map(formHTML).join("");
    show(root, active);

    root.querySelectorAll("[data-cf-tab]").forEach(function (t) {
      t.addEventListener("click", function (e) {
        e.preventDefault();
        var id = t.getAttribute("data-cf-tab");
        show(root, id);
        // Keep the anchor in the address bar without jumping the page,
        // so these tabs stay linkable from the nav.
        if (window.history.replaceState) window.history.replaceState(null, "", "#" + id);
      });
    });
    // Arriving from the nav on a page that is already open.
    window.addEventListener("hashchange", function () {
      var h = (window.location.hash || "").replace("#", "");
      if (defs.some(function (d) { return d.id === h; })) show(root, h);
    });

    defs.forEach(function (def) {
      var form = root.querySelector('[data-cf-form="' + def.id + '"]');
      if (form) form.addEventListener("submit", function (e) { e.preventDefault(); submit(def, form); });
    });
  }

  YAWW.initContact = init;
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

})(window, document);
