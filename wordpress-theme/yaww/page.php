<?php
/** Plain page: title + editor content. Dedicated layouts live in page-templates/. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); ?>
<section class="about-section about-prose">
  <div class="about-inner">
    <h1 class="about-intro__title"><?php the_title(); ?></h1>
    <div class="about-body"><?php the_content(); ?></div>
  </div>
</section>
<?php endwhile;
get_footer();
