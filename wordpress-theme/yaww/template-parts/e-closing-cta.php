<?php
/** The "Your next week away starts here" band that closes the listing, people and About pages (static copy). */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
  <section class="home-cta">
    <div class="home-cta__bg" aria-hidden="true"></div>
    <div class="home-cta__content">
      <h2 class="home-cta__title">Your next week away starts here</h2>
      <p class="home-cta__desc">Pick a place you've always wanted to know. We'll take care of the rest.</p>
      <div class="home-cta__btns">
        <a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">Explore upcoming retreats</a>
        <?php /* TODO (static too): route "Questions? Talk to us" to a contact/sales flow. */ ?>
        <a class="btn btn--light" href="#">Questions? Talk to us</a>
      </div>
    </div>
  </section>
