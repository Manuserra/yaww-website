<?php
/**
 * Journal index (journal.html): every post in one grid, filtered in place by topic
 * (assets/js-theme-e/journal-filter.js). Also used for category archives (archive.php), with that topic preselected.
 * Fields (post): card_image / card_image_2x (listing photo, 672x406; falls back to the featured image),
 * excerpt, author_name, categories.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'journal', array( 'journal-filter' ) );
$active = is_category() ? get_queried_object()->slug : 'all';
$topics = yaww_e_journal_topics();
$journal_ids  = yaww_e_journal_posts();
$GLOBALS['yaww_main_class'] = 'main main-journal'; // journal.html: <main id="main" class="main main-journal">
get_header(); ?>

  <div class="container">
    <div class="main__header main__header--small">
      <h1 class="main__header--title heading-h4 text-bold text-center">Journal</h1>
    </div>
  </div>

  <div class="tabbar">
    <div class="container">
      <div class="tabs" role="tablist" aria-label="Filter journal by topic">
        <button class="tab<?php echo 'all' === $active ? ' is-active' : ''; ?>" data-tab="all" role="tab" aria-selected="<?php echo 'all' === $active ? 'true' : 'false'; ?>">All</button>
<?php foreach ( $topics as $t ) : $on = $t->slug === $active; ?>
        <button class="tab<?php echo $on ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr( $t->slug ); ?>" role="tab" aria-selected="<?php echo $on ? 'true' : 'false'; ?>"><?php echo esc_html( $t->name ); ?></button>
<?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="main__content">
      <div class="journal-grid">
<?php foreach ( $journal_ids as $i => $pid ) :
	$cats  = yaww_e_post_topics( $pid );
	$title = get_the_title( $pid );
	$img_id = (int) yaww_meta( 'card_image', $pid );
	if ( ! $img_id ) { $img_id = (int) get_post_thumbnail_id( $pid ); }
	$img2  = (int) yaww_meta( 'card_image_2x', $pid );
	$src   = yaww_e_img_src( $img_id, 'full' );
	$src2  = yaww_e_img_src( $img2, 'full' );
	$exc   = has_excerpt( $pid ) ? get_post_field( 'post_excerpt', $pid ) : '';
	?>
        <article class="journal" data-cats="<?php echo esc_attr( implode( ' ', wp_list_pluck( $cats, 'slug' ) ) ); ?>">
          <a class="journal__link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>">
            <div class="journal__image">
              <?php if ( $src ) : ?><img width="672" height="406" src="<?php echo esc_url( $src ); ?>"<?php if ( $src2 ) : ?> srcset="<?php echo esc_attr( esc_url( $src ) . ' 672w, ' . esc_url( $src2 ) . ' 1218w' ); ?>" sizes="(max-width: 672px) 100vw, 672px"<?php endif; ?> alt="<?php echo esc_attr( $title ); ?>" decoding="async" <?php echo 0 === $i ? 'fetchpriority="high"' : 'loading="lazy"'; ?>><?php endif; ?>
            </div>
            <div class="journal__cat text-slug"><?php foreach ( $cats as $c ) { echo '<div>' . esc_html( $c->name ) . '</div>'; } ?></div>
            <div class="journal__title">
              <div class="heading-h5 text-bold"><?php echo esc_html( $title ); ?></div>
            </div>
            <div class="journal__caption text-left text-small"><?php echo $exc ? '<p>' . esc_html( $exc ) . '</p>' : ''; ?></div>
            <div class="journal__author text-caption"><?php echo esc_html( yaww_e_author_name( $pid ) ); ?></div>
          </a>
        </article>
<?php endforeach; ?>

      </div>
      <p class="journal-empty" hidden>No posts in this topic yet.</p>
    </div>
  </div>


<?php get_footer();
