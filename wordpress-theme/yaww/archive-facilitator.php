<?php
/** Facilitators grid (facilitators.html). Only people with `listed` set; unlisted ones keep a page but no card. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<section class="people">
  <div class="people__head"><h1 class="section-title">Facilitators</h1></div>
  <div class="people-grid">
    <?php while ( have_posts() ) : the_post(); if ( ! yaww_meta( 'listed' ) ) { continue; } yaww_part( 'person-card' ); endwhile; ?>
  </div>
</section>
<?php get_footer();
