# Audit recommendations (night 08, responsive + UX/a11y pass)

What was fixed tonight is in OVERNIGHT-REPORT.md §8. These are the items left
as recommendations: bigger, riskier, or a decision for Christina/Manuel.

## Accessibility
1. **Skip link.** No page has "Skip to content". Add one visually hidden link
   as the first child of `<body>` on every page (and in
   `retreatpage-elcuyo.html` / `locations.html` so the builders carry it),
   pointing at `<main id="main">`. Mechanical, but it touches ~110 files.
2. **Photo-slider dots.** The 6px dots now have a 24px-tall hit area, but the
   6px gap between them means the hit area is only 12px wide. Swipe and the
   arrows are the real controls; if dots should be tappable on their own,
   widen the gap to 18px (visual change) or hide the dots from the tab order
   and keep them as indicators.
3. **Journal card alts.** Cards now carry the post title as alt (SEO pass), so
   a screen reader hears the title twice (image + heading in the same link).
   Better: a short description of each photo in `data/journal.js`
   (`hero.alt`), then use it in journal.html and the post pages.
4. **Who-we-host tabs (home).** Inactive titles are now sea-blue-200 (AA);
   the difference to the active one is mostly the weight of the colour. If it
   reads too similar, mark the active tab with the same 2px indicator the
   other tab bars use.
5. **Screen reader pass** with VoiceOver on the lead modal, room modal and
   filter sheet (focus trap, Escape, return focus) is still to do by hand.

## Layout
6. **Reviews at 768–1023 (retreat pages).** Three quotes in a two-column
   grid leave the third alone on its row. Options: one column, or a rail.
7. **Card label on narrow cards** now stacks tier over status (container
   query under 260px). If the stacked label feels heavy, the alternative is
   to drop the tier on 2-up phone grids and keep only the status.

## Consistency (BRIEF 1.6)
8. **Small labels.** Location "Good to know" labels use 12px / 0.12em; the
   site kicker is 14px / 0.56px. Fine as a sub-label, but if we want one
   eyebrow style, make them `.kicker` and size the list to match.
9. **Big italic intro (location pages, 36px Baskerville italic)** has no twin
   on other pages. Either reuse it on About / Teach intros or keep it as the
   location page's signature.

## Data / copy
10. `data/retreats.js` officialName "Strong at Heart : A Yoga & Culture
    Retreat" has a space before the colon. Left as is (it may be the live
    name); confirm.

## Deploy hygiene
11. CSS changed tonight in base, retreat, retreats, about, home, journal,
    contact, teach. Only `CSS_VERSION` in build_page.py busts the retreat
    stylesheet; the other `?v=` query strings in page heads were not bumped
    (another agent owned the heads). Bump them before publishing.
