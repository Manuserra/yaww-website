/* Retreats search prototypes: shared data + results rendering.
   Real data from data/*.js through js/retreat-cards.js (window.YAWW).
   Selection model used by every option:
     { tier: "all"|"deep-dives"|"signature-collection",
       places: [names], months: ["2027-05"], teachers: [names], text: "" }
   OR inside a facet, AND across facets. */
(function () {
  var Y = window.YAWW;
  var esc = Y.esc;
  var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

  function monthKey(iso) { return iso.slice(0, 7); }
  function monthLabel(key, short) {
    var m = MONTHS[+key.slice(5, 7) - 1];
    return (short ? m.slice(0, 3) : m) + " " + key.slice(0, 4);
  }

  var rows = Y.getUpcomingRetreats().map(function (r) {
    var loc = Y.getLocation(r.locationId);
    return {
      r: r, loc: loc, place: loc ? loc.name : "", tier: r.collection,
      month: monthKey(r.startDate), teachers: Y.teacherNames(r),
      season: Y.seasonLabel(r, "listing"),
      haystack: [r.officialName, loc && loc.displayName, loc && loc.country, loc && loc.region, Y.teacherNames(r).join(" ")].join(" ").toLowerCase()
    };
  });

  function count(pred) { return rows.filter(pred).length; }

  var locations = [];
  Y.locationGroups().forEach(function (g) {
    g.items.forEach(function (l) {
      locations.push({
        id: l.id, name: l.name, display: l.displayName, region: g.title, country: l.country,
        image: l.image, thumb: l.image ? "images/location-thumbs/" + l.id + ".jpg" : null,
        hue: l.placeholderHue != null ? l.placeholderHue : 200,
        count: count(function (x) { return x.place === l.name; }),
        waitlist: Y.waitlistHref(l)
      });
    });
  });
  var regions = Y.locationGroups().map(function (g) { return g.title; });

  var teachers = Y.teacherRoster().map(function (t) {
    return { id: t.id, name: t.name, photo: t.avatar || t.photo || null,
             count: count(function (x) { return x.teachers.indexOf(t.name) !== -1; }) };
  });

  var months = [];
  rows.forEach(function (x) { if (months.indexOf(x.month) === -1) months.push(x.month); });
  months.sort();
  months = months.map(function (k) { return { key: k, label: monthLabel(k), short: monthLabel(k, true), count: count(function (x) { return x.month === k; }) }; });

  function blank() { return { tier: "all", places: [], months: [], teachers: [], text: "" }; }
  function match(x, s, skip) {
    if (skip !== "tier" && s.tier !== "all" && x.tier !== s.tier) return false;
    if (skip !== "places" && s.places.length && s.places.indexOf(x.place) === -1) return false;
    if (skip !== "months" && s.months.length && s.months.indexOf(x.month) === -1) return false;
    if (skip !== "teachers" && s.teachers.length && !x.teachers.some(function (t) { return s.teachers.indexOf(t) !== -1; })) return false;
    if (skip !== "text" && s.text && x.haystack.indexOf(s.text.toLowerCase()) === -1) return false;
    return true;
  }
  function filter(s) { return rows.filter(function (x) { return match(x, s); }); }
  /* Faceted count: what you'd get adding this value, given the other facets. */
  function facetCount(s, facet, value) {
    return rows.filter(function (x) {
      if (!match(x, s, facet)) return false;
      if (facet === "places") return x.place === value;
      if (facet === "months") return x.month === value;
      if (facet === "teachers") return x.teachers.indexOf(value) !== -1;
      if (facet === "tier") return value === "all" || x.tier === value;
      return true;
    }).length;
  }

  function plural(n, word) { word = word || "retreat"; return n + " " + word + (n === 1 ? "" : "s"); }
  function thumb(l, size) {
    return '<span class="thumb" aria-hidden="true" style="width:' + size + 'px;height:' + size + 'px;background:linear-gradient(155deg,hsl(' + l.hue + ' 32% 80%),hsl(' + l.hue + ' 26% 60%))">' +
      (l.thumb ? '<img src="' + esc(l.thumb) + '" alt="" width="' + size + '" height="' + size + '" loading="lazy">' : '') + '</span>';
  }
  function avatar(t, size) {
    var ini = t.name.split(" ").map(function (p) { return p.charAt(0); }).join("").slice(0, 2);
    return '<span class="avatar" aria-hidden="true" style="width:' + size + 'px;height:' + size + 'px">' + esc(ini) +
      (t.photo ? '<img src="' + esc(t.photo) + '" alt="" loading="lazy" onerror="this.remove()">' : '') + '</span>';
  }

  /* Results grouped by season, like the live page. A lone destination
     with no dates gets its waitlist instead of an empty list. */
  function renderResults(el, s, summaryEl, onClear) {
    var list = filter(s);
    var anySet = s.tier !== "all" || s.places.length || s.months.length || s.teachers.length || s.text;
    if (summaryEl) {
      summaryEl.innerHTML = '<span>' + (anySet ? plural(list.length) + " match" + (list.length === 1 ? "es" : "") : plural(list.length) + " coming up") + '</span>' +
        (anySet ? '<button type="button" data-clear-all>Clear all</button>' : '');
      var b = summaryEl.querySelector("[data-clear-all]");
      if (b && onClear) b.addEventListener("click", onClear);
    }
    if (!list.length) {
      var lone = s.places.length === 1 && locations.filter(function (l) { return l.name === s.places[0] && !l.count; })[0];
      el.innerHTML = lone
        ? '<section class="season"><h2 class="season__title">Dates to be announced</h2><div class="grid">' + Y.renderWaitlistCard(Y.getLocation(lone.id)) + '</div></section>'
        : '<div class="empty"><h2>Nothing matches all of that</h2><p>Try removing one of your choices.</p>' + (onClear ? '<button type="button" class="btn" data-clear-all>Clear all</button>' : '') + '</div>';
      var b2 = el.querySelector("[data-clear-all]");
      if (b2 && onClear) b2.addEventListener("click", onClear);
      return;
    }
    var groups = [], by = {};
    list.forEach(function (x) { if (!by[x.season]) { by[x.season] = []; groups.push(x.season); } by[x.season].push(x); });
    el.innerHTML = groups.map(function (g) {
      return '<section class="season"><h2 class="season__title">' + esc(g) + '</h2><div class="grid">' +
        by[g].map(function (x) { return Y.renderRetreatCard(x.r); }).join("") + '</div></section>';
    }).join("");
  }

  function track(name, detail) {
    /* Stand-in for analytics: the live page fires the same event. */
    document.dispatchEvent(new CustomEvent("yaww:track", { detail: Object.assign({ event: name }, detail || {}) }));
  }

  window.P = { Y: Y, esc: esc, rows: rows, locations: locations, regions: regions, teachers: teachers, months: months,
    blank: blank, filter: filter, facetCount: facetCount, plural: plural, thumb: thumb, avatar: avatar,
    renderResults: renderResults, track: track };
})();
