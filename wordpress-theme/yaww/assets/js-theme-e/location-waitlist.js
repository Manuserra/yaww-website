/* Location page waitlist (no upcoming dates): js/retreat-cards.js wireWaitlistForm(), per-field mode,
   same words and rules as the lead modal. The static site has no endpoint; here the payload is
   POSTed as JSON to window.YAWW_WAITLIST_ENDPOINT when the forms module sets one, and the success
   state shows once it answers 2xx (or straight away when no endpoint is set, as on the static site). */
(function () {
  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  function checkField(form, name) {
    var el = form.elements[name];
    if (!el) return true;
    var v = (el.value || '').trim(), msg = '';
    if (name === 'first_name' && !v) msg = 'Please tell us your first name.';
    if (name === 'email') {
      if (!v) msg = 'We need an email to write to you.';
      else if (!EMAIL_RE.test(v)) msg = 'That email looks incomplete. Could you check it?';
    }
    var out = form.querySelector('[data-error-for="' + name + '"]');
    el.setAttribute('aria-invalid', msg ? 'true' : 'false');
    var wrap = el.closest('.lead-form__field');
    if (wrap) wrap.classList.toggle('is-invalid', !!msg);
    if (out) out.textContent = msg;
    return !msg;
  }
  function checkFields(form) {
    form.dataset.attempted = '1';
    var bad = null;
    ['first_name', 'email'].forEach(function (n) { if (!checkField(form, n) && !bad) bad = form.elements[n]; });
    return bad;
  }

  function wire(form) {
    if (!form || form.dataset.yawwWired) return;
    form.dataset.yawwWired = '1';
    ['first_name', 'email'].forEach(function (n) {
      var el = form.elements[n];
      if (!el) return;
      el.addEventListener('input', function () { if (el.getAttribute('aria-invalid') === 'true') checkField(form, n); });
      el.addEventListener('blur', function () { if (form.dataset.attempted || el.value) checkField(form, n); });
    });
    var successEl = document.getElementById(form.getAttribute('data-waitlist-success') || '');

    function done(payload) {
      if (!successEl) return;
      var doneTitle = successEl.querySelector('[data-wait-done-title]');
      if (doneTitle) doneTitle.textContent = 'You are on the list, ' + payload.firstName + '.';
      var body = form.closest('[data-loc-wait-body]');
      if (body) body.hidden = true;
      form.hidden = true;
      successEl.hidden = false;
      successEl.setAttribute('tabindex', '-1');
      successEl.focus();
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var bad = checkFields(form);
      if (bad) { bad.focus(); return; }
      var loc = form.elements['location'];
      var teach = form.elements['teacher'];
      var payload = {
        firstName: form.elements['first_name'].value.trim(),
        email: form.elements['email'].value.trim(),
        locationId: loc ? loc.value : '',
        teacherId: teach ? teach.value : '',
        source: window.location.pathname
      };
      document.dispatchEvent(new CustomEvent('yaww:track', {
        detail: { event: 'waitlist_submit', locationId: payload.locationId, teacherId: payload.teacherId }
      }));
      var url = window.YAWW_WAITLIST_ENDPOINT;
      if (!url || !window.fetch) { done(payload); return; }
      var btn = form.querySelector('[type="submit"]');
      if (btn) btn.disabled = true;
      fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload), credentials: 'same-origin' })
        .then(function (r) { if (!r.ok) throw new Error(r.status); done(payload); })
        .catch(function () {
          if (btn) btn.disabled = false;
          var out = form.querySelector('[data-error-for="email"]');
          if (out) out.textContent = 'Something went wrong sending this. Please try again, or write to us from the contact page.';
        });
    });
  }

  document.querySelectorAll('[data-waitlist-form]').forEach(wire);
})();
