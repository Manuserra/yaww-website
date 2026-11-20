# YAWW WordPress theme (draft 0.1)

Classic PHP theme (no build step, WordPress 6.x, PHP 7.4+) that will reproduce the official static site.
The static HTML site is untouched: this folder only reads from `../../css`, `../../js` and `../../images`.
ACF Pro is not required yet; everything works on native post meta and is ready for ACF.

## Structure

| Path | What |
|---|---|
| `style.css` | theme header only (the real CSS is the site's `css/`) |
| `functions.php` | supports, image sizes, enqueues (page CSS chosen by `yaww_page_css()`), contact config |
| `inc/post-types.php` | CPTs `retreat`, `location`, `teacher`, `facilitator`, taxonomy `retreat_tier`; Journal = built-in posts. mu-plugin style: can move to `mu-plugins/` as is |
| `inc/fields-spec.json` | single source for every custom field |
| `inc/fields.php` | `register_post_meta` (show_in_rest) from the spec, plus a stop-gap meta box (scalars as inputs, arrays as JSON) that disappears when ACF is active |
| `inc/helpers.php` | `yaww_meta()`, `yaww_list()`, `yaww_rows()`, dates, status badge, hotel label, nav |
| `inc/testimonials.json` | guest quotes lifted from `index.html` |
| `acf-json/` | ACF Pro field groups (generated, see below); ACF loads them automatically |
| `template-parts/` | `retreat-card`, `lead-modal` (trigger), `for-teachers`, `testimonials`, `hero`, `person-card`, `logo-symbol` |
| `page-templates/` | `about`, `contact`, `teach-with-us` |
| views | `front-page`, `page`, `archive-retreat`, `taxonomy-retreat_tier`, `single-retreat`, `archive-location`, `single-location`, `archive-teacher`, `single-teacher`, `archive-facilitator`, `single-facilitator`, `home` (Journal), `archive`, `single`, `index` |
| `assets/theme.js` | nav toggle, tabs, FAQ, Lenis. `assets/css`, `js`, `images`, `media` are copied from the static site, not committed |
| `bin/sync-assets.sh` | copies css, js, used images and the hero video from the static site into `assets/` |
| `bin/gen-acf.py` | regenerates `acf-json/*.json` from the spec; `--table` prints the field map below |

## Install

1. `bash bin/sync-assets.sh` (needs python3), then zip the `yaww/` folder and upload it, or symlink it into `wp-content/themes/`.
2. Activate. Create pages with slugs `journal` (becomes the Posts page automatically), `about` (template About), `contact` (Contact), `teach-with-us` (Teach with us), `policies`. Set Settings > Reading > front page to a static page.
3. Settings > Permalinks > Save (flushes `/retreats/`, `/locations/`, `/teachers/`, `/facilitators/`).
4. Content: add posts per CPT. Retreat title = `officialName`, content = about text, `location_id` / `teacher_ids` are post IDs. Fields are in the "YAWW fields" box, or in ACF once installed.

## Rules baked in

- Hotel names never reach a page: no hotel-name field exists. Rooms and retreats carry only `hotel_label` ("boutique hotel in Chiang Mai"). Utopia is ours, so write it in the label field if wanted.
- Templates call `yaww_meta()` only, never `get_field` / `get_post_meta`, so ACF can be switched on with no template change (the ACF value wins, native meta is the fallback).
- Retreat dates, badge and teacher line follow `js/retreat-cards.js` (`yaww_date_range`, `yaww_status_label`).
- `retreat-rooms.js` and `booking-bar.js` run unchanged: `TIERS`, `ROOM_VENUES` and `YAWW_WETRAVEL` are emitted from the `rooms` rows and `wetravel_url`.

## What is done

Everything in the structure table. `single-retreat.php` is the full port (hero, about, teachers, includes tabs, itinerary, stay label, testimonials, rooms and how to book, extras, travel tips with location fallback, gallery, FAQ, lightbox shell). The other views are faithful but leaner. PHP was not linted (no `php` on the build machine): run `find . -name '*.php' -exec php -l {} \;` first.

## What is left

- **ACF Pro**: install, activate, open Custom Fields > Field Groups and sync the four YAWW groups. Check that repeater storage (`rooms_0_name`) reads through `yaww_meta()`; `list` repeaters return `[{value}]`, handled by `yaww_list()`. Then drop the stop-gap meta box.
- **Content import**: a script (WP-CLI or REST) from `data/*.js` and `retreats/*/data.json` into posts and meta, plus a media import for card, hero and gallery images (image fields hold attachment IDs).
- **WeTravel embed**: `wetravel_url` only feeds the Reserve links and `window.YAWW_WETRAVEL`; the embed itself is not built (see `docs/wetravel-cta.md`).
- **Forms endpoint**: `yaww_contact_config()` endpoints are `null` (submit logs and shows success, as on the static site). The newsletter form in `footer.php`, the waitlist on `single-location.php` and the contact and teach forms are shells; port the field sets from `contact.html` and `teach-with-us.html`.
- **Permalinks and SEO**: the static site links `retreatpage-<id>.html` and `locationpage-*.html`. On WordPress they become `/retreats/<slug>/` and `/locations/<slug>/`. Update canonicals, Open Graph URLs, JSON-LD and sitemap in `retreats/_tools/seo.py` (and `robots.txt`, `sitemap.xml`) to the new permalinks, and use `seo_description` for meta descriptions (or an SEO plugin); carry the `noindex` on drafts over.
- **Behaviours still inline in the static pages**: itinerary progress rail, `photo-slider`, gallery slider and lightbox, accommodation and extras tab builders, `rail` arrows, home hero effects. Extract copies into `assets/` (the static pages must not change).
- **Not ported yet**: retreats listing filters (`css/filters.css` UI, view toggle, URL state), location blocks (guide, quote, more destinations), teacher "retreats with her" rail and media blocks, Journal category filter and "Explore this retreat" card, About cards and quotes, Teach-with-us steps, split and FAQ, home `tiers2`, `pillar`, `movement`, `booking`, `who` and FAQ sections.
- Menus use `yaww_nav_items()`; `register_nav_menus` is declared but not yet wired to `wp_nav_menu`.
- Journal: the 34 posts in `data/journal.js` (text and image blocks) need an import into post content; authors map to users or a field.

## Field map

Regenerate with `python3 bin/gen-acf.py --table`. Relations use post IDs; image fields are attachment IDs. Title, content and featured image carry `officialName`, the about text and the portrait (teacher and facilitator bio paragraphs = content).

| Post type | Meta key | Type | Source in the static site |
|---|---|---|---|
| `retreat` | `subtitle` | text | retreats/*/data.json subtitle |
| `retreat` | `location_id` | post | retreats.js locationId |
| `retreat` | `teacher_ids` | posts | retreats.js teacherIds |
| `retreat` | `start_date` | date | retreats.js startDate |
| `retreat` | `end_date` | date | retreats.js endDate |
| `retreat` | `status` | select | retreats.js status |
| `retreat` | `spaces_left` | number | retreats.js spacesLeft |
| `retreat` | `collection` | select | retreats.js collection (mirrors retreat_tier taxonomy) |
| `retreat` | `card_image` | image | retreats.js image / image2x |
| `retreat` | `hero_image` | image | data.json hero_image |
| `retreat` | `hotel_label` | text | BRIEF s.12: e.g. boutique hotel in Chiang Mai |
| `retreat` | `nights` | number | data.json nights |
| `retreat` | `includes` | list | data.json includes |
| `retreat` | `not_included` | list | data.json not_included |
| `retreat` | `itinerary` | rows | data.json itinerary |
| `retreat` | `rooms` | rows | data.json rooms (hotel name dropped) |
| `retreat` | `extras` | rows | data.json extras |
| `retreat` | `travel_tips` | rows | data.json travel_tips |
| `retreat` | `faqs` | rows | data.json faqs |
| `retreat` | `gallery` | gallery | data.json gallery |
| `retreat` | `gallery_title` | text | data.json gallery_title |
| `retreat` | `gallery_intro` | textarea | data.json gallery_intro |
| `retreat` | `deposit_usd` | number | data.json booking.deposit_usd |
| `retreat` | `balance_due_days` | number | data.json booking.balance_due_days |
| `retreat` | `roommate_note` | textarea | data.json booking.roommate_note |
| `retreat` | `wetravel_url` | url | window.YAWW_WETRAVEL.url (js/retreat-rooms.js) |
| `retreat` | `seo_description` | textarea | data.json seo_description |
| `location` | `display_name` | text | locations.js displayName |
| `location` | `region` | select | locations.js region |
| `location` | `country` | text | locations.js country |
| `location` | `intro` | textarea | locationpage-*.html hero copy |
| `location` | `hero_image` | image | locations.js image |
| `location` | `card_image` | image | locations.js image |
| `location` | `placeholder_hue` | number | locations.js placeholderHue |
| `location` | `hotel_label` | text | BRIEF s.12 |
| `location` | `travel_tips` | rows | page_content.py |
| `location` | `gallery` | gallery | locationpage-*.html |
| `teacher` | `short_name` | text | teachers.js shortName |
| `teacher` | `role` | text | teachers.js role |
| `teacher` | `short_bio` | textarea | teachers.js shortBio |
| `teacher` | `quote` | text | teachers.js quote |
| `teacher` | `specialties` | list | teachers.js specialties |
| `teacher` | `video_url` | url | teachers.js video |
| `teacher` | `spotify_url` | url | teachers.js spotify |
| `teacher` | `avatar` | image | teachers.js avatar |
| `teacher` | `photo_position` | text | teachers.js photoPosition |
| `teacher` | `needs_content` | bool | teachers.js needsContent |
| `facilitator` | `role` | text | team.js role |
| `facilitator` | `short_bio` | textarea | team.js shortBio |
| `facilitator` | `listed` | bool | team.js listed |
