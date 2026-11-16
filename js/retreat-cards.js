/* ============================================================
   YAWW — RETREAT CARD COMPONENT + DATA HELPERS

   Everything that turns a row of data/retreats.js into markup lives
   here, and nowhere else. Load order on a page:

     <script src="data/locations.js"></script>
     <script src="data/teachers.js"></script>
     <script src="data/retreats.js"></script>
     <script src="js/retreat-cards.js"></script>

   Plain classic scripts and window globals rather than modules or
   fetch(), so the prototype still works opened straight off disk as
   file:// — which is how it gets reviewed.

   The markup below is byte-for-byte the markup that used to be
   hardcoded/inlined on each page. Two card designs exist on the site
   and both are kept exactly as they were:

     variant "narrow"   .card.card--narrow   — home rail + /retreats
     variant "location" .loc-card            — location pages

   Cards do NOT link to individual retreat pages: those do not exist
   yet. `retreat.url` carries the eventual permalink, and for now the
   card links to the retreat's location page (falling back to the full
   listing when that location has no page built).
   ============================================================ */
(function (window, document) {
  "use strict";

  var YAWW = window.YAWW || (window.YAWW = {});

  /* ---------- catalogs ------------------------------------------------ */

  function locations() { return window.YAWW_LOCATIONS || []; }
  function teachers()  { return window.YAWW_TEACHERS  || []; }
  function retreats()  { return window.YAWW_RETREATS  || []; }
  function regions()   { return window.YAWW_REGIONS   || []; }

  function byId(list, id) {
    for (var i = 0; i < list.length; i++) if (list[i].id === id) return list[i];
    return null;
  }
  function getLocation(id) { return byId(locations(), id); }
  function getTeacher(id)  { return byId(teachers(), id); }

  /* The short label the location-page card shows on its own line:
     "Reykjavík & Vik, Iceland" -> "Reykjavík & Vik". The country is
     already in the page heading there, so the design drops it. */
  function locationShortLabel(location) {
    if (!location) return "";
    var i = location.displayName.lastIndexOf(",");
    return i === -1 ? location.displayName : location.displayName.slice(0, i).trim();
  }

  /* ---------- collections --------------------------------------------- */

  var COLLECTIONS = {
    "deep-dives":           { label: "Deep Dives" },
    "signature-collection": { label: "Signature Collection" }
  };
  function collectionLabel(id) {
    return (COLLECTIONS[id] || COLLECTIONS["deep-dives"]).label;
  }

  /* ---------- status -------------------------------------------------- */

  /* Returns null when there is no badge to show, otherwise
     { label, flag } where flag is the existing CSS modifier. */
  function statusBadge(retreat) {
    switch (retreat.status) {
      case "sold_out":
        return { label: "Sold out", flag: "soldout" };
      case "filling_fast":
        return { label: "Filling fast", flag: "scarce" };
      case "spaces_left":
        var n = retreat.spacesLeft;
        if (!n) return null;
        return { label: n + (n === 1 ? " space left" : " spaces left"), flag: "scarce" };
      case "new":
        return { label: "New retreat", flag: "scarce" };
      default:                       // "available" and anything unknown
        return null;
    }
  }

  /* ---------- dates --------------------------------------------------- */

  var MONTHS = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];

  /* ISO -> parts, parsed by hand. new Date("2026-12-01") is UTC midnight,
     which in any negative-offset timezone renders as November 30. */
  function parts(iso) {
    var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(iso || ""));
    if (!m) return null;
    return { y: +m[1], m: +m[2] - 1, d: +m[3] };
  }
  function sortKey(iso) {
    var p = parts(iso);
    return p ? p.y * 10000 + p.m * 100 + p.d : 0;
  }

  /* The one place a date range becomes text. Never store these strings.
       same month      -> "December 1 – 6, 2026"
       month crossing  -> "January 29 – February 2, 2027"
       year crossing   -> "December 29, 2026 – January 3, 2027"
     opts.spaced:false uses the tight dash the .loc-card design uses
     ("October 11–19, 2026"); the default is the spaced dash every
     .card--narrow has always used. */
  function formatDateRange(startISO, endISO, opts) {
    var a = parts(startISO), b = parts(endISO);
    if (!a) return "";
    var dash = (opts && opts.spaced === false) ? "–" : " – ";
    if (!b) return MONTHS[a.m] + " " + a.d + ", " + a.y;
    if (a.y !== b.y) {
      return MONTHS[a.m] + " " + a.d + ", " + a.y + dash +
             MONTHS[b.m] + " " + b.d + ", " + b.y;
    }
    if (a.m !== b.m) {
      return MONTHS[a.m] + " " + a.d + dash + MONTHS[b.m] + " " + b.d + ", " + b.y;
    }
    return MONTHS[a.m] + " " + a.d + dash + b.d + ", " + b.y;
  }
  function retreatDateLabel(retreat, opts) {
    return formatDateRange(retreat.startDate, retreat.endDate, opts);
  }

  /* ---------- seasons -------------------------------------------------- */

  /* Meteorological seasons off the START month. A winter retreat is
     labelled with the pair of years it straddles, so December 2026 and
     January 2027 land in the same bucket — which is how the listing
     has always grouped them. */
  var SEASONS = ["Winter", "Spring", "Summer", "Fall"];
  function seasonOf(retreat) {
    var p = parts(retreat.startDate);
    if (!p) return null;
    var idx = Math.floor(((p.m + 1) % 12) / 3);     // Dec,Jan,Feb -> 0 ...
    var name = SEASONS[idx];
    if (name !== "Winter") return { name: name, y1: p.y, y2: p.y };
    // December opens the winter; January/February close the one before.
    var y1 = p.m === 11 ? p.y : p.y - 1;
    return { name: name, y1: y1, y2: y1 + 1 };
  }
  /* Two label styles, because the two pages word it differently and
     neither design is being changed:
       "listing" -> "Winter, 2026 / 2027"   (/retreats group headings)
       "filter"  -> "Winter 2026 / 27"      (home When filter) */
  function seasonLabel(retreat, style) {
    var s = seasonOf(retreat);
    if (!s) return "";
    var cross = s.y1 !== s.y2;
    if (style === "filter") {
      return s.name + " " + (cross ? s.y1 + " / " + String(s.y2).slice(2) : s.y1);
    }
    return s.name + ", " + (cross ? s.y1 + " / " + s.y2 : s.y1);
  }

  /* ---------- queries -------------------------------------------------- */

  /* "Past" is decided on the END date: a retreat that is under way is
     still a retreat that is coming up for everyone reading about it. */
  function todayKey() {
    var now = new Date();
    return now.getFullYear() * 10000 + now.getMonth() * 100 + now.getDate();
  }
  function isUpcoming(retreat) { return sortKey(retreat.endDate) >= todayKey(); }
  function byStartDate(a, b) {
    return sortKey(a.startDate) - sortKey(b.startDate) ||
           String(a.officialName).localeCompare(String(b.officialName));
  }

  function getUpcomingRetreats() {
    return retreats().filter(isUpcoming).sort(byStartDate);
  }
  function getRetreatsByLocation(locationId) {
    return getUpcomingRetreats().filter(function (r) { return r.locationId === locationId; });
  }
  /* Used by the teacher pages that come next. Matches co-taught
     retreats too — teacherIds is always an array. */
  function getRetreatsByTeacher(teacherId) {
    return getUpcomingRetreats().filter(function (r) {
      return (r.teacherIds || []).indexOf(teacherId) !== -1;
    });
  }

  /* ---------- teacher line --------------------------------------------- */

  /* "Alicia Delaney" · "Genevieve Morin & Rachel Gray" · for an
     unannounced teacher, the catalog's placeholder ("a YAWW
     instructor") rather than dropping the line: the card body is a
     centred stack and a missing line visibly shortens one card in an
     otherwise even row. */
  function teacherNames(retreat) {
    return (retreat.teacherIds || []).map(function (id) {
      var t = getTeacher(id);
      if (!t) return id;
      return t.name || t.placeholderLabel || "a YAWW instructor";
    });
  }
  function teacherLine(retreat) { return teacherNames(retreat).join(" & "); }

  /* ---------- links ----------------------------------------------------- */

  /* No individual retreat pages exist yet, so a card points at its
     location page. `fallbackUrl` is where a location with no page of
     its own goes (the full listing, as before); `currentUrl` lets a
     page avoid linking a card straight back to itself. */
  function retreatHref(retreat, opts) {
    opts = opts || {};
    var loc = getLocation(retreat.locationId);
    var href = loc && loc.pageUrl ? loc.pageUrl : null;
    if (href && opts.currentUrl && href === opts.currentUrl) href = null;
    if (!href && opts.linkOnlyWhenPage) return "";
    return href || opts.fallbackUrl || "retreats.html";
  }

  /* ---------- markup ---------------------------------------------------- */

  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;")
      .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  /* Where a card's picture comes from, in order: the retreat's own
     thumbnail, then its location's photo, then nothing (the caller
     paints the gradient placeholder). Returns the 1x source plus the
     retina one when there is a separate file for it. */
  function retreatMedia(retreat, location) {
    if (retreat && retreat.image) return { src: retreat.image, src2x: retreat.image2x || null };
    if (location && location.image) return { src: location.image, src2x: null };
    return null;
  }
  /* srcset lets a non-retina screen skip the double-size file, which
     is most of the page weight on a grid of these. */
  function imgHTML(media, alt) {
    var srcset = media.src2x
      ? ' srcset="' + esc(media.src) + ' 1x, ' + esc(media.src2x) + ' 2x"'
      : '';
    return '<img src="' + esc(media.src) + '"' + srcset +
           ' alt="' + esc(alt) + '" loading="lazy" decoding="async">';
  }

  function placeholderStyle(location) {
    var hue = (location && location.placeholderHue != null) ? location.placeholderHue : 200;
    return "background: linear-gradient(155deg, hsl(" + hue + " 32% 80%), hsl(" + hue + " 26% 60%));";
  }
  function flagHTML(retreat) {
    var badge = statusBadge(retreat);
    if (!badge) return "";
    return '<span class="card__flag card__flag--' + badge.flag + '">' + esc(badge.label) + '</span>';
  }

  /* --- variant "narrow": the .card.card--narrow design ---------------- */
  function narrowCardHTML(retreat, opts) {
    var loc = getLocation(retreat.locationId);
    var alt = retreat.officialName + (loc ? " — " + loc.displayName : "");
    var media = retreatMedia(retreat, loc);
    var overlay = '<span class="card__badge">' + esc(collectionLabel(retreat.collection)) + '</span>' +
                  flagHTML(retreat);
    var inner =
      '<article class="card card--narrow">' +
        (media
          ? '<div class="card__media">' + imgHTML(media, alt) + overlay + '</div>'
          : '<div class="card__media" style="' + placeholderStyle(loc) + '">' + overlay + '</div>') +
        '<div class="card__body">' +
          '<div class="card__title">' + esc(retreat.officialName) + '</div>' +
          '<div class="card__loc">' + esc(loc ? loc.displayName : "") + '</div>' +
          '<div class="card__date">' + esc(retreatDateLabel(retreat)) + '</div>' +
          '<div class="card__teacher">with ' + esc(teacherLine(retreat)) + '</div>' +
        '</div>' +
      '</article>';

    var href = retreatHref(retreat, opts);
    return href ? '<a class="result-link" href="' + esc(href) + '">' + inner + '</a>' : inner;
  }

  /* --- variant "location": the .loc-card design on location pages ----- */
  function locationCardHTML(retreat, opts) {
    var loc = getLocation(retreat.locationId);
    var alt = retreat.officialName + (loc ? " in " + loc.name : "");
    var href = retreatHref(retreat, opts);
    var lMedia = retreatMedia(retreat, loc);
    var body =
      (lMedia
        ? '<div class="loc-card__media">' + imgHTML(lMedia, alt) + '</div>'
        : '<div class="loc-card__media" style="' + placeholderStyle(loc) + '"></div>') +
      '<h3 class="loc-card__name">' + esc(retreat.officialName) + '</h3>' +
      '<p class="loc-card__where">' + esc(locationShortLabel(loc)) + '</p>' +
      '<p class="loc-card__date">' + esc(retreatDateLabel(retreat, { spaced: false })) + '</p>' +
      '<p class="loc-card__teacher">with ' + esc(teacherLine(retreat)) + '</p>';

    return '<article class="loc-card">' +
             (href ? '<a class="loc-card__link" href="' + esc(href) + '">' + body + '</a>'
                   : '<div class="loc-card__link">' + body + '</div>') +
           '</article>';
  }

  /* THE card component. Every retreat card on the site comes through
     here, so a change to a card is a change in one place.
       opts.variant          "narrow" (default) | "location"
       opts.fallbackUrl      where a location with no page points
       opts.currentUrl       don't link a card back to the current page
       opts.linkOnlyWhenPage render an unlinked card when there is no
                             location page (what /retreats did before) */
  function renderRetreatCard(retreat, opts) {
    opts = opts || {};
    return opts.variant === "location"
      ? locationCardHTML(retreat, opts)
      : narrowCardHTML(retreat, opts);
  }

  /* The list-view row on /retreats — same data, same markup as before. */
  function renderRetreatRow(retreat, opts) {
    var loc = getLocation(retreat.locationId);
    var badge = statusBadge(retreat);
    var inner =
      '<div class="list-row">' +
        '<div class="r-namecell">' +
          '<span class="r-tier">' + esc(collectionLabel(retreat.collection)) + '</span>' +
          '<span class="r-name">' + esc(retreat.officialName) + '</span>' +
        '</div>' +
        '<span class="r-loc">' + esc(loc ? loc.displayName : "") + '</span>' +
        '<span class="r-date">' + esc(retreatDateLabel(retreat)) + '</span>' +
        '<span class="r-teacher">with ' + esc(teacherLine(retreat)) + '</span>' +
        '<span class="r-avail">' + esc(badge ? badge.label : "") + '</span>' +
      '</div>';
    var href = retreatHref(retreat, opts);
    return href ? '<a class="result-link" href="' + esc(href) + '">' + inner + '</a>' : inner;
  }

  /* ---------- waitlist ------------------------------------------------- */

  /* Shared by the location-page empty state. No backend yet: validate,
     swap in the success message, done.

     TODO(backend): POST the payload below to the Zapier catch hook (or
     whatever endpoint the WP build exposes) and only then show the
     success state. Everything needed is already in `payload`.
         fetch(YAWW.WAITLIST_ENDPOINT, {
           method: "POST",
           headers: { "Content-Type": "application/json" },
           body: JSON.stringify(payload)
         })
  */
  YAWW.WAITLIST_ENDPOINT = null;   // TODO(backend): set to the webhook URL

  function wireWaitlistForm(form) {
    if (!form || form.dataset.yawwWired) return;
    form.dataset.yawwWired = "1";

    var errorEl   = form.querySelector("[data-waitlist-error]");
    var successEl = document.getElementById(form.getAttribute("data-waitlist-success") || "");

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var first = form.elements["first_name"];
      var email = form.elements["email"];
      var loc   = form.elements["location"];

      var msg = "";
      if (!first.value.trim()) msg = "Please add your first name.";
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) msg = "Please add a valid email address.";

      if (msg) {
        if (errorEl) { errorEl.textContent = msg; errorEl.hidden = false; }
        (msg.indexOf("name") > -1 ? first : email).focus();
        return;
      }
      if (errorEl) { errorEl.textContent = ""; errorEl.hidden = true; }

      var payload = {
        firstName: first.value.trim(),
        email: email.value.trim(),
        locationId: loc ? loc.value : "",
        source: window.location.pathname
      };
      // Same hook every other interaction on the site dispatches.
      document.dispatchEvent(new CustomEvent("yaww:track", {
        detail: { event: "waitlist_submit", locationId: payload.locationId }
      }));

      if (successEl) {
        form.hidden = true;
        successEl.hidden = false;
        successEl.setAttribute("tabindex", "-1");
        successEl.focus();
      }
    });
  }

  /* ---------- facets (filters read these, never the raw strings) ------- */

  /* All catalog locations, grouped the way the Destination filter shows
     them. Locations with no announced dates stay in the list on
     purpose — that is the muted "No dates yet" row. */
  function locationGroups() {
    return regions().map(function (region) {
      return {
        title: region,
        items: locations()
          .filter(function (l) { return l.region === region; })
          .sort(function (a, b) { return a.name.localeCompare(b.name); })
      };
    }).filter(function (g) { return g.items.length; });
  }
  /* Named teachers only — the "tbd" stand-in is not something to
     filter by. */
  function teacherRoster() {
    return teachers()
      .filter(function (t) { return t.name && !t.unannounced; })
      .sort(function (a, b) { return a.name.localeCompare(b.name); });
  }
  /* Season buckets in chronological order, derived from the retreats
     themselves rather than listed by hand. */
  function seasonOptions(style) {
    var seen = {}, out = [];
    getUpcomingRetreats().forEach(function (r) {
      var label = seasonLabel(r, style);
      if (label && !seen[label]) { seen[label] = true; out.push(label); }
    });
    return out;
  }

  /* ---------- exports --------------------------------------------------- */

  YAWW.getLocation            = getLocation;
  YAWW.getTeacher             = getTeacher;
  YAWW.locationShortLabel     = locationShortLabel;
  YAWW.collectionLabel        = collectionLabel;
  YAWW.statusBadge            = statusBadge;
  YAWW.formatDateRange        = formatDateRange;
  YAWW.retreatDateLabel       = retreatDateLabel;
  YAWW.seasonOf               = seasonOf;
  YAWW.seasonLabel            = seasonLabel;
  YAWW.getUpcomingRetreats    = getUpcomingRetreats;
  YAWW.getRetreatsByLocation  = getRetreatsByLocation;
  YAWW.getRetreatsByTeacher   = getRetreatsByTeacher;
  YAWW.teacherNames           = teacherNames;
  YAWW.teacherLine            = teacherLine;
  YAWW.retreatHref            = retreatHref;
  YAWW.retreatMedia           = retreatMedia;
  YAWW.renderRetreatCard      = renderRetreatCard;
  YAWW.renderRetreatRow       = renderRetreatRow;
  YAWW.wireWaitlistForm       = wireWaitlistForm;
  YAWW.locationGroups         = locationGroups;
  YAWW.teacherRoster          = teacherRoster;
  YAWW.seasonOptions          = seasonOptions;
  YAWW.esc                    = esc;

})(window, document);
