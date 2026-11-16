/* ============================================================
   YAWW — PEOPLE (team + teachers): cards, detail pages, queries

   Same shape as js/retreat-cards.js: the data lives in
   data/team.js and data/teachers.js, and this is the only place
   markup for a person is written.

     <script src="data/teachers.js"></script>
     <script src="data/team.js"></script>
     <script src="data/locations.js"></script>   (teacher pages, for
     <script src="data/retreats.js"></script>     their retreat cards)
     <script src="js/retreat-cards.js"></script>
     <script src="js/people-cards.js"></script>

   A teacher page lists that teacher's upcoming retreats through
   YAWW.getRetreatsByTeacher(), so a date added to data/retreats.js
   shows up on her page with nothing else to edit.
   ============================================================ */
(function (window, document) {
  "use strict";

  var YAWW = window.YAWW || (window.YAWW = {});
  function esc(s) { return YAWW.esc ? YAWW.esc(s) : String(s == null ? "" : s); }

  /* ---------- queries -------------------------------------------------- */

  function team() { return window.YAWW_TEAM || []; }
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

  /* ---------- card ----------------------------------------------------- */

  /* The card used by both index pages — same photo ratio, heading and
     body scale as the .location card on locations.html.

     A teacher with no bio written yet still gets a card and a link:
     her page carries a visible placeholder rather than invented copy. */
  function renderPersonCard(person) {
    var media = person.photo
      ? '<div class="person__image"><img src="' + esc(person.photo) + '" alt="' + esc(person.name) + '" loading="lazy" width="672" height="888"></div>'
      : '<div class="person__image person__image--empty" aria-hidden="true"></div>';

    var blurb = person.shortBio
      ? '<p class="person__bio">' + esc(person.shortBio) + '</p>'
      : '<p class="person__bio person__bio--pending">Bio coming soon.</p>';

    var inner = media +
      '<h2 class="person__name">' + esc(person.name) + '</h2>' +
      blurb;

    return '<article class="person">' +
             (person.pageUrl
               ? '<a class="person__link" href="' + esc(person.pageUrl) + '">' + inner + '</a>'
               : '<div class="person__link">' + inner + '</div>') +
           '</article>';
  }

  function renderPeopleGrid(list) {
    return list.map(renderPersonCard).join("");
  }

  /* ---------- detail page ---------------------------------------------- */

  function renderPersonPage(person) {
    if (!person) return '<p class="person-page__pending">This profile could not be found.</p>';

    var html = "";
    html += person.photo
      ? '<div class="person-page__photo"><img src="' + esc(person.photo) + '" alt="' + esc(person.name) + '" width="672" height="888"></div>'
      : '<div class="person-page__photo person-page__photo--empty" aria-hidden="true"></div>';

    html += '<h1 class="person-page__name">' + esc(person.name) + '</h1>';

    if (person.bio && person.bio.length) {
      html += '<div class="person-page__bio">' +
        person.bio.map(function (p) { return '<p>' + esc(p) + '</p>'; }).join("") +
              '</div>';
    } else {
      /* Deliberately visible: this teacher has retreats on the books
         but no bio written yet. Nothing is invented on her behalf. */
      html += '<div class="person-page__pending">' +
                '<p class="person-page__pending-title">Bio coming soon</p>' +
                '<p>We are still writing this one. In the meantime you can see ' +
                  esc(firstName(person.name)) + '&rsquo;s upcoming retreats below.</p>' +
              '</div>';
    }
    return html;
  }

  function firstName(name) { return String(name || "").split(" ")[0]; }

  /* ---------- a teacher's retreats -------------------------------------- */

  /* Reads the shared retreat data, so a new date needs no edit here.
     Renders the standard card; returns "" when she has none on sale. */
  function renderTeacherRetreats(teacher) {
    if (!YAWW.getRetreatsByTeacher) return "";
    var list = YAWW.getRetreatsByTeacher(teacher.id);
    if (!list.length) return "";
    return list.map(function (r) {
      return YAWW.renderRetreatCard(r, { fallbackUrl: "retreats.html" });
    }).join("");
  }

  /* ---------- page wiring ----------------------------------------------- */

  /* Every index and detail page carries the data attributes below and
     nothing else, so a new person is a data edit plus a copied shell. */
  function init() {
    // Index pages: <div data-people-grid="team|teachers">
    document.querySelectorAll("[data-people-grid]").forEach(function (el) {
      var which = el.getAttribute("data-people-grid");
      el.innerHTML = renderPeopleGrid(which === "team" ? team() : teachers());
    });
    // ...and their lede, which lives with the data it introduces.
    document.querySelectorAll("[data-people-lede]").forEach(function (el) {
      var which = el.getAttribute("data-people-lede");
      var copy = which === "team" ? window.YAWW_TEAM_INTRO : window.YAWW_TEACHERS_INTRO;
      if (copy) el.textContent = copy;
    });

    // Detail pages: <div data-person="slug" data-person-kind="team|teacher">
    document.querySelectorAll("[data-person]").forEach(function (el) {
      var slug = el.getAttribute("data-person");
      var kind = el.getAttribute("data-person-kind");
      var person = kind === "team" ? getTeamMember(slug) : getTeacher(slug);
      el.innerHTML = renderPersonPage(person);
      if (person && person.name) document.title = "YAWW · " + person.name;

      // Optional retreat rail underneath a teacher page.
      var section = document.querySelector("[data-person-retreats]");
      if (!section) return;
      var grid = section.querySelector("[data-person-retreats-grid]");
      var cards = (person && kind !== "team") ? renderTeacherRetreats(person) : "";
      if (cards && grid) {
        grid.innerHTML = cards;
        section.hidden = false;
        var h = section.querySelector("[data-person-retreats-title]");
        if (h && person) h.textContent = "Upcoming retreats with " + firstName(person.name);
      } else {
        section.hidden = true;
      }
    });
  }

  YAWW.getTeam             = team;
  YAWW.getTeachers         = teachers;
  YAWW.getTeamMember       = getTeamMember;
  YAWW.getTeacherBySlug    = getTeacher;
  YAWW.renderPersonCard    = renderPersonCard;
  YAWW.renderPeopleGrid    = renderPeopleGrid;
  YAWW.renderPersonPage    = renderPersonPage;
  YAWW.renderTeacherRetreats = renderTeacherRetreats;
  YAWW.initPeople          = init;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

})(window, document);
