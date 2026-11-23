<?php
/**
 * Plain page (Policies and any page without a template): title + editor content, set in the About
 * page's type (the static site has no Policies page: its footer link is "#", so this has no spec to match).
 * Dedicated layouts live in page-templates/.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'page' );
get_header();
while ( have_posts() ) : the_post(); ?>
  <section class="about-intro">
    <h1 class="about-intro__title"><?php the_title(); ?></h1>
  </section>
  <section class="about-section">
    <div class="about-inner about-prose">
      <?php
      $content = get_the_content();
      if ( '' === trim( $content ) ) {
	      echo '<p class="about-body">' . esc_html( yaww_e_missing( 'page content' ) ) . '</p>';
      } else {
	      // Paragraphs in the About body style.
	      echo str_replace( '<p>', '<p class="about-body">', apply_filters( 'the_content', $content ) ); // phpcs:ignore
      }
      ?>
    </div>
  </section>
<?php endwhile;
get_footer();
