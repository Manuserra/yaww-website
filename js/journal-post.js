/* ============================================================
   YAWW — JOURNAL ARTICLE

   Renders one post from data/journal.js. The article pages carry
   nothing but a slug, so a copy or image change is a data change.

   Pages live at journal/<slug>.html — a folder, because that is the
   permalink WordPress will serve (/journal/<slug>/) and the URLs
   should survive the move. Everything those pages reference is one
   level up, hence BASE below.
   ============================================================ */
(function (window, document) {
  "use strict";

  var YAWW = window.YAWW || (window.YAWW = {});

  function esc(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;")
      .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  /* Article pages sit one folder down; everything else is at the root. */
  var BASE = /\/journal\/[^/]+$/.test(window.location.pathname) ? "../" : "";
  function url(p) { return p ? BASE + p : p; }

  function posts() { return window.YAWW_JOURNAL || []; }
  function getPost(slug) {
    var list = posts();
    for (var i = 0; i < list.length; i++) if (list[i].slug === slug) return list[i];
    return null;
  }

  /* Both densities when there is a retina file, so a standard screen
     never downloads the double-size one. */
  function img(src, src2x, alt, eager) {
    var ss = src2x ? ' srcset="' + esc(url(src)) + ' 1x, ' + esc(url(src2x)) + ' 2x"' : "";
    return '<img src="' + esc(url(src)) + '"' + ss + ' alt="' + esc(alt || "") + '"' +
           (eager ? ' fetchpriority="high"' : ' loading="lazy"') + ' decoding="async">';
  }

  function headerHTML(post) {
    return '<div class="post__head">' +
             '<h1 class="post__title">' + esc(post.title) + '</h1>' +
             (post.categories && post.categories.length
               ? '<div class="post__cats">' +
                   post.categories.map(function (c) { return '<span>' + esc(c) + '</span>'; }).join("") +
                 '</div>'
               : "") +
             // Ten posts carry no date on the source site; those simply
             // get no date line rather than an invented one.
             (post.date
               ? '<p class="post__date"><time datetime="' + esc(post.dateISO || "") + '">' +
                   esc(post.date) + '</time></p>'
               : "") +
           '</div>';
  }

  /* A text block is one run of copy. Its inner arrays are paragraphs
     and the strings inside them are lines separated by <br>, which is
     how the source writes them — rendering each line as its own <p>
     opened a 32px gap where the original has a plain line break. */
  function bodyHTML(post) {
    return post.blocks.map(function (b) {
      if (b.type === "img") {
        return '<figure class="post__figure">' + img(b.src, b.src2x, "") + '</figure>';
      }
      return '<div class="post__text">' +
        b.paras.map(function (lines) {
          return '<p>' + lines.map(esc).join("<br>") + '</p>';
        }).join("") +
      '</div>';
    }).join("");
  }

  /* The author card. When that person has a page on this site the name
     becomes a link and a CTA is added; otherwise they are credited
     without one. */
  function authorHTML(post) {
    var a = post.author;
    if (!a) return "";
    var name = a.pageUrl
      ? '<a class="post-author__name-link" href="' + esc(url(a.pageUrl)) + '">' + esc(a.name) + '</a>'
      : esc(a.name);
    var cta = a.pageUrl
      ? '<a class="btn btn--secondary post-author__cta" href="' + esc(url(a.pageUrl)) + '">' +
          'Read more about ' + esc(a.name.split(" ")[0]) +
        '</a>'
      : "";
    return '<section class="post-author">' +
             '<div class="post-author__box">' +
               (a.photo ? '<div class="post-author__photo">' + img(a.photo, a.photo2x, a.name) + '</div>' : "") +
               '<div class="post-author__text">' +
                 '<p class="post-author__slug">About the author</p>' +
                 '<p class="post-author__name">' + name + '</p>' +
                 a.bio.map(function (p) { return '<p class="post-author__bio">' + esc(p) + '</p>'; }).join("") +
                 cta +
               '</div>' +
             '</div>' +
           '</section>';
  }

  /* "Explore this retreat". The button goes to that destination's own
     page when one exists, and to the full listing until then. */
  function retreatHTML(post) {
    var r = post.retreat;
    if (!r) return "";
    var loc = (YAWW.getLocation && r.locationId) ? YAWW.getLocation(r.locationId) : null;
    var href = url((loc && loc.pageUrl) || "retreats.html");
    return '<section class="post-retreat">' +
             '<h2 class="post-retreat__head">Explore this retreat</h2>' +
             '<div class="post-retreat__box">' +
               (r.image ? '<div class="post-retreat__image">' + img(r.image, r.image2x, r.title) + '</div>' : "") +
               '<div class="post-retreat__text">' +
                 '<p class="post-retreat__title">' + esc(r.title) + '</p>' +
                 (r.subtitle ? '<p class="post-retreat__sub">' + esc(r.subtitle) + '</p>' : "") +
                 (r.text ? '<p class="post-retreat__desc">' + esc(r.text) + '</p>' : "") +
                 '<a class="btn btn--primary" href="' + esc(href) + '">Explore retreat</a>' +
               '</div>' +
             '</div>' +
           '</section>';
  }

  /* Three more posts, newest first, never this one. */
  function relatedHTML(post) {
    var others = posts().filter(function (p) { return p.slug !== post.slug; }).slice(0, 3);
    if (!others.length) return "";
    return '<section class="post-related">' +
             '<h2 class="post-related__head">More from the journal</h2>' +
             '<div class="post-related__grid">' +
               others.map(function (p) {
                 return '<a class="post-related__item" href="' + esc(url("journal/" + p.slug + ".html")) + '">' +
                          '<div class="post-related__figure">' + img(p.hero.src, p.hero.src2x, "") + '</div>' +
                          '<p class="post-related__title">' + esc(p.title) + '</p>' +
                        '</a>';
               }).join("") +
             '</div>' +
           '</section>';
  }

  function render(el) {
    var post = getPost(el.getAttribute("data-journal-post"));
    if (!post) { el.innerHTML = '<p class="post__missing">This article could not be found.</p>'; return; }
    document.title = "YAWW · " + post.title;
    el.innerHTML =
      headerHTML(post) +
      '<div class="post__hero">' + img(post.hero.src, post.hero.src2x, post.title, true) + '</div>' +
      '<div class="post__body">' + bodyHTML(post) + '</div>' +
      authorHTML(post) + retreatHTML(post) + relatedHTML(post);
  }

  function init() {
    document.querySelectorAll("[data-journal-post]").forEach(render);
  }

  YAWW.getPost = getPost;
  YAWW.initJournalPost = init;

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

})(window, document);
