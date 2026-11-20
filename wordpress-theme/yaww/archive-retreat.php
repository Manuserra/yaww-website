<?php
/**
 * Retreats listing (retreats.html). Cards are server-rendered, ordered by start date. The static page's
 * filter UI (js/filters + css/filters.css, view toggle, URL state) is a gap; tier terms link to retreat_tier archives.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$q = new WP_Query( array(
	'post_type' => 'retreat', 'posts_per_page' => -1, 'meta_key' => 'start_date', 'orderby' => 'meta_value', 'order' => 'ASC',
	'tax_query' => is_tax( 'retreat_tier' ) ? array( array( 'taxonomy' => 'retreat_tier', 'terms' => get_queried_object_id() ) ) : array(),
) );
?>
<section class="people">
  <div class="people__head">
    <h1 class="section-title"><?php echo is_tax( 'retreat_tier' ) ? esc_html( single_term_title( '', false ) ) : 'Retreats'; ?></h1>
  </div>
</section>
<section class="results" id="results" aria-live="polite">
  <div class="grid">
    <?php while ( $q->have_posts() ) : $q->the_post(); yaww_part( 'retreat-card', array( 'post_id' => get_the_ID() ) ); endwhile; wp_reset_postdata(); ?>
  </div>
</section>
<?php get_footer();
