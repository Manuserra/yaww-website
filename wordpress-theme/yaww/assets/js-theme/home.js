/* ============================================================
   YAWW (WordPress) · home behaviour
   The inline <script>s of index.html after the Lenis init, verbatim:
   the "Where we're going next" rail (js/retreat-cards.js over the
   WordPress catalog), rail arrows, the "who comes" postcards, the FAQ,
   the hero cue, the newsletter and the image reveal. Edits marked WP:
   the "see all" link is the listing's WordPress URL and the postcard
   photos resolve from the theme's assets folder. Keep in step with
   index.html.
   ============================================================ */
/* ============================================================
   "Where we're going next" — retreats rail + Teacher/Destination/
   When filter above it.

   Reads the shared dataset in data/retreats.js (same data
   retreats.html renders), projected to flat `place`/`season` values so
   the filter can match against it. Renders the --narrow card variant,
   built by js/retreat-cards.js, into the same
   native horizontal scroller as before; the filter only changes
   WHICH of those cards are in the track, never how the track itself
   behaves. See yaww-retreat-filter-handoff.md for the filter spec.
   ============================================================ */
(function () {
  /* Single source of truth: data/retreats.js, turned into markup by the
     one card component in js/retreat-cards.js. The small view objects
     below exist only so this filter can match on flat values — every
     string the reader actually sees still comes out of the component,
     so a card only ever has to be changed in one place. */
  var Y = window.YAWW;
  var RETREATS = Y.getUpcomingRetreats().map(function (r) {
    var loc = Y.getLocation(r.locationId);
    return {
      retreat: r,
      place: loc ? loc.name : "",
      season: Y.seasonLabel(r, "filter"),
      teachers: Y.teacherNames(r)
    };
  });

  function esc(s) { return Y.esc(s); }
  /* Where a card points is the component's call (location page, falling
     back to the full listing) — see retreatHref() there. */
  function cardHTML(v) { return Y.renderRetreatCard(v.retreat); }

  var track = document.getElementById('nextRetreatsTrack');
  if (!track) return;

  var rail = document.getElementById('retreatsRail');
  var prevBtn = rail.querySelector('.rail__arrow--prev');
  var nextBtn = rail.querySelector('.rail__arrow--next');
  var EPS = 4;   // sub-pixel scroll rounding

  function setArrow(btn, visible) {
    btn.classList.toggle('is-hidden', !visible);
    btn.tabIndex = visible ? 0 : -1;
    btn.setAttribute('aria-hidden', visible ? 'false' : 'true');
  }

  /* Paging geometry — the standard "peek carousel" shape: every page
     past the first centres a run of whole cards with the same peek
     split evenly on both sides (half a card showing either edge). Page
     0 is simply whatever that leaves at the start — it is never width-
     clamped to match, because clamping it is what produced a reserved
     strip of nothing between the last card and the arrow: there is no
     page to centre against at the very start, so there is nothing to
     reserve for. Measured off the rendered card, not assumed, so it
     holds at any viewport and recomputes on resize the way .gallery
     recomputes --edge. */
  var geo = { shown: 1, stride: 0, firstStep: 0, pad: 0, unit: 1 };
  function computeGeometry() {
    var first = track.querySelector('.result-link');
    if (!first) return;
    var cardW = first.getBoundingClientRect().width;
    var gap = parseFloat(getComputedStyle(rail).getPropertyValue('--gap')) || 0;
    var pad = parseFloat(getComputedStyle(track).paddingLeft) || 0;
    var railW = rail.getBoundingClientRect().width;
    var unit = cardW + gap;

    /* A page of `shown` whole cards, flanked by a gap and a peek on
       EACH side, has to add up to the rail's own width:
         railW = 2*peek + 2*gap + shown*cardW + (shown-1)*gap
       solved for peek. shown is the largest count whose peek still
       clears the minimum — this is also exactly where page 0 hands
       off: the first click lands on the same peek this page settles
       into on both edges. */
    var minPeek = cardW * 0.3;   // enough of a card to read as deliberate, not a sliver
    var shown = Math.max(1, Math.floor((railW - gap - 2 * minPeek) / unit));
    var peek = (railW - shown * cardW - (shown + 1) * gap) / 2;

    geo.shown = shown;
    geo.stride = shown * unit;
    geo.firstStep = pad + geo.stride - gap - peek;
    // Cached so a later resize can re-derive which *card* the current
    // scrollLeft was pointing at under THIS geometry, even after the
    // viewport (and so --gap/pad, read live from the DOM) has already
    // moved on to a new one by the time that resize is handled.
    geo.pad = pad;
    geo.unit = unit;
  }

  function updateArrows() {
    var max = track.scrollWidth - track.clientWidth;
    setArrow(prevBtn, track.scrollLeft > EPS);
    setArrow(nextBtn, track.scrollLeft < max - EPS);
  }
  function goNext() {
    var amount = track.scrollLeft < EPS ? geo.firstStep : geo.stride;
    track.scrollBy({ left: amount, behavior: 'smooth' });
  }
  function goPrev() {
    var amount = track.scrollLeft < geo.firstStep + EPS ? track.scrollLeft : geo.stride;
    track.scrollBy({ left: -amount, behavior: 'smooth' });
  }
  // Which card a scroll position sits flush against, and the inverse —
  // cards lay out at pad + i*unit, so this is just that solved both
  // ways. Anchoring resize on a *card index* rather than a page number
  // is the part that matters: "page 2" covers a different run of cards
  // once a wider/narrower rail changes how many show per page, so
  // re-landing on the same page number still swaps out what's on
  // screen — re-landing on the same card index is what actually keeps
  // whatever you were looking at in view.
  function cardIndexAt(scrollLeft, g) {
    return Math.max(0, Math.round((scrollLeft - g.pad) / g.unit));
  }
  function scrollLeftForCardIndex(index, g) {
    return g.pad + index * g.unit;
  }

  // Re-renders the track from whatever list the filter currently
  // resolves to, then re-measures the peek geometry from scratch —
  // the card count just changed, so the old shown/stride/firstStep no
  // longer describe it.
  /* The home is a showcase, not a search: the next few
     departures, then a closing card that hands off to the one place
     that filters (the retreats page). */
  var SHOWCASE = 8;
  var ALL_URL = (window.YAWW_WP_LINKS && window.YAWW_WP_LINKS['retreats.html']) || 'retreats.html';   /* WP */
  function moreCardHTML(total, shown) {
    var rest = total - shown;
    var places = [];
    RETREATS.slice(shown).forEach(function (v) {
      var l = Y.getLocation(v.retreat.locationId);
      var c = l ? l.country : '';
      if (c && places.indexOf(c) === -1) places.push(c);
    });
    return '<a class="result-link rail-more" href="' + ALL_URL + '">' +
      '<span class="rail-more__box">' +
        '<span class="rail-more__count">+' + rest + '</span>' +
        '<span class="rail-more__title">more retreats</span>' +
        '<span class="rail-more__places">' + esc(places.slice(0, 5).join(' · ')) + '</span>' +
        '<span class="rail-more__cta">See all ' + total + '</span>' +
      '</span></a>';
  }
  function renderTrack(list) {
    var shown = list.slice(0, SHOWCASE);
    track.innerHTML = shown.map(cardHTML).join('') +
      (list.length > shown.length ? moreCardHTML(list.length, shown.length) : '');
    var btn = document.getElementById('seeAllBtn');
    if (btn) btn.textContent = 'See all ' + list.length + ' upcoming retreats';
    track.scrollLeft = 0;
    computeGeometry();
    updateArrows();
  }

  var ticking = false;
  track.addEventListener('scroll', function () {
    if (!ticking) {
      requestAnimationFrame(function () { updateArrows(); ticking = false; });
      ticking = true;
    }
  }, { passive: true });

  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      // Re-anchor on whichever card was leading the view instead of
      // leaving scrollLeft as a stale pixel value: the rail's width
      // just changed, so the same pixel offset now points at a
      // different slice of the strip — without this, resizing reads
      // as cards sliding out from the side rather than the view just
      // resizing in place around what was already showing.
      var anchor = cardIndexAt(track.scrollLeft, geo);
      var wasAtStart = track.scrollLeft < EPS;   // page 0 is flush-left by definition, not pad + 0*unit
      computeGeometry();
      var max = track.scrollWidth - track.clientWidth;
      track.scrollLeft = wasAtStart ? 0 : Math.max(0, Math.min(scrollLeftForCardIndex(anchor, geo), max));
      updateArrows();
    }, 150);
  });

  prevBtn.addEventListener('click', goPrev);
  nextBtn.addEventListener('click', goNext);

  renderTrack(RETREATS);

})();

  /* "In our guests' words" carousel — reuses the site's .rail arrow/
     fade styling, but with simple start↔end navigation instead of the
     retreats rail's "N cards per page" paging above. That page-based
     math assumes several pages' worth of content; with only 3 fixed
     testimonials at 722px, the viewport (1440 minus padding) already
     fits close to 2 cards, so there's only ever one real "page" of
     new content to reveal. Paging by a computed page width left the
     first click stranded re-centering the already-mostly-visible
     middle card instead of making progress toward the one still
     fully offscreen — going straight to the scroll end instead gets
     the hidden card fully into view in exactly one click. That broke
     below ~1100px, where the middle card was never shown whole, so
     the arrows now step one card at a time (see stops() below). */
  (function () {
    var track = document.getElementById('quotesTrack');
    if (!track) return;

    var rail = document.getElementById('quotesRail');
    var prevBtn = rail.querySelector('.rail__arrow--prev');
    var nextBtn = rail.querySelector('.rail__arrow--next');
    var EPS = 4;

    function setArrow(btn, visible) {
      btn.classList.toggle('is-hidden', !visible);
      btn.tabIndex = visible ? 0 : -1;
      btn.setAttribute('aria-hidden', visible ? 'false' : 'true');
    }
    function maxScroll() {
      return track.scrollWidth - track.clientWidth;
    }
    function updateArrows() {
      setArrow(prevBtn, track.scrollLeft > EPS);
      setArrow(nextBtn, track.scrollLeft < maxScroll() - EPS);
    }
    // One card per click: the next/previous card's left edge, lined up
    // with the track's own padding (the same spot scroll-snap rests on).
    var cards = Array.prototype.slice.call(track.querySelectorAll('.quote-card'));
    function stops() {
      var pad = parseFloat(getComputedStyle(track).paddingLeft) || 0;
      var origin = track.getBoundingClientRect().left - track.scrollLeft;
      return cards.map(function (c) {
        return Math.min(c.getBoundingClientRect().left - origin - pad, maxScroll());
      });
    }
    function goNext() {
      var x = track.scrollLeft;
      var target = stops().filter(function (s) { return s > x + EPS; })[0];
      track.scrollTo({ left: target === undefined ? maxScroll() : target, behavior: 'smooth' });
    }
    function goPrev() {
      var x = track.scrollLeft;
      var before = stops().filter(function (s) { return s < x - EPS; });
      track.scrollTo({ left: before.length ? before[before.length - 1] : 0, behavior: 'smooth' });
    }

    updateArrows();

    var ticking = false;
    track.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(function () { updateArrows(); ticking = false; });
        ticking = true;
      }
    }, { passive: true });

    // scroll-snap re-settles on a whole card by itself after a resize.
    window.addEventListener('resize', updateArrows);

    prevBtn.addEventListener('click', goPrev);
    nextBtn.addEventListener('click', goNext);
  })();

  // Mobile nav toggle
  (function () {
    var nav = document.querySelector('.site-header__nav');
    var btn = document.querySelector('.nav-toggle');
    if (!nav || !btn) return;
    btn.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  })();

  // Newsletter form — no endpoint yet, swallow the submit
  document.querySelectorAll('.footer-news__form').forEach(function (form) {
    form.addEventListener('submit', function (e) { e.preventDefault(); });
  });

  // FAQ accordion — same as the retreat pages' "Additional info". The
  // button owns the state and the class owns the animation; aria-expanded
  // is the same fact the class is, said to a screen reader.
  document.querySelectorAll('.faq__trigger').forEach(function (btn) {
    var item = btn.closest('.faq');
    btn.addEventListener('click', function () {
      var open = !item.classList.contains('is-open');
      item.classList.toggle('is-open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  // CTA band: background parallax — same rig as .retreat-includes__bg
  // (retreat pages), same SPEED/MAX_OFFSET_RATIO values.
  (function () {
    var section = document.querySelector('.home-cta');
    var bg = document.querySelector('.home-cta__bg');
    if (!section || !bg) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var SPEED = 0.3;
    var MAX_OFFSET_RATIO = 0.2;
    var ticking = false;
    var listening = false;

    function update() {
      ticking = false;
      var rect = section.getBoundingClientRect();
      var raw = (window.innerHeight / 2 - (rect.top + rect.height / 2)) * SPEED;
      var max = rect.height * MAX_OFFSET_RATIO;
      var offset = Math.max(-max, Math.min(max, raw));
      bg.style.transform = 'translate3d(0,' + offset.toFixed(1) + 'px,0)';
    }

    function onScroll() {
      if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }

    new IntersectionObserver(function (entries) {
      var visible = entries[0].isIntersecting;
      if (visible && !listening) {
        listening = true;
        window.addEventListener('scroll', onScroll, { passive: true });
        update();
      } else if (!visible && listening) {
        listening = false;
        window.removeEventListener('scroll', onScroll);
      }
    }, { rootMargin: '20% 0px' }).observe(section);
  })();

  // Image reveal — fade in once loaded, skip images already decoded (cached / above the fold)
  document.querySelectorAll('img').forEach(function (img) {
    if (img.complete) return;
    img.classList.add('img-reveal');
    function done() { img.classList.add('is-loaded'); }
    img.addEventListener('load', done);
    img.addEventListener('error', done);
  });

  // "Who comes on our retreats" — guest-type tablist + postcard stack.
  // The active profile's own progress bar is a real CSS animation; its
  // animationend event IS the auto-advance clock, so pausing is just
  // pausing that one animation (hover/focus on the list, or the
  // play/pause button) rather than a separate timer to keep in sync.
  (function () {
    var section = document.querySelector('.who');
    if (!section) return;

    // `alt` is scene-descriptive, not a name — none of these photos
    // are of the person quoted, per the handoff.
    var DATA = [
      { title: 'First time traveling solo', body: 'Many of our guests travel on their own. We answer every question before you book, and you meet the whole group at our opening circle.', quote: 'I have been wanting to take a solo trip but was always a little nervous. Jennifer made me feel so comfortable throughout the sign-up process, and the rest of the team were so welcoming in El Cuyo. Can’t wait for the next trip!', name: 'Allyson M.', place: 'El Cuyo Retreat', photo: 'guest-solo.jpg', alt: 'A guest smiling on a colorful street' },
      { title: 'New to yoga', body: "Classes are built for every level. Plenty of our guests don't call themselves yogis.", quote: 'She has invited me several times and I always hesitated because I’m not a yogi. I finally decided to go on a retreat to El Cuyo, and I’m so glad I did. Not only did I enjoy the yoga, I enjoyed every single moment of the trip.', name: 'Melissa M.', place: 'El Cuyo Retreat', photo: 'guest-new-to-yoga.jpg', avatar: 'melissa-m-avatar.png', alt: 'A guest smiling mid-stretch during an open-air core class' },
      { title: 'Traveling with someone', body: 'A friend, partner or daughter. Book together and share a room, or come as a group and make the week your own.', quote: 'My Yoga Adventure retreat in El Cuyo with my daughter has been one of the most incredible things that I have done! It felt like family coming together, at each meal, to share stories of life and daily adventures.', name: 'Deborah C.', place: 'El Cuyo Retreat', photo: 'guest-with-someone.jpg', alt: 'Two guests smiling face to face atop ancient stone ruins' },
      { title: 'Coming back again', body: 'Many guests travel with us year after year, and run into old friends on the next trip.', quote: 'I have been traveling with Yoga Adventures Worldwide for over ten years and they keep surprising me with amazing locations and activities! Even though we were strangers at the beginning of the week, we parted as friends and look forward to running into each other at the next adventure.', name: 'Mimi S.', place: 'Montenegro Retreat', photo: 'guest-returning.jpg', avatar: 'mimi-s.png', alt: 'Two guests laughing together on a terrace above a bay' }
    ];
    var n = DATA.length;

    var POS = [
      { transform: 'translate(0px, 0px) rotate(0deg)', zIndex: 40, opacity: 1, boxShadow: '0 12px 24px rgba(40, 49, 57, 0.08), 0 1px 3px rgba(40, 49, 57, 0.06)' },
      { transform: 'translate(16px, 12px) rotate(1.5deg)', zIndex: 30, opacity: 1, boxShadow: '0 4px 10px rgba(40, 49, 57, 0.05)' },
      { transform: 'translate(-10px, 20px) rotate(-1.5deg)', zIndex: 20, opacity: 1, boxShadow: '0 3px 8px rgba(40, 49, 57, 0.04)' },
      { transform: 'translate(-10px, 20px) rotate(-1.5deg)', zIndex: 10, opacity: 0, boxShadow: 'none' }
    ];

    var profilesEl = document.getElementById('whoProfiles');
    var stackEl = document.getElementById('whoStack');
    var counterEl = document.getElementById('whoCounter');
    var playBtn = document.getElementById('whoPlayBtn');
    var prevBtn = document.getElementById('whoPrevBtn');
    var nextBtn = document.getElementById('whoNextBtn');
    if (!profilesEl || !stackEl) return;

    // On phones the profile list is hidden (it and the card can't share
    // one screen) and each card carries its own profile title, so the
    // stack starts stopped: the visitor swipes or taps the arrows, the
    // component never changes a card they may be halfway through reading.
    var mobileMq = window.matchMedia('(max-width: 767px)');
    var state = { index: 0, hover: false, stopped: mobileMq.matches };
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function esc(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;'); }
    function pad2(x) { return x < 10 ? '0' + x : String(x); }
    // No avatar photo yet for every guest — initials-on-a-circle is the
    // stand-in, using the same light stone/sea-blue pairing as the real
    // avatar photos' background, not a one-off color.
    function initials(name) {
      return String(name).trim().split(/\s+/).map(function (w) { return w.charAt(0).toUpperCase(); }).join('').slice(0, 2);
    }

    function renderProfiles() {
      profilesEl.innerHTML = DATA.map(function (d, k) {
        var active = k === state.index;
        return (
          '<div class="who__profile' + (active ? ' is-active' : '') + '">' +
            (active ? '<span class="who__progress" aria-hidden="true"></span>' : '') +
            '<button type="button" class="who__tab" role="tab" aria-selected="' + active + '" data-i="' + k + '" id="whoTab' + k + '" aria-controls="whoCard' + k + '" tabindex="' + (active ? '0' : '-1') + '">' +
              '<span class="who__tab-title">' + esc(d.title) + '</span>' +
            '</button>' +
            (active ? '<p class="who__tab-body">' + esc(d.body) + '</p>' : '') +
          '</div>'
        );
      }).join('');
      var bar = profilesEl.querySelector('.who__progress');
      if (bar) {
        bar.addEventListener('animationend', function () {
          if (!state.stopped) go(state.index + 1);
        });
      }
      applyPauseState();
    }

    // Built once: the CSS transition on .who__card can only animate a
    // property change on a node that persists across renders. Rebuilding
    // the 4 cards from scratch on every go() (the original renderStack)
    // gave each one a brand-new element with no "before" state to
    // interpolate from, so the postcards snapped straight to position
    // instead of visibly passing each other — updateStackPositions below
    // is what actually moves them.
    function renderStackOnce() {
      stackEl.innerHTML = DATA.map(function (d, k) {
        return (
          '<article class="who__card" id="whoCard' + k + '" role="tabpanel" aria-labelledby="whoTab' + k + '">' +
            '<div class="who__card-row">' +
              '<div class="who__card-message">' +
                '<p class="kicker who__card-kicker">' + esc(d.title) + '</p>' +
                '<blockquote class="who__quote">&ldquo;' + esc(d.quote) + '&rdquo;</blockquote>' +
                '<div class="who__signer">' +
                  (d.avatar
                    ? '<img class="who__avatar" src="' + (window.YAWW_ASSETS || '') + 'images/' + d.avatar + '" alt="">'
                    : '<span class="who__avatar who__avatar--initials" aria-hidden="true">' + esc(initials(d.name)) + '</span>') +
                  '<span class="who__signer-text">' +
                    '<span class="who__name">' + esc(d.name) + '</span>' +
                    '<span class="who__place">' + esc(d.place) + '</span>' +
                  '</span>' +
                '</div>' +
              '</div>' +
              '<div class="who__card-media">' +
                '<img class="who__photo" src="' + (window.YAWW_ASSETS || '') + 'images/' + d.photo + '" alt="' + esc(d.alt) + '" loading="lazy">' +
              '</div>' +
            '</div>' +
          '</article>'
        );
      }).join('');
      DATA.forEach(function (d, k) {
        document.getElementById('whoCard' + k).style.gridArea = '1 / 1';
      });
    }

    function updateStackPositions() {
      DATA.forEach(function (d, k) {
        var card = document.getElementById('whoCard' + k);
        var pos = (k - state.index + n) % n;
        var p = POS[pos];
        card.style.transform = p.transform;
        card.style.zIndex = p.zIndex;
        card.style.opacity = p.opacity;
        card.style.boxShadow = p.boxShadow;
        card.setAttribute('aria-hidden', String(pos !== 0));
      });
    }

    function renderCounter() {
      counterEl.textContent = pad2(state.index + 1) + ' / ' + pad2(n);
    }

    function renderPlayBtn() {
      playBtn.setAttribute('aria-label', state.stopped ? 'Play postcards' : 'Pause postcards');
      playBtn.innerHTML = state.stopped
        ? '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M5 3 13 8 5 13Z" fill="currentColor"/></svg>'
        : '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M5 3v10M11 3v10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
    }

    function isPaused() { return state.hover || state.stopped || reduceMotion; }
    function applyPauseState() {
      var bar = profilesEl.querySelector('.who__progress');
      if (bar) bar.style.animationPlayState = isPaused() ? 'paused' : 'running';
    }

    function go(k) {
      state.index = (k + n) % n;
      renderProfiles();
      updateStackPositions();
      renderCounter();
    }

    // Any direct pick — a profile tab, the arrow keys, or prev/next —
    // stops the auto-advance for good, same as hitting pause. Only the
    // internal animationend callback above is allowed to call go()
    // without this, since that's the auto-advance itself.
    function stopAuto() {
      if (state.stopped) return;
      state.stopped = true;
      renderPlayBtn();
    }

    profilesEl.addEventListener('click', function (e) {
      var tab = e.target.closest('.who__tab');
      if (tab) { stopAuto(); go(parseInt(tab.dataset.i, 10)); }
    });
    profilesEl.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;
      e.preventDefault();
      stopAuto();
      go(state.index + (e.key === 'ArrowRight' ? 1 : -1));
      var tab = profilesEl.querySelector('.who__tab[aria-selected="true"]');
      if (tab) tab.focus();
    });
    profilesEl.addEventListener('mouseenter', function () { state.hover = true; applyPauseState(); });
    profilesEl.addEventListener('mouseleave', function () { state.hover = false; applyPauseState(); });
    profilesEl.addEventListener('focusin', function () { state.hover = true; applyPauseState(); });
    profilesEl.addEventListener('focusout', function () { state.hover = false; applyPauseState(); });

    prevBtn.addEventListener('click', function () { stopAuto(); go(state.index - 1); });
    nextBtn.addEventListener('click', function () { stopAuto(); go(state.index + 1); });
    playBtn.addEventListener('click', function () {
      state.stopped = !state.stopped;
      renderPlayBtn();
      applyPauseState();
    });

    var onMq = function (e) { if (e.matches) { stopAuto(); applyPauseState(); } };
    if (mobileMq.addEventListener) mobileMq.addEventListener('change', onMq);
    else if (mobileMq.addListener) mobileMq.addListener(onMq);

    // Horizontal swipe on the card stack = prev/next. Only a clearly
    // horizontal gesture counts, so vertical page scrolling over the
    // card is never hijacked.
    var touchX = null, touchY = null;
    stackEl.addEventListener('touchstart', function (e) {
      if (e.touches.length !== 1) { touchX = null; return; }
      touchX = e.touches[0].clientX; touchY = e.touches[0].clientY;
    }, { passive: true });
    stackEl.addEventListener('touchend', function (e) {
      if (touchX === null) return;
      var t = e.changedTouches[0], dx = t.clientX - touchX, dy = t.clientY - touchY;
      touchX = null;
      if (Math.abs(dx) < 40 || Math.abs(dx) < Math.abs(dy) * 1.5) return;
      stopAuto();
      go(state.index + (dx < 0 ? 1 : -1));
    }, { passive: true });

    renderPlayBtn();
    renderProfiles();
    renderStackOnce();
    updateStackPositions();
    renderCounter();
  })();

  /* index-video · hero video
     Two jobs, both about not playing footage nobody is watching:
     reduced-motion users get the poster frame held still, and the
     loop pauses while the hero is scrolled off screen (the page is
     long — there is no reason to keep decoding frames behind the
     retreats rail). */
  (function () {
    var video = document.querySelector('.home-hero__media--video video');
    if (!video) return;

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

    function stopForReducedMotion() {
      video.pause();
      video.removeAttribute('autoplay');
      try { video.currentTime = 0; } catch (e) {}
    }

    if (reduced.matches) stopForReducedMotion();
    /* addEventListener on a MediaQueryList is the modern form;
       addListener is the Safari < 14 fallback. */
    if (reduced.addEventListener) {
      reduced.addEventListener('change', function (e) {
        if (e.matches) stopForReducedMotion(); else video.play().catch(function () {});
      });
    } else if (reduced.addListener) {
      reduced.addListener(function (e) {
        if (e.matches) stopForReducedMotion(); else video.play().catch(function () {});
      });
    }

    /* Scroll cue: same handler as evaneos.es — one viewport down. */
    var cue = document.querySelector('.home-hero__cue');
    if (cue) cue.addEventListener('click', function () {
      window.scrollTo({ top: window.innerHeight, behavior: 'smooth' });
    });

    if (!('IntersectionObserver' in window)) return;
    new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (reduced.matches) return;
        if (entry.isIntersecting) {
          /* play() returns a rejected promise if the browser blocks
             autoplay — nothing to recover from, the poster stays. */
          video.play().catch(function () {});
        } else {
          video.pause();
        }
      });
    }, { threshold: 0 }).observe(video);
  })();
