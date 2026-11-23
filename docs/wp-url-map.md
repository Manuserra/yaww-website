# WordPress URL map: live site -> new site

Source: the live sitemaps of https://yogaadventuresworldwide.com, read on 2026-11-23 (Yoast: `sitemap_index.xml` and its sub-sitemaps; `robots.txt`). Nothing else was crawled.
Implemented in `wordpress-theme/yaww/inc/redirects.php` (the table below and the PHP map come from the same data).

## How it works

- The redirect runs only when WordPress answers 404, so **"keep"** (the new site already serves the same URL) costs nothing and a URL that exists is never touched. Candidates are tried in order; the first that exists wins; the last is the fallback.
- **"301"** = old URL does not exist on the new site; the visitor and Google go to the equivalent page. Query strings are kept.
- Permalinks must be `/%postname%/` (Settings > Permalinks) and the media folder `wp-content/uploads/` must be migrated untouched, so every image URL stays.

## Live inventory (23 Nov 2026)

| Type | Live URLs | Handling |
|---|---|---|
| Pages | 21 | table below |
| Journal posts (`/<slug>/`) + `/journals/` | 34 + 1 | table below (27 keep, 7 slug differences) |
| Retreats (`/retreat/<slug>/`, singular) | 46 | table below, all 301 (new base is `/retreats/`, and the old posts group several dates) |
| Blog categories `/category/*` | 9 | 301 to `/journal/` |
| Journal categories `/journals/*` | 11 | 301 to `/journal/` |
| Retreat categories `/Retreats/*` (capital R) | 8 | 301 (`signature-retreats` to `/tier/signature-collection/`, rest to `/retreats/`) |
| Tags | 0 | none in the sitemap |
| Authors | 0 | none in the sitemap (Yoast had them off) |
| Attachment pages | 6,345 in 7 sitemaps | rule: 301 to the parent's new URL (see below) |
| Locations, teachers, facilitators | 0 | do not exist on the live site; new URLs only |

Total checked: 130 listed URLs + 6,345 attachment pages = 6,475.

### Attachment pages (6,345)

Yoast lists every media file's own page and they answer 200 today (for example `/retreat/yoga-retreat-portugal/portugal-coast-1-3/`). By first segment: `retreat/` 5,611, `team/` 215, `home/` 158, `gives-global-program/` 46, `press/` 38, journal posts 207, `yoga-food-and-wine/` 25, the rest on other pages. One rule covers all: drop the last segment and redirect as the parent would (a retreat attachment goes to that retreat's new URL, `team/` to `/facilitators/`, `home/` to `/`). The image files themselves stay at `/wp-content/uploads/...`.

### robots.txt and sitemaps

- Live robots.txt: `Allow` all, `Crawl-delay: 10`, `Sitemap: .../sitemap_index.xml`. The new robots.txt is WordPress's virtual one (no physical file may be left on the server) and has no crawl-delay; it points to `/wp-sitemap.xml`, and the Yoast sitemap URLs 301 to it. Submit `/wp-sitemap.xml` in Search Console after launch.
- The static repo's `sitemap.xml` and `robots.txt` are for the prototype and are not deployed to WordPress.

## Table


### Pages (21)

| Old URL | New URL (first that exists) | Method | Note |
|---|---|---|---|
| `/` | `/` | keep | home |
| `/faqs/` | `/contact/` | 301 | No standalone FAQ page (FAQs live on each retreat). If a page with this slug is created it is kept. QUESTION |
| `/salsa-retreats/` | `/retreats/` | 301 | Cuba salsa; not offered now. QUESTION |
| `/test-template/` | `/` | 301 | Test page, junk |
| `/creative-expression/` | `/retreats/` | 301 | Collection page; no equivalent. QUESTION |
| `/power-retreats/` | `/retreats/` | 301 | Collection page; no equivalent. QUESTION |
| `/tribe-test/` | `/` | 301 | Test page, junk |
| `/press/` | `/about/` | 301 | No press page. If a page with this slug is created it is kept. QUESTION |
| `/privacy_policies/` | `/contact/` | 301 | POLICY PAGE: create a page with slug privacy_policies (underscore kept) so the URL stays. QUESTION |
| `/yoga-food-and-wine/` | `/retreats/` | 301 | Collection page; no equivalent. QUESTION |
| `/thank-you-contact/` | `/contact/` | 301 | Thank-you page of the old form; the new form shows its own confirmation |
| `/give-the-gift-of-yoga/` | `/contact/` | 301 | No gift page. If created with this slug it is kept. QUESTION |
| `/yoga/` | `/retreats/` | 301 | Generic landing; no equivalent |
| `/contact-about-yoga-retreats/` | `/contact/` | 301 | Contact page |
| `/gives-global-program/` | `/about/` | 301 | No Gives Global page. If created with this slug it is kept. QUESTION |
| `/yoga-teacher-training/` | `/retreats/` | 301 | Teacher-training offer; no equivalent. QUESTION |
| `/termsandconditions/` | `/contact/` | 301 | POLICY PAGE: create a page with slug termsandconditions. QUESTION |
| `/about-our-yoga-retreats/` | `/about/` | 301 | About page |
| `/cancellation_policies/` | `/contact/` | 301 | POLICY PAGE: create a page with slug cancellation_policies. QUESTION |
| `/team/` | `/facilitators/` | 301 | Old team page = the facilitators roster (the static site did the same: team.html moves to facilitators.html) |
| `/all-upcoming-dates/` | `/retreats/` | 301 | Listing with every date |

### Journal index (1)

| Old URL | New URL (first that exists) | Method | Note |
|---|---|---|---|
| `/journals/` | `/journal/` | 301 | Journal index |

### Journal posts (34)

| Old URL | New URL (first that exists) | Method | Note |
|---|---|---|---|
| `/the-smart-yogis-travel-guide-to-cuba-september-2018/` | `/the-smart-yogis-travel-guide-to-cuba-september-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-utopian-newsletter-art-issue-july-11-2018/` | `/the-utopian-newsletter-art-issue-july-11-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/summer-solstice-2018/` | `/summer-solstice-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/travel_and_yoga/` | `/synergy-of-travel-and-retreats/` , then `/journal/` | 301 | Slug differs in data/journal.js (synergy-of-travel-and-retreats). Preferred: set the post slug to the live one (travel_and_yoga) and the URL is kept |
| `/following-your-heart/` | `/following-my-heart/` , then `/journal/` | 301 | Slug differs in data/journal.js (following-my-heart). Preferred: set the post slug to the live one (following-your-heart) and the URL is kept |
| `/how-to-meditate/` | `/yogis-guide-how-to-meditate/` , then `/journal/` | 301 | Slug differs in data/journal.js (yogis-guide-how-to-meditate). Preferred: set the post slug to the live one (how-to-meditate) and the URL is kept |
| `/why-we-love-santorini/` | `/why-we-love-santorini/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/namaste-and-mean-it/` | `/namaste-and-mean-it/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/gay-havana/` | `/i-include-myself-gay-havana/` , then `/journal/` | 301 | Slug differs in data/journal.js (i-include-myself-gay-havana). Preferred: set the post slug to the live one (gay-havana) and the URL is kept |
| `/the-utopian-newsletter-interior-design-issue-september-12-2018/` | `/the-utopian-newsletter-interior-design-issue-september-12-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/libra-full-moon/` | `/libra-full-moon/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/listening-to-joy/` | `/listening-to-joy/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-utopian-newsletter-gives-global-issue-august-1-2018/` | `/the-utopian-newsletter-gives-global-issue-august-1-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-utopian-newsletter-eat-drink-be-merry-issue-august-8-2018/` | `/the-utopian-newsletter-eat-drink-be-merry-issue-august-8-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/virgo-full-moon/` | `/virgo-full-moon/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/harness-the-overwhelm/` | `/harness-the-overwhelm/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-utopian-newsletter-henna-issue-july-18-2018/` | `/the-utopian-newsletter-henna-issue-july-18-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-utopian-newsletter-summer-issue-2018/` | `/the-utopian-newsletter-summer-issue-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-utopian-newsletter-shopping-issue-august-22-2018/` | `/the-utopian-newsletter-shopping-issue-august-22-2018/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/stong_hearts/` | `/on-strong-hearts/` , then `/journal/` | 301 | Slug differs in data/journal.js (on-strong-hearts). Preferred: set the post slug to the live one (stong_hearts) and the URL is kept |
| `/tacos-are-life/` | `/tacos-are-life/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-utopian-newsletter-cuban-revolution-issue-july-25-2018/` | `/the-utopian-newsletter-cuban-revolution-issue/` , then `/journal/` | 301 | Slug differs in data/journal.js (the-utopian-newsletter-cuban-revolution-issue). Preferred: set the post slug to the live one (the-utopian-newsletter-cuban-revolution-issue-july-25-2018) and the URL is kept |
| `/yoga-retreat-travel-musts/` | `/yoga-retreat-travel-musts/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/new-moon-in-aquarius/` | `/new-moon-in-aquarius/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/open-letter-to-all-cuba-retreat-goers/` | `/open-letter-to-all-cuba-retreat-goers/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/why-go-on-a-yoga-retreat/` | `/why-go-on-a-yoga-retreat/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/getting-to-know-you-amy-appel/` | `/getting-to-know-you-amy-appel/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/travel-makes-you-happy/` | `/travel-makes-you-happy/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/finding-stillness/` | `/finding-stillness-a-practice/` , then `/journal/` | 301 | Slug differs in data/journal.js (finding-stillness-a-practice). Preferred: set the post slug to the live one (finding-stillness) and the URL is kept |
| `/the-gift-that-yoga-offers/` | `/the-gift-that-yoga-offers/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/the-shifting/` | `/the-shifting/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/a-love-letter-to-basque-country/` | `/a-love-letter-to-basque-country/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/sitting-with-death-the-beauty-of-dia-de-muertos/` | `/sitting-with-death-the-beauty-of-dia-de-muertos/` , then `/journal/` | keep | Same slug in data/journal.js |
| `/a-somatic-journey-in-morocco/` | `/a-somatic-journey-in-morocco/` , then `/journal/` | keep | Same slug in data/journal.js |

### Blog categories (/category/) (9)

| Old URL | New URL (first that exists) | Method | Note |
|---|---|---|---|
| `/category/asana/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/astrology-tarot/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/foodies/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/guides/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/inspo/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/lifestyle/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/newsletter/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/travel/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |
| `/category/yoga/` | `/journal/` | 301 | Old blog category; the journal has no category pages on the new site yet |

### Journal categories (/journals/) (11)

| Old URL | New URL (first that exists) | Method | Note |
|---|---|---|---|
| `/journals/all/` | `/journal/` | 301 | Old journal category archive |
| `/journals/asana/` | `/journal/` | 301 | Old journal category archive |
| `/journals/astrology-tarot/` | `/journal/` | 301 | Old journal category archive |
| `/journals/community/` | `/journal/` | 301 | Old journal category archive |
| `/journals/guides/` | `/journal/` | 301 | Old journal category archive |
| `/journals/inspiration/` | `/journal/` | 301 | Old journal category archive |
| `/journals/lifestyle/` | `/journal/` | 301 | Old journal category archive |
| `/journals/newsletters/` | `/journal/` | 301 | Old journal category archive |
| `/journals/practice/` | `/journal/` | 301 | Old journal category archive |
| `/journals/travel/` | `/journal/` | 301 | Old journal category archive |
| `/journals/witchy/` | `/journal/` | 301 | Old journal category archive |

### Retreat categories (/Retreats/) (8)

| Old URL | New URL (first that exists) | Method | Note |
|---|---|---|---|
| `/Retreats/archived/` | `/retreats/` | 301 | Collection with no tier equivalent (Deep Dives was not on the old site) |
| `/Retreats/art-creative/` | `/retreats/` | 301 | Collection with no tier equivalent (Deep Dives was not on the old site) |
| `/Retreats/custom-retreats/` | `/contact/#private-groups` | 301 | Custom retreats = private groups |
| `/Retreats/family-retreats/` | `/retreats/` | 301 | Collection with no tier equivalent (Deep Dives was not on the old site) |
| `/Retreats/power-retreats/` | `/retreats/` | 301 | Collection with no tier equivalent (Deep Dives was not on the old site) |
| `/Retreats/signature-retreats/` | `/tier/signature-collection/` , then `/retreats/` | 301 | = Signature Collection tier |
| `/Retreats/yoga/` | `/retreats/` | 301 | Collection with no tier equivalent (Deep Dives was not on the old site) |
| `/Retreats/yoga-food-and-wine/` | `/retreats/` | 301 | Collection with no tier equivalent (Deep Dives was not on the old site) |

### Retreats (/retreat/) (46)

| Old URL | New URL (first that exists) | Method | Note |
|---|---|---|---|
| `/retreat/yoga-and-pilates-retreat/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/creative-retreat-in-cuba/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/rockin-havana-with-sadie-nardini/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/the-lush-life/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/cuban-salsa-retreats/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/guatemala-yoga-art-retreat/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/guatemala-yoga-aerial-silks-retreat/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/yoga-retreat-colombia/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/power-yoga-r3bar-holbox-island/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/tulum-yoga-art-retreat/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/farm-to-table-retreats/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/yoga-retreat-holbox-island/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/cuba-yoga-retreat-art/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/sup-yoga-retreat-holbox-island/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/yoga-retreat-tulum-mexico/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/beach-yoga-retreat-tulum/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/yoga-retreat-in-oaxaca/` | `/locations/oaxaca/` , then `/retreats/` | 301 | Destination exists, no dated edition for this post |
| `/retreat/yoga-teacher-training-mexico/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/yoga-retreat-little-corn-island/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/napa-valley-wine-yoga-retreats/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/custom-retreats/` | `/contact/#private-groups` | 301 | Custom retreats = private groups on the contact page |
| `/retreat/yoga-retreat-in-costa-rica/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/croatia-family-yoga-retreat/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/power_yoga_barre_boxing/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/yoga-astrology-retreats/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/yoga-retreat-cuba/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/basque-country-spain-yoga-gastronomy-retreat/` | `/retreats/spain-basque-country/` , then `/locations/spain-basque-country/` , then `/retreats/` | 301 | waitlist page / destination page |
| `/retreat/thailand-yoga-reverence-retreat/` | `/locations/thailand/` , then `/retreats/` | 301 | Destination exists, no dated edition for this post |
| `/retreat/croatia-yoga-food-wine-retreat/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/peru-machu-picchu-yoga-retreat/` | `/retreats/` | 301 | No destination or edition on the new site. QUESTION |
| `/retreat/istanbul-yoga-retreat-art/` | `/retreats/turkey/` , then `/locations/turkey/` , then `/retreats/` | 301 | waitlist page / destination page |
| `/retreat/embodied-the-art-of-being-seen/` | `/retreats/el-cuyo-feb-2027-morin-gray/` , then `/locations/el-cuyo/` , then `/retreats/` | 301 | single edition -> its dated page |
| `/retreat/the-paris-champagne-experience/` | `/retreats/france/` , then `/locations/france/` , then `/retreats/` | 301 | waitlist page / destination page |
| `/retreat/yoga-food-wine-adventure-sardinia/` | `/retreats/sardinia/` , then `/locations/sardinia/` , then `/retreats/` | 301 | waitlist page / destination page |
| `/retreat/yoga-retreat-in-iceland/` | `/retreats/iceland/` , then `/locations/iceland/` , then `/retreats/` | 301 | waitlist page / destination page |
| `/retreat/yoga-retreat-day-of-the-dead-oaxaca/` | `/retreats/oaxaca-oct-2027/` , then `/locations/oaxaca/` , then `/retreats/` | 301 | single edition -> its dated page |
| `/retreat/treasures-of-thailand-a-yoga-wellness-retreat/` | `/retreats/thailand-dec-2026/` , then `/locations/thailand/` , then `/retreats/` | 301 | single edition -> its dated page |
| `/retreat/yoga-retreat-montenegro/` | `/locations/montenegro/` , then `/retreats/` | 301 | old post with several dates -> evergreen location page |
| `/retreat/yoga-retreat-argentina/` | `/retreats/argentina-mar-2027/` , then `/locations/argentina/` , then `/retreats/` | 301 | single edition -> its dated page |
| `/retreat/mini-yoga-retreats-in-paradise/` | `/retreats/el-cuyo-jan-2027-coffey/` , then `/locations/el-cuyo/` , then `/retreats/` | 301 | single edition -> its dated page |
| `/retreat/camino-de-santiago/` | `/locations/camino-de-santiago/` , then `/retreats/` | 301 | old post with several dates -> evergreen location page |
| `/retreat/yoga-retreat-morocco/` | `/retreats/morocco/` , then `/locations/morocco/` | 301 | waitlist page / destination page |
| `/retreat/beach-yoga-retreat-mexico/` | `/locations/el-cuyo/` , then `/retreats/` | 301 | old post with several dates -> evergreen location page |
| `/retreat/yoga-retreat-portugal/` | `/retreats/portugal-may-2027/` , then `/locations/portugal/` , then `/retreats/` | 301 | single edition -> its dated page |
| `/retreat/yoga-retreat-greece/` | `/locations/santorini/` , then `/retreats/` | 301 | old post with several dates -> evergreen location page |
| `/retreat/morocco-yoga-retreat-deep-dive/` | `/retreats/morocco/` , then `/locations/morocco/` | 301 | waitlist page / destination page |


## Static prototype URLs (not live today, redirected anyway)

| Pattern | New URL |
|---|---|
| `/index.html`, `/index-*.html`, `/proto-*`, `/premium/*`, `/proposals/*` | `/` for `index.html`; variants and drafts are not redirected (they never go to the server) |
| `/retreats.html`, `/retreats-full.html`, `/locations.html`, `/about.html`, `/teachers.html`, `/facilitators.html`, `/journal.html`, `/contact.html`, `/teach-with-us.html` | `/retreats/`, `/locations/`, `/about/`, `/teachers/`, `/facilitators/`, `/journal/`, `/contact/`, `/teach-with-us/` |
| `/team.html` | `/facilitators/` |
| `/retreatpage-<id>.html` (23 dated + 6 waitlist) | `/retreats/<id>/`, else its location, else `/retreats/`; `retreatpage-iceland-oct-2026` goes to `iceland` (its old canonical) |
| `/locationpage-<id>.html` (14) | `/locations/<id>/` |
| `/teacher-<id>.html` (15) | `/teachers/<id>/` |
| `/facilitator-<id>.html`, `/team-<id>.html` (7) | `/facilitators/<id>/` |
| `/journal/<slug>.html` (34) | `/<slug>/` |

## New URLs (no old equivalent)

`/retreats/<id>/` for the 19 dated editions and the 5 waitlist retreats (ids from `retreats/_index.json`), `/locations/<id>/` (14), `/teachers/<id>/` (15), `/facilitators/<id>/` (7), `/tier/<tier>/` (3).

## Questions for you

1. **Policy pages**: the live site has `/privacy_policies/`, `/termsandconditions/`, `/cancellation_policies/`. The new site has no policy pages yet. Create three pages with exactly those slugs (underscore kept) and the URLs stay; until then they redirect to `/contact/`. The text must be copied from the live pages (not invented).
2. **Retreats without a new destination** (redirected to `/retreats/`): Cuba (5 posts: `creative-retreat-in-cuba`, `rockin-havana-with-sadie-nardini`, `cuban-salsa-retreats`, `cuba-yoga-retreat-art`, `yoga-retreat-cuba`), Guatemala (2), Holbox (3), Tulum (3), Colombia, Costa Rica, Croatia (2), Little Corn Island, Napa Valley, Peru, Lush Life, Yoga and Pilates, Farm to table, Teacher training Mexico, Power yoga barre boxing, Yoga astrology. Are they retired for good? If one comes back, create it with the live slug under `/retreats/` or tell me and I add the redirect.
3. **Pages with no equivalent**: `/faqs/`, `/press/`, `/gives-global-program/`, `/give-the-gift-of-yoga/`, `/yoga-teacher-training/`, `/salsa-retreats/`, `/power-retreats/`, `/creative-expression/`, `/yoga-food-and-wine/`. Currently to `/contact/`, `/about/` or `/retreats/`. Create them with the same slug if you want them kept (press and Gives Global carry 38 and 46 attachment pages, so they had images indexed).
4. **Journal slugs**: 7 live posts have a slug that differs from `data/journal.js`: `following-your-heart` (new `following-my-heart`), `how-to-meditate` (`yogis-guide-how-to-meditate`), `gay-havana` (`i-include-myself-gay-havana`), `travel_and_yoga` (`synergy-of-travel-and-retreats`), `stong_hearts` (`on-strong-hearts`), `finding-stillness` (`finding-stillness-a-practice`), `the-utopian-newsletter-cuban-revolution-issue-july-25-2018` (`...-cuban-revolution-issue`). Best for SEO: import them with the **live** slug (URL kept, no redirect); the 301 is the safety net if the new slug is used.
5. **Morocco**: `yoga-retreat-morocco` and `morocco-yoga-retreat-deep-dive` go to `/retreats/morocco/` (waitlist) if that retreat exists, else `/locations/morocco/`. The dated edition (`morocco-nov-2026`) is sold out and already under way; confirm the waitlist page is the better target.
6. **Retreat base**: live is `/retreat/<slug>/`, new is `/retreats/<slug>/`. A 301 keeps the equity, but if you prefer to keep `/retreat/` change the CPT rewrite slug in `inc/post-types.php`. The old posts group several dates, so most go to the location page anyway.
