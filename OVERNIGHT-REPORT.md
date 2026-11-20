
## Locations grid photos (live-site images)

Source: card images in "Yoga Retreat Locations" on https://yogaadventuresworldwide.com/yoga/ (all under /wp-content/uploads/). Each was cropped to 864x1136 (3:4-ish, matches existing) and exported as jpg q78-82 (140-220 KB).

- Argentina: 2019/05/monte-tronador-view-e1572206780560.jpg (source only 1485 px wide, so upscaled ~1.4x)
- El Cuyo: 2022/01/228-scaled.jpg (empty open-air yoga hall; this is the "Yucatan Yoga & Spa Beach Retreat" card, not the headstand one)
- France: 2026/04/laura-adai-Is6ioeSdg_Q-unsplash-scaled-e1775336497126.jpg
- Oaxaca: 2019/11/destination-oaxaca-mexico.jpg (source 1600x700, upscaled ~1.6x, slightly soft)
- Portugal: 2017/04/groupportbeach.jpeg
- Santorini: 2017/03/santorini-hike-2.jpg (crop trims the outer guests)
- Basque Country: 2023/10/lucian-alexe-maCNBAhdG_w-unsplash-scaled-e1697269189853.jpg

All 7 found. Alt text in locations.html updated to match the new photos.

## 1 · Global, items 1–3 (buttons, card hover, stone colour)

Commits (branch overnight/2026-11-20, tag `night-01-global`):
- `bb0e8d4` global: button hover fill ported from the premium version
- `0e454c4` global: one card hover for every card type, defined in base.css
- `febd0de` global: stone-600 becomes bronze #765C3A, with four options on the board

Decisions made:
- **Buttons.** The premium "fill rises from the bottom" hover is now on every `.btn` (primary, secondary, light), in `css/base.css`. Our colours are kept (primary fills to sea-blue-400, secondary to stone-600 with a stone label, light to stone-300), as is the shared 304px width. Timing is shorter than premium's 600ms: 400ms on the site's strong ease-out, so it answers immediately. Hover only on `(hover: hover)`; on touch the press fills it; reduced motion shows the fill with no travel. `css/retreat.css` had its own duplicate `.btn` block that loaded after base.css and overrode it; removed.
- **Card hover.** "Veil" from `proto-card-hover.html` (the pick from the previous hover round): a 28% layer of the page cream over the photo plus a fading title underline. Defined once in base.css as a selector list covering retreat cards (`.result-link`), location-page retreat cards (`.loc-card`), the locations grid, teachers/team (`.person`), the journal grid and the journal post "more" row. Old per-page underline rules in home.css, people.css, retreats.css and journal-post.css removed. The home "tiers" postcards and the "+N more retreats" tile keep their own hovers (not cards of the listed types). About-page cards are not links, so no hover.
- **Stone colour.** `--stone-600` changed from #877C71 to **#765C3A (bronze)**. Board: `proposals/index.html`; options with buttons, eyebrows, module swatches and computed AA ratios: `proposals/stone-color/index.html`.
  - A warm taupe #7E6B55: 4.59 on page, 4.44 on stone-200, 3.74 on stone-300
  - B toasted oak #85684A: 4.64 / 4.49 / 3.78
  - C bronze #765C3A (implemented): 5.63 / 5.44 / 4.58, AA everywhere, including the stone-300 "For teachers" band
  - D walnut #6B5236: 6.56 / 6.34 / 5.34
  - Old #877C71: 3.67 / 3.55 / 2.99 (failed AA for the eyebrows everywhere)

Verified in the browser at 1440 and 390: retreat page buttons (widths unchanged: 304 desktop, full width minus gutters on phone; hover fill mid-animation captured), teachers grid hover, card hover rules present on index, retreats, locations, journal and a journal post, stone page at both widths, homepage "For teachers" band in the new colour.

Not verified / notes:
- The pages load `css/base.css?v=202611192000`. I did not bump the version string (it is in every HTML file other agents are editing); a browser with a cached base.css will show the old styles until a hard reload. Bump it in the cleanup pass before anything is published.
- No screenshots embedded on the board; the stone page itself is the visual.
- Hover-fill motion judged by eye in the pane, not on a real trackpad.

### Journal: filter tab hover and topic separators
- Filter tabs (css/journal.css): unselected tabs keep the sea-blue label hover shared with retreats, plus a 2px stone rule that grows from the centre (pointer devices only, reduced-motion safe). focus-visible uses an inset ring, since the scrolling row would clip an outset one.
- Topic labels now read INSPO · LIFESTYLE · TRAVEL via a CSS ::before middle dot (empty alt text, so screen readers skip it) on journal cards, post headers and related-post cards (css/journal-post.css). The old bullet on post pages became the same dot.
- Checked at 1440 and 390, no horizontal overflow. Commit prefixed `journal:`.

## 3 · Retreats listing

Commits (tag `night-03-retreats`):
- `20d7457` retreats: tier tabs take the site's hover wash on hover
- `f6daae8` retreats: filter clear (x) keeps its contrast on a hovered segment
- `e2df570` retreats: destination options show a square thumbnail of the place
- `5738981` retreats: season options name their months; picks and card clicks are tracked
- `e8375fb` retreats: search study, three prototypes and the board entry

What changed on `retreats.html`:
- **Tier tabs.** Hover uses the site's hover wash (Stone-300 @ 40%, the one on the filter segments, list rows and view icons) plus the label deepening to sea-blue. Press goes one step deeper.
- **Clear (×).** It was sea-blue-300 and its own hover (stone-200) was lighter than the segment's hover wash, so it disappeared. Now it is full sea-blue; when the segment is hovered or open it sits on a stone-100 square with a hairline; on its own hover it inverts to sea-blue. Square, not round. Focus ring outside the box. Label "Clear Destination: El Cuyo". After clearing, focus goes back to that segment instead of being lost. 40px target on phones. There was also a specificity clash that would have left the × invisible on its own hover; fixed.
- **Destination options** show a 40px square photo of the place (new `images/location-thumbs/`, 96px crops, 62 KB for 13). Camino has no photo, so it shows its placeholder colour. If the locations agent changes a location photo, regenerate its thumb.
- **Season options** show their months: "Spring 2027 · Mar – May · 6 retreats", worked out from the dates.
- **Tracking:** `filter_pick` and `result_click` (position, filters, view) added next to the existing `yaww:track` events. Nothing listens yet.

Search study: `docs/retreats-search-product.md`. Board entry "How people find a retreat" in `proposals/index.html`.
- Recommendation: keep the bar, single choice, latest pick wins. Tiers stay as tabs. No multi-select, no search in dropdowns, no global search. Reasons: 17 dates and 14 places fit on one scroll, and stacking choices mostly ends in empty combinations.
- Next to test: **B · Destination gallery** (photo tiles by region). Not implemented: it changes the main component, and Christina should see it first.
- Prototypes on real data: `proposals/retreats-search/option-b.html`, `option-c.html` (one search field with chips), `option-d.html` (four multi-selects with search and Apply).

Verified at 1440 and 390 in my own browser tab (another agent drives the shared tab, which navigated under me twice). Hover states checked by forcing the rules with a class, since the pane is a background page and transitions don't run there. No console errors.

Questions / notes:
- While all Deep Dives are in El Cuyo, the Deep Dives tab and the El Cuyo destination do the same thing. Worth knowing before deciding whether the tabs stay.
- The thresholds in the doc's "decision rules" are starting points, not benchmarks.
- `retreats-full.html` is only linked from `index-full-hero.html`, so I treated it as a draft and left it alone.
- CSS version strings bumped on `retreats.html` only (filters.css, retreats.css).
