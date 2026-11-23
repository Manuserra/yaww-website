/* ============================================================
   YAWW (WordPress) · retreats listing behaviour
   The inline <script>s of retreats.html (after the Lenis init), verbatim,
   with three edits marked WP:
     · the tier archive (/tier/<slug>/) preselects its collection
       (window.YAWW_LISTING_TIER) and writes its state onto the
       listing's own URL (window.YAWW_LISTING_BASE)
     · location thumbnails resolve from the theme's assets folder
   Keep in step with retreats.html.
   ============================================================ */
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


/* ============================================================
   Retreats listing — one filter system

   collection tabs (tier) + When / Destination / Teacher bar (the
   home's component) → results grouped by season. State lives in the
   URL so any page can deep-link into a filtered list.
   ============================================================ */
(function () {
const Y = window.YAWW;
const esc = Y.esc;
const SEASON_ORDER = Y.seasonOptions("listing");

/* Each description is a list of lines: every entry starts on its own
   line, so the break falls where the reader already pauses. "\u00a0"
   glues names and short phrases ("Deep Dives", "price point", the last
   word) so a line never splits them or ends on a lone word. */
const NB = "\u00a0";
const COLLECTION_COPY = {
  all: { title: "All Retreats", desc: [
    "Every journey we run, in one place.",
    "From intimate Deep" + NB + "Dives to the Signature" + NB + "Collection, find the one that fits" + NB + "you."
  ] },
  "deep-dives": { title: "Deep Dives", desc: [
    "Grounded, intimate retreats in beautiful places.",
    "A focused practice and a warm community, at our most accessible price" + NB + "point."
  ] },
  "signature-collection": { title: "Signature Collection", desc: [
    "Our most exclusive journeys, in rare places and exceptional venues.",
    "Every detail considered. Yoga" + NB + "Adventures at its" + NB + "finest."
  ] }
};

/* Flat view rows so the facets can match on plain values. */
const ROWS = Y.getUpcomingRetreats().map(function (r) {
  const loc = Y.getLocation(r.locationId);
  return { retreat: r, place: loc ? loc.name : "", when: Y.seasonLabel(r, "filter"), teacher: Y.teacherNames(r), tier: r.collection };
});

const TEACHERS = Y.teacherRoster().map(t => t.name);
/* Teacher photos for the dropdown avatars, keyed by name: the square,
   face-centred `avatar` crop when there is one, else the portrait. A
   teacher without either (or whose image fails to load) keeps her
   initials. */
const TEACHER_PHOTO = {};
Y.teacherRoster().forEach(t => { const img = t.avatar || t.photo; if (img) TEACHER_PHOTO[t.name] = img; });
const PLACES = Y.locationGroups().map(g => ({ title: g.title, items: g.items.map(l => l.name) }));
const WHENS = Y.seasonOptions("filter");
/* The months each season actually covers, from the dates themselves
   ("Mar – May"), so "Spring 2027" says which spring. */
const MON = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
const WHEN_SPAN = {};
WHENS.forEach(function (w) {
  const keys = ROWS.filter(v => v.when === w).map(v => v.retreat.startDate.slice(0, 7)).sort();
  if (!keys.length) return;
  const a = MON[+keys[0].slice(5) - 1], b = MON[+keys[keys.length - 1].slice(5) - 1];
  WHEN_SPAN[w] = a === b ? a : a + "\u2009\u2013\u2009" + b;
});
const SEGMENTS = [
  { key: "when",    label: "When",        any: "Any time",    param: "when",        groups: [{ title: "", items: WHENS, avatar: false }] },
  { key: "place",   label: "Destination", any: "Anywhere",    param: "destination", groups: PLACES.map(g => ({ title: g.title, items: g.items, avatar: false })) },
  { key: "teacher", label: "Teacher",     any: "Any teacher", param: "teacher",     groups: [{ title: "", items: TEACHERS, avatar: true }] }
];
const FACETS = ["when", "place", "teacher"];
/* Every axis a pick can clash on: the collection tabs count too. */
const DIMS = ["tier", "when", "place", "teacher"];
const LOCATION_BY_NAME = {};
Y.locationGroups().forEach(g => g.items.forEach(l => { LOCATION_BY_NAME[l.name] = l; }));
const TEACHER_BY_NAME = {};
Y.teacherRoster().forEach(t => { TEACHER_BY_NAME[t.name] = t; });

const state = { tier: "all", when: "", place: "", teacher: "", view: "grid", open: "" };
/* The "we cleared X" line: { text, undo } where undo is the state to go
   back to, or null when there is nothing to undo (a deep link). */
let note = null;

/* ---------- URL <-> state ---------- */
function slug(s) {
  return String(s).toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "")
    .replace(/&/g, " and ").replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");
}
function allValues(def) { return def.groups.flatMap(g => g.items); }
function readURL() {
  const p = new URLSearchParams(location.search);
  const tier = p.get("tier") || window.YAWW_LISTING_TIER /* WP */;
  if (tier && COLLECTION_COPY[tier]) state.tier = tier;
  if (p.get("view") === "list") state.view = "list";
  SEGMENTS.forEach(function (def) {
    const v = p.get(def.param);
    if (!v) return;
    const hit = allValues(def).find(x => slug(x) === slug(v));
    if (hit) state[def.key] = hit;
  });
}
function writeURL() {
  const p = new URLSearchParams();
  if (state.tier !== "all") p.set("tier", state.tier);
  SEGMENTS.forEach(def => { if (state[def.key]) p.set(def.param, slug(state[def.key])); });
  if (state.view === "list") p.set("view", "list");
  const qs = p.toString();
  history.replaceState(null, "", (window.YAWW_LISTING_BASE || location.pathname) + (qs ? "?" + qs : ""));
}

/* ---------- matching ---------- */
function matches(v, s, skip) {
  return (s.tier === "all" || v.tier === s.tier) &&
    (skip === "when"    || !s.when    || v.when === s.when) &&
    (skip === "place"   || !s.place   || v.place === s.place) &&
    (skip === "teacher" || !s.teacher || v.teacher.indexOf(s.teacher) !== -1);
}
function filtered(s) { return ROWS.filter(v => matches(v, s || state)); }
/* Faceted counts: each option counts what you'd get if you picked it,
   given everything else already chosen. */
function facetCount(key, value) {
  return ROWS.filter(function (v) {
    if (!matches(v, state, key)) return false;
    return key === "teacher" ? v.teacher.indexOf(value) !== -1 : v[key] === value;
  }).length;
}
function anyCount(key) { return ROWS.filter(v => matches(v, state, key)).length; }
function plural(n) { return n === 1 ? "1 retreat" : n + " retreats"; }

/* ---------- the latest pick wins ----------
   With 17 dates, nine in ten combinations of these filters are empty,
   and most people narrow by one thing at a time and then switch. So no
   choice that has dates of its own can end in an empty list: picking it
   keeps whatever still fits alongside it and drops what clashes, and
   the note above the results says what went, with Undo. The only empty
   result left is picking something with no dates at all, which is the
   waitlist for it. */
function snapshot(s) { return { tier: s.tier, when: s.when, place: s.place, teacher: s.teacher }; }
function blank() { return { tier: "all", when: "", place: "", teacher: "" }; }
function isSet(s, d) { return d === "tier" ? s.tier !== "all" : !!s[d]; }
function countOf(s) { return filtered(s).length; }
/* How many dates this one value has on its own, ignoring every other pick. */
function aloneCount(key, value) { const s = blank(); s[key] = value; return countOf(s); }
function resolvePick(key, value) {
  const next = snapshot(state);
  next[key] = value;
  if (!isSet(next, key) || countOf(next)) return { next: next, cleared: [] };
  /* Start from the new pick alone, then add back each earlier pick that
     still leaves something to show. A pick with no dates keeps nothing. */
  const base = blank();
  base[key] = value;
  const cleared = [];
  DIMS.forEach(function (d) {
    if (d === key || !isSet(next, d)) return;
    const trial = Object.assign({}, base);
    trial[d] = next[d];
    if (countOf(base) && countOf(trial)) base[d] = next[d]; else cleared.push(d);
  });
  return { next: base, cleared: cleared };
}
function dimLabel(d, s) { return d === "tier" ? COLLECTION_COPY[s.tier].title : s[d]; }
function listJoin(a) { return a.length < 2 ? a.join("") : a.slice(0, -1).join(", ") + " and " + a[a.length - 1]; }
function showPhrase(key, s) {
  if (key === "tier") return s.tier === "all" ? "every date" : COLLECTION_COPY[s.tier].title + " dates";
  const v = s[key];
  if (!aloneCount(key, v)) return "the waitlist for " + v;
  return (key === "teacher" ? "dates with " : "dates in ") + v;
}
function track(detail) { document.dispatchEvent(new CustomEvent("yaww:track", { detail: detail })); }
function pick(key, value, opts) {
  track({ event: "filter_pick", key: key, value: value });
  const before = snapshot(state);
  const r = resolvePick(key, value);
  Object.assign(state, r.next);
  note = null;
  if (!r.cleared.length) return;
  note = {
    text: "We cleared " + listJoin(r.cleared.map(d => dimLabel(d, before))) + " to show " + showPhrase(key, state) + ".",
    undo: opts && opts.noUndo ? null : before
  };
  track({ event: "filter_pick_cleared", key: key, value: value, cleared: r.cleared.map(d => dimLabel(d, before)) });
}
/* What an option's second line promises: its count with the other picks,
   or, when it clashes, what picking it shows and what it drops. */
function optionMeta(key, value) {
  const n = facetCount(key, value);
  if (n) return plural(n);
  if (!aloneCount(key, value)) return "Waitlist";
  const r = resolvePick(key, value);
  return plural(countOf(r.next)) + " · clears " + listJoin(r.cleared.map(d => dimLabel(d, state)));
}

/* ---------- filter bar markup ---------- */
function initials(name) { return name.split(" ").map(p => p.charAt(0)).join("").slice(0, 2).toUpperCase(); }
function checkSVG() {
  return '<svg class="filter-option__check" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3.5 8.5 6.5 11.5 12.5 5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}
function anyOptionHTML(key, label) {
  const sel = state[key] === "";
  return '<li role="option" aria-selected="' + sel + '">' +
    '<button type="button" class="filter-option' + (sel ? ' is-selected' : '') + '" data-key="' + key + '" data-value="">' +
      '<span class="filter-option__text"><span class="filter-option__name">' + esc(label) + '</span>' +
      '<span class="filter-option__meta">' + plural(anyCount(key)) + '</span></span>' +
      (sel ? checkSVG() : '') +
    '</button></li>';
}
function avatarHTML(name) {
  const photo = TEACHER_PHOTO[name];
  return '<span class="filter-option__avatar" aria-hidden="true">' + esc(initials(name)) +
    (photo ? '<img class="filter-option__photo" src="' + esc(photo) + '" alt="" decoding="async" onerror="this.remove()">' : '') +
    '</span>';
}
/* Destinations get a small square of their own photo, the way teachers
   get their face: people choose a place by how it looks. 96px crops of
   the location photos (images/location-thumbs/), so the open panel costs
   about 60 KB. A place without a photo shows its placeholder colour. */
function thumbHTML(name) {
  const loc = LOCATION_BY_NAME[name];
  if (!loc) return '';
  const hue = loc.placeholderHue != null ? loc.placeholderHue : 200;
  return '<span class="filter-option__thumb" aria-hidden="true" style="background:linear-gradient(155deg, hsl(' + hue + ' 32% 80%), hsl(' + hue + ' 26% 60%))">' +
    (loc.image ? '<img src="' + (window.YAWW_ASSETS || '') + 'images/location-thumbs/' + esc(loc.id) + '.jpg" alt="" width="40" height="40" loading="lazy" decoding="async" onerror="this.remove()">' : '') +
    '</span>';
}
function optionHTML(key, value, withAvatar) {
  const sel = state[key] === value;
  return '<li role="option" aria-selected="' + sel + '">' +
    '<button type="button" class="filter-option' + (sel ? ' is-selected' : '') + '" data-key="' + key + '" data-value="' + esc(value) + '">' +
      (withAvatar ? avatarHTML(value) : key === "place" ? thumbHTML(value) : '') +
      '<span class="filter-option__text"><span class="filter-option__name">' + esc(value) + '</span>' +
      '<span class="filter-option__meta">' + esc((key === "when" && WHEN_SPAN[value] ? WHEN_SPAN[value] + " \u00b7 " : "") + optionMeta(key, value)) + '</span></span>' +
      (sel ? checkSVG() : '') +
    '</button></li>';
}
function panelHTML(def) {
  const ungrouped = def.groups.length === 1 && !def.groups[0].title;
  let html = '<div class="filter-group"><ul role="listbox" aria-label="' + esc(def.label) + '">' +
    anyOptionHTML(def.key, def.any) +
    (ungrouped ? def.groups[0].items.map(v => optionHTML(def.key, v, def.groups[0].avatar)).join("") : "") +
    '</ul></div>';
  if (!ungrouped) {
    def.groups.forEach(function (g) {
      html += '<div class="filter-group">' + (g.title ? '<p class="filter-group__title">' + esc(g.title) + '</p>' : '') +
        '<ul role="listbox" aria-label="' + esc(g.title || def.label) + '">' +
        g.items.map(v => optionHTML(def.key, v, g.avatar)).join("") + '</ul></div>';
    });
  }
  return html;
}
function sheetItemsHTML(def) {
  let html = anyOptionHTML(def.key, def.any);
  def.groups.forEach(function (g) {
    if (g.title) html += '<li role="presentation" class="filter-group"><p class="filter-group__title">' + esc(g.title) + '</p></li>';
    html += g.items.map(v => optionHTML(def.key, v, g.avatar)).join("");
  });
  return html;
}

const filterBar = document.getElementById("filterBar");
const summaryText = document.getElementById("filterSummaryText");
const clearBtn = document.getElementById("filterClearBtn");
const noteEl = document.getElementById("filterNote");
const noteText = document.getElementById("filterNoteText");
const noteUndo = document.getElementById("filterNoteUndo");
let waitlistFor = "";
const results = document.getElementById("results");
const sheet = document.getElementById("filterSheet");
const sheetScrim = document.getElementById("filterSheetScrim");
const sheetPanel = document.getElementById("filterSheetPanel");
const sheetTitle = document.getElementById("filterSheetTitle");
const sheetList = document.getElementById("filterSheetList");
const sheetCloseBtn = document.getElementById("filterSheetCloseBtn");
const sheetClearBtn = document.getElementById("filterSheetClearBtn");
const sheetConfirmBtn = document.getElementById("filterSheetConfirmBtn");

function isMobile() { return window.matchMedia("(max-width: 767px)").matches; }
function segByKey(key) { return SEGMENTS.find(d => d.key === key); }

function renderFilterBar() {
  filterBar.innerHTML = SEGMENTS.map(function (def) {
    const open = state.open === def.key && !isMobile();
    const empty = !state[def.key];
    return '<div class="filter-seg' + (open ? ' is-open' : '') + '">' +
      '<div class="filter-seg__btn" data-seg="' + def.key + '">' +
        '<button type="button" class="filter-seg__trigger" data-seg="' + def.key + '" aria-haspopup="listbox" aria-expanded="' + open + '">' +
          '<span class="filter-seg__text"><span class="filter-seg__label">' + esc(def.label) + '</span>' +
          '<span class="filter-seg__value' + (empty ? ' is-empty' : '') + '">' + esc(state[def.key] || def.any) + '</span></span>' +
        '</button>' +
        (empty ? '' : '<button type="button" class="filter-seg__clear" data-seg="' + def.key + '" aria-label="Clear ' + esc(def.label) + ': ' + esc(state[def.key]) + '"><svg width="12" height="12" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 3 13 13 M13 3 3 13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></button>') +
        '<svg class="filter-seg__chev" width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 5.25 7 9.25 11 5.25" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
      '</div>' +
      (open ? '<div class="filter-panel" data-lenis-prevent>' + panelHTML(def) + '</div>' : '') +
    '</div>';
  }).join("");
}

function renderIntro() {
  const c = COLLECTION_COPY[state.tier] || COLLECTION_COPY.all;
  document.getElementById("collectionTitle").textContent = c.title;
  document.getElementById("collectionDesc").innerHTML =
    c.desc.map(line => '<span class="collection-intro__line">' + esc(line) + '</span>').join(" ");
  document.querySelectorAll(".tabs__item[data-tier]").forEach(function (b) {
    const on = b.dataset.tier === state.tier;
    b.classList.toggle("is-active", on);
    b.setAttribute("aria-selected", on ? "true" : "false");
  });
  moveTabIndicator(tabSlide);
  tabSlide = false;
}

/* Sliding indicator under the active collection tab. It slides only when
   a tab is clicked (tabSlide), and jumps into place on load, on resize
   and when fonts land. */
let tabSlide = false;
const REDUCE_MOTION = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
function moveTabIndicator(animate) {
  const bar = document.querySelector(".tabs");
  const tab = bar && bar.querySelector(".tabs__item.is-active");
  const ind = bar && bar.querySelector(".tabs__indicator");
  if (!tab || !ind || !tab.offsetWidth) return;
  bar.classList.toggle("is-sliding-ready", !!animate && !REDUCE_MOTION);
  ind.style.width = tab.offsetWidth + "px";
  /* -1 so the bar sits astride the hairline */
  ind.style.transform = "translate(" + tab.offsetLeft + "px, " + (tab.offsetTop + tab.offsetHeight - 1) + "px)";
}

const CARD_OPTS = { linkOnlyWhenPage: true };

function groupHTML(title, cards, rows, extraClass) {
  const cls = extraClass ? " " + extraClass : "";
  return state.view === "grid"
    ? '<section class="season' + cls + '"><div class="season__aside"><h2 class="season__title">' + esc(title) + '</h2></div>' +
        '<div class="season__cards"><div class="grid">' + cards + '</div></div></section>'
    : '<section class="season season--list' + cls + '"><h2 class="season__title">' + esc(title) + '</h2>' +
        '<div class="list">' + rows + '</div></section>';
}
function seasonsHTML(list) {
  return SEASON_ORDER.filter(s => list.some(r => Y.seasonLabel(r, "listing") === s)).map(function (g) {
    const items = list.filter(r => Y.seasonLabel(r, "listing") === g);
    return groupHTML(g,
      items.map(r => Y.renderRetreatCard(r, CARD_OPTS)).join(""),
      items.map(r => Y.renderRetreatRow(r, CARD_OPTS)).join(""));
  }).join("");
}
/* Destinations between editions, after the last season: a card each,
   so Morocco is still findable while it has no date. Each opens that
   destination's dateless retreat page, where the waitlist is. */
function datesToComeHTML(locs) {
  if (!locs.length) return "";
  return groupHTML("Dates to come",
    locs.map(l => Y.renderWaitlistCard(l)).join(""),
    locs.map(l => Y.renderWaitlistRow(l)).join(""),
    "season--waitlist");
}

function renderNote() {
  noteEl.hidden = !note;
  if (!note) return;
  noteText.textContent = note.text;
  noteUndo.hidden = !note.undo;
}

/* A pick with no dates (a destination between editions) reads like any
   other result: its waitlist card under "Dates to come", opening the
   destination's retreat page where the waitlist is. Every other date
   lists underneath, so the page still sells. */
function renderWaitlist() {
  const loc = state.place ? LOCATION_BY_NAME[state.place] : null;
  const teacher = !loc && state.teacher ? TEACHER_BY_NAME[state.teacher] : null;
  const name = loc ? loc.name : teacher ? teacher.name : "";
  const key = (loc ? "loc:" + loc.id : "") + (teacher ? "t:" + teacher.id : "");
  summaryText.textContent = loc ? "No dates in " + name + " yet" : "No dates with " + name + " yet";
  if (key !== waitlistFor) {
    waitlistFor = key;
    track({ event: "waitlist_view", locationId: loc ? loc.id : "", teacherId: teacher ? teacher.id : "" });
  }
  results.innerHTML = (loc ? datesToComeHTML([loc]) : "") +
    '<h2 class="results__lead">Other retreats coming up</h2>' +
    seasonsHTML(ROWS.map(v => v.retreat));
}

function renderResults() {
  const list = filtered().map(v => v.retreat);
  const n = list.length;
  const parts = [];
  if (state.teacher) parts.push("with " + state.teacher);
  if (state.place) parts.push("in " + state.place);
  if (state.when) parts.push(state.when);
  summaryText.textContent = plural(n) + (parts.length ? " " + parts.join(", ") : " coming up");
  clearBtn.hidden = !FACETS.some(k => state[k]);
  renderNote();

  if (n === 0) {
    renderWaitlist();
    writeURL();
    return;
  }
  waitlistFor = "";

  const browsing = state.tier === "all" && !FACETS.some(k => state[k]);
  results.innerHTML = seasonsHTML(list) + (browsing ? datesToComeHTML(Y.getWaitlistDestinations()) : "");
  writeURL();
}

function renderAll() { renderIntro(); renderFilterBar(); renderResults(); }

/* ---------- mobile sheet ---------- */
function renderSheetContent(key) {
  const def = segByKey(key);
  sheetTitle.textContent = def.label;
  sheetList.setAttribute("aria-label", def.label);
  sheetList.innerHTML = sheetItemsHTML(def);
  const n = filtered().length;
  sheetConfirmBtn.textContent = n === 0 ? "Show the waitlist" : n === 1 ? "Show 1 retreat" : "Show " + n + " retreats";
}
function openSheet(key) {
  state.open = key;
  renderFilterBar();
  renderSheetContent(key);
  sheet.classList.add("is-open");
  sheet.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
  requestAnimationFrame(() => sheetCloseBtn.focus());
}
function closeSheet() {
  sheet.classList.remove("is-open");
  sheet.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
  const key = state.open;
  state.open = "";
  renderFilterBar();
  const b = filterBar.querySelector('.filter-seg__trigger[data-seg="' + key + '"]');
  if (b) b.focus();
}

/* ---------- wiring ---------- */
/* Which card people open, from where in the list and with which filters:
   the number that says whether the finding tools are working
   (docs/retreats-search-product.md). */
results.addEventListener("click", function (e) {
  const a = e.target.closest("a.result-link");
  if (!a) return;
  const all = Array.from(results.querySelectorAll("a.result-link"));
  track({ event: "result_click", href: a.getAttribute("href"), position: all.indexOf(a) + 1, of: all.length,
          waitlist: !!a.dataset.waitlistDest, filters: snapshot(state), view: state.view });
});
filterBar.addEventListener("click", function (e) {
  const clr = e.target.closest(".filter-seg__clear");
  if (clr) {
    const key = clr.dataset.seg;
    state[key] = ""; note = null; renderFilterBar(); renderResults();
    /* The x is gone once its value is; keep focus on that segment. */
    const t = filterBar.querySelector('.filter-seg__trigger[data-seg="' + key + '"]');
    if (t) t.focus();
    e.stopPropagation(); return;
  }
  const seg = e.target.closest(".filter-seg__btn");
  if (seg) {
    const key = seg.dataset.seg;
    if (isMobile()) { openSheet(key); e.stopPropagation(); return; }
    state.open = state.open === key ? "" : key;
    renderFilterBar();
    if (state.open) {
      const panel = filterBar.querySelector(".filter-panel");
      const f = panel && (panel.querySelector(".filter-option.is-selected") || panel.querySelector(".filter-option"));
      if (f) f.focus();
    }
    e.stopPropagation();
    return;
  }
  const opt = e.target.closest(".filter-option");
  if (opt) {
    const k = opt.dataset.key, v = opt.dataset.value;
    state.open = "";
    pick(k, state[k] === v ? "" : v);
    renderAll();
    e.stopPropagation();
  }
});
filterBar.addEventListener("keydown", function (e) {
  if (!state.open || isMobile()) return;
  if (["ArrowDown", "ArrowUp", "Home", "End"].indexOf(e.key) === -1) return;
  const panel = filterBar.querySelector(".filter-panel");
  if (!panel) return;
  const opts = Array.from(panel.querySelectorAll(".filter-option"));
  const idx = opts.indexOf(document.activeElement);
  e.preventDefault();
  if (e.key === "ArrowDown") (opts[idx + 1] || opts[0]).focus();
  else if (e.key === "ArrowUp") (opts[idx - 1] || opts[opts.length - 1]).focus();
  else if (e.key === "Home") opts[0].focus();
  else opts[opts.length - 1].focus();
});
document.addEventListener("click", function (e) {
  if (!state.open || isMobile()) return;
  if (!filterBar.contains(e.target)) { state.open = ""; renderFilterBar(); }
});
document.addEventListener("keydown", function (e) {
  if (e.key !== "Escape") return;
  if (sheet.classList.contains("is-open")) { closeSheet(); return; }
  if (state.open) { state.open = ""; renderFilterBar(); }
});
sheetList.addEventListener("click", function (e) {
  const opt = e.target.closest(".filter-option");
  if (!opt) return;
  const k = opt.dataset.key, v = opt.dataset.value;
  pick(k, state[k] === v ? "" : v);
  renderIntro(); renderResults(); renderSheetContent(k);
});
sheetClearBtn.addEventListener("click", function () {
  if (!state.open) return;
  state[state.open] = "";
  note = null;
  renderResults(); renderSheetContent(state.open);
});
[sheetCloseBtn, sheetScrim, sheetConfirmBtn].forEach(el => el.addEventListener("click", closeSheet));

clearBtn.addEventListener("click", function () {
  FACETS.forEach(k => { state[k] = ""; });
  state.open = "";
  note = null;
  renderFilterBar(); renderResults();
});
noteUndo.addEventListener("click", function () {
  if (!note || !note.undo) return;
  Object.assign(state, note.undo);
  note = null;
  tabSlide = true;
  renderAll();
  track({ event: "filter_pick_undo" });
});
document.querySelectorAll(".tabs__item[data-tier]").forEach(function (btn) {
  btn.addEventListener("click", function () {
    if (state.tier === btn.dataset.tier) return;
    pick("tier", btn.dataset.tier); tabSlide = true; renderAll();
  });
});
if (window.ResizeObserver) new ResizeObserver(function () { moveTabIndicator(false); }).observe(document.querySelector(".tabs"));
if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { moveTabIndicator(false); });
document.querySelectorAll(".view-toggle button").forEach(function (btn) {
  btn.addEventListener("click", function () {
    state.view = btn.dataset.view;
    document.querySelectorAll(".view-toggle button").forEach(function (b) {
      const on = b.dataset.view === state.view;
      b.classList.toggle("is-active", on);
      b.setAttribute("aria-pressed", on ? "true" : "false");
    });
    renderResults();
  });
});
let rt;
window.addEventListener("resize", function () {
  clearTimeout(rt);
  rt = setTimeout(function () {
    if (!isMobile() && sheet.classList.contains("is-open")) closeSheet();
    renderFilterBar();
  }, 150);
});

readURL();
/* A deep link can carry a combination with no dates. Apply its picks in
   order through the same rule, so the most specific one (teacher, then
   destination) wins and the note says what was dropped. */
(function () {
  const want = snapshot(state);
  Object.assign(state, blank());
  DIMS.forEach(function (d) { if (isSet(want, d)) pick(d, want[d], { noUndo: true }); });
})();
document.querySelectorAll(".view-toggle button").forEach(function (b) {
  const on = b.dataset.view === state.view;
  b.classList.toggle("is-active", on);
  b.setAttribute("aria-pressed", on ? "true" : "false");
});
renderAll();
})();

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
