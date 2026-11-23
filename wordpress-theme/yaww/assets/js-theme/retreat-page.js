/* ============================================================
   YAWW (WordPress) · retreat page behaviour

   The retreat pages' main inline <script> (retreatpage-*.html, as
   retreats/_tools/build_page.py writes them), verbatim, except that its
   data literals (HOTELS, EXTRAS, TIERS, ROOM_VENUES, URGENCY,
   TESTIMONIALS) are read from window.YAWW_RETREAT, which
   single-retreat.php / inc/views.php write from the retreat post. Every
   change is marked WP. TIERS and URGENCY stay page globals: js/booking-bar.js
   and js/retreat-rooms.js read them.

   Includes tabs + parallax, itinerary rail, accommodations tabs,
   extras tabs, lightbox, rooms list hook, sliding tab indicators,
   gallery strip, FAQ, newsletter, image reveal, testimonials, hero label.
   Keep in step with the static pages.
   ============================================================ */
var R = window.YAWW_RETREAT || {};   /* WP */

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


  // Includes: What Includes / What's Not Included tabs
  document.querySelectorAll('.retreat-includes__tab').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.retreat-includes__tab').forEach(function (b) {
        var on = b === btn;
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      document.querySelectorAll('.retreat-includes__panel').forEach(function (p) {
        var on = p.dataset.panel === btn.dataset.panel;
        p.classList.toggle('is-hidden', !on);
        p.setAttribute('aria-hidden', on ? 'false' : 'true');
      });
    });
  });

  // Includes: background parallax (bg moves slower than the card as you scroll)
  (function () {
    var section = document.querySelector('.retreat-includes');
    var bg = document.querySelector('.retreat-includes__bg');
    if (!section || !bg) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var SPEED = 0.3;              // bg travels at 30% of scroll speed — lower = more dramatic
    var MAX_OFFSET_RATIO = 0.2;   // clamp to 20% of section height (bg has a 25% buffer, so this never exposes an edge)
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

    // Only listen to scroll while the section is near the viewport.
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

  /* Itinerary: rail progress ---------------------------------------------
     Fills the timeline down to a reading line at 55% of the viewport — the
     line lands on what you are actually reading, not on an abstract section
     percentage, so it stays honest at any content length.
     Cost per frame is one style write. Every measurement (the two end dots,
     each dot's centre) is cached and only refreshed when the list itself
     resizes, so scrolling never reads layout; the scroll listener is passive
     and only attached while the timeline is on screen. */
  (function () {
    var timeline = document.querySelector('.retreat-itinerary__timeline');
    if (!timeline) return;
    var fill = timeline.querySelector('.retreat-itinerary__progress');
    var list = timeline.querySelector('.retreat-itinerary__list');
    var dots = Array.prototype.slice.call(timeline.querySelectorAll('.retreat-itinerary__dot'));
    if (!fill || dots.length < 2) return;

    var READ_LINE = 0.55;   // fraction of the viewport height
    var from = 0, to = 1, fracs = [];
    var lastP = -1, lastReached = -1, queued = false, listening = false;

    function centreOf(el) {
      var r = el.getBoundingClientRect();
      return r.top + r.height / 2 + window.scrollY;
    }

    /* Everything the scroll handler needs is resolved here, in scroll-offset
       space, so update() is pure arithmetic — no layout reads per frame. */
    function measure() {
      var centres = dots.map(centreOf);
      var startY = centres[0];
      var span = centres[centres.length - 1] - startY;
      var vh = window.innerHeight;

      fracs = centres.map(function (c) { return span > 0 ? (c - startY) / span : 0; });

      var base = timeline.getBoundingClientRect().top + window.scrollY;
      fill.style.top = (startY - base) + 'px';
      fill.style.height = span + 'px';

      /* The scroll offsets at which the reading line sits on the first and
         last dot. The end is clamped to the last scrollable offset: the
         itinerary sits near the foot of the page, so the line can never
         physically reach the final dot and the timeline would stall at ~94%
         with the last ring never lighting. Clamping trades a slightly faster
         fill over the final stretch for a timeline that actually completes. */
      from = startY - vh * READ_LINE;
      to = centres[centres.length - 1] - vh * READ_LINE;
      var maxScroll = document.documentElement.scrollHeight - vh;
      if (to > maxScroll) to = maxScroll;

      lastP = lastReached = -1;
      update();
    }

    function update() {
      var p = to > from ? (window.scrollY - from) / (to - from) : 1;
      p = p < 0 ? 0 : p > 1 ? 1 : p;
      if (p !== lastP) {
        fill.style.setProperty('--p', p);
        lastP = p;
      }
      /* Rings follow the fill, not the reading line, so the tip of the line
         and the ring it lights are always the same point on screen. */
      var reached = 0;
      if (p > 0) while (reached < fracs.length && fracs[reached] <= p) reached++;
      if (reached !== lastReached) {
        for (var i = 0; i < dots.length; i++) {
          dots[i].classList.toggle('is-reached', i < reached);
        }
        lastReached = reached;
      }
    }

    function onScroll() {
      if (queued) return;
      queued = true;
      requestAnimationFrame(function () { queued = false; update(); });
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
    }, { rootMargin: '20% 0px' }).observe(timeline);

    /* Two refresh paths on purpose. The observer catches layout changes the
       window never hears about (a breakpoint reflow, a late font); the resize
       listener is the cheap guarantee if the observer is unavailable or
       throttled. Both are rare, and measure() is a dozen rect reads.
       The observer is held in a variable — an unreferenced one can be
       collected in some engines even while it has live observations. */
    var ro = window.ResizeObserver ? new ResizeObserver(measure) : null;
    if (ro) ro.observe(list);
    window.addEventListener('resize', measure);
    measure();
  })();

  /* Accommodations -------------------------------------------------------
     One tab per hotel, each with its own gallery and copy. Rendered from
     data because retreats run from one hotel to three, and a static panel
     per hotel would mean editing markup for every trip. Runs before the
     slider setup so the generated sliders get wired like any other. */
  (function () {
    var HOTELS = R.hotels || [];   /* WP: from the retreat post (stays) */

    window.YAWW_HOTELS = HOTELS;   // the room modal shows each hotel too

    var tabsEl = document.querySelector('.retreat-accom__tabs');
    var panelsEl = document.querySelector('.retreat-accom__panels');
    if (!tabsEl || !panelsEl) return;

    var ARROW = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M%s"/></svg>';

    function panelHTML(hotel) {
      return (
        '<div class="photo-slider retreat-accom__slider" data-lightbox role="group" aria-roledescription="carousel" aria-label="' + hotel.name + ' photos">' +
          '<div class="photo-slider__track">' +
            hotel.gallery.map(function (src, n) {
              return '<div class="photo-slider__slide"><img src="' + src + '" alt="' + (n === 0 ? hotel.name : '') + '" loading="lazy"></div>';
            }).join('') +
          '</div>' +
          '<button class="photo-slider__arrow photo-slider__arrow--prev" aria-label="Previous photo">' + ARROW.replace('%s', '15 18l-6-6 6-6') + '</button>' +
          '<button class="photo-slider__arrow photo-slider__arrow--next" aria-label="Next photo">' + ARROW.replace('%s', '9 18l6-6-6-6') + '</button>' +
          '<div class="photo-slider__dots">' +
            hotel.gallery.map(function (_, n) {
              return '<button class="photo-slider__dot' + (n === 0 ? ' is-active' : '') + '" aria-label="Photo ' + (n + 1) + '"></button>';
            }).join('') +
          '</div>' +
        '</div>' +
        '<div class="retreat-accom__copy">' +
          '<h3 class="retreat-accom__name">' + hotel.name + '</h3>' +
          '<div class="retreat-accom__text">' + hotel.text.map(function (t) { return '<p>' + t + '</p>'; }).join('') + '</div>' +
        '</div>'
      );
    }

    /* One hotel needs no tabs: a single tab is a control that switches
       nothing. The bar is removed and the panel stands on its own. */
    var single = HOTELS.length < 2;
    if (single) tabsEl.remove();

    HOTELS.forEach(function (hotel, i) {
      var id = 'accom-' + i;
      if (single) {
        var solo = document.createElement('div');
        solo.className = 'retreat-accom__panel';
        panelsEl.appendChild(solo);
        solo.innerHTML = panelHTML(hotel);
        return;
      }

      var tab = document.createElement('button');
      tab.className = 'tabs__item retreat-accom__tab' + (i === 0 ? ' is-active' : '');
      tab.setAttribute('role', 'tab');
      tab.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
      tab.setAttribute('aria-controls', id);
      tab.textContent = hotel.tab || hotel.name;
      tabsEl.appendChild(tab);

      var panel = document.createElement('div');
      panel.className = 'retreat-accom__panel';
      panel.id = id;
      panel.setAttribute('role', 'tabpanel');
      if (i !== 0) panel.hidden = true;
      panel.innerHTML = panelHTML(hotel);
      panelsEl.appendChild(panel);

      tab.addEventListener('click', function () {
        tabsEl.querySelectorAll('.retreat-accom__tab').forEach(function (b, bi) {
          var on = bi === i;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        panelsEl.querySelectorAll('.retreat-accom__panel').forEach(function (pn, pi) { pn.hidden = pi !== i; });
      });
    });
  })();

  /* Optional Extras -------------------------------------------------------
     One tab per extra, each with its own gallery, copy and price. Runs
     before the lightbox so its sliders get the [data-lightbox] hook, and
     before the slider loop so arrows and dots get wired. */
  (function () {
    var EXTRAS = R.extras || [];   /* WP: from the retreat post (extras) */

    var tabsEl = document.querySelector('.extras__tabs');
    var panelsEl = document.querySelector('.extras__panels');
    if (!tabsEl || !panelsEl) return;

    var PREV = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>';
    var NEXT = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>';

    EXTRAS.forEach(function (x, i) {
      var id = 'extra-' + i;

      var tab = document.createElement('button');
      tab.className = 'tabs__item extras__tab' + (i === 0 ? ' is-active' : '');
      tab.type = 'button';
      tab.setAttribute('role', 'tab');
      tab.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
      tab.setAttribute('aria-controls', id);
      tab.textContent = x.tab || x.name;
      tabsEl.appendChild(tab);

      var panel = document.createElement('div');
      panel.className = 'extras__panel';
      panel.id = id;
      panel.setAttribute('role', 'tabpanel');
      if (i !== 0) panel.hidden = true;
      panel.innerHTML =
        '<div class="extras__text">' +
          '<h3 class="extras__name">' + x.name + '</h3>' +
          '<p class="extras__desc">' + x.desc + '</p>' +
          '<p class="extras__price">' +
            '<span class="extras__price-amount">' + x.price + '</span>' +
            '<span class="extras__price-unit">' + x.unit + '</span>' +
          '</p>' +
        '</div>' +
        (!x.gallery.length ? '' :
        '<div class="extras__media">' +
          '<div class="photo-slider extras__slider" data-lightbox role="group" aria-roledescription="carousel" aria-label="' + x.name + ' photos">' +
            '<div class="photo-slider__track">' +
              x.gallery.map(function (src, n) {
                return '<div class="photo-slider__slide"><img src="' + src + '" alt="' + (n === 0 ? x.name : '') + '" loading="lazy"></div>';
              }).join('') +
            '</div>' +
            '<button class="photo-slider__arrow photo-slider__arrow--prev" aria-label="Previous photo">' + PREV + '</button>' +
            '<button class="photo-slider__arrow photo-slider__arrow--next" aria-label="Next photo">' + NEXT + '</button>' +
            '<div class="photo-slider__dots">' +
              x.gallery.map(function (_, n) {
                return '<button class="photo-slider__dot' + (n === 0 ? ' is-active' : '') + '" aria-label="Photo ' + (n + 1) + '"></button>';
              }).join('') +
            '</div>' +
          '</div>' +
        '</div>');
      panelsEl.appendChild(panel);

      tab.addEventListener('click', function () {
        tabsEl.querySelectorAll('.extras__tab').forEach(function (b, bi) {
          var on = bi === i;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        panelsEl.querySelectorAll('.extras__panel').forEach(function (p, pi) { p.hidden = pi !== i; });
      });
    });
  })();

  /* Lightbox ------------------------------------------------------------
     One dialog serves every gallery marked [data-lightbox]. <dialog> does
     the hard parts natively (focus trap, Esc, page inert, focus returned to
     the thumbnail), so this only has to swap the src and keep the index.
     On close the underlying slider is moved to whatever photo was last
     viewed, so the user never loses their place. */
  (function () {
    var dlg = document.querySelector('.lightbox');
    if (!dlg || !dlg.showModal) return;   // no <dialog>: images simply stay static

    var img = dlg.querySelector('.lightbox__img');
    var count = dlg.querySelector('.lightbox__count');
    var photos = [], index = 0, source = null, active = false;

    /* Feeds the CSS two numbers per photo: its aspect ratio, and how far it
       may be enlarged. 1.4x its own pixels is about where a photograph starts
       to look soft — past that the lightbox is inventing detail, which reads
       as worse quality rather than as a bigger picture. Tying the ceiling to
       the file means better originals automatically display bigger. */
    function fit(w, h) {
      if (!w || !h) return;
      dlg.style.setProperty('--lb-ratio', w + ' / ' + h);
      dlg.style.setProperty('--lightbox-max', Math.round(w * 1.4) + 'px');
    }

    /* A gallery may arrive as <img> elements already on the page (the
       sliders) or as bare URLs (the rooms module builds its lists in JS and
       only one photo per hotel is ever in the DOM). Both collapse to the
       same shape here, which is the only thing show() needs to know. */
    function normalize(list) {
      return list.map(function (item) {
        if (typeof item === 'string') return { src: item, alt: '' };
        return { src: item.src, alt: item.alt || '', w: item.naturalWidth, h: item.naturalHeight };
      });
    }

    function show(i) {
      index = (i + photos.length) % photos.length;
      var photo = photos[index];
      img.src = photo.src;
      img.alt = photo.alt;
      fit(photo.w, photo.h);   // 0 for a bare URL — the load listener fills it in
      count.textContent = (index + 1) + ' / ' + photos.length;
      // arrows and a "1 / 1" counter are noise on a single photo
      dlg.classList.toggle('is-single', photos.length < 2);
    }

    // fallback for a thumbnail that had not finished loading yet
    img.addEventListener('load', function () { fit(img.naturalWidth, img.naturalHeight); });

    /* Cleanup is a function, not just a 'close' listener, and it is
       idempotent: our own close paths run it immediately instead of waiting
       for the event, and the listener still catches the closes the browser
       does on its own (Esc). Whichever happens first wins; the second is a
       no-op. */
    function teardown() {
      if (!active) return;
      active = false;
      document.documentElement.classList.remove('has-lightbox');
      if (source && source.goTo) source.goTo(index);
      img.removeAttribute('src');
      source = null;
    }
    function dismiss() { dlg.close(); teardown(); }

    /* The one way in. `back` is optional and only used by the sliders, which
       want to be left on the photo the reader stopped at. */
    dlg.openGallery = function (list, start, back) {
      if (!list || !list.length) return;
      photos = normalize(list);
      source = back || null;
      show(start || 0);
      active = true;
      document.documentElement.classList.add('has-lightbox');
      dlg.showModal();
    };

    document.querySelectorAll('.photo-slider[data-lightbox]').forEach(function (slider) {
      var imgs = Array.prototype.slice.call(slider.querySelectorAll('.photo-slider__slide img'));
      imgs.forEach(function (el, i) {
        el.addEventListener('click', function () { dlg.openGallery(imgs, i, slider); });
      });
    });

    dlg.querySelector('.lightbox__btn--prev').addEventListener('click', function () { show(index - 1); });
    dlg.querySelector('.lightbox__btn--next').addEventListener('click', function () { show(index + 1); });
    dlg.querySelector('.lightbox__btn--close').addEventListener('click', dismiss);

    dlg.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { e.preventDefault(); show(index + 1); }
      else if (e.key === 'ArrowLeft') { e.preventDefault(); show(index - 1); }
    });

    /* Clicking the backdrop closes. The dialog fills the viewport, so the
       test is whether the click landed outside the photo itself. */
    dlg.addEventListener('click', function (e) {
      if (e.target.closest('.lightbox__btn') || e.target === img) return;
      dismiss();
    });

    dlg.addEventListener('close', teardown);    // Esc, or any browser-driven close
    dlg.addEventListener('cancel', teardown);
  })();

  /* Rooms pricing --------------------------------------------------------
     Data first, render second. A tier is priced once, per person, and lists
     the hotels that offer it; the tier's lightbox gallery is every hotel's
     photos end to end, so a reader who opens one hotel can keep going
     through the rest of that tier without closing anything. Swapping TIERS
     is the whole per-retreat edit. Runs after the lightbox so
     dlg.openGallery exists. */
  /* This date's urgency, same shape the index list uses:
     {type:'scarce', label:'...'}, {type:'soldout'}, or null for none.
     This one matches the Beach Escape row in index.html, which carries the
     same retreat on the same dates — keep the two in step. */
  var URGENCY = R.urgency || null;   /* WP */

  /* The page's room data. Lives out here rather than inside the pricing
     module because the sticky booking bar reads it too — both need the
     same numbers, and a second copy is a second thing to forget. */
  /* PLACEHOLDER prices, attrs and photos. Room counts follow the
     Accommodations text (five private queens, three shared twins); the
     images reuse hotel photos because no per-room shots exist in the repo
     yet. Any of bed / bath / view may be absent — a missing one simply
     renders no chip. */
  var TIERS = R.tiers || [];   /* WP: from the retreat post (rooms) */
  /* Where the rooms are: one name per hotel, in the order you stay. */
  var ROOM_VENUES = R.roomVenues || [];   /* WP */

  /* The list and the room modal are rendered by js/retreat-rooms.js,
     from TIERS and ROOM_VENUES above. */

  /* Tabs: sliding indicator ----------------------------------------------
     Runs last, so every tab bar the modules render already exists. It reads
     the clicked tab's own box, so it does not care which module owns the
     is-active class. */
  (function () {
    var BARS = [
      { bar: '.tabs', item: '.tabs__item', cls: 'tabs__indicator' },
      { bar: '.tabbar .container', item: '.tab', cls: 'tabbar__indicator' }
    ];
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    BARS.forEach(function (kind) {
      document.querySelectorAll(kind.bar).forEach(function (bar) {
        var tabs = bar.querySelectorAll(kind.item);
        if (!tabs.length) return;

        var ind = document.createElement('span');
        ind.className = kind.cls;
        ind.setAttribute('aria-hidden', 'true');
        bar.appendChild(ind);
        bar.classList.add('is-sliding');

        function move(tab, animate) {
          if (!tab) return;
          bar.classList.toggle('is-sliding-ready', !!animate);
          ind.style.width = tab.offsetWidth + 'px';
          /* -1 so the bar sits astride the hairline, exactly where the old
             per-tab rule sat at bottom:-1px */
          ind.style.transform = 'translate(' + tab.offsetLeft + 'px, ' +
                                (tab.offsetTop + tab.offsetHeight - 1) + 'px)';
        }
        function active() { return bar.querySelector(kind.item + '.is-active'); }

        move(active(), false);
        bar.addEventListener('click', function (e) {
          var t = e.target.closest(kind.item);
          if (t && bar.contains(t)) {
            move(t, !reduce);
            if (bar.scrollWidth > bar.clientWidth) {
              bar.scrollTo({ left: t.offsetLeft - (bar.clientWidth - t.offsetWidth) / 2, behavior: reduce ? 'auto' : 'smooth' });
            }
          }
        });
        /* Observe the bar, not the window: the tabs can rewrap or change
           width without the viewport doing anything, and a resize listener
           misses exactly those cases. Held in a variable so it cannot be
           collected. */
        var ro = window.ResizeObserver
          ? new ResizeObserver(function () { move(active(), false); })
          : null;
        if (ro) ro.observe(bar);
        window.addEventListener('resize', function () { move(active(), false); });
        // web fonts change tab widths after first paint
        if (document.fonts && document.fonts.ready) {
          document.fonts.ready.then(function () { move(active(), false); });
        }
      });
    });
  })();

  // Photo sliders (itinerary days + the accommodations gallery)
  document.querySelectorAll('.photo-slider').forEach(function (slider) {
    var track = slider.querySelector('.photo-slider__track');
    var slides = slider.querySelectorAll('.photo-slider__slide');
    var dots = slider.querySelectorAll('.photo-slider__dot');
    var total = slides.length;
    var index = 0;

    function goTo(i) {
      index = (i + total) % total;
      track.style.transform = 'translateX(-' + (index * 100) + '%)';
      dots.forEach(function (d, di) { d.classList.toggle('is-active', di === index); });
    }

    slider.querySelector('.photo-slider__arrow--prev').addEventListener('click', function () { goTo(index - 1); });
    slider.querySelector('.photo-slider__arrow--next').addEventListener('click', function () { goTo(index + 1); });
    dots.forEach(function (dot, di) { dot.addEventListener('click', function () { goTo(di); }); });

    // exposed so the lightbox can hand the slider back on whatever photo the
    // viewer was last looking at
    slider.goTo = goTo;
  });

  /* Gallery --------------------------------------------------------------
     An endless strip. Three copies of the set are laid end to end on a rail,
     the reader is parked in the middle one, and the rail is moved back by
     exactly one set whenever they travel into an outer copy. The jump lands
     on an identical photo at an identical offset, so there is nothing to
     see: no first photo, no last photo, and never a bare margin at either
     edge however wide the window gets.

     Everything visible is a function of one number — which photo the strip
     is resting on. The rail's transform and each photo's opacity are both
     derived from it, so the two can never disagree, and a press is just
     `at += 1` followed by a re-render. There is no scroll position to keep
     in sync and nothing for the browser's own scrolling to fight.

     Runs last, so dlg.openGallery already exists. */
  (function () {
    var track = document.querySelector('.gallery__track');
    var rail = document.querySelector('.gallery__rail');
    if (!track || !rail) return;

    var originals = Array.prototype.slice.call(rail.querySelectorAll('.gallery__slide'));
    var n = originals.length;
    if (!n) return;

    var prev = document.querySelector('.gallery__arrow--prev');
    var next = document.querySelector('.gallery__arrow--next');
    var dlg = document.querySelector('.lightbox');
    /* The lightbox is fed the originals only, so it counts 1 / 10 rather
       than 1 / 30 and a click on a copy opens the photo it is a copy of. */
    var photos = originals.map(function (s) { return s.querySelector('img'); });

    // The copies are decoration: the photo is already in the tree once, and
    // a screen reader should not be read the same ten captions three times.
    function copy(before) {
      var frag = document.createDocumentFragment();
      originals.forEach(function (s) {
        var c = s.cloneNode(true);
        c.setAttribute('aria-hidden', 'true');
        frag.appendChild(c);
      });
      if (before) rail.insertBefore(frag, originals[0]);
      else rail.appendChild(frag);
    }
    copy(true);
    copy(false);

    var slides = Array.prototype.slice.call(rail.querySelectorAll('.gallery__slide'));
    var at = n;          // the photo the strip is resting on, into `slides`
    var moving = false;  // a press is mid-flight; ignore further presses

    function gap() { return parseFloat(getComputedStyle(rail).columnGap) || 0; }
    function slideW() { return slides[0].getBoundingClientRect().width; }
    function pitch() { return slideW() + gap(); }

    // Distance along the rail to slide k. Measured off the DOM rather than
    // computed from widths and gaps, so it stays right whatever the
    // breakpoints do to either and nothing can accumulate.
    function along(k) { return slides[k].offsetLeft - slides[0].offsetLeft; }
    function setSize() { return along(n); }

    /* Where the resting group sits. How many photos are whole at once is a
       layout decision, so it lives in the stylesheet as --shown and this only
       centres however many that is — dropping one at a time if the width
       cannot actually hold them, which is what makes the narrow end degrade
       to a single centred photo with a sliver on each side rather than one
       fat margin. */
    function edge() {
      var w = track.clientWidth;
      var gutter = parseFloat(getComputedStyle(track).getPropertyValue('--gutter')) || 24;
      var shown = parseFloat(getComputedStyle(track).getPropertyValue('--shown')) || 1;
      var sw = slideW(), g = gap();
      while (shown > 1 && shown * sw + (shown - 1) * g > w - gutter * 2) shown--;
      return Math.max(gutter, (w - (shown * sw + (shown - 1) * g)) / 2);
    }

    function setX(x) { rail.style.transform = 'translate3d(' + x + 'px, 0, 0)'; }
    /* Where the rail actually is this frame, read off the computed
       transform rather than the inline one. Mid-transition the inline value
       is already the destination, so normalise() would have shifted from a
       place the strip had not reached yet and snapped it there. Reading the
       live matrix is what lets a press land while another is still running.
       While dragging the two agree, so nothing changes there. */
    function currentX() {
      var t = getComputedStyle(rail).transform;
      if (t && t !== 'none') {
        var m = /matrix(3d)?\(([^)]+)\)/.exec(t);
        if (m) {
          var parts = m[2].split(',');
          var v = parseFloat(m[1] ? parts[12] : parts[4]);
          if (!isNaN(v)) return v;
        }
      }
      var inline = /translate3d\((-?[\d.]+)px/.exec(rail.style.transform);
      return inline ? parseFloat(inline[1]) : edge() - along(at);
    }

    /* One frame of the strip. Both halves of it — where the rail sits and
       which photos are whole — come out of the same number, so they cannot
       disagree. Two states, decided per frame, never a ramp. */
    function paint(x) {
      var w = track.clientWidth, sw = slideW();
      setX(x);
      for (var k = 0; k < slides.length; k++) {
        var left = x + along(k);
        // half a pixel of slack: --edge is fractional at most widths
        slides[k].classList.toggle('is-whole', left >= -0.5 && left + sw <= w + 0.5);
      }
      drawProgress();
    }
    function render() { paint(edge() - along(at)); }

    /* Progress ----------------------------------------------------------
       Width is the share of the set the window shows, measured off the two
       real numbers rather than counted in photos. Position maps the ten
       photos across the travel the mark has, so the first sits flush left
       and the last flush right. */
    var progress = document.querySelector('.gallery__progress');
    var bar = progress && progress.querySelector('.gallery__progress-bar');
    var lastIdx = null;
    function drawProgress() {
      if (!bar) return;
      var railW = progress.clientWidth;
      if (!railW || !setSize()) return;
      var idx = ((at % n) + n) % n;
      var w = Math.min(1, track.clientWidth / setSize()) * railW;
      var x = n > 1 ? (idx / (n - 1)) * (railW - w) : 0;
      /* Coming back to the first photo is a jump, not a journey. Sliding the
         mark the whole way back would say the strip travelled backwards,
         which is the opposite of what the press did. The first draw counts
         as one too: the mark should already be its size when the section
         arrives, not grow into it. */
      var wrapped = lastIdx === null || Math.abs(idx - lastIdx) > 1;
      if (wrapped) progress.classList.add('is-jumping');
      bar.style.width = w + 'px';
      bar.style.transform = 'translateX(' + x + 'px)';
      if (wrapped) { void bar.offsetWidth; progress.classList.remove('is-jumping'); }
      lastIdx = idx;
    }

    // Repaint with no transition anywhere, for the loop jump and the first
    // paint. The reflow between is what makes the browser take the silent
    // frame before animations are allowed again.
    function silently(fn) {
      rail.classList.add('is-jumping');
      fn();
      void rail.offsetWidth;
      rail.classList.remove('is-jumping');
    }

    /* Bring the index back into the middle copy, carrying whatever the rail
       is currently showing with it. Both move by exactly one set and the
       photo underneath is identical, so the swap is invisible — including
       part-way through a drag, which is why it shifts the live transform
       rather than snapping to the canonical one. */
    function normalise() {
      if (at >= n && at < n * 2) return;
      var x = currentX();
      var forward = at < n;
      at += forward ? n : -n;
      var shifted = x + (forward ? -setSize() : setSize());
      /* Repaint, not just re-position. Shifting the rail by one set hands
         every slot over to a different element — the photo standing in it is
         identical and has not moved, but the element carrying `is-whole` is
         now off screen and the one that replaced it is not marked. Leaving
         the classes to the next render made all of them change at once with
         the fade running, so the entire strip dropped to .4 together on the
         way round. It is a relabelling, not a state change, and it belongs
         inside the silent frame with the transform. */
      silently(function () { paint(shifted); });
    }

    var lastW = 0;
    function relayout() {
      if (track.clientWidth === lastW) return;
      lastW = track.clientWidth;
      silently(render);
    }

    var timer = null;
    function settle() {
      if (!moving) return;
      moving = false;
      normalise();
    }
    function go(steps) {
      normalise();          // always start with a whole set of runway ahead
      at += steps;
      normalise();          // a long flick can overshoot the copy in one go
      moving = true;
      render();
      /* transitionend is the normal release. This is the one that has to
         exist anyway: with reduced motion there is no transition to end, and
         a background tab may never fire one. Without it a single press would
         lock the strip for good. */
      clearTimeout(timer);
      timer = setTimeout(settle, 700);
    }

    /* One photo per press. Paging by the full viewport would skip past
       photos the reader has only half seen, which is exactly the ones the
       dimming is inviting them towards.

       A press that lands while one is still running now re-aims instead of
       being dropped. Refusing it was costing every press inside the half
       second after the last one, so pressing through the strip at any pace
       above slow felt like the arrow was ignoring you. It cannot land
       anywhere uncomposed: render() always goes to edge() - along(at), the
       same frame a resting strip would be in, and the rail simply
       transitions there from wherever it has got to. */
    function step(dir) {
      go(dir);
    }
    prev.addEventListener('click', function () { step(-1); });
    next.addEventListener('click', function () { step(1); });

    /* The rail is the only thing whose transform transitions, but the slides
       transition opacity, so filter — otherwise the last photo to finish
       fading would release the lock a little late. */
    rail.addEventListener('transitionend', function (e) {
      if (e.target !== rail || e.propertyName !== 'transform') return;
      clearTimeout(timer);
      settle();
    });

    /* Dragging ----------------------------------------------------------
       Touch and pen only. A phone that does not answer the finger reads as
       broken, whatever the arrows do. A mouse is deliberately left out: on a
       strip where clicking a photo opens it, a drag and a click are the same
       gesture until it is too late to tell them apart — and on a desktop the
       arrows are meant to be the way through.

       The axis is decided once, on the first few pixels of movement, and a
       gesture that turns out to be vertical is handed straight back to the
       page. touch-action: pan-y in the stylesheet is the other half of that
       deal: the browser keeps vertical scrolling and gives us the rest. */
    var drag = null;
    var AXIS = 6;      // px before the gesture commits to an axis
    var FLICK = 40;    // px that count as a deliberate short flick

    track.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'mouse' || moving) return;
      drag = { x: e.clientX, y: e.clientY, base: currentX(), axis: null };
    });

    track.addEventListener('pointermove', function (e) {
      if (!drag) return;
      var dx = e.clientX - drag.x, dy = e.clientY - drag.y;
      if (drag.axis === null) {
        if (Math.abs(dx) < AXIS && Math.abs(dy) < AXIS) return;
        drag.axis = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
        if (drag.axis === 'x') rail.classList.add('is-dragging');
        else { drag = null; return; }
      }
      setX(drag.base + dx);
    }, { passive: true });

    function release(e) {
      if (!drag) return;
      var d = drag; drag = null;
      rail.classList.remove('is-dragging');
      if (d.axis !== 'x') return;
      var dx = e.clientX - d.x;
      // the click that follows this gesture belongs to the drag, not to the
      // photo under the finger
      rail.dataset.dragged = '1';
      /* Round to whole photos, so letting go always lands on a composition.
         A short flick that did not travel a full photo still counts as one:
         the intent was unmistakable and refusing it feels like the strip is
         stuck. */
      var steps = Math.round(-dx / pitch());
      if (!steps && Math.abs(dx) > FLICK) steps = dx < 0 ? 1 : -1;
      go(steps);
    }
    track.addEventListener('pointerup', release);
    track.addEventListener('pointercancel', function () {
      if (!drag) return;
      drag = null;
      rail.classList.remove('is-dragging');
      go(0);          // put the rail back where it was
    });

    window.addEventListener('resize', relayout);
    // held in a variable so it is not collected while the section is alive
    var ro = null;
    if (window.ResizeObserver) { ro = new ResizeObserver(relayout); ro.observe(track); }
    /* The slides are aspect-ratio boxes, so the layout is already right
       before the photos arrive; the only thing an arriving photo can move
       is the page's scrollbar. This used to call silently(render), which
       kills the transition and snaps the rail to its resting position — and
       the photos are lazy, so they finish downloading exactly while the
       reader is pressing through them. A press would travel 77% of the way
       and then jump the rest, at a different point every time depending on
       what the network did. That was the strip feeling stuck.

       relayout() is the same correction with the guard already written: it
       returns immediately unless the track's width really changed. */
    slides.forEach(function (s) {
      s.querySelector('img').addEventListener('load', relayout);
    });

    /* The crop is the strip's composition; the lightbox is the photograph.
       It sizes itself from naturalWidth/Height, so opening a square slide
       gives back the full landscape frame it was cut from. A photo that was
       dragged is not a photo that was clicked, so a gesture that moved the
       rail does not also open the viewer. */
    if (dlg && dlg.openGallery) {
      slides.forEach(function (s, i) {
        s.querySelector('img').addEventListener('click', function () {
          if (rail.dataset.dragged === '1') { rail.dataset.dragged = '0'; return; }
          dlg.openGallery(photos, i % n);
        });
      });
    }

    lastW = track.clientWidth;
    silently(render);
  })();

  /* FAQ ------------------------------------------------------------------
     The button owns the state and the class owns the animation. aria-expanded
     is the same fact the class is, said to a screen reader. */
  document.querySelectorAll('.faq__trigger').forEach(function (btn) {
    var item = btn.closest('.faq');
    btn.addEventListener('click', function () {
      var open = !item.classList.contains('is-open');
      item.classList.toggle('is-open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  /* Newsletter ------------------------------------------------------------
     No signup provider is wired up yet, and a bare submit would reload the
     page with the subscriber's address sitting in the query string. Swallow
     it until there is an endpoint to post to. */
  document.querySelectorAll('.footer-news__form').forEach(function (form) {
    form.addEventListener('submit', function (e) { e.preventDefault(); });
  });

  /* Image reveal ---------------------------------------------------------
     An image that has already decoded — cached, or above the fold — is left
     alone: fading in something the reader is already looking at reads as a
     glitch rather than as polish. The lightbox is skipped because its src is
     swapped on every arrow press, and a one-shot listener would fire once and
     then leave the rest of the gallery invisible. */
  document.querySelectorAll('img:not(.lightbox__img)').forEach(function (img) {
    if (img.complete) return;
    img.classList.add('img-reveal');
    function done() { img.classList.add('is-loaded'); }
    img.addEventListener('load', done);
    img.addEventListener('error', done);   // a broken image must not stay invisible
  });

  /* Sticky booking bar: js/booking-bar.js (reads TIERS). */

  /* Testimonials ---------------------------------------------------------
     TODO — all three entries are placeholders. Replace them with real guest
     words, names and photos before this page goes anywhere public: an
     invented testimonial is the one kind of placeholder that does harm if it
     ships, because a reader cannot tell it from a real one. The copy below
     is written to be obviously unusable for exactly that reason, while still
     running to the length a real quote would.

     The faces are stock portraits off Unsplash's CDN, chosen as women across
     the 35-65 audience so the row is cast the way the real one will be — the
     card has to be judged with the right faces in it, not just with faces. They are strangers: shipping them beside
     a name and a retreat date would be inventing a person, so they go out with
     the quotes. Setting `photo` back to '' renders the grey circle instead. */
  (function () {
    var TESTIMONIALS = R.testimonials || [];   /* WP: from the retreat post (testimonials) */

    var grid = document.getElementById('quotes-grid');
    if (!grid) return;

    function el(tag, cls, text) {
      var n = document.createElement(tag);
      if (cls) n.className = cls;
      if (text != null) n.textContent = text;
      return n;
    }

    TESTIMONIALS.forEach(function (t) {
      var card = el('figure', 'quote-card');

      /* Real reviews (docs/testimonials-by-retreat.md) come with no
         portrait, and a stock face beside a real name would invent a
         person, so a card shows a photo only when one is given. */
      if (t.photo) {
        var img = el('img', 'quote-card__photo');
        img.src = t.photo;
        img.alt = t.name;
        img.loading = 'lazy';
        card.appendChild(img);
      }

      card.appendChild(el('blockquote', 'quote-card__quote', t.quote));

      var by = el('figcaption', 'quote-card__by');
      by.appendChild(el('p', 'quote-card__name', t.name));
      if (t.detail) by.appendChild(el('p', 'quote-card__detail', t.detail));
      card.appendChild(by);

      grid.appendChild(card);
    });
  })();

  /* Hero label: the tier and this date's status are written into the
     hero by build_page.py, the same label the retreat cards carry. */
