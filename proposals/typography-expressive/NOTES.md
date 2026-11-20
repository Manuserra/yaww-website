# Typography proposal: notes (brief 12.12)

Folder: `proposals/typography-expressive/`. Open `index.html` (specimen, rules, before/after), then the four pages.

## What's here
- `type.css`: the override. It only changes type (family, size, weight, style, leading, tracking, wrapping, hanging quotes, one drop cap). It goes after every site stylesheet and can be adopted as is, or merged into `css/base.css` + page sheets.
- `home.html`, `retreatpage-el-cuyo-dec-2026-delaney.html`, `locationpage-oaxaca.html`, `about.html`: copies of the live pages, with paths rebased to `../../` and `type.css` linked last. The home copy is `home.html` because `index.html` is the specimen.
- `proposal.js`: rebases the paths that page scripts build at runtime (cards from `data/*.js`, galleries) and draws the on/off switch (top right; bottom right on phones). `T` toggles; `?type=off` opens a page with it off; the choice is remembered per browser.
- `shots/`: before/after at 1440 used on the specimen page.

The copies were taken from the live pages when this proposal was committed. To refresh them, re-run the copy step (rebase `href/src/poster/srcset` and inline `url(images/…)` to `../../`, insert `proposal.js` first in `<head>`, `type.css` last; wrap "sought-after" in the Oaxaca intro title in `<span class="tx-nowrap">`).

## Decisions
- **Scale.** Display 40→80, h2 28→42, lyric h2 30→48, h3 20→24, quote 20→26, lede 17→19, eyebrow 12→13 (390→1440, clamp()). Today one 24px bold does six jobs.
- **Weight follows size.** Big type is Baskerville Regular; only small named things (people, rooms, extras, card titles) stay Bold. This is the biggest visible change and what makes it read editorial.
- **Italic moments.** Home hero, the line under the retreat/location name, Day N, tier headlines, location guide pillars, About cards, quotes, one lyric h2 per page (Oaxaca's intro, About's h1) and the CTA band. Nothing else.
- **Eyebrows.** Five variants (11–14px, 0.04–0.14em) become one: 12–13px, 600, caps, +0.16em. Colours untouched.
- **Tracking.** Large Baskerville −0.01 to −0.02em; caps (buttons, tabs, labels, meta) +0.1 to +0.16em; 16px text unchanged.
- **Leading.** Prose blocks 1.65; card text untouched (the locations grid relies on 3-line clamps).
- **Numbers.** Day N, prices, the review score and "+9" go to Baskerville. No oldstyle or tabular figures: the Google Fonts files we load have neither (tested in the browser; `font-variant-numeric` changes nothing). Self-hosting the full Raleway would give oldstyle figures if wanted later.
- **Small caps.** Not used. Neither font has real ones; the browser fakes them thin. Caps + tracking instead (quote attributions).
- **Hanging punctuation.** Left-aligned quotes hang their opening mark (native in Safari, `text-indent: -0.42em` elsewhere). Centred quotes (Oaxaca) don't.
- **Drop cap.** Once: About → Our story. `initial-letter: 3` (Chrome 110+, Safari) with a float fallback.
- **Wrapping.** `text-wrap: balance` on headings, `pretty` on paragraphs. About's h1 keeps its `<br>` on desktop (italic 46 fits both lines in the 760px measure) and drops it on phones, where it left "journeys," alone.

## Checked
- 1440 and 390, switch on and off, all four pages: no horizontal overflow, no broken images (runtime-built cards and room photos included), toggle clear of the sticky nav and the retreat booking bar.
- Section-title containers set by layout (e.g. the 555px "Three ways to travel with us") now wrap to two balanced lines. Left as is: that is the layout's measure.

## Open / for the user
- Copy notes seen while checking, not changed: "In our guests' words" uses a straight apostrophe; About body has an em dash ("felt right—places").
- Hyphenated words in quotes can still break at the hyphen ("like-/minded"); only titles get the nowrap treatment.
- If adopted: merge `type.css` into the real sheets (tokens into `:root` in base.css), and add `.tx-nowrap` (or a non-breaking hyphen) wherever a title has a compound word.
