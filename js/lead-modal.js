/* ============================================================
   YAWW — "I want more info": the lead modal

   One modal per page, built here on first use, opened by anything
   marked [data-lead-open]. The page keeps a real link underneath
   (contact.html#general-inquiry), so with scripting off the button
   still goes somewhere useful.

   Opener attributes:
     data-lead-source   where the click came from: hero, sticky-bar,
                        rooms, room-card. Sent with the lead.
     data-lead-room     a room the reader picked (from a room card).
     data-lead-intent   "reserve" when the reader pressed Reserve and
                        online booking is not wired yet: same form,
                        worded for booking.

   The retreat is read from the page: .retreat-hero[data-retreat-id],
   its title, the first fact under it (the dates) and its photograph.

   Submission: the form posts JSON to its data-endpoint. While that is
   empty (no CRM yet, WeTravel later) the send is simulated and the
   success state shown, so the whole flow can be reviewed. Field names
   and why each exists: docs/lead-form.md.

   Accessibility: a native <dialog> opened with showModal(), so the rest
   of the page is inert and Esc closes it; on top of that, Tab is kept
   inside, the page underneath does not scroll, focus returns to the
   button that opened it, and every error is written under its field
   and tied to it with aria-describedby.
   ============================================================ */
(function (window, document) {
  "use strict";

  var ENDPOINT = "";   // TODO(backend): e.g. a WeTravel / CRM webhook. Empty = simulate.
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)");

  var dlg, form, opener, ctx = {};

  function esc(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  function pageContext() {
    var hero = document.querySelector(".retreat-hero");
    var title = hero && hero.querySelector(".retreat-hero__title");
    var meta = hero && hero.querySelector(".retreat-hero__meta span");
    var img = hero && hero.querySelector(".retreat-hero__image");
    var bg = img ? (img.style.backgroundImage || "").replace(/^url\(["']?|["']?\)$/g, "") : "";
    return {
      id: (hero && hero.getAttribute("data-retreat-id")) || location.pathname.replace(/^.*\/|\.html$/g, ""),
      name: title ? title.textContent.trim() : document.title.replace(/^YAWW · /, ""),
      dates: meta ? meta.textContent.trim() : "",
      photo: bg
    };
  }

  var ICON_CLOSE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>';

  function pills(name, type, options) {
    return options.map(function (o, i) {
      var id = "lead-" + name + "-" + i;
      return '<label class="lead-pill" for="' + id + '">' +
               '<input type="' + type + '" id="' + id + '" name="' + name + '" value="' + o[0] + '"' + (o[2] ? " checked" : "") + '>' +
               '<span>' + o[1] + '</span>' +
             '</label>';
    }).join("");
  }

  function build() {
    ctx = pageContext();
    dlg = document.createElement("dialog");
    dlg.className = "lead-modal";
    dlg.setAttribute("aria-labelledby", "lead-title");
    dlg.setAttribute("aria-describedby", "lead-intro");
    dlg.setAttribute("data-lenis-prevent", "");
    dlg.innerHTML =
      '<div class="lead-modal__sheet">' +
        '<aside class="lead-modal__context"' + (ctx.photo ? ' style="background-image: url(\'' + esc(ctx.photo) + '\')"' : "") + '>' +
          '<div class="lead-modal__context-text">' +
            '<p class="lead-modal__eyebrow">You are asking about</p>' +
            '<p class="lead-modal__retreat">' + esc(ctx.name) + '</p>' +
            (ctx.dates ? '<p class="lead-modal__dates">' + esc(ctx.dates) + '</p>' : "") +
            '<p class="lead-modal__room" data-lead-room-line hidden></p>' +
          '</div>' +
        '</aside>' +
        '<div class="lead-modal__main">' +
          '<button class="lead-modal__close" type="button" aria-label="Close">' + ICON_CLOSE + '</button>' +
          '<div class="lead-modal__body" data-lead-body>' +
            '<h2 class="lead-modal__title" id="lead-title" data-lead-title>Ask us about this retreat</h2>' +
            '<p class="lead-modal__intro" id="lead-intro" data-lead-intro>Leave your name and email, and a question if you have one. Someone from our team will write back to you personally.</p>' +
            '<form class="lead-form" novalidate data-endpoint="' + esc(ENDPOINT) + '">' +
              '<div class="lead-form__row">' +
                field("first_name", "First name", "text", "given-name", true) +
                field("email", "Email", "email", "email", true) +
              '</div>' +
              '<fieldset class="lead-form__group">' +
                '<legend class="lead-form__label">Who is coming? <span class="lead-form__opt">Optional</span></legend>' +
                '<div class="lead-pills">' + pills("travel_party", "radio", [["solo", "Just me"], ["two", "Two of us"], ["group", "A group"]]) + '</div>' +
              '</fieldset>' +
              '<fieldset class="lead-form__group">' +
                '<legend class="lead-form__label">What would you like to know? <span class="lead-form__opt">Optional</span></legend>' +
                '<div class="lead-pills">' + pills("topics", "checkbox", [["rooms", "Rooms and prices"], ["level", "Is it right for my level?"], ["travel", "Getting there"], ["other", "Something else"]]) + '</div>' +
              '</fieldset>' +
              '<div class="lead-form__field">' +
                '<label class="lead-form__label" for="lead-message">Your question <span class="lead-form__opt">Optional</span></label>' +
                '<textarea class="lead-form__input lead-form__input--area" id="lead-message" name="message" rows="3" maxlength="2000"></textarea>' +
              '</div>' +
              '<fieldset class="lead-form__group">' +
                '<legend class="lead-form__label">How should we reach you?</legend>' +
                '<div class="lead-pills">' + pills("contact_method", "radio", [["email", "Email", true], ["whatsapp", "WhatsApp"], ["phone", "Phone call"]]) + '</div>' +
              '</fieldset>' +
              '<div class="lead-form__reveal" data-lead-phone hidden>' +
                field("phone", "Phone number, with country code", "tel", "tel", false) +
              '</div>' +
              '<label class="lead-check" for="lead-optin">' +
                '<input type="checkbox" id="lead-optin" name="marketing_opt_in" value="yes">' +
                '<span>Also send me news of future retreats</span>' +
              '</label>' +
              '<input type="hidden" name="retreat_id" value="' + esc(ctx.id) + '">' +
              '<input type="hidden" name="retreat_name" value="' + esc(ctx.name) + '">' +
              '<input type="hidden" name="retreat_dates" value="' + esc(ctx.dates) + '">' +
              '<input type="hidden" name="room" value="">' +
              '<input type="hidden" name="intent" value="info">' +
              '<input type="hidden" name="source" value="">' +
              '<input type="hidden" name="page_url" value="">' +
              '<input type="hidden" name="utm" value="">' +
              '<div class="lead-form__foot">' +
                '<button class="btn btn--primary lead-form__submit" type="submit"><span data-lead-submit-label>Send my question</span></button>' +
                '<p class="lead-form__fine">We only use your details to answer you.</p>' +
              '</div>' +
              '<p class="lead-form__status" role="alert" data-lead-status hidden></p>' +
            '</form>' +
          '</div>' +
          '<div class="lead-modal__done" data-lead-done hidden tabindex="-1">' +
            '<svg class="lead-modal__tick" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22.5"/><path d="M15 24.5l6 6 12-13"/></svg>' +
            '<h2 class="lead-modal__title" data-lead-done-title>Thank you</h2>' +
            '<p class="lead-modal__intro" data-lead-done-text></p>' +
            '<button class="btn btn--secondary lead-modal__back" type="button">Back to the retreat</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(dlg);
    form = dlg.querySelector("form");
    wire();
  }

  function field(name, label, type, auto, required) {
    var id = "lead-" + name;
    return '<div class="lead-form__field">' +
             '<label class="lead-form__label" for="' + id + '">' + label + (required ? "" : "") + '</label>' +
             '<input class="lead-form__input" id="' + id + '" name="' + name + '" type="' + type + '" autocomplete="' + auto + '"' +
               (required ? " required aria-required=\"true\"" : "") + ' aria-describedby="' + id + '-err">' +
             '<p class="lead-form__error" id="' + id + '-err" data-error-for="' + name + '"></p>' +
           '</div>';
  }

  /* ---------------------------------------------------------- validation */
  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  var attempted = false;

  function check(name) {
    var el = form.elements[name];
    if (!el) return true;
    var v = (el.value || "").trim(), msg = "";
    if (name === "first_name" && !v) msg = "Please tell us your first name.";
    if (name === "email") {
      if (!v) msg = "We need an email to write back to you.";
      else if (!EMAIL.test(v)) msg = "That email looks incomplete. Could you check it?";
    }
    if (name === "phone" && form.elements.contact_method.value !== "email") {
      if (v.replace(/\D/g, "").length < 7) msg = "Please add a number we can reach, with the country code.";
    }
    var out = form.querySelector('[data-error-for="' + name + '"]');
    el.setAttribute("aria-invalid", msg ? "true" : "false");
    el.closest(".lead-form__field").classList.toggle("is-invalid", !!msg);
    if (out) out.textContent = msg;
    return !msg;
  }

  function validate() {
    var names = ["first_name", "email", "phone"], firstBad = null;
    names.forEach(function (n) { if (!check(n) && !firstBad) firstBad = form.elements[n]; });
    return firstBad;
  }

  /* ---------------------------------------------------------------- wire */
  function wire() {
    dlg.querySelector(".lead-modal__close").addEventListener("click", close);
    dlg.querySelector(".lead-modal__back").addEventListener("click", close);
    // A click on the backdrop (the dialog box itself, outside the sheet) closes.
    dlg.addEventListener("click", function (e) { if (e.target === dlg) close(); });
    // Esc: run our own close so it animates and gives focus back.
    dlg.addEventListener("cancel", function (e) { e.preventDefault(); close(); });
    dlg.addEventListener("keydown", trap);

    ["first_name", "email", "phone"].forEach(function (n) {
      var el = form.elements[n];
      el.addEventListener("blur", function () { if (attempted || el.value) check(n); });
      el.addEventListener("input", function () { if (el.getAttribute("aria-invalid") === "true") check(n); });
    });

    var phoneBox = form.querySelector("[data-lead-phone]");
    Array.prototype.forEach.call(form.elements.contact_method, function (r) {
      r.addEventListener("change", function () {
        var need = form.elements.contact_method.value !== "email";
        phoneBox.hidden = !need;
        form.elements.phone.required = need;
        if (need) form.elements.phone.focus();
      });
    });

    form.addEventListener("submit", submit);
  }

  function focusables() {
    return Array.prototype.filter.call(
      dlg.querySelectorAll('button, [href], input:not([type="hidden"]), textarea, select, [tabindex]:not([tabindex="-1"])'),
      function (el) { return !el.disabled && el.offsetParent !== null; });
  }

  function trap(e) {
    if (e.key !== "Tab") return;
    var f = focusables();
    if (!f.length) return;
    var first = f[0], last = f[f.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }

  /* ---------------------------------------------------------- open/close */
  var scrollY = 0;
  function lockScroll(on) {
    var html = document.documentElement;
    if (on) {
      var bar = window.innerWidth - html.clientWidth;
      html.style.setProperty("--lead-scrollbar", bar + "px");
      html.classList.add("has-lead-modal");
      if (window.yawwLenis) window.yawwLenis.stop();
    } else {
      html.classList.remove("has-lead-modal");
      html.style.removeProperty("--lead-scrollbar");
      if (window.yawwLenis) window.yawwLenis.start();
    }
  }

  function open(trigger) {
    if (!dlg) build();
    opener = trigger || document.activeElement;
    var room = trigger && trigger.getAttribute("data-lead-room");
    var intent = (trigger && trigger.getAttribute("data-lead-intent")) || "info";
    var source = (trigger && trigger.getAttribute("data-lead-source")) || "page";

    // A fresh form each time it is opened after a success.
    if (!dlg.querySelector("[data-lead-done]").hidden) reset();

    form.elements.room.value = room || "";
    form.elements.intent.value = intent;
    form.elements.source.value = source;
    form.elements.page_url.value = location.href.split("#")[0];
    form.elements.utm.value = (location.search.match(/utm_[a-z]+=[^&]*/g) || []).join("&");

    var roomLine = dlg.querySelector("[data-lead-room-line]");
    roomLine.hidden = !room;
    roomLine.textContent = room ? "Room: " + room : "";
    if (room) {
      var rooms = form.querySelector('input[name="topics"][value="rooms"]');
      if (rooms) rooms.checked = true;
    }
    var reserve = intent === "reserve";
    dlg.querySelector("[data-lead-title]").textContent = reserve ? "Reserve your spot" : "Ask us about this retreat";
    dlg.querySelector("[data-lead-intro]").textContent = reserve
      ? "Leave your name and email and we will write to you to book" + (room ? " this room" : " your place") + ", with the deposit details and anything you would like to ask first."
      : "Leave your name and email, and a question if you have one. Someone from our team will write back to you personally.";
    dlg.querySelector("[data-lead-submit-label]").textContent = reserve ? "Send my request" : "Send my question";

    dlg.classList.remove("is-closing");
    lockScroll(true);
    dlg.showModal();
    // Focus the first field, not the close button: the reader came here to type.
    setTimeout(function () { form.elements.first_name.focus({ preventScroll: true }); }, 30);
  }

  function close() {
    if (!dlg || !dlg.open || dlg.classList.contains("is-closing")) return;
    var done = function () {
      dlg.classList.remove("is-closing");
      dlg.close();
      lockScroll(false);
      if (opener && opener.focus) opener.focus({ preventScroll: true });
    };
    if (reduce.matches) return done();
    dlg.classList.add("is-closing");
    setTimeout(done, 180);
  }

  function reset() {
    form.reset();
    attempted = false;
    dlg.querySelector(".lead-modal__sheet").style.minHeight = "";
    form.querySelectorAll(".is-invalid").forEach(function (f) { f.classList.remove("is-invalid"); });
    form.querySelectorAll(".lead-form__error").forEach(function (p) { p.textContent = ""; });
    form.querySelector("[data-lead-phone]").hidden = true;
    dlg.querySelector("[data-lead-body]").hidden = false;
    dlg.querySelector("[data-lead-done]").hidden = true;
  }

  /* --------------------------------------------------------------- submit */
  function submit(e) {
    e.preventDefault();
    attempted = true;
    var bad = validate();
    if (bad) { bad.focus(); return; }

    var data = {};
    new FormData(form).forEach(function (v, k) {
      if (k === "topics") (data.topics = data.topics || []).push(v);
      else data[k] = v;
    });
    var btn = form.querySelector(".lead-form__submit");
    var label = btn.querySelector("[data-lead-submit-label]");
    var was = label.textContent;
    btn.disabled = true;
    btn.classList.add("is-sending");
    label.textContent = "Sending";
    var status = form.querySelector("[data-lead-status]");
    status.hidden = true;

    var endpoint = form.getAttribute("data-endpoint");
    var send = endpoint
      ? fetch(endpoint, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data) })
          .then(function (r) { if (!r.ok) throw new Error(r.status); })
      : new Promise(function (res) { setTimeout(res, 700); });   // simulated until there is an endpoint

    send.then(function () {
      success(data);
    }, function () {
      status.hidden = false;
      status.textContent = "Something went wrong sending this. Please try again, or write to us from the contact page.";
    }).then(function () {
      btn.disabled = false;
      btn.classList.remove("is-sending");
      label.textContent = was;
    });
  }

  function success(data) {
    var done = dlg.querySelector("[data-lead-done]");
    // Keep the sheet at the height it had, so the thank-you replaces the
    // form in place instead of the whole box collapsing around it.
    var sheet = dlg.querySelector(".lead-modal__sheet");
    sheet.style.minHeight = sheet.offsetHeight + "px";
    dlg.querySelector("[data-lead-done-title]").textContent = "Thank you, " + data.first_name + ".";
    dlg.querySelector("[data-lead-done-text]").textContent =
      "Your " + (data.intent === "reserve" ? "request" : "question") + " about " + (ctx.name || "this retreat") +
      " is with us. " + ({
        whatsapp: "We will message you on WhatsApp at " + data.phone + ".",
        phone: "We will call you at " + data.phone + "."
      }[data.contact_method] || "We will write to you at " + data.email + ".");
    dlg.querySelector("[data-lead-body]").hidden = true;
    done.hidden = false;
    done.focus();
    // For the analytics layer, when there is one.
    document.dispatchEvent(new CustomEvent("yaww:lead", { detail: data }));
  }

  /* --------------------------------------------------------------- boot */
  document.addEventListener("click", function (e) {
    var t = e.target.closest && e.target.closest("[data-lead-open]");
    if (!t) return;
    if (typeof HTMLDialogElement === "undefined") return;   // very old browser: follow the link
    e.preventDefault();
    open(t);
  });

  window.YAWW = window.YAWW || {};
  window.YAWW.openLead = open;
})(window, document);
