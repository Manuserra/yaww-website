<?php
/** Teachers grid (teachers.html). Photo, name, blurb; people-cards.js not needed, cards are server-rendered. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<section class="people">
  <div class="people__head"><h1 class="section-title">Teachers</h1></div>
  <div class="people-grid">
    <?php while ( have_posts() ) : the_post(); if ( yaww_meta( 'needs_content' ) ) { continue; } yaww_part( 'person-card' ); endwhile; ?>
  </div>
</section>
<?php get_footer();
