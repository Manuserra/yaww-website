# Review badges on About (Google and Tripadvisor)

`about.html` shows two static badges under "What our guests say", each an `<a class="review-badge" data-review-widget="google|tripadvisor">`. They are styled stand-ins with the same footprint the real widgets will need, so swapping them does not move the page. Styles are in `css/about.css` (`.review-badge`).

The figures (4.9 Google, 4.8 Tripadvisor) were carried over from the previous version of the page. **Confirm both before publishing**, and fill in the two `href="#"` links.

## Tripadvisor: official widget, recommended

- Tripadvisor still offers free widgets at https://www.tripadvisor.com/Widgets (rating badge, latest-review snippet, "Review us"). You search for the listing, pick a style and copy a script snippet.
- **Access needed:** the YAWW listing must exist on Tripadvisor. Generating the code works from the public Widgets page; claiming the listing (free, https://www.tripadvisor.com/Owners, Management Center) is recommended so the business details and widget can be managed. Question for Christina: who holds the Tripadvisor owner login?
- **Swap:** replace the Tripadvisor `<a class="review-badge">` with the snippet. The widget brings its own styling and loads a third-party script (cookies), so it needs to be covered by the cookie notice.
- If the official widget looks off-brand, keep our static badge and link it to the listing. The rating changes slowly; updating it by hand once a quarter is honest and loads nothing.

## Google: no official embed

- Google does not offer an embeddable reviews or rating widget for a Business Profile.
- **Option A (recommended for now):** keep the static badge, link it to the Google reviews page (Maps "Reviews" URL or the `g.page/r/.../review` short link from the Business Profile), and update the score by hand. Needs: access to the Google Business Profile to copy the link.
- **Option B, live data:** Google Places API (Place Details: `rating`, `userRatingCount`, up to 5 `reviews`). Needs a Google Cloud project with billing enabled, an API key restricted to the site's domain, and the Place ID. Google's terms require showing Google attribution next to the data, and the call should be made server-side or at build time (e.g. a WordPress cron that caches the value), not on every page view.
- **Option C:** a third-party widget (Elfsight, Trustindex, EmbedSocial and similar) connected to the Business Profile. Fastest to set up, but it is another paid service, adds its own script and branding, and usually sets cookies.

## Questions for Christina

1. Who has owner access to the Tripadvisor listing and to the Google Business Profile?
2. Are 4.9 (Google) and 4.8 (Tripadvisor) the current figures?
3. OK with a third-party script on the About page (Tripadvisor widget), or prefer the static badge linking out?
