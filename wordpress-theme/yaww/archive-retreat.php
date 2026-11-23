<?php
/**
 * Retreats listing: retreats.html, verbatim. The collection tabs, the When / Destination /
 * Teacher bar, the season groups, grid/list views and the URL state are the static page's
 * own script (assets/js-theme/retreats-listing.js) over js/retreat-cards.js, fed by the
 * WordPress catalog (inc/views.php, yaww_catalog_js). The tier archive (/tier/<slug>/) is
 * this page with that collection picked.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/views.php';
get_header();
?>
  <!-- One finding system for the whole site.
       The tabs ("Upcoming dates / By location / By teacher") are gone:
       they were groupings of the same data the home filters by. In their
       place the When / Destination / Teacher bar from the home — the same
       component, the same words. Order on the page:
         collection (what kind of journey) → bar (when, where, with whom)
         → results, always grouped by season.
       Every choice is mirrored in the URL (?tier=&when=&destination=&teacher=),
       so a location page or a teacher bio can link straight into a
       filtered list, e.g. ?destination=el-cuyo or ?teacher=sue-berger. -->
  <div class="container retreats">
    <h1 class="visually-hidden">Yoga retreats around the world</h1>

    <div class="controls">
      <div class="tabs" role="tablist" aria-label="Collection">
        <button type="button" class="tabs__item is-active" data-tier="all"                  role="tab" aria-selected="true">All Retreats</button>
        <button type="button" class="tabs__item"           data-tier="deep-dives"           role="tab" aria-selected="false">Deep Dives</button>
        <button type="button" class="tabs__item"           data-tier="signature-collection" role="tab" aria-selected="false">Signature Collection</button>
        <span class="tabs__indicator" aria-hidden="true"></span>
      </div>
    </div>

    <div class="collection-intro">
      <h2 class="collection-intro__title" id="collectionTitle">All Retreats</h2>
      <p class="collection-intro__desc" id="collectionDesc"></p>
    </div>

    <div class="filter-bar" role="group" aria-label="Filter retreats" id="filterBar"></div>

    <div class="filter-summary">
      <div class="filter-summary__left">
        <p class="filter-summary__text" id="filterSummaryText"></p>
        <button type="button" class="filter-summary__clear" id="filterClearBtn" hidden>Clear all</button>
      </div>
      <div class="view-toggle view-toggle--inline" aria-label="View">
        <button class="is-active" data-view="grid" aria-label="Grid view" aria-pressed="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></button>
        <button data-view="list" aria-label="List view" aria-pressed="false"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 6h13M8 12h13M8 18h13"/><circle cx="3.5" cy="6" r="1" fill="currentColor" stroke="none"/><circle cx="3.5" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="3.5" cy="18" r="1" fill="currentColor" stroke="none"/></svg></button>
      </div>
    </div>

    <!-- When a pick clashes with the others, the latest pick wins and the
         clashing ones are dropped. This line says what went, with Undo. -->
    <p class="filter-note" id="filterNote" role="status" hidden>
      <span id="filterNoteText"></span>
      <button type="button" class="filter-note__undo" id="filterNoteUndo">Undo</button>
    </p>

    <section class="results" id="results" aria-live="polite"></section>

  </div>

  <!-- Mobile filter sheet (same component as the home) -->
  <div class="filter-sheet" id="filterSheet" aria-hidden="true">
    <div class="filter-sheet__scrim" id="filterSheetScrim"></div>
    <div role="dialog" aria-modal="true" aria-labelledby="filterSheetTitle" class="filter-sheet__panel" id="filterSheetPanel">
      <div class="filter-sheet__grabber-row"><span class="filter-sheet__grabber" aria-hidden="true"></span></div>
      <div class="filter-sheet__head">
        <h3 class="filter-sheet__title" id="filterSheetTitle"></h3>
        <button type="button" class="filter-sheet__close" aria-label="Close" id="filterSheetCloseBtn">
          <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 3 13 13 M13 3 3 13" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
        </button>
      </div>
      <ul role="listbox" class="filter-sheet__list" id="filterSheetList" data-lenis-prevent></ul>
      <div class="filter-sheet__foot">
        <button type="button" class="filter-sheet__clear" id="filterSheetClearBtn">Clear</button>
        <button type="button" class="btn btn--primary filter-sheet__confirm" id="filterSheetConfirmBtn">Show retreats</button>
      </div>
    </div>
  </div>
<?php get_footer();
