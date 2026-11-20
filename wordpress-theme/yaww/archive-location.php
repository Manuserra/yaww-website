<?php
/** Locations listing (locations.html): image, title, caption per destination. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<section class="locations">
  <div class="locations__head">
    <h1 class="section-title">Locations</h1>
  </div>
  <div class="locations-grid">
    <?php while ( have_posts() ) : the_post(); ?>
    <article class="location">
      <a class="location__link" href="<?php the_permalink(); ?>">
        <div class="location__image"><?php echo yaww_img( 'card_image', 'yaww-loc-card', array( 'alt' => get_the_title(), 'loading' => 'lazy' ) ) ?: get_the_post_thumbnail( null, 'yaww-loc-card' ); // phpcs:ignore ?></div>
        <h2 class="location__title"><?php the_title(); ?></h2>
        <p class="location__caption"><?php echo esc_html( yaww_meta( 'intro' ) ); ?></p>
      </a>
    </article>
    <?php endwhile; ?>
  </div>
</section>
<?php get_footer();
