<?php
/**
 * Home. Ported from index.html. The static page renders the retreat rail and testimonials with JS from
 * data/*.js; here the rail is a WP_Query. Gaps: hero video (see README), tiers2 / pillar / movement /
 * booking / who sections and the home FAQ are editorial copy still to be moved into page fields.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$next = new WP_Query( array(
	'post_type' => 'retreat', 'posts_per_page' => 8, 'meta_key' => 'start_date', 'orderby' => 'meta_value', 'order' => 'ASC',
	'meta_query' => array( array( 'key' => 'start_date', 'value' => gmdate( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ) ),
) );
?>
<section class="home-hero home-hero--overlay">
  <div class="home-hero__media home-hero__media--video">
    <video src="<?php echo esc_url( YAWW_URI . '/assets/media/hero-home.mp4' ); ?>" poster="<?php echo esc_url( YAWW_URI . '/assets/images/hero-home-poster.jpg' ); ?>" autoplay muted loop playsinline preload="auto" aria-hidden="true" tabindex="-1"></video>
  </div>
  <div class="home-hero__content">
    <h1 class="home-hero__title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
    <a class="btn btn--light" href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">Explore upcoming retreats</a>
  </div>
</section>

<section class="home-next home-next--headed" id="next-retreats">
  <div class="home-next__head">
    <p class="kicker home-next__kicker">Upcoming retreats</p>
    <h2 class="section-title">Where we're going next</h2>
  </div>
  <div class="rail" id="retreatsRail">
    <div class="rail__track" id="nextRetreatsTrack" tabindex="0" aria-label="Upcoming retreats">
      <?php while ( $next->have_posts() ) : $next->the_post(); yaww_part( 'retreat-card', array( 'post_id' => get_the_ID() ) ); endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
  <div class="home-next__foot"><a class="btn btn--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">See all upcoming dates</a></div>
</section>

<?php yaww_part( 'testimonials' ); ?>
<?php yaww_part( 'for-teachers' ); ?>

<section class="home-cta">
  <div class="home-cta__bg" aria-hidden="true"></div>
  <div class="home-cta__content">
    <h2 class="home-cta__title">Your next week away starts here</h2>
    <p class="home-cta__desc">Pick a place you've always wanted to know. We'll take care of the rest.</p>
    <div class="home-cta__btns">
      <a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">Explore upcoming retreats</a>
      <a class="btn btn--light" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Questions? Talk to us</a>
    </div>
  </div>
</section>
<?php get_footer();
