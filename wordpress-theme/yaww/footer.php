<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
$tiers = get_terms( array( 'taxonomy' => 'retreat_tier', 'hide_empty' => false ) );
$locs  = get_posts( array( 'post_type' => 'location', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
$cats  = get_categories( array( 'hide_empty' => false ) );
?>
</main>

<footer class="site-footer" id="newsletter">
  <div class="container">

    <section class="footer-news">
      <div class="footer-news__head">
        <h2 class="footer-news__title">Subscribe to our newsletter.</h2>
        <p class="footer-news__sub">Sign up with your email address to receive all the juicy updates.</p>
      </div>
      <?php /* TODO: newsletter endpoint (see README "What is left"). */ ?>
      <form class="footer-news__form" novalidate>
        <label class="footer-news__label-sr" for="nl-email">Email address</label>
        <input class="footer-news__input" id="nl-email" name="email" type="email" placeholder="Email address" autocomplete="email">
        <button class="footer-news__submit" type="submit">Sign up</button>
      </form>
    </section>

    <nav class="footer-nav" aria-label="Footer">
      <div class="footer-nav__cols">

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">Retreats</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">All Retreats</a></li>
            <?php foreach ( (array) $tiers as $t ) : if ( is_wp_error( $t ) ) { continue; } ?>
              <li><a class="footer-link" href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></li>
            <?php endforeach; ?>
            <li><a class="footer-link" href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">Upcoming Dates</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo esc_url( get_post_type_archive_link( 'location' ) ); ?>">Locations</a></h3>
          <ul class="footer-col__list">
            <?php foreach ( $locs as $l ) : ?>
              <li><a class="footer-link" href="<?php echo esc_url( get_permalink( $l ) ); ?>"><?php echo esc_html( yaww_meta( 'country', $l->ID ) ?: $l->post_title ); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Company</a></li>
            <li><a class="footer-link" href="<?php echo esc_url( get_post_type_archive_link( 'teacher' ) ); ?>">Teachers</a></li>
            <li><a class="footer-link" href="<?php echo esc_url( get_post_type_archive_link( 'facilitator' ) ); ?>">Facilitators</a></li>
            <li><a class="footer-link" href="<?php echo esc_url( home_url( '/policies/' ) ); ?>">Policies</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo esc_url( home_url( '/journal/' ) ); ?>">Journal</a></h3>
          <ul class="footer-col__list">
            <?php foreach ( $cats as $c ) : if ( 1 === (int) $c->term_id ) { continue; } ?>
              <li><a class="footer-link" href="<?php echo esc_url( get_category_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-col__title"><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></h3>
          <ul class="footer-col__list">
            <li><a class="footer-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Ask a question</a></li>
            <li><a class="footer-link" href="<?php echo esc_url( home_url( '/contact/#private-groups' ) ); ?>">Private groups</a></li>
            <li><a class="footer-link" href="<?php echo esc_url( home_url( '/teach-with-us/' ) ); ?>">Teach with us</a></li>
          </ul>
        </div>

      </div>
    </nav>

    <div class="footer-logo">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Yoga Adventures Worldwide — home">
        <svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg>
      </a>
      <ul class="footer-social-row">
        <?php /* TODO: real social URLs (static site uses "#"). */ ?>
        <li><a class="footer-social" href="#" aria-label="Yoga Adventures Worldwide on Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3.75" y="3.75" width="16.5" height="16.5" rx="4.5"/><circle cx="12" cy="12" r="3.9"/><circle cx="16.9" cy="7.1" r="1" fill="currentColor" stroke="none"/></svg></a></li>
        <li><a class="footer-social" href="#" aria-label="Yoga Adventures Worldwide on Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="8.25"/><path d="M13.9 8.1h-1c-.9 0-1.5.6-1.5 1.5v10.4M9.8 12.4h3.7" stroke-linecap="round"/></svg></a></li>
        <li><a class="footer-social" href="#" aria-label="Yoga Adventures Worldwide on LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3.75" y="3.75" width="16.5" height="16.5" rx="2.5"/><path d="M8.1 10.6v5.8M8.1 7.9v.01" stroke-linecap="round"/><path d="M11.9 16.4v-5.8M11.9 13c0-1.3 1-2.4 2.2-2.4s2.2 1.1 2.2 2.4v3.4" stroke-linecap="round"/></svg></a></li>
      </ul>
    </div>

    <p class="footer-copy"><a class="footer-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span aria-hidden="true"> · </span>Yoga Adventures Worldwide, LLC © <?php echo esc_html( gmdate( 'Y' ) ); ?></p>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
