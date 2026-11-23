<?php
/**
 * Journal post (journal/<slug>.html, js/journal-post.js): head, hero, body, author card,
 * "Explore this retreat" (when the post has one), "More from the journal".
 *
 * Fields (post): featured image = hero, hero_image_2x; date_label (the date line as the source writes it; empty on the ten posts the source has no date for);
 * post content = the body, ideally already as journal-post.js writes it (<div class="post__text"><p>…</p></div>,
 * <figure class="post__figure"><img></figure>); author_name, author_bio (one paragraph per line),
 * author_photo, author_photo_2x, author_person (post id of a teacher / facilitator);
 * retreat_title, retreat_subtitle, retreat_text, retreat_location (location post id), retreat_image, retreat_image_2x.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'post', array( 'cta-parallax' ) );
get_header();
while ( have_posts() ) : the_post();
	$this_post    = get_the_ID();
	$title = get_the_title( $this_post );

	// Categories as data/journal.js lists them: A to Z, "Journal" included.
	$cats = yaww_e_post_cat_names( $this_post );
	$cats_html = $cats ? '<div class="post__cats">' . implode( '', array_map( function ( $n ) { return '<span>' . esc_html( $n ) . '</span>'; }, $cats ) ) . '</div>' : '';

	/* Image with an optional retina twin (journal-post.js img()). */
	$img = function ( $id1, $id2, $alt, $eager = false ) {
		$src = yaww_e_img_src( $id1, 'full' );
		if ( ! $src ) { return ''; }
		return yaww_e_img( $id1, 'full', array( 'srcset' => yaww_e_srcset( $id1, $id2 ), 'alt' => $alt, 'fetchpriority' => $eager ? 'high' : null, 'loading' => $eager ? null : 'lazy', 'decoding' => 'async' ) );
	};

	$body = yaww_e_journal_body( $this_post );
	?>
  <article class="post" data-journal-post="<?php echo esc_attr( get_post_field( 'post_name', $this_post ) ); ?>"><div class="post__head"><h1 class="post__title"><?php echo esc_html( $title ); ?></h1><?php echo $cats_html; // phpcs:ignore ?><?php $dl = yaww_meta( 'date_label', $this_post ); if ( $dl ) : ?><p class="post__date"><time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d', $this_post ) ); ?>"><?php echo esc_html( $dl ); ?></time></p><?php endif; ?></div><div class="post__hero"><?php echo $img( get_post_thumbnail_id( $this_post ), yaww_meta( 'hero_image_2x', $this_post ), $title, true ); // phpcs:ignore ?></div><div class="post__body"><?php echo $body; // phpcs:ignore ?></div><?php

	/* ---- author card ---- */
	$a_name = yaww_e_author_name( $this_post );
	if ( $a_name ) {
		$a_bio  = yaww_meta( 'author_bio', $this_post );
		if ( ! $a_bio ) { $a_bio = get_the_author_meta( 'description', (int) get_post_field( 'post_author', $this_post ) ); }
		$a_bio  = array_filter( array_map( 'trim', preg_split( '/\R+/', (string) $a_bio ) ), 'strlen' );
		$a_page = (int) yaww_meta( 'author_person', $this_post );
		$a_url  = $a_page ? get_permalink( $a_page ) : '';
		$a_ph   = (int) yaww_meta( 'author_photo', $this_post );
		$parts  = preg_split( '/\s+/', $a_name );
		echo '<section class="post-author"><div class="post-author__box">';
		if ( $a_ph ) { echo '<div class="post-author__photo">' . $img( $a_ph, yaww_meta( 'author_photo_2x', $this_post ), $a_name ) . '</div>'; } // phpcs:ignore
		echo '<div class="post-author__text"><p class="post-author__slug">About the author</p><p class="post-author__name">'
			. ( $a_url ? '<a class="post-author__name-link" href="' . esc_url( $a_url ) . '">' . esc_html( $a_name ) . '</a>' : esc_html( $a_name ) ) . '</p>';
		foreach ( $a_bio as $p ) { echo '<p class="post-author__bio">' . esc_html( $p ) . '</p>'; }
		if ( $a_url ) { echo '<a class="btn btn--secondary post-author__cta" href="' . esc_url( $a_url ) . '">Read more about ' . esc_html( $parts[0] ) . '</a>'; }
		echo '</div></div></section>';
	}

	/* ---- "Explore this retreat" ---- */
	$r_title = yaww_meta( 'retreat_title', $this_post );
	if ( $r_title ) {
		$r_loc  = (int) yaww_meta( 'retreat_location', $this_post );
		$r_href = $r_loc ? get_permalink( $r_loc ) : get_post_type_archive_link( 'retreat' );
		$r_img  = $img( (int) yaww_meta( 'retreat_image', $this_post ), yaww_meta( 'retreat_image_2x', $this_post ), $r_title );
		$r_sub  = yaww_meta( 'retreat_subtitle', $this_post );
		$r_txt  = yaww_meta( 'retreat_text', $this_post );
		echo '<section class="post-retreat"><h2 class="post-retreat__head">Explore this retreat</h2><div class="post-retreat__box">'
			. ( $r_img ? '<div class="post-retreat__image">' . $r_img . '</div>' : '' )
			. '<div class="post-retreat__text"><p class="post-retreat__title">' . esc_html( $r_title ) . '</p>'
			. ( $r_sub ? '<p class="post-retreat__sub">' . esc_html( $r_sub ) . '</p>' : '' )
			. ( $r_txt ? '<p class="post-retreat__desc">' . esc_html( $r_txt ) . '</p>' : '' )
			. '<a class="btn btn--primary post-retreat__cta" href="' . esc_url( $r_href ) . '">Explore retreat</a></div></div></section>'; // phpcs:ignore
	}

	/* ---- "More from the journal": three more, newest first, never this one ---- */
	$others = yaww_e_journal_posts( $this_post, 3, 'date' );
	if ( $others ) {
		echo '<section class="post-related"><h2 class="post-related__head">More from the journal</h2><div class="post-related__grid">';
		foreach ( $others as $oid ) {
			$oc = yaww_e_post_cat_names( $oid );
			$oe = has_excerpt( $oid ) ? get_post_field( 'post_excerpt', $oid ) : '';
			echo '<a class="post-related__item" href="' . esc_url( get_permalink( $oid ) ) . '"><div class="post-related__figure">'
				. $img( get_post_thumbnail_id( $oid ), yaww_meta( 'hero_image_2x', $oid ), '' ) . '</div>' // phpcs:ignore
				. ( $oc ? '<div class="post-related__cats">' . implode( '', array_map( function ( $n ) { return '<span>' . esc_html( $n ) . '</span>'; }, $oc ) ) . '</div>' : '' )
				. '<p class="post-related__title">' . esc_html( get_the_title( $oid ) ) . '</p>'
				. ( $oe ? '<p class="post-related__excerpt">' . esc_html( $oe ) . '</p>' : '' )
				. '</a>';
		}
		echo '</div><div class="post-related__foot"><a class="btn btn--secondary" href="' . esc_url( home_url( '/journal/' ) ) . '">Back to journal</a></div></section>';
	}
	?></article>
<?php endwhile;
get_footer();
