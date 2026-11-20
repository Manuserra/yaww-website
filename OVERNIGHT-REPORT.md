
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

## 6–9 · About, Teachers, Teacher pages, Facilitators

Commits (tag `night-04-people`):
- `24c6cbb` teachers: profile layout with optional slots, new teachers, real photos
- `4d1b11b` facilitators: Team becomes Facilitators, old URLs redirect
- `e553ff1` about: bronze eyebrows, team 2x2, real reviews, people rails, shared For teachers
- `da2044b` teachers: teacher page proposals, board entries and content request list
- (last) facilitators: nav and footer link Team -> Facilitators on every page (97 files, only those link lines; diff checked)

Done:
- **About.** Eyebrows in bronze and rewritten (About us, Since 2008, Founder, Our approach, Team, Reviews; "Our story" no longer sits on top of "Our story"). How-we-work buttons now link somewhere; card 4 is "Our team, with you" with "See all facilitators". Team grid: Sofi, JL, Manu, Maggie, 2×2, rectangular 4:5 portraits (2 columns on phone too). Three real reviews from `docs/testimonials-by-retreat.md` with initial monograms (no guest photos exist). Static Google/Tripadvisor badges ready to swap; `docs/review-widgets.md` covers access needed. Teacher and facilitator rails with arrows and "See all" links, from the data files. For teachers is the homepage module: same markup, its CSS moved from `home.css` into `css/lead.css` and loaded by both pages; both buttons go to `teach-with-us.html` (homepage TODO resolved). Also fixed a pre-existing overflow: the founder photo pushed the phone layout to 544px.
- **Teachers.** Carie Cook, Anj Manitsas and Amy Appel added (copy from their Canva flyers, no certifications invented). Bonnie, Jamie, Yessica, Amy Sennett-Starner get photos and bios; Alicia and Alison use the pro El Cuyo set. Rachel Gray shows an "RG" monogram. All blurbs 140–165 characters; "More about [Name]" at the foot of each card; lede balanced so "way." is no longer alone.
- **Teacher pages.** New layout (option A): sticky vertical photo left; role, name, quote, bio, specialties right; "Upcoming retreats with [Name]" on the stone-300 band; "Practice with [Name]" band for class video + Spotify. Each slot shows only if `data/teachers.js` has it. Demo with every slot as a placeholder: `teacher-alicia-delaney.html?slots=demo`. Option B in `proposals/teacher-page/`. `docs/teacher-content-request.md` lists what to ask teachers for.
- **Facilitators.** `facilitators.html` + `facilitator-*.html` (git mv). Old `team*.html` are redirects (meta refresh + canonical + noindex); `team-jenniferlyn-chiemingo.html` goes to her teacher page. Marina, Fleur and JL off the listing (`listed: false`); Marina and Fleur keep unlisted pages because retreat pages link to them (board item). Renata has her pro photo.

Verified at 1440 and 390: teachers, Alicia (demo), Carie (demo), Rachel, facilitators, Renata, About (all sections), homepage For teachers band. No console errors, no horizontal overflow.

Open / questions:
- Manu and Maggie: no photo, surname or role on file; visible `[missing photo]` / `[missing data]` placeholders on About.
- Christina: no real portrait exists; the facilitators card keeps the current candid. Ask her for a headshot.
- Rachel Gray: no portrait. Canva-sourced photos (Bonnie, Jamie, Yessica, Amy S-S, Carie, Anj, Amy Appel) are 230–630px and look soft at page size; ask for originals.
- Specialties and quotes were taken from each teacher's own bio or flyer; teachers should confirm them.
- Carie Cook's Dec 2026 El Cuyo retreat is not in `data/retreats.js` (not my file), so her page shows no retreat band.
- Review figures 4.9 / 4.8 were carried over; confirm, and add the two review URLs (`href="#"` now).
- `css/home.css` and `index.html` were touched (For teachers styles moved to `css/lead.css`, button href). Retreat pages still link `team-*.html`; the redirects cover them.

## 2 · Homepage

Commits (tag `night-06-home`):
- `ea2ceaf` home: more air between and inside sections, on a fluid spacing scale
- `7d1bd70` home: hero video script, shot list from existing footage, web specs
- (next) home: before/after screenshots and this report section

**Spacing.** New fluid tokens in `css/base.css` (added, nothing existing changed), values at 390 / 1440 / 1920:
- `--space-section` 48 / 82 / 101: top and bottom of every stone module (was 32 / 48 / 48). Neighbours now sit ~164px apart at 1440 instead of 96.
- `--space-band` 72 / 104 / 128: the blue "Three ways to travel" band (was 72 flat).
- `--space-head` 40 / 60 / 72: heading → content in every section (was a mix of 40, 48 and 56). The tiers band keeps one step more (+16) for its heavy content, as before.
- `--space-stack-xs/sm/md` (8→16, 16→28, 24→48): eyebrow → title, title → body, body → button inside the heading groups and pillars (was 8 or 16 everywhere).
Applied to Where we're going next, tiers, quotes, the three pillars, movement, booking, who comes, FAQ. Untouched: the hero (fits the viewport), the closing CTA and the footer (as asked), and the "For teachers" band, which now lives in `css/lead.css` shared with About (not my file; it gets more room around it anyway from its neighbours). Page height 10,500 → 11,307 at 1440, 12,846 → 13,102 at 390. CTA and footer heights unchanged at all three widths.
Full-page before/after at 1440, 1920 and 390: `docs/home-spacing/before-*.jpg`, `after-*.jpg` (headless Chrome, my own instance, not the shared pane). No overflow at 390.
Version strings for base.css and home.css bumped on `index.html` only; other pages still load the old `base.css?v=` (the new tokens are unused there, so nothing breaks).

**Hero video.** `docs/hero-video-script.md`: a 24 s loop, one retreat day from first light to golden hour in 8 shots (water, practice wide, practice close, into the water, the place, the table, people, golden-hour water), slow moves, dissolves, no text, loop dissolve baked in so the last frame equals the first. Each shot has candidate files from the Selects Bin (by name only). Main find: the Montenegro May 2025 camera clips (`C0xxx`, `yawwNNN`) are 1920x1080 at 100 fps, so the whole loop can come from that shoot with clean slow motion and one grade. Current `hero-home.mp4` is 22 s, 1080p30, 18.6 MB (6.8 Mbps) and goes to phones too; spec targets ≤6 MB desktop (~2 Mbps, no audio, faststart), a 720x1280 mobile file ≤3.5 MB, and matching posters ≤150 KB.

Questions:
- Is ~164px between sections at 1440 the right amount, or a step too much? It's one token (`--space-section`) to tune.
- Video: Montenegro only (one consistent look), or one shot per destination?

## 11 · Contact and Teach with us

Commits (tag `night-05-contact`):
- `5c3b38a` contact: one human form with topics, direct lines and doors to the other journeys
- `73c8b2e` teach: Teach with us page with how it works, real teacher quotes, FAQ and application form
- `bdcd3a7` contact: strategy doc, two alternative prototypes and the board entry

What changed:
- **contact.html.** The three tab forms are gone. One form asks "What is it about?" (A retreat / A trip for my group / Something else) and shows only what that topic needs: an optional retreat field with upcoming editions as suggestions, or group size and rough timing for groups. Required: first name, email, message. "Send message", errors under each field, a success state that names the person and the email we will answer. Left column: "We would love to hear from you", email and phone, then four doors (a question about one retreat, leading a retreat, a private group, press). On a phone the form comes before the doors.
- **Old anchors still work.** `contact.html#teach-with-us` redirects to the teacher page; `#build-a-custom-retreat` / `#private-groups` preselect the group topic. So the other pages' menus keep working until their owners relabel them ("Ask a question / Private groups / Teach with us", already done on contact.html and teach-with-us.html).
- **"Build a custom retreat"** becomes the "A trip for my group" topic, not a page: there is no defined product behind it yet.
- **teach-with-us.html.** Hero = the homepage For teachers module as H1. Why YAWW (live About figures: since 2008, 550+ retreats, 4,500 yogis), five steps, who does what, two real teacher quotes (Genevieve Morin and Jenniferlyn Chiemingo, their Google reviews, trimmed), FAQ, and the application (inline errors, success state, `YAWW_CONTACT.endpoints.teach` placeholder in `data/contact.js`).
- Shared form kit `.yf` in `css/contact.css` + `js/contact.js`; page styles in the new `css/teach.css`.
- Strategy: `docs/contact-strategy.md`. Board entry "What the Contact page does" with B (signposts, no form) and C (conversation first) in `proposals/contact/`.

Verified at 1440 and 390 in my own tab: no horizontal overflow, validation and success on both forms, topic switching, hash redirect, no console errors.

Questions for the morning (visible on the pages as `[missing data: …]`):
1. The public email for the contact page (the live page only shows a personal inbox).
2. Is +52 1 984 202 6072 (live footer) the right guest line, and does it take WhatsApp?
3. Teacher FAQ: minimum students a teacher brings, how teachers are paid, typical lead time.

Notes:
- The FAQ accordion CSS is copied into teach.css because home.css is not loaded there; move it to base.css in the cleanup pass.
- `bdcd3a7` staged the whole `proposals/index.html`, which may include board entries other agents had not committed yet.
- No live teach/host page exists on yogaadventuresworldwide.com, so the teacher copy is new, in the homepage module's register.
