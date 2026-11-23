# WordPress build · from 2026-11-23 · launch target Friday 2026-11-27

The file every agent reads first and returns to after a context reset.

## Decisions (from the user, 2026-11-23)

- **Build locally**, in the LocalWP site "localwp.com" (`~/Local Sites/localwpcom/app/public`, http://localwpcom.local, nginx, PHP 8.2.29, MySQL 8.4, WordPress 7.1.4, ACF Pro active and licensed).
- **Launch:** replace the WordPress that is live today at yogaadventuresworldwide.com, **same domain**, and **keep the old URLs** (same slugs where possible, 301 redirects for every URL that changes) so no SEO is lost.
- **Editable in WP by the team:** retreats, teachers (and facilitators), journal, locations' data. Home, About, Contact, Teach with us, Policies stay as theme templates (code).
- **Forms** (contact, retreat pop-up, teach application, waitlist, newsletter): each submission is **emailed** to the team, **stored in WordPress** (a private `lead` post type), **posted to Slack** (incoming webhook) and **sent to WeTravel's CRM** if an API exists. Webhook URL / API keys go in a settings page; empty = skipped.
- **Pixel parity:** the WordPress site must look and behave exactly like the static site on `main` (commit f72b046). The static site is the spec. Reuse `css/` and `js/` as they are.

## Rules

- **Branch:** `wp/2026-11-23` (from main). Push only this branch. **Never push `main`.** Nothing goes to any live server.
- The theme lives in `wordpress-theme/yaww/` and is **symlinked** into LocalWP's `wp-content/themes/yaww`.
- Tools: PHP `~/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin/bin/php`; WP-CLI `/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar` (DB socket: see `~/Local Sites/localwpcom/conf` / Local's run dir). Mail is caught by Mailpit (Local).
- **Confidentiality:** no hotel names on any page except Utopia (`hotel_label` field only). Scan diffs before commit.
- **Copy:** never invent; missing data stays `[missing data: …]`.
- **CTAs:** Book now / WeTravel behaviour unchanged (user is talking to WeTravel).
- Agents own disjoint files; `git add` explicit paths only; retry on `index.lock`; commits end with `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
- Report: append to `WP-REPORT.md` (Spanish, short).

## Ownership

| Area | Owner files |
|---|---|
| A · Setup + import | `wordpress-theme/yaww/functions.php`, `inc/post-types.php`, `inc/fields*.{php,json}`, `inc/helpers.php`, `acf-json/`, `bin/`, the LocalWP install (theme link, plugins, settings, menus, content, media) |
| B · URLs + SEO | `docs/wp-url-map.md`, `wordpress-theme/yaww/inc/redirects.php`, `inc/seo.php` |
| C · Forms | `wordpress-theme/yaww/inc/forms.php`, `inc/settings.php`, `page-templates/contact.php`, `page-templates/teach-with-us.php`, `template-parts/lead-modal.php` |
| D · Templates 1 | `front-page.php`, `archive-retreat.php`, `taxonomy-retreat_tier.php`, `single-retreat.php`, `template-parts/retreat-card.php`, `template-parts/hero.php`, `template-parts/testimonials.php`, `header.php`, `footer.php` |
| E · Templates 2 | `archive-location.php`, `single-location.php`, `archive-teacher.php`, `single-teacher.php`, `archive-facilitator.php`, `single-facilitator.php`, `page-templates/about.php`, `home.php`, `single.php`, `archive.php`, `page.php`, `template-parts/person-card.php` |

Anything outside your row: write the exact request in your final report.

## Priority

1. A (nothing renders without content) · 2. D (retreat pages and listing = revenue) · 3. C (forms) · 4. B (URLs before launch) · 5. E.

## Days

- **Mon–Tue:** theme active, all content imported, every template at parity.
- **Wed:** forms end-to-end, URL map + redirects, SEO, menus.
- **Thu:** QA (desktop, phone, keyboard, speed), admin UX for the team, staging/migration plan.
- **Fri:** content check with Christina, launch on the current host, verify redirects, backup.

## Open questions (for the user)

- Current host of yogaadventuresworldwide.com and access (for the Friday migration).
- Slack incoming-webhook URL and channel.
- WeTravel CRM/API: does their account have API access/keys?
- Which email(s) receive form messages (Jenniferlyn's?) and the public email for the site.
