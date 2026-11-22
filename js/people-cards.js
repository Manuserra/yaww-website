/* ============================================================
   YAWW — PEOPLE (facilitators + teachers): cards, profile pages,
   rails and queries

   Same shape as js/retreat-cards.js: the data lives in
   data/team.js (facilitators) and data/teachers.js, and this is the
   only place markup for a person is written.

     <script src="data/teachers.js"></script>
     <script src="data/team.js"></script>
     <script src="data/locations.js"></script>   (teacher pages, for
     <script src="data/retreats.js"></script>     their retreat cards)
     <script src="js/retreat-cards.js"></script>
     <script src="js/people-cards.js"></script>

   A teacher page lists that teacher's upcoming retreats through
   YAWW.getRetreatsByTeacher(), so a date added to data/retreats.js
   shows up on her page with nothing else to edit.

   Optional profile slots (teacher pages): quote, specialties, video,
   spotify. Each one renders only when the data has it, so an empty
   field leaves no gap and no empty state. Add ?slots=demo to any
   teacher URL to see every empty slot as a labelled placeholder.
   ============================================================ */
(function (window, document) {
  "use strict";

  var YAWW = window.YAWW || (window.YAWW = {});
  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }
  var DEMO = /[?&]slots=demo\b/.test(window.location.search);

  /* ---------- queries -------------------------------------------------- */

  function team() { return window.YAWW_TEAM || []; }
  /* The facilitators grid: everyone on the team who is listed. People
     marked `listed: false` keep their data (and, for some, a page that
     retreat pages still link to) but no card. */
  function facilitators() { return team().filter(function (p) { return p.listed !== false; }); }
  /* Real, named teachers: the "tbd" stand-in is not a person and never
     gets a card or a page. */
  function teachers() {
    return (window.YAWW_TEACHERS || []).filter(function (t) { return t.name && !t.unannounced; });
  }
  function findBySlug(list, slug) {
    for (var i = 0; i < list.length; i++) if (list[i].slug === slug || list[i].id === slug) return list[i];
    return null;
  }
  function getTeamMember(slug) { return findBySlug(team(), slug); }
  function getTeacher(slug)    { return findBySlug(teachers(), slug); }

  function firstName(person) {
    return person.shortName || String(person.name || "").split(" ")[0];
  }
  function initials(name) {
    return String(name || "").split(/\s+/).filter(Boolean)
      .map(function (w) { return w.charAt(0); }).join("").slice(0, 2).toUpperCase();
  }

  /* No photograph yet: a stone block carrying her initials in the
     heading face. Never a stock face, never a broken image. */
  function monogram(person, cls) {
    return '<div class="' + cls + ' ' + cls + '--empty" role="img" aria-label="' +
           esc(person.name) + ', photo coming soon"><span class="monogram">' +
           esc(initials(person.name)) + '</span></div>';
  }
  function photo(person, cls, w, h, lazy) {
    if (!person.photo) return monogram(person, cls);
    var pos = person.photoPosition ? ' style="object-position:' + esc(person.photoPosition) + '"' : "";
    return '<div class="' + cls + '"><img src="' + esc(person.photo) + '" alt="' + esc(person.name) +
           '"' + (lazy ? ' loading="lazy"' : "") + ' width="' + w + '" height="' + h + '"' + pos + '></div>';
  }

  /* ---------- card ----------------------------------------------------- */

  /* The card used by the index pages and the About rails: same 672/888
     photo, heading and body scale as the .location card. "More about"
     sits at the foot so a row of cards lines up whatever the blurb. */
  function renderPersonCard(person, opts) {
    opts = opts || {};
    var inner = photo(person, "person__image", 672, 888, true) +
      '<h2 class="person__name">' + esc(person.name) + '</h2>';
    if (!opts.compact) {
      inner += person.shortBio
        ? '<p class="person__bio">' + esc(person.shortBio) + '</p>'
        : '<p class="person__bio person__bio--pending">Bio coming soon.</p>';
    }
    inner += '<span class="person__more">More about ' + esc(firstName(person)) + '</span>';

    return '<article class="person">' +
             (person.pageUrl
               ? '<a class="person__link" href="' + esc(person.pageUrl) + '">' + inner + '</a>'
               : '<div class="person__link">' + inner + '</div>') +
           '</article>';
  }

  function renderPeopleGrid(list, opts) {
    return list.map(function (p) { return renderPersonCard(p, opts); }).join("");
  }

  /* ---------- profile slots --------------------------------------------- */

  function placeholder(label, cls) {
    return '<div class="slot-placeholder ' + (cls || "") + '">[placeholder: ' + esc(label) + ']</div>';
  }

  function quoteSlot(person) {
    if (person.quote) return '<blockquote class="profile__quote">&ldquo;' + esc(person.quote) + '&rdquo;</blockquote>';
    if (DEMO) return '<blockquote class="profile__quote profile__quote--demo">&ldquo;[placeholder: one line in her own words, under 20 words]&rdquo;</blockquote>';
    return "";
  }

  function specialtiesSlot(person) {
    var list = person.specialties || [];
    if (!list.length && DEMO) list = ["[placeholder]", "[style]", "[focus]"];
    if (!list.length) return "";
    return '<div class="profile__facts">' +
             '<h2 class="profile__facts-title">Specialties</h2>' +
             '<ul class="profile__tags">' +
               list.map(function (s) { return '<li>' + esc(s) + '</li>'; }).join("") +
             '</ul>' +
           '</div>';
  }

  /* "Listen & watch": a Spotify embed and a YouTube embed share one band
     under the bio. Data fields (all optional, empty = nothing rendered):
       spotifyPlaylist  Spotify URL, URI or bare id (spotify = older alias)
       youtube          YouTube video id or any youtube.com / youtu.be URL
                        (video = older alias, also accepts an embed URL) */
  function spotifyEmbed(value) {
    var v = String(value).trim();
    var m = v.match(/(playlist|album|track|show|episode|artist)[\/:]([A-Za-z0-9]+)/);
    var kind = m ? m[1] : "playlist", id = m ? m[2] : v;
    return "https://open.spotify.com/embed/" + kind + "/" + encodeURIComponent(id) + "?utm_source=generator&theme=0";
  }
  function youtubeEmbed(value) {
    var v = String(value).trim();
    var m = v.match(/(?:youtu\.be\/|[?&]v=|\/embed\/|\/shorts\/|\/live\/)([A-Za-z0-9_-]{11})/);
    var id = m ? m[1] : (/^[A-Za-z0-9_-]{11}$/.test(v) ? v : "");
    if (id) return "https://www.youtube-nocookie.com/embed/" + id;
    return /^https:\/\/(player\.vimeo\.com|www\.youtube(-nocookie)?\.com)\//.test(v) ? v : "";
  }
  function renderMedia(person) {
    var name = firstName(person);
    var ySrc = (person.youtube || person.video) ? youtubeEmbed(person.youtube || person.video) : "";
    var sSrc = (person.spotifyPlaylist || person.spotify) ? spotifyEmbed(person.spotifyPlaylist || person.spotify) : "";
    var video = ySrc
      ? '<div class="profile-media__video"><iframe src="' + esc(ySrc) + '" title="A class with ' + esc(name) +
        '" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="encrypted-media; picture-in-picture" allowfullscreen></iframe></div>'
      : (DEMO ? placeholder("a 1 to 3 minute clip of " + name + " teaching", "profile-media__video") : "");
    var music = sSrc
      ? '<div class="profile-media__playlist"><iframe src="' + esc(sSrc) + '" title="' + esc(name) +
        '&rsquo;s playlist on Spotify" loading="lazy" allow="encrypted-media"></iframe></div>'
      : (DEMO ? placeholder(name + "’s Spotify playlist", "profile-media__playlist") : "");
    if (!video && !music) return "";
    var cols = video && music ? " profile-media__grid--two" : "";
    return '<h2 class="section-title profile-media__title">Listen &amp; watch</h2>' +
           '<div class="profile-media__grid' + cols + '">' +
             (video ? '<div><p class="kicker profile-media__label">A class with ' + esc(name) + '</p>' + video + '</div>' : "") +
             (music ? '<div><p class="kicker profile-media__label">On ' + esc(name) + '&rsquo;s playlist</p>' + music + '</div>' : "") +
           '</div>';
  }

  /* Website and Instagram: plain links under the bio, new tab. */
  function externalLink(v) {
    if (!v) return null;
    var url = typeof v === "string" ? v : v.url;
    if (!url) return null;
    return { url: url, label: typeof v === "string" ? "" : (v.label || "") };
  }
  function instagramUrl(v) {
    v = String(v || "").trim();
    if (!v) return "";
    if (/^https?:\/\//.test(v)) return v;
    return "https://www.instagram.com/" + v.replace(/^@/, "").replace(/\/+$/, "") + "/";
  }
  function linksSlot(person) {
    var items = [];
    var w = externalLink(person.website);
    if (w) items.push('<li><a href="' + esc(w.url) + '" target="_blank" rel="noopener">' + esc(w.label || "Website") + '</a></li>');
    var ig = instagramUrl(person.instagram);
    if (ig) items.push('<li><a href="' + esc(ig) + '" target="_blank" rel="noopener">Instagram</a></li>');
    if (!items.length) return "";
    return '<ul class="profile__links">' + items.join("") + '</ul>';
  }

  /* ---------- profile page ---------------------------------------------- */

  /* Photo on the left (vertical, sticky on desktop), everything that
     sells her on the right: role, name, her own line, the bio, then the
     specialties as a short scannable list. */
  function renderPersonPage(person, kind) {
    if (!person) return '<p class="profile__pending">This profile could not be found.</p>';

    var role = person.role || (kind === "team" ? "Facilitator" : "Teacher");
    var html = '<div class="profile">';
    html += photo(person, "profile__photo", 672, 888, false);
    html += '<div class="profile__text">';
    html += '<p class="kicker profile__kicker">' + esc(role) + '</p>';
    html += '<h1 class="profile__name">' + esc(person.name) + '</h1>';
    if (kind !== "team") html += quoteSlot(person);

    if (person.bio && person.bio.length) {
      html += '<div class="profile__bio">' +
        person.bio.map(function (p) { return '<p>' + esc(p) + '</p>'; }).join("") +
              '</div>';
    } else {
      /* Deliberately visible: nothing is invented on her behalf. */
      html += '<div class="profile__pending">' +
                '<p class="profile__pending-title">Bio coming soon</p>' +
                '<p>We are still writing this one. In the meantime you can see ' +
                  esc(firstName(person)) + '&rsquo;s upcoming retreats below.</p>' +
              '</div>';
    }
    if (kind !== "team") html += specialtiesSlot(person);
    html += linksSlot(person);
    html += '</div></div>';
    return html;
  }

  /* ---------- a teacher's retreats -------------------------------------- */

  function renderTeacherRetreats(teacher) {
    if (!YAWW.getRetreatsByTeacher) return "";
    var list = YAWW.getRetreatsByTeacher(teacher.id);
    if (!list.length) return "";
    return list.map(function (r) {
      return YAWW.renderRetreatCard(r, { fallbackUrl: "retreats.html" });
    }).join("");
  }

  /* ---------- rails (About page) ---------------------------------------- */

  function wireRail(rail) {
    var track = rail.querySelector("[data-rail-track]");
    var prev = rail.querySelector("[data-rail-prev]");
    var next = rail.querySelector("[data-rail-next]");
    if (!track || !prev || !next) return;
    function step() {
      var card = track.firstElementChild;
      return card ? card.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap || 0) : track.clientWidth;
    }
    function update() {
      var max = track.scrollWidth - track.clientWidth - 2;
      prev.disabled = track.scrollLeft <= 2;
      next.disabled = track.scrollLeft >= max;
      rail.classList.toggle("is-static", max <= 0);
    }
    var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    prev.addEventListener("click", function () { track.scrollBy({ left: -step(), behavior: reduce ? "auto" : "smooth" }); });
    next.addEventListener("click", function () { track.scrollBy({ left: step(), behavior: reduce ? "auto" : "smooth" }); });
    track.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    update();
  }

  /* ---------- page wiring ----------------------------------------------- */

  function listFor(which) { return which === "team" || which === "facilitators" ? facilitators() : teachers(); }

  function init() {
    // Index pages: <div data-people-grid="facilitators|teachers">
    document.querySelectorAll("[data-people-grid]").forEach(function (el) {
      el.innerHTML = renderPeopleGrid(listFor(el.getAttribute("data-people-grid")));
    });
    // ...and their lede, which lives with the data it introduces.
    document.querySelectorAll("[data-people-lede]").forEach(function (el) {
      var which = el.getAttribute("data-people-lede");
      var copy = which === "teachers" ? window.YAWW_TEACHERS_INTRO : window.YAWW_TEAM_INTRO;
      if (copy) el.textContent = copy;
    });
    // Rails: <div data-people-rail="teachers|facilitators"> with a
    // [data-rail-track] inside it.
    document.querySelectorAll("[data-people-rail]").forEach(function (rail) {
      var track = rail.querySelector("[data-rail-track]");
      if (!track) return;
      track.innerHTML = renderPeopleGrid(listFor(rail.getAttribute("data-people-rail")), { compact: true });
      wireRail(rail);
    });

    // Profile pages: <div data-person="slug" data-person-kind="team|teacher">
    document.querySelectorAll("[data-person]").forEach(function (el) {
      var slug = el.getAttribute("data-person");
      var kind = el.getAttribute("data-person-kind");
      var person = kind === "team" ? getTeamMember(slug) : getTeacher(slug);
      el.innerHTML = renderPersonPage(person, kind);
      if (person && person.name) {
        document.title = person.name + " · " + (kind === "team" ? "Facilitator" : (person.role || "Teacher")) + " · YAWW";
      }

      // Upcoming retreats, on the stone band.
      var section = document.querySelector("[data-person-retreats]");
      if (section) {
        var grid = section.querySelector("[data-person-retreats-grid]");
        var cards = (person && kind !== "team") ? renderTeacherRetreats(person) : "";
        if (cards && grid) {
          grid.innerHTML = cards;
          section.hidden = false;
          var h = section.querySelector("[data-person-retreats-title]");
          if (h) h.textContent = "Upcoming retreats with " + firstName(person);
        } else {
          section.hidden = true;
        }
      }

      // Class video + playlist, only when there is one.
      var media = document.querySelector("[data-person-media]");
      if (media) {
        var mhtml = (person && kind !== "team") ? renderMedia(person) : "";
        media.innerHTML = mhtml;
        media.hidden = !mhtml;
      }
    });
  }

  YAWW.getTeam               = team;
  YAWW.getFacilitators       = facilitators;
  YAWW.getTeachers           = teachers;
  YAWW.getTeamMember         = getTeamMember;
  YAWW.getTeacherBySlug      = getTeacher;
  YAWW.renderPersonCard      = renderPersonCard;
  YAWW.renderPeopleGrid      = renderPeopleGrid;
  YAWW.renderPersonPage      = renderPersonPage;
  YAWW.renderTeacherRetreats = renderTeacherRetreats;
  YAWW.initPeople            = init;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

})(window, document);
