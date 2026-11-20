<?php
/**
 * Template Name: About
 * about.html: intro, story sections, how-we-work cards, team, quotes. Page content (editor) carries the story
 * for now; the cards/team grids are a gap (team = facilitator posts with `listed`).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); ?>
<section class="about-intro">
  <div class="about-intro__figure"><?php the_post_thumbnail( 'yaww-hero' ); ?></div>
  <h1 class="about-intro__title"><?php the_title(); ?></h1>
  <div class="about-intro__lede"><?php echo esc_html( get_the_excerpt() ); ?></div>
</section>
<section class="about-section"><div class="about-inner about-body"><?php the_content(); ?></div></section>
<section class="about-section about-team">
  <div class="about-team__head"><h2 class="about-split__head">The team</h2></div>
  <div class="about-team__grid">
    <?php $team = new WP_Query( array( 'post_type' => 'facilitator', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'meta_key' => 'listed', 'meta_value' => '1' ) );
    while ( $team->have_posts() ) : $team->the_post(); ?>
      <article class="about-person"><a href="<?php the_permalink(); ?>">
        <div class="about-person__media"><?php the_post_thumbnail( 'yaww-person', array( 'loading' => 'lazy' ) ); ?></div>
        <h3 class="about-person__name"><?php the_title(); ?></h3>
        <p class="about-person__role"><?php echo esc_html( yaww_meta( 'role' ) ); ?></p>
      </a></article>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</section>
<?php endwhile;
get_footer();
