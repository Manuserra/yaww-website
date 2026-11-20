<?php
/** Journal index at /journal/ (journal.html). Category filter UI is a gap; categories link to category archives. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>
<section class="people"><div class="people__head"><h1 class="section-title">Journal</h1></div></section>
<section class="journal-grid">
  <?php while ( have_posts() ) : the_post(); $cats = get_the_category(); ?>
  <article class="journal" data-cats="<?php echo esc_attr( implode( ' ', wp_list_pluck( $cats, 'slug' ) ) ); ?>">
    <a class="journal__link" href="<?php the_permalink(); ?>">
      <div class="journal__image"><?php the_post_thumbnail( 'yaww-journal', array( 'loading' => 'lazy' ) ); ?></div>
      <div class="journal__cat text-slug"><div><?php echo $cats ? esc_html( $cats[0]->name ) : ''; ?></div></div>
      <div class="journal__title"><div class="heading-h5 text-bold"><?php the_title(); ?></div></div>
      <div class="journal__caption text-left text-small"><p><?php echo esc_html( get_the_excerpt() ); ?></p></div>
      <div class="journal__author text-caption"><?php the_author(); ?></div>
    </a>
  </article>
  <?php endwhile; ?>
</section>
<?php the_posts_pagination(); get_footer();
