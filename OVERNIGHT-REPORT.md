
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

## 4 · Retreat page (items 1–12)

Tag `night-02-retreat-page`. How the pages are built: every `retreatpage-*.html` is generated by `retreats/_tools/build_page.py` from `retreatpage-elcuyo.html` (the template) plus `data/*.js` and `retreats/<id>/data.json`. So every change was made once (template, builder, `css/retreat.css`, new JS files) and all 26 pages rebuilt (`python3 retreats/_tools/build_page.py && python3 retreats/_tools/build_page.py --waitlist`, about 1 minute). A dry rebuild before starting reproduced the committed pages exactly. **Do not hand-edit the built pages**: the next rebuild overwrites them.

Commits:
- `b9f0d8d` no hotel names, no tabs for a single hotel (items 7 + confidentiality). `PC.SCRUB` in `page_content.py` rewrites the Basque posada's name and owners and the San Sebastian chef/restaurant on the finished page. `retreatpage-iceland-oct-2026.html` was hand-built and named all three Iceland hotels: it is now built from data like the others (`ARCHIVE` in build_page.py, not listed in data/retreats.js).
- `d35038e` hero above the fold (items 1, 2, 3): photo fills the viewport under the header with name, facts and both CTAs on a blue scrim; the card label ("Deep Dive · 2 spaces left", Signature in bronze, square) above the title. Checked at 1440x900, 1920x1080, 390x844. New `.btn--ghost`.
- `1b407e5` lead modal (item 4): `js/lead-modal.js`, `css/lead-modal.css`, `docs/lead-form.md`.
- `0b0481c` hosts (item 5): text top-aligned to the portrait, "Meet X" on its bottom edge, sublabels gone, Alicia's credentials moved into her bio, more air before Includes. New photos come from data/teachers.js and team.js with each photoPosition.
- `4e2a30d` itinerary (item 6): "Day N" in Baskerville italic, 2px bronze progress rule, rings fill as reached.
- `a1ac752` testimonials (item 8): 3 real reviews per page from `docs/testimonials-by-retreat.md`, parsed by the builder; stock faces removed; no clamp.
- `b20bccb` rooms (item 9): `js/retreat-rooms.js`, `docs/wetravel-cta.md`, `proposals/rooms/`.
- `c8e101d` Additional info (item 11): `retreats/_tools/additional_info.py` (written per retreat by a subagent from data.json + live pages, reviewed for slop: no em dashes, no hotel names) + booking terms from data + the old site's policy answers verbatim.
- `2d0592d` carbon offset (item 10): band removed, now the last Additional info question.
- `591ebb7` sticky bar (item 12): `js/booking-bar.js`, "Ask for info" opens the lead modal; waitlist variant.
- `9f67880` board entries.

Decisions:
- **Hero:** overlay (A). Alternatives B split, C copy-above in `proposals/hero/`. Scoped to `.retreat-hero--overlay`, so `locationpage-iceland.html` is untouched; the locations agent can adopt the class for consistency.
- **Tier + status:** merged into the one label the retreat cards already use, rather than a badge plus a separate pill. Square corners.
- **Lead form:** required = first name + email only. Optional one-tap fields: who is coming, what they want to know, free question, preferred channel (WhatsApp/phone reveal a phone field), newsletter opt-in unticked. Hidden: retreat id/name/dates, room, intent, source button, page URL, UTMs. Submission simulated (`ENDPOINT` empty). Verified: focus trap, Esc, backdrop click, focus return, scroll lock (Lenis stopped via `window.yawwLenis`), inline errors, phone reveal, success state, bottom sheet on phone.
- **Rooms:** the user's 4+8 stacked layout is implemented; it is better than the two-up grid for comparing prices. A "…, shared bed" rate reuses its own room's photos; otherwise a visible `[missing photo: …]` (Thailand shared rooms, Oaxaca shared rooms). Hotel general photos are no longer passed off as room photos. Room descriptions from the old site are now shown (they were in data.json but never rendered).
- **Reserve / WeTravel:** both a section CTA and one per room. Until `window.YAWW_WETRAVEL = { url }` exists, every Reserve opens the lead form in "Reserve your spot" wording with the room filled in.
- **Facilitators:** kept where `PC.FACILITATORS` assigns one (El Cuyo: Renata, Thailand: Fleur); no facilitator anywhere else, no empty states.

Open questions for the user:
- Facilitators per date: Renata/Fleur are marked PLACEHOLDER in the builder (assigned by location, not confirmed per date). Should they show before confirmation?
- WeTravel: trip URLs per edition, and can a link preselect a package/room? (`docs/wetravel-cta.md`)
- Lead endpoint: which tool receives leads (CRM/Zapier)? Field mapping in `docs/lead-form.md`.
- Carbon offset partner to link, or remove (option A)?
- Testimonials: Argentina shows Lori Gray's review, which the .md says to verify (no country named). Basque Country shows two reviews by its own teacher (Genevieve). Rachel Gray's host portrait is the old site's photo, and the people doc says no portrait of her exists: check it is her.
- `[missing data]` left visible: France, Sardinia, Turkey weather month; Thailand temple hike length; Turkey Istanbul to Cappadocia flight included or not; carbon partner.
- Room info on the live site not in our data (from the subagent's read): Oaxaca shared-room photos exist on the live page; Argentina floor per room type at Villa La Angostura; Thailand Deluxe Suites (described, not priced); Sardinia twin/single beds and sea-view upgrade. Not added: would need data.json edits and re-scraping.
- `retreatpage-iceland-oct-2026.html` is a past edition no page links to; consider redirecting it to `retreatpage-iceland.html`.
- `retreatpage-elcuyo.html` (the template) still shows placeholder content (Beach Escape Retreat, lorem FAQs); it is the design reference, not a real page, and should not be published.

Not verified / notes:
- Checked in the browser at 1440 and 390 (hero also 1920x1080): hero, lead modal (desktop + bottom sheet), hosts, itinerary, testimonials, rooms list + room modal (Thailand, Santorini), sticky bar (edition + waitlist), Additional info. No console errors on the pages loaded. Not every one of the 26 pages was opened; all were rebuilt and every inline script syntax-checked.
- Screen reader pass not done (structure and ARIA checked by reading, not with VoiceOver).
- `css/retreat.css` version is bumped through `CSS_VERSION` in build_page.py; `base.css` version still not bumped (see section 1 note).
- The room modal's hotel tabs show each hotel's photos and first two paragraphs; rooms are not split per hotel because the data prices one room type across all hotels.

## 5 · Locations (items 2 and 3)

Tag `night-07-locations`. Like the retreat pages, the location pages are generated: `python3 retreats/_tools/build_location.py` writes all 14 `locationpage-<id>.html`, the cards on `locations.html` (between the GRID markers) and each location's `pageUrl` in `data/locations.js`. Copy lives in `retreats/_tools/location_content.py` (one entry per place, sourced from `docs/locations-content.md` and `additional_info.py`). Chrome (header, nav, footer) is copied from `locations.html` on every build. **Do not hand-edit the pages.** Rebuild when a place gains its first date or loses its last one (the build decides retreats vs waitlist; the cards themselves are drawn live from `data/retreats.js`).

Commits:
- `3460cef` template for every location page: `build_location.py`, `location_content.py`, `css/location.css` rewritten, 14 pages (Iceland redone). `js/retreat-cards.js`: `wireWaitlistForm` gives per-field errors when a form has `[data-error-for]` lines (retreat pages unchanged). `js/lead-modal.js`: on a hero with `data-lead-kind="location"` it says "Ask us about Oaxaca", offers "Upcoming dates" as a topic and sends `location_id`.
- `bbfbebb` grid: blurbs rewritten to 129–138 characters, three lines on every card at 1280, 1366, 1440 and 1920 (measured); Camino de Santiago added (it was in the data, not on the grid), photo cropped from the Santiago cathedral frame; every card links to its page; lede says "Fourteen".
- `521beb2` proposals B and C (`proposals/location-template/`), board rows.

Decisions:
- **Template A, "field guide"** (B editorial long-read and C compact on the board). Order: hero, retreats band, why we go, the place through the homepage pillars (landscape, table, culture, movement: four 3:4 photos on the 312 column), good to know (season, getting there, pace, one more), one real review, three more places, closing CTA.
- **Hero:** the retreat page's overlay hero (`retreat-hero--overlay`, same CSS), with a plain eyebrow where the retreat has its tier label, the live-site line for the place in italic, and three label/value facts (when we go, fly into, pace). CTAs: "See the retreats" or "Join the waitlist", plus "Ask us about <place>" (lead modal). No tier, no "Next retreat · … · Hosting here since" line.
- **Retreats band:** stone-300 like "For teachers", directly under the hero. With 1–3 dates the heading takes the first column and the cards follow it, so a single card is not stranded.
- **Waitlist state:** the retreat's own waitlist card (links to the dateless retreat page with the itinerary) beside a panel: "Our next dates in Iceland go to the waitlist first" / "Leave your name and email and we will write to you as soon as the dates for The Iceland Experience are set, before they are published anywhere else." Join the waitlist (primary) with Ask us about Iceland next to it; "See upcoming dates" gone. Errors under each field in a reserved line, so nothing moves (verified: buttons stay at the same y with and without errors); success "You are on the list, Ana." in place.
- **Iceland:** tagline is the live "Designed to move you. From Reykjavík to the wild."; the invented guide and hotel name of the old page are gone.
- **Photos:** all real, from `images/retreat-photos/<id>/`, heroes chosen to differ from each retreat page's hero where a good alternative existed (Argentina and Sardinia's best landscapes are shared). France has two visible placeholders: `[photo: vineyard rows in Champagne]`, `[photo: morning practice in the Champagne countryside]`.

Open questions:
- France, Sardinia, Turkey: month of the next dates (`[missing data]` in Good to know; their hero facts use other facts instead).
- Morocco hero fact says "Autumn" (last edition Nov 2026); confirm that is when it will run again.
- Basque Country's quote is from its retreat teacher (Genevieve Morin), labelled "Retreat teacher".
- "Ask us which one suits you" / "Ask us anything else" open the same lead modal; leads from a location page carry `location_id` and an empty `retreat_id`. Map in the CRM when it exists (`docs/lead-form.md`).

Verified in the browser (own tab, localhost:8777): Oaxaca, El Cuyo, Santorini (with dates) and Iceland, France (waitlist) at 1440x900, 1920x1080 and 390x844; no horizontal scroll at 390; waitlist validation (empty, bad email, success) at 1440 and 390; lead modal opens with the location wording; no console errors. Not opened one by one: the other nine pages (same template, built without errors). The retreat card's own label clips on 2-up phone cards ("Filling fa…"); that is the site-wide card component, not changed here.

## SEO (BRIEF section 1, item 5)

Tag `night-09-seo`. Only `<head>` was touched. Everything is written by `retreats/_tools/seo.py`; `build_page.py` and `build_location.py` call `seo.apply()` just before writing, so a rebuild keeps it. `python3 retreats/_tools/seo.py` rewrites every official page in place (home, about, teachers, facilitators and journal pages are not generated, so this is how they are updated) and `--audit` lists what is out of range.

What every official page (103) now has: unique `<title>` (60 characters or fewer, "… | Yoga Adventures Worldwide", `| YAWW` where the full suffix does not fit), meta description of 140–160 characters, canonical, Open Graph and Twitter card (og:image from the hero or portrait), `lang="en"` (already there). Audit: no duplicate titles, none out of range.
- JSON-LD: Organization and WebSite on home (no `sameAs`, the footer social links are all `href="#"`); `Event` on every dated retreat (dates, place, performers, `AggregateOffer` in USD from the room prices shown on the page, SoldOut when sold out); `TouristTrip` on the six waitlist pages (no dates, no offers); `Person` on teachers and facilitators; `BlogPosting` on journal posts (only `datePublished` where the post has a date); `TouristDestination` on location pages; `BreadcrumbList` on all of them.
- `noindex` added to the 128 draft/variant files (`index-*.html`, `proto-*`, `premium/**`, `proposals/**`, `retreats-full.html`, `yaww-home-wireframe.html`; `team*.html` redirects already had it) and to `retreatpage-elcuyo.html`, which is the template the builder reads (the built pages drop it). `sitemap.xml` (102 URLs, `lastmod` from git) and `robots.txt` (allow all + sitemap line) are new.
- `retreatpage-iceland-oct-2026.html` is the same retreat as `retreatpage-iceland.html`: its canonical points there and it is out of the sitemap.

Needs a decision or follow-up:
- URLs: canonicals and the sitemap use `https://yogaadventuresworldwide.com/` plus today's filenames. They change in WordPress (`/retreats/<slug>/`, `/journal/<slug>/`): change `BASE` and `url_for()` in `seo.py` and re-run. The live site is already at that domain, so until launch these canonicals point at pages that do not exist yet; do not publish this build under that domain without the swap.
- Header count of H1: `retreats.html` has no H1 at all (the listing has "All Retreats" only as a tab). Teacher, facilitator and journal-post pages have no H1 in the static HTML; it is injected by `js/people-cards.js` and `js/journal-post.js` (one each, fine for Google, invisible to a crawler that does not run JS). Every other official page has exactly one.
- og:image files are WebP (retreat and journal photos). Facebook and X read WebP, LinkedIn and some chat apps may not; export JPEGs for og:image in WordPress.
- Images with empty or missing alt: `index.html` (4 decorative icons and the avatar template, `alt=""` is right for those), `journal.html` (34 card images with `alt=""`; the card title sits next to each so that is acceptable, a descriptive alt would be better), the three El Cuyo template-based retreat pages `…el-cuyo-dec-2026-delaney`, `…feb-2027-delaney`, `…jan-2027-berger` (24 itinerary photos each, `images/dayN-M.jpg`, `alt=""`: body markup from the template, not fixed here), and the lightbox image placeholders on every retreat page (filled by JS). Retreat photos from `data.json` and the location pages all have alt text.
- Social links in the footer are `#`: add the real Instagram/Facebook/LinkedIn URLs and put them in `sameAs` in `seo.py` (`index.html` entry).
- Not run: `marketing:seo-audit` (not cheap), no Search Console / structured data test tool pass (JSON-LD parses, not validated against Google's rich-result test).

## Night 10: WordPress theme (draft)

- `wordpress-theme/yaww/`: classic PHP theme, no build step, ACF Pro not required. CPTs `retreat`, `location`, `teacher`, `facilitator` + taxonomy `retreat_tier`, native `register_post_meta` fields from one spec (`inc/fields-spec.json`), generated `acf-json/` groups, `yaww_meta()` so templates are ACF-agnostic. `single-retreat.php` is the full port; the other views are faithful but lean.
- Hotel names are not exposed anywhere: only `hotel_label`.
- `bin/sync-assets.sh` copies css/js/images into the theme (assets are gitignored); the static site is untouched.
- Not verified: no `php` on this machine, so nothing was linted or run in WordPress. Left: ACF wiring, content import, WeTravel embed, forms endpoint, permalinks into the canonicals in `retreats/_tools/seo.py`, inline-page behaviours (sliders, lightbox). See `wordpress-theme/yaww/README.md`.

## 8 · Responsive pass and UX/a11y audit (items 8 and 9)

Tag `night-08-responsive`. Checked in my own tab at localhost:8777 with scripted checks (horizontal overflow, elements past the viewport, clipped text, tap targets, contrast, labels, names, heading order) at 1440, 1024, 768, 390 and 320 on: index, retreats, 4 retreat pages (Argentina with 2 hotels, El Cuyo Berger, Thailand, Iceland waitlist), locations + Oaxaca, El Cuyo, Iceland, Camino, about, teachers + Alicia Delaney + Sue Berger, facilitators + Christina Thomas, journal + Tacos are life, contact, teach-with-us. Visual contact sheets at 768 and 1024 for the main templates.

Commits:
- `644e91b` menus and footers: "General Inquiry / Teach with us / Build a Custom Retreat" (and the footer's dead `#` links, "Become a Teacher", "Custom Retreat") become "Ask a question / Private groups / Teach with us", as on contact.html, on every official page. Retreat and location pages via their chrome sources (retreatpage-elcuyo.html, locations.html) and a rebuild.
- `d43d64d` 320px: Retreats collection tabs wrap instead of overflowing; room pills wrap ("Garden views in Buenos Aires" pushed the Argentina page 7px wide); slider dots and Reserve/Details get bigger hit areas.
- `4e81166` (coordinator's SEO body list): retreats.html visually hidden H1 and collection title h6 -> h2; static H1 in teacher, facilitator and journal-post pages (JS still replaces it); 34 journal card alts from post titles; itinerary photo alts in the El Cuyo template (3 pages rebuilt); `.visually-hidden` utility.
- `515bcf4` About prose ragged at 768–1024 (justified text opened rivers on a 270px column).
- `255cc15` retreat card label stacks tier over status on narrow cards (2-up phone grids cut "Filling fa…").
- `f8a2f2b` contrast: `--sea-blue-200` #657C8F -> #5A6F80 (3.9 -> 4.7:1 on stone) for the small secondary text it sets; inactive tabs (retreats, retreat page, journal, home "who" titles) from sea-blue-100 (2.8:1) to sea-blue-200; placeholder on contact likewise.
- teach-with-us form title 28 -> 24px (= section-title, as Contact).
- `ea1a50f` docs/audit-recommendations.md: skip link, slider dots, journal alt redundancy, reviews 2+1 at tablet, label variants, CSS `?v=` bumps.

Consistency findings: buttons are already one size everywhere (304x58, same type); section H2s are 24px Baskerville bold on every page; kickers are one style (14px/600/0.56px stone-600) except the location page's small "Good to know" labels (left, noted). Focus: every control has a visible focus style or a replacement (border) where the outline is removed. No heading-level skips on the audited pages.

Not done / notes:
- No skip link anywhere (recommendation 1).
- CSS `?v=` strings in page heads not bumped (the heads belonged to the SEO agent): bump before publishing.
- 768 retreat page: three reviews leave one alone on the second row (recommendation 6).
- Screen-reader pass by hand not done.

## 11 · Code cleanup (BRIEF section 1, item 4)

Tag `night-11-cleanup`. Official site only: drafts, `premium/`, `proposals/` and `index-*` untouched (they share `css/`, so a rule used only by a draft was kept).

Commits:
- `1fbe9ba` dead and overridden CSS. Dead (no class anywhere: pages, drafts, JS, builders, WP theme): `.yf__chips--stack`, `.yf__fine`, `.yf__chip small`, `.retreats-hero`, `.retreats { }`, `.season__title-*`, `.rooms__aside-fine`, `.footer-col--home`. 59 declarations that a later rule with the same selector always overrides (first versions of the itinerary label, booking bar, quote card, hosts slot) removed; the two booking-bar blocks merged into one; later same-selector rules folded into the first where no rule of equal specificity sets the property in between, so the cascade is unchanged.
- `300f653` tokens in base.css: `--ease-slide` (9 uses), `--ease-fold` (4), `--error` #9A3B2E (4) replace repeated literals.
- `2a3ec37` skip link (audit recommendation 1): first in `<body>` on all 103 official pages + the retreat template, lands on `<main id="main">` below the sticky nav. New `retreats/_tools/chrome.py`, called by both builders before `seo.apply()`; `python3 retreats/_tools/chrome.py` for hand-written pages. Location pages' Lenis anchor handler now lets the skip link through (it swallowed the jump and the focus).
- `52da845` one cache-buster `?v=202611210600` for every `css/` and `js/` reference (was 20 different values). Bump `ASSET_VERSION` in chrome.py from now on; data/*.js versions unchanged.

Bytes (CSS, before → after cleanup): retreat.css −3,538 (110,734 → 107,196), retreats.css −1,494, contact.css −611, people.css −106, filters.css −92, journal.css −38, home.css −24, teach.css −24, lead-modal.css +20 and base.css +205 (token definitions): **−5,702 net**. The skip link then adds 686 to base.css and ~60 per page.

Verification: own headless Chrome (CDP), full-page screenshots at 1440 and 390 of the 13 pages in the brief, plus booking bar, lead modal with errors, filter panel and FAQ states; before/after pixel diff with PIL. Full pages: identical. Some 390 runs differ in Raleway glyph antialiasing only (same layout, also happens between two runs of the unchanged site); re-shot until stable. Builders re-run with no diff, `_validate.py` clean. Baseline screenshots in `docs/cleanup-baseline/` (gitignored, ~100 MB).

Left for a decision (not deleted):
- CSS used only by drafts (kept so drafts render as before): `.card__flag*`, `.retreat-hero__badge/__urgency/__image--photo`, `.carbon__*`, `.retreat-teacher__*`, `.host__meta`, `.gallery__more`, `.filter-empty*`, `.footer-col__list--split`. If premium/ and index-* stop mattering, these go.
- 52 tracked images referenced by nothing (~10 MB): 39 in `images/retreat-photos/el-cuyo/`, plus room photos for Sardinia (4), Turkey (4), Basque Country (3) and two teacher portraits. `build_page.py` exports retreat photos from data.json, so check before deleting.
- Not done: JS dead-code search found no unused functions; shared helpers duplicated across js files (escaping, etc.) could become one module, not attempted. The per-heading `-webkit-font-smoothing` repeats are intentional (body text uses `auto`).
