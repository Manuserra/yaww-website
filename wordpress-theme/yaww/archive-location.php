<?php
/** Locations listing (locations.html): every destination, A to Z, then the closing band. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'locations', array( 'cta-parallax' ) );
$locs = get_posts( array( 'post_type' => 'location', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'suppress_filters' => false ) );
usort( $locs, function ( $a, $b ) { return strcmp( strtolower( get_the_title( $a ) ), strtolower( get_the_title( $b ) ) ); } );
get_header(); ?>
  <section class="locations">
    <div class="locations__head">
      <h1 class="section-title">Where we go</h1>
      <p class="locations__lede"><?php echo esc_html( yaww_e_count_word( count( $locs ) ) ); ?> destinations we return to year after year, each with the hotels, teachers and local guides we have come to trust. Pick a place to see what a week there looks like.</p>
    </div>

    <div class="locations-grid">
<?php foreach ( $locs as $lid ) { yaww_part( 'e-location-card', array( 'post_id' => $lid ) ); } ?>
    </div>
  </section>

<?php yaww_part( 'e-closing-cta' ); ?>
<?php get_footer();
