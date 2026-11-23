<?php
/**
 * Site footer: the static pages' footer (newsletter + one column per nav section + sign-off),
 * same labels and order, WordPress URLs. Locations link to the location page of that country
 * when there is exactly one, else to the Locations page; Journal topics to their category.
 * The newsletter form keeps the static markup; agent C wires its submission.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/views.php';
$yaww_tier = function ( $slug ) {
	$t = get_term_by( 'slug', $slug, 'retreat_tier' );
	$l = $t ? get_term_link( $t ) : '';
	return esc_url( $l && ! is_wp_error( $l ) ? $l : yaww_static_url( 'retreats.html?tier=' . $slug ) );
};
$yaww_country = function ( $country ) {
	$ids = get_posts( array( 'post_type' => 'location', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) );
	$hit = array();
	foreach ( $ids as $id ) {
		if ( 0 === strcasecmp( (string) yaww_meta( 'country', $id ), $country ) ) { $hit[] = $id; }
	}
	return esc_url( 1 === count( $hit ) ? get_permalink( $hit[0] ) : yaww_static_url( 'locations.html' ) );
};
$yaww_cat = function ( $name ) {
	$c = get_term_by( 'name', $name, 'category' );
	return esc_url( $c ? get_category_link( $c ) : yaww_static_url( 'journal.html' ) );
};
?>
</main>

<footer class="site-footer" id="newsletter">
  <div class="container">

    <!-- Newsletter -->
    <section class="footer-news">
      <div class="footer-news__head">
        <h2 class="footer-news__title">Subscribe to our newsletter.</h2>
        <p class="footer-news__sub">Sign up with your email address to receive all the juicy updates.</p>
      </div>
      <form class="footer-news__form" novalidate>
        <label class="footer-news__label-sr" for="nl-email">Email address</label>
        <input class="footer-news__input" id="nl-email" name="email" type="email" placeholder="Email address" autocomplete="email">
        <button class="footer-news__submit" type="submit">Sign up</button>
      </form>
    </section>

    <!-- Sitemap — one column per nav section, in nav order, so the footer
         cannot drift out of step with the header again. Home is not a nav
         section, so it sits in the utility line at the very bottom instead
         of taking a sixth column and breaking the five. -->
    <nav class="footer-nav" aria-label="Footer">
      <div class="footer-nav__cols">

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo yaww_u( 'retreats.html' ); ?>">Retreats</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo yaww_u( 'retreats.html' ); ?>">All Retreats</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_tier( 'signature-collection' ); ?>">Signature Collection</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_tier( 'the-reserve' ); ?>">The Reserve</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_tier( 'deep-dives' ); ?>">Deep Dives</a></li>
            <li><a class="footer-link" href="<?php echo yaww_u( 'retreats.html' ); ?>">Upcoming Dates</a></li>
          </ul>
        </div>

        <!-- Preview: single column instead of the usual 2-rank split (see
             footer-col__list--split below) — the user asked to see all ten
             destinations and all seven Journal topics stacked in one line
             each, to compare against the split version. -->
        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo yaww_u( 'locations.html' ); ?>">Locations</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo $yaww_country( 'France' ); ?>">France</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Greece' ); ?>">Greece</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Iceland' ); ?>">Iceland</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Italy' ); ?>">Italy</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Mexico' ); ?>">Mexico</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Montenegro' ); ?>">Montenegro</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Morocco' ); ?>">Morocco</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Portugal' ); ?>">Portugal</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Spain' ); ?>">Spain</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_country( 'Thailand' ); ?>">Thailand</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo yaww_u( 'about.html' ); ?>">About</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo yaww_u( 'about.html' ); ?>">Company</a></li>
            <li><a class="footer-link" href="<?php echo yaww_u( 'teachers.html' ); ?>">Teachers</a></li>
            <li><a class="footer-link" href="<?php echo yaww_u( 'facilitators.html' ); ?>">Facilitators</a></li>
            <li><a class="footer-link" href="<?php echo yaww_u( 'policies.html' ); ?>">Policies</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo yaww_u( 'journal.html' ); ?>">Journal</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo $yaww_cat( 'Astrology & Tarot' ); ?>">Astrology &amp; Tarot</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_cat( 'Foodies' ); ?>">Foodies</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_cat( 'Guides' ); ?>">Guides</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_cat( 'Inspo' ); ?>">Inspo</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_cat( 'Lifestyle' ); ?>">Lifestyle</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_cat( 'Travel' ); ?>">Travel</a></li>
            <li><a class="footer-link" href="<?php echo $yaww_cat( 'Yoga' ); ?>">Yoga</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo yaww_u( 'contact.html' ); ?>">Contact</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo yaww_u( 'contact.html' ); ?>">Ask a question</a></li>
            <li><a class="footer-link" href="<?php echo yaww_u( 'contact.html#private-groups' ); ?>">Private groups</a></li>
            <li><a class="footer-link" href="<?php echo yaww_u( 'teach-with-us.html' ); ?>">Teach with us</a></li>
          </ul>
        </div>

      </div>
    </nav>

    <!-- Sign-off: the logo goes home, the icons leave the site, and the
         utility line carries Home as a word for anyone who never learned
         that a logo is clickable. -->
    <div class="footer-logo">
      <a href="<?php echo yaww_u( 'index.html' ); ?>" aria-label="Yoga Adventures Worldwide — home">
        <svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg>
      </a>
      <ul class="footer-social-row">
        <li>
          <a class="footer-social" href="#" aria-label="Yoga Adventures Worldwide on Instagram">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
              <rect x="3.75" y="3.75" width="16.5" height="16.5" rx="4.5"/>
              <circle cx="12" cy="12" r="3.9"/>
              <circle cx="16.9" cy="7.1" r="1" fill="currentColor" stroke="none"/>
            </svg>
          </a>
        </li>
        <li>
          <a class="footer-social" href="#" aria-label="Yoga Adventures Worldwide on Facebook">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
              <circle cx="12" cy="12" r="8.25"/>
              <path d="M13.9 8.1h-1c-.9 0-1.5.6-1.5 1.5v10.4M9.8 12.4h3.7" stroke-linecap="round"/>
            </svg>
          </a>
        </li>
        <li>
          <a class="footer-social" href="#" aria-label="Yoga Adventures Worldwide on LinkedIn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
              <rect x="3.75" y="3.75" width="16.5" height="16.5" rx="2.5"/>
              <path d="M8.1 10.6v5.8M8.1 7.9v.01" stroke-linecap="round"/>
              <path d="M11.9 16.4v-5.8M11.9 13c0-1.3 1-2.4 2.2-2.4s2.2 1.1 2.2 2.4v3.4" stroke-linecap="round"/>
            </svg>
          </a>
        </li>
      </ul>
    </div>

    <p class="footer-copy"><a class="footer-link" href="<?php echo yaww_u( 'index.html' ); ?>">Home</a><span aria-hidden="true"> · </span>Yoga Adventures Worldwide, LLC © 2025</p>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
