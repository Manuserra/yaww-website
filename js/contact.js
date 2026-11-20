/* ============================================================
   YAWW · FORMS (contact.html, teach-with-us.html)

   Every <form data-yform="key"> on the page gets:
   - inline errors under each field, shown on submit and then kept
     live as the person fixes them (never while they are still typing
     a first time);
   - conditional rows: [data-show-when="field=value"] appear only when
     that radio/select has that value, and are skipped by validation
     while hidden;
   - a success state ([data-yform-done]) that names the person back;
   - one place to post: YAWW_CONTACT.endpoints[key] (data/contact.js).

   Markup contract for a field:
     <div class="yf__field" data-field="email">
       <label for="x">…</label> <input id="x" name="email" required>
       <p class="yf__error" id="x-error" hidden></p>
     </div>
   Radio/checkbox groups use <fieldset class="yf__field" data-field="name"
   data-required> with the error <p> inside the fieldset.
   ============================================================ */
(function (window, document) {
  "use strict";

  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  function cfg() { return window.YAWW_CONTACT || { endpoints: {} }; }

  function track(detail) {
    document.dispatchEvent(new CustomEvent("yaww:track", { detail: detail }));
  }

  /* --- one field ------------------------------------------------------ */
  function controls(field) {
    return field.querySelectorAll("input, select, textarea");
  }

  function isHidden(field) {
    var row = field.closest("[data-show-when]");
    return !!(row && row.hidden);
  }

  function message(field) {
    var first = controls(field)[0];
    var custom = field.getAttribute("data-error") || (first && first.getAttribute("data-error"));
    if (field.tagName === "FIELDSET") {
      var picked = field.querySelector("input:checked");
      if (field.hasAttribute("data-required") && !picked) return custom || "Please choose one.";
      return "";
    }
    if (!first) return "";
    var v = first.value.trim();
    if (first.required && !v) return custom || "Please fill this in.";
    if (v && first.type === "email" && !EMAIL.test(v)) return "That email doesn't look right. Check for a typo?";
    return "";
  }

  function paint(field, msg) {
    var err = field.querySelector(".yf__error");
    field.classList.toggle("is-invalid", !!msg);
    Array.prototype.forEach.call(controls(field), function (c) {
      if (msg) c.setAttribute("aria-invalid", "true");
      else c.removeAttribute("aria-invalid");
    });
    if (field.tagName === "FIELDSET") {
      if (msg) field.setAttribute("aria-invalid", "true"); else field.removeAttribute("aria-invalid");
    }
    if (err) { err.textContent = msg; err.hidden = !msg; }
  }

  function check(field) {
    var msg = isHidden(field) ? "" : message(field);
    paint(field, msg);
    return !msg;
  }

  /* --- conditional rows ---------------------------------------------- */
  function syncConditions(form) {
    Array.prototype.forEach.call(form.querySelectorAll("[data-show-when]"), function (row) {
      var rule = row.getAttribute("data-show-when").split("=");
      var name = rule[0], values = rule[1].split("|");
      var el = form.elements[name];
      var val = el ? el.value : "";   // works for a select and a RadioNodeList
      var show = values.indexOf(val) !== -1;
      row.hidden = !show;
      Array.prototype.forEach.call(row.querySelectorAll("input, select, textarea"), function (c) {
        c.disabled = !show;
      });
    });
  }

  /* --- payload -------------------------------------------------------- */
  function payload(form) {
    var out = { form: form.getAttribute("data-yform"), page: location.pathname.split("/").pop() || "index.html" };
    Array.prototype.forEach.call(form.elements, function (el) {
      if (!el.name || el.disabled || el.type === "submit") return;
      if ((el.type === "radio" || el.type === "checkbox") && !el.checked) return;
      var v = el.value.trim();
      if (el.type === "checkbox") {
        (out[el.name] = out[el.name] || []).push(v);
      } else {
        out[el.name] = v;
      }
    });
    return out;
  }

  function send(key, data) {
    var url = (cfg().endpoints || {})[key];
    if (!url) {
      // No backend yet: log so the payload can be checked, then succeed.
      if (window.console) console.info("[yform] no endpoint for '" + key + "', payload:", data);
      return Promise.resolve();
    }
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    }).then(function (r) { if (!r.ok) throw new Error(r.status); });
  }

  /* --- one form ------------------------------------------------------- */
  function initForm(form) {
    var key = form.getAttribute("data-yform");
    var wrap = form.closest("[data-yform-wrap]") || form.parentNode;
    var done = wrap.querySelector("[data-yform-done]");
    var failed = form.querySelector("[data-yform-failed]");
    var submit = form.querySelector('[type="submit"]');
    var fields = form.querySelectorAll(".yf__field");
    var tried = false;

    syncConditions(form);

    form.addEventListener("change", function (e) {
      syncConditions(form);
      var f = e.target.closest(".yf__field");
      if (tried && f) check(f);
    });
    // After the first submit attempt, errors clear as soon as the field
    // is fixed, so people see the form agree with them.
    form.addEventListener("input", function (e) {
      var f = e.target.closest(".yf__field");
      if (tried && f && f.classList.contains("is-invalid")) check(f);
    });
    form.addEventListener("focusout", function (e) {
      var f = e.target.closest(".yf__field");
      if (tried && f && !f.contains(e.relatedTarget)) check(f);
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      tried = true;
      var firstBad = null;
      Array.prototype.forEach.call(fields, function (f) {
        if (!check(f) && !firstBad) firstBad = f;
      });
      if (firstBad) {
        var c = firstBad.querySelector("input:not([disabled]), select, textarea");
        if (c) c.focus();
        track({ event: "form_invalid", form: key });
        return;
      }

      var data = payload(form);
      if (failed) failed.hidden = true;
      submit.disabled = true;
      submit.classList.add("is-sending");
      var label = submit.textContent;
      submit.textContent = "Sending…";

      send(key, data).then(function () {
        track({ event: "form_submit", form: key, topic: data.topic || null });
        if (done) {
          Array.prototype.forEach.call(done.querySelectorAll("[data-yform-name]"), function (n) {
            n.textContent = data.first_name || data.name || "";
          });
          Array.prototype.forEach.call(done.querySelectorAll("[data-yform-email]"), function (n) {
            n.textContent = data.email || "";
          });
          form.hidden = true;
          done.hidden = false;
          done.focus();
        }
      }).catch(function () {
        if (failed) failed.hidden = false;
      }).then(function () {
        submit.disabled = false;
        submit.classList.remove("is-sending");
        submit.textContent = label;
      });
    });
  }

  /* --- contact.html: old anchors and topic links ---------------------- */
  // The old page had three tabs at #general-inquiry, #teach-with-us and
  // #build-a-custom-retreat, and other pages' menus still point there.
  // Teachers go to their own page; the other two pick the form's topic.
  function routeHash() {
    var h = location.hash.replace("#", "");
    if (!h) return;
    if (h === "teach-with-us") { location.replace("teach-with-us.html"); return; }
    var topic = { "build-a-custom-retreat": "private-group", "private-groups": "private-group",
                  "general-inquiry": "retreat", "ask": "retreat" }[h];
    if (!topic) return;
    pickTopic(topic);
  }

  function pickTopic(topic) {
    var form = document.querySelector('[data-yform="contact"]');
    if (!form) return;
    var radio = form.querySelector('input[name="topic"][value="' + topic + '"]');
    if (!radio) return;
    radio.checked = true;
    syncConditions(form);
  }

  function init() {
    Array.prototype.forEach.call(document.querySelectorAll("form[data-yform]"), initForm);

    // A published email, if set in data/contact.js, replaces the placeholder.
    var email = cfg().email;
    if (email) {
      Array.prototype.forEach.call(document.querySelectorAll("[data-contact-email]"), function (a) {
        a.textContent = email;
        a.href = "mailto:" + email;
        a.classList.remove("is-placeholder");
      });
    }

    // Links like <a href="#form" data-pick-topic="private-group">.
    document.addEventListener("click", function (e) {
      var a = e.target.closest("[data-pick-topic]");
      if (a) pickTopic(a.getAttribute("data-pick-topic"));
    });

    routeHash();
    window.addEventListener("hashchange", routeHash);
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();
})(window, document);
