# YAWW WordPress theme (draft 0.1)

Classic PHP theme (no build step, WordPress 6.x, PHP 7.4+) that will reproduce the official static site.
The static HTML site is untouched: this folder only reads from `../../css`, `../../js` and `../../images`.
ACF Pro is the field storage (field groups in `acf-json/`); without ACF the fields fall back to native post meta and a stop-gap meta box.

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

## Local site (LocalWP "localwp.com", http://localwpcom.local)

The theme folder is symlinked into `~/Local Sites/localwpcom/app/public/wp-content/themes/yaww` and active.
Start the site in Local first (its MySQL listens on a socket in Local's run dir).

| Step | Command (from the repo root) |
|---|---|
| WP-CLI | `bash wordpress-theme/yaww/bin/wp.sh <args>` e.g. `... wp.sh post list --post_type=retreat` (uses Local's PHP 8.2 and MySQL socket; `LOCAL_SITE_ID`, `LOCAL_PHP` override) |
| Assets | `bash wordpress-theme/yaww/bin/sync-assets.sh` after **any** change in `css/`, `js/` or a template-level image (copies into `assets/`, gitignored) |
| Fields | edit `inc/fields-spec.json`, then `python3 wordpress-theme/yaww/bin/gen-acf.py` (rewrites `acf-json/`, then the README table with `--table`) and sync in ACF (Custom Fields > Sync, or `wp.sh eval 'yaww...'`, see below) |
| Content | `bash wordpress-theme/yaww/bin/import.sh` = `bin/export-content.py` (static site -> `bin/content.json`) + `wp.sh eval-file bin/import.php`. Idempotent; `only=media,locations,people,retreats,journal,site` limits it |
| Lint | `find wordpress-theme/yaww -name '*.php' -exec "$PHP" -l {} \;` with Local's PHP |
| Debug | `WP_DEBUG_LOG` is on: `~/Local Sites/localwpcom/app/public/wp-content/debug.log` |

### What the import does

- Reads what the static pages **show**, not the raw scrape: `retreatpage-*.html` (about, hosts, includes, itinerary, stays,
  rooms, extras, tips, gallery, Additional info, testimonials, booking terms), `data/*.js`, `retreats/_tools/location_content.py`.
- Retreats: the 17 dated editions of `data/retreats.js` (not `santorini-sep-2027-berger`, removed) plus the 6 dateless
  "program" retreats of `data/locations.js` (status `waitlist`, slug = location id, linked from the location's `program_retreat`).
- Journal: 34 posts at `/<slug>/` (7 keep the live site's slug, see B's map), Gutenberg paragraph/image blocks grouped in
  `post__text` groups, categories in the static order (`yaww_post_categories()`), undated posts get `date_label` empty.
- Site: pages Home (front), About, Contact, Teach with us (templates), Journal (posts page), Policies; permalinks `/%postname%/`; menus Primary/Footer.
- Media: each file once, in `uploads/yaww/<path under images/>` with its **original file name** (`_yaww_source` = repo path).
  `big_image_size_threshold` is off, so `full` is the original file. A `@2x` twin is the attachment's `2x` size.

### Reading fields in templates

- `yaww_meta()`, `yaww_list()` (list = ACF repeater of `{value}`), `yaww_rows()` (rows), `yaww_lines()` for "one per line" sub fields.
  ACF is the storage; dates come back `Y-m-d`; image/gallery fields are attachment IDs; relations are post IDs.
- Images: print `wp_get_attachment_image( $id, 'full' )` (or `yaww_img_1x2x()` for the static `1x, 2x` srcset) so `src` ends in
  `/<original-name>.webp` and **css/photo-focus.css keeps matching** the itinerary photos. Resized sizes rename the file (`-1200x730`).
- `yaww_data_js( array( 'locations', 'regions', 'teachers', 'team', 'retreats', 'journal' ) )` prints `window.YAWW_*` in the
  shape of `data/*.js` (absolute permalinks and media URLs). `yaww_url_map()` maps `*.html` to permalinks.

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
| `retreat` | `facilitator_ids` | posts | retreats/_tools/page_content.py FACILITATORS |
| `retreat` | `start_date` | date | retreats.js startDate |
| `retreat` | `end_date` | date | retreats.js endDate |
| `retreat` | `status` | select | retreats.js status; waitlist = dateless retreat from locations.js program |
| `retreat` | `spaces_left` | number | retreats.js spacesLeft |
| `retreat` | `collection` | select | retreats.js collection (mirrors retreat_tier taxonomy) |
| `retreat` | `card_image` | image | retreats.js image / image2x |
| `retreat` | `hero_image` | image | retreatpage-*.html hero |
| `retreat` | `hotel_label` | text | BRIEF s.12: e.g. boutique hotel in Chiang Mai |
| `retreat` | `nights` | number | data.json nights |
| `retreat` | `includes` | list | data.json includes |
| `retreat` | `not_included` | list | data.json not_included |
| `retreat` | `includes_notes` | list | retreatpage-*.html .retreat-includes__footnote |
| `retreat` | `includes_image` | image | retreatpage-*.html .retreat-includes__bg |
| `retreat` | `itinerary` | rows | data.json itinerary |
| `retreat` | `itinerary_notes` | list | retreatpage-*.html .retreat-itinerary__note |
| `retreat` | `stays` | rows | retreatpage-*.html var HOTELS |
| `retreat` | `room_venues` | list | retreatpage-*.html var ROOM_VENUES |
| `retreat` | `rooms` | rows | retreatpage-*.html var TIERS (hotel name dropped) |
| `retreat` | `extras` | rows | retreatpage-*.html var EXTRAS |
| `retreat` | `travel_tips` | rows | retreatpage-*.html .tips__group |
| `retreat` | `faqs` | rows | retreatpage-*.html .faq (Additional info) |
| `retreat` | `gallery` | gallery | retreatpage-*.html .gallery__slide (--r cycles 28/17,1/1,28/17,5/4,28/17,1/1,28/17,5/4,1/1,28/17) |
| `retreat` | `gallery_title` | text | data.json gallery_title |
| `retreat` | `gallery_intro` | textarea | data.json gallery_intro |
| `retreat` | `testimonials` | rows | retreatpage-*.html var TESTIMONIALS |
| `retreat` | `deposit_usd` | number | data.json booking.deposit_usd |
| `retreat` | `balance_due_days` | number | data.json booking.balance_due_days |
| `retreat` | `roommate_note` | textarea | data.json booking.roommate_note |
| `retreat` | `wetravel_url` | url | window.YAWW_WETRAVEL.url (js/retreat-rooms.js) |
| `retreat` | `seo_description` | textarea | retreatpage-*.html meta description |
| `location` | `display_name` | text | locations.js displayName |
| `location` | `card_name` | text | locations.js cardName |
| `location` | `short_name` | text | locations.js name |
| `location` | `region` | select | locations.js region |
| `location` | `country` | text | locations.js country |
| `location` | `hero_image` | image | location_content.py hero |
| `location` | `card_image` | image | images/location-<id>.jpg (locations.html grid) |
| `location` | `placeholder_hue` | number | locations.js placeholderHue |
| `location` | `hotel_label` | text | BRIEF s.12 |
| `location` | `eyebrow` | text | location_content.py eyebrow |
| `location` | `tagline` | text | location_content.py tagline |
| `location` | `blurb` | textarea | location_content.py blurb |
| `location` | `facts` | rows | location_content.py facts |
| `location` | `intro_title` | text | location_content.py intro_title / intro |
| `location` | `intro_image` | image | location_content.py intro_img |
| `location` | `guide` | rows | location_content.py guide |
| `location` | `know` | rows | location_content.py know |
| `location` | `quote_text` | textarea | location_content.py quote[0] |
| `location` | `quote_name` | text | location_content.py quote[1] |
| `location` | `quote_source` | text | location_content.py quote[2] |
| `location` | `program_retreat` | post | locations.js program |
| `location` | `travel_tips` | rows | page_content.py |
| `location` | `gallery` | gallery | locationpage-*.html |
| `teacher` | `short_name` | text | teachers.js shortName |
| `teacher` | `role` | text | teachers.js role |
| `teacher` | `short_bio` | textarea | teachers.js shortBio |
| `teacher` | `host_bio` | textarea | retreatpage-*.html .host__bio |
| `teacher` | `quote` | text | teachers.js quote |
| `teacher` | `specialties` | list | teachers.js specialties |
| `teacher` | `youtube` | url | teachers.js youtube / video |
| `teacher` | `spotify_url` | url | teachers.js spotifyPlaylist / spotify |
| `teacher` | `website` | url | teachers.js website |
| `teacher` | `website_label` | text | teachers.js website.label |
| `teacher` | `instagram` | text | teachers.js instagram |
| `teacher` | `avatar` | image | teachers.js avatar |
| `teacher` | `photo_position` | text | teachers.js photoPosition |
| `teacher` | `needs_content` | bool | teachers.js needsContent |
| `facilitator` | `role` | text | team.js role |
| `facilitator` | `short_bio` | textarea | team.js shortBio |
| `facilitator` | `listed` | bool | team.js listed |
| `facilitator` | `photo_position` | text | team.js photoPosition |
| `post` | `date_label` | text | journal.js date |
| `post` | `author_name` | text | journal.js author.name |
| `post` | `author_photo` | image | journal.js author.photo / photo2x |
| `post` | `author_bio` | textarea | journal.js author.bio |
| `post` | `author_person` | post | journal.js author.pageUrl |
| `post` | `retreat_title` | text | journal.js retreat.title |
| `post` | `retreat_subtitle` | text | journal.js retreat.subtitle |
| `post` | `retreat_text` | textarea | journal.js retreat.text |
| `post` | `retreat_location` | post | journal.js retreat.locationId |
| `post` | `retreat_image` | image | journal.js retreat.image / image2x |
