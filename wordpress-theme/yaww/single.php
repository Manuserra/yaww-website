<?php
/**
 * Journal post (journal/*.html, css/journal-post.css). Gap: "Explore this retreat" card (needs a
 * retreat_id post field on posts) and related posts.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); ?>
<article class="post">
  <header class="post__head">
    <p class="post__cats"><?php the_category( ', ' ); ?></p>
    <h1 class="post__title"><?php the_title(); ?></h1>
    <?php the_post_thumbnail( 'yaww-journal' ); ?>
  </header>
  <div class="post__body"><?php the_content(); ?></div>
  <aside class="post-author"><div class="post-author__box">
    <div class="post-author__photo"><?php echo get_avatar( get_the_author_meta( 'ID' ), 160 ); ?></div>
    <div class="post-author__text"><p class="post-author__name"><?php the_author(); ?></p><p class="post-author__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p></div>
  </div></aside>
</article>
<?php endwhile;
get_footer();
