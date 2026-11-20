# Retreats listing: how people find a retreat

Brief section 3.3. Written 20 Nov 2026 on `overnight/2026-11-20`.
Prototypes: `proposals/retreats-search/` (board entry: `proposals/index.html#retreats-search`).

## Recommendation

Keep what the live page has: collection tabs on top, then one bar with **When / Destination / Teacher**, single choice per dropdown, "the latest pick wins" so a pick never leads to an empty list. Do not add multi-select, search inside dropdowns, a fourth dropdown for tiers, or a global search box.

Shipped tonight as small, clear wins:

- Destination options show a square photo of the place, the same way teacher options already show a face.
- Season options say which months they cover: "Spring 2027 · Mar – May · 6 retreats".
- The × that clears a dropdown is readable on hover, has its own hover and focus state, and is announced as "Clear Destination: El Cuyo".
- Tier tabs take the site's hover wash.
- Tracking events `filter_pick` and `result_click` sit next to the existing `filter_pick_cleared`, `filter_pick_undo` and `waitlist_view`, so the numbers below can be read as soon as analytics listens to `yaww:track`.

Next step worth testing: **Option B, the destination gallery.** Destination opens a panel of photo tiles grouped by region instead of a text list. It is the one idea from Kindred and Evaneos that fits YAWW. It is not live because it changes the main component and adds a second wall of photos above a list that is already all photos. Christina should look at it first, and it should be measured against the current list (see below).

## The business it has to serve

| Fact | What it means for search |
|---|---|
| 17 upcoming dates, 14 destinations (9 with dates, 5 waitlist-only), 12 named teachers, 3 seasons | The whole catalogue fits on one scroll. Nobody is looking for a needle. The job of the tools is to frame the choice, not to narrow a haystack. |
| High-ticket, emotional, considered purchase | People decide on the place, the person and whether the dates fit their life. Photos and faces sell; option counts don't. |
| They arrive with intent from a location page, a teacher's Instagram or a newsletter | Deep links (`?destination=el-cuyo`, `?teacher=sue-berger`) already land them on a filtered list. That is the main way in, not the dropdowns. |
| Every combination has very few results | With 17 dates, about nine in ten combinations of the three filters are empty. Any design that lets people stack choices produces dead ends. |
| All Deep Dives are in El Cuyo today | The Deep Dives tab is, in practice, an "El Cuyo, lower price" shortcut. |

## The four questions

### 1. Should the tiers become another dropdown?

No. There are two values, and the tabs do something a dropdown can't: they explain the collections with the intro copy right under them, which is positioning, not filtering. A dropdown would hide Signature Collection behind a click and make the bar four segments wide, which no longer fits 1440 at the current segment width. Option D shows what it looks like.

Watch: while Deep Dives = El Cuyo, the tab and the El Cuyo destination do the same thing. If the tab gets almost no use (`filter_pick` with `key: "tier"`), consider moving the collection story to the cards and the retreat page and dropping the tabs. If Deep Dives expand beyond El Cuyo, the tabs earn their place.

### 2. Should the dropdowns be multi-select with search?

No to both, for now.

- **Search inside a dropdown** helps when a list is too long to scan. Ours have 14, 12 and 3 entries, all visible in one panel. Typing "Mon" is slower than seeing Montenegro.
- **Multi-select** ("Santorini or Portugal") is a real need for a few people, but the unfiltered list already shows both, a season apart. It also costs a lot: an Apply step, "Montenegro +1" labels that hide what is chosen, and stacked choices that hit the empty combinations the live page was built to avoid. Option D shows it, including the "None with your other choices" rows that appear immediately.

Revisit when a facet passes about 25 options or the calendar passes about 50 dates, or if `filter_pick_undo` is common, which would mean people want combinations the latest-pick rule takes away.

### 3. Is a global search needed?

Not on this page. A free-text box over 17 retreats finds what the three dropdowns already find, adds a way to get zero results ("yoga", "beach", misspelt names) and adds a decision to the page. Option C shows the best version of it: one field that suggests places with photos, months and teachers, and turns each pick into a chip. It feels modern, but it brings back multi-select and dead ends, and on a phone the keyboard covers half the suggestions.

A site-wide search (journal, teachers, locations) is a separate question for when the journal is large. Not needed today.

### 4. A destination picker that opens a list with images, like Kindred or Evaneos?

Yes, in two steps.

1. **Done:** a small photo next to each destination in the existing dropdown and mobile sheet. It costs about 60 KB for all 13 photos (`images/location-thumbs/`, 96 px crops) and makes the list feel like places, not words.
2. **To test:** Option B, a gallery panel. Destination opens a panel the width of the bar with 4:5 photo tiles grouped by region, each with its number of dates or "Waitlist". On a phone it is a full-screen sheet, two tiles a row. It lets the five waitlist destinations sell themselves too.

What the references do well and what we left out. Evaneos leads with "where do you want to go" and a photo per country, because it sells hundreds of destinations; we kept the photo-first panel, not the search box. Kindred puts places on a map with photos; a map adds little for 14 places on four continents.

## What to measure

Read from the `yaww:track` events. Baseline for four weeks before changing anything else.

| Question | Metric | Event(s) |
|---|---|---|
| Do people use the tools at all? | Share of listing sessions with at least one `filter_pick` | `filter_pick` |
| Which way do they look? | Split of picks by `key` (when / place / teacher / tier) | `filter_pick` |
| Does the listing send people on? | Listing → retreat page click-through; position of the card clicked | `result_click` |
| Do filters help or hurt? | Click-through with filters vs without | `result_click.filters` |
| Do they want combinations? | `filter_pick_cleared` and `filter_pick_undo` per session | both |
| Is the waitlist found? | Waitlist views and waitlist card clicks | `waitlist_view`, `result_click.waitlist` |
| Does it end in money? | Retreat page → lead form or reserve, split by entry (deep link, filter, unfiltered) | lead form / WeTravel events, when wired |

Decision rules:

- Destination is more than half of all picks **and** listing → retreat click-through is under ~35% → build Option B and A/B it against the current dropdown, judged on retreat-page leads, not clicks on tiles.
- Undo on more than ~10% of cleared picks → people want combinations; prototype a multi-select Destination only.
- Fewer than ~10% of listing sessions touch a filter → the tools matter less than the order and the cards; spend the effort there (featured dates, "spaces left", photography).

## Prototypes

All use real data from `data/*.js` through `js/retreat-cards.js`, and frozen styles in `proposals/retreats-search/proto.css`.

- **A · Live page** (`retreats.html`): the current bar with tonight's changes. Implemented.
- **B · Destination gallery** (`option-b.html`): recommended next test.
- **C · One search field** (`option-c.html`): global search with typed suggestions and chips.
- **D · Four multi-selects** (`option-d.html`): tiers as a dropdown, every dropdown multi-select with a search box and Apply.

Note: B, C and D filter by month rather than season, to show that option. With 17 dates, seasons with their months shown (now live) read better; months make sense once there are dates in most months.
