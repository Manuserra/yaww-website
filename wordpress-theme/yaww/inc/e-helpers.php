<?php
/**
 * Helpers for the location, people, About and Journal views (agent E).
 * Each of those templates require_once's this file, so functions.php does not have to know about it
 * (it may require it too; the guard below makes that safe).
 *
 * Data is read through yaww_meta() / yaww_list() / yaww_rows() only (inc/helpers.php).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( function_exists( 'yaww_e_assets' ) ) { return; }

/** The stylesheets a static page links, in its own order (after Google Fonts). */
function yaww_e_view_css( $view ) {
	$tail = array( 'type', 'cards', 'nav', 'photo-focus' );
	$map  = array(
		'location'  => array( 'base', 'retreat', 'lead-modal', 'locations', 'location' ),
		'locations' => array( 'base', 'locations' ),
		'people'    => array( 'base', 'people' ),
		'about'     => array( 'base', 'people', 'lead', 'about' ),
		'journal'   => array( 'base', 'journal' ),
		'post'      => array( 'base', 'journal-post' ),
		'page'      => array( 'base', 'about' ),
	);
	return isset( $map[ $view ] ) ? array_merge( $map[ $view ], $tail ) : array();
}

/**
 * A view's assets, exactly as its static page loads them (same pattern as D's inc/views.php):
 * the theme's generic enqueues (functions.php) are dequeued, then the static page's stylesheet chain,
 * Lenis with the static inline init (assets/js-theme/lenis-init.js), the page chrome the static pages
 * run inline (assets/js-theme-e/page-chrome.js) and E's own scripts from assets/js-theme-e/.
 * Call before get_header().
 */
function yaww_e_assets( $view, $scripts = array() ) {
	add_action( 'wp_enqueue_scripts', function () use ( $view, $scripts ) {
		// functions.php's baseline chain (yaww-css-*, and older handle names) yields to the view's own.
		foreach ( (array) wp_styles()->queue as $h ) {
			if ( 0 === strpos( $h, 'yaww-css-' ) || in_array( $h, array( 'yaww-page', 'yaww-lead-modal', 'yaww-base', 'yaww-hugeicons' ), true ) ) { wp_dequeue_style( $h ); }
		}
		foreach ( (array) wp_scripts()->queue as $h ) {
			if ( 0 === strpos( $h, 'yaww-js-' ) || 'yaww-data' === $h ) { wp_dequeue_script( $h ); }
		}
		// theme.js re-implements nav, tabs and Lenis generically; journal-post.js would redraw the
		// server-rendered article from data/journal.js; lead-modal.js only where the static page has it.
		foreach ( array( 'yaww-theme', 'yaww-journal-post', 'yaww-lead-modal' ) as $h ) {
			wp_dequeue_script( $h );
		}

		$prev = wp_style_is( 'yaww-fonts', 'registered' ) ? array( 'yaww-fonts' ) : array();
		foreach ( yaww_e_view_css( $view ) as $name ) {
			$rel = 'css/' . $name . '.css';
			wp_enqueue_style( 'yaww-e-' . $name, YAWW_URI . '/assets/' . $rel, $prev, yaww_asset_ver( $rel ) );
			$prev = array( 'yaww-e-' . $name );
		}
		if ( in_array( $view, array( 'people', 'about', 'post' ), true ) ) {
			wp_enqueue_style( 'yaww-e-hugeicons', 'https://use.hugeicons.com/font/icons.css', array(), null );
		}

		wp_enqueue_script( 'yaww-lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js', array(), '1.1.18', true );
		$li = YAWW_DIR . '/assets/js-theme/lenis-init.js';
		wp_enqueue_script( 'yaww-lenis-init', YAWW_URI . '/assets/js-theme/lenis-init.js', array( 'yaww-lenis' ), file_exists( $li ) ? (string) filemtime( $li ) : YAWW_VERSION, true );

		if ( in_array( $view, array( 'location', 'people', 'post' ), true ) ) {
			// As locationpage-*.html, teacher-*.html, facilitator-*.html and journal/*.html: retreat-cards.js is on the
			// page before nav.js runs, so the Retreats panel lists the next three retreats at build time
			// (otherwise nav.js fetches it late and the panel can stay empty).
			wp_enqueue_script( 'yaww-e-retreat-cards', YAWW_URI . '/assets/js/retreat-cards.js', array(), yaww_asset_ver( 'js/retreat-cards.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
			$nav = wp_scripts()->query( 'yaww-nav' );
			if ( $nav && ! in_array( 'yaww-e-retreat-cards', $nav->deps, true ) ) { $nav->deps[] = 'yaww-e-retreat-cards'; }
		}
		if ( 'location' === $view ) {
			wp_enqueue_script( 'yaww-e-lead-modal', YAWW_URI . '/assets/js/lead-modal.js', array(), yaww_asset_ver( 'js/lead-modal.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		}
		foreach ( array_merge( array( 'page-chrome' ), $scripts ) as $s ) {
			$rel = 'js-theme-e/' . $s . '.js';
			wp_enqueue_script( 'yaww-e-' . $s, YAWW_URI . '/assets/' . $rel, array( 'yaww-lenis-init' ), yaww_asset_ver( $rel ), true );
		}
	}, 30 );
}

/** URL of a file the static site serves from images/ (synced into assets/images/ by bin/sync-assets.sh). */
function yaww_e_static_img( $rel ) {
	return YAWW_URI . '/assets/' . ltrim( $rel, '/' );
}

/** Visible gap marker, as the static pages write it. */
function yaww_e_missing( $what ) {
	return '[missing data: ' . $what . ']';
}

/** Typographic apostrophes, as build_location.py's t() does. Input is escaped here. */
function yaww_e_t( $s ) {
	return esc_html( str_replace( "'", '’', (string) $s ) );
}

/** "[missing data: x]" inside a text becomes the static pages' .loc-missing span. */
function yaww_e_t_missing( $s ) {
	return preg_replace( '/\[missing data: ([^\]]+)\]/', '<span class="loc-missing">[missing data: $1]</span>', yaww_e_t( $s ) );
}

/** Post content as an array of plain paragraphs (for content that the static pages set paragraph by paragraph). */
function yaww_e_paragraphs( $post_id ) {
	$raw = (string) get_post_field( 'post_content', $post_id );
	if ( '' === trim( $raw ) ) { return array(); }
	$raw = preg_replace( '/<!--.*?-->/s', '', $raw );
	if ( preg_match_all( '#<p[^>]*>(.*?)</p>#s', $raw, $m ) ) {
		$out = array_map( function ( $p ) { return trim( html_entity_decode( wp_strip_all_tags( $p ), ENT_QUOTES, 'UTF-8' ) ); }, $m[1] );
	} else {
		$out = preg_split( '/\n\s*\n/', html_entity_decode( wp_strip_all_tags( $raw ), ENT_QUOTES, 'UTF-8' ) );
	}
	return array_values( array_filter( array_map( 'trim', $out ), 'strlen' ) );
}

/** Lines of a textarea field split on blank lines or line breaks. */
function yaww_e_lines( $text ) {
	return function_exists( 'yaww_lines' ) ? yaww_lines( $text ) : array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}

/** Y-m-d / Ymd / any strtotime string -> Ymd int, 0 when empty. */
function yaww_e_datekey( $v ) {
	$v = trim( (string) $v );
	if ( '' === $v ) { return 0; }
	if ( preg_match( '/^\d{8}$/', $v ) ) { return (int) $v; }
	$t = strtotime( $v );
	return $t ? (int) gmdate( 'Ymd', $t ) : 0;
}

/**
 * Upcoming retreats (end date not past), sorted by start date then title: js/retreat-cards.js
 * getUpcomingRetreats(). $filter( $id ) narrows the list.
 */
function yaww_e_upcoming_retreats( $filter = null ) {
	static $all = null;
	if ( null === $all ) {
		$all = get_posts( array( 'post_type' => 'retreat', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'suppress_filters' => false ) );
	}
	$today = (int) current_time( 'Ymd' );
	$out   = array();
	foreach ( $all as $id ) {
		$end = yaww_e_datekey( yaww_meta( 'end_date', $id ) );
		if ( ! $end || $end < $today ) { continue; }
		if ( $filter && ! call_user_func( $filter, $id ) ) { continue; }
		$out[] = $id;
	}
	usort( $out, function ( $a, $b ) {
		$d = yaww_e_datekey( yaww_meta( 'start_date', $a ) ) - yaww_e_datekey( yaww_meta( 'start_date', $b ) );
		return $d ? $d : strcmp( get_the_title( $a ), get_the_title( $b ) );
	} );
	return $out;
}

function yaww_e_retreats_by_location( $loc_id ) {
	return yaww_e_upcoming_retreats( function ( $id ) use ( $loc_id ) { return (int) yaww_meta( 'location_id', $id ) === (int) $loc_id; } );
}

function yaww_e_retreats_by_teacher( $teacher_id ) {
	return yaww_e_upcoming_retreats( function ( $id ) use ( $teacher_id ) {
		return in_array( (int) $teacher_id, array_map( 'intval', (array) yaww_meta( 'teacher_ids', $id ) ), true );
	} );
}

/**
 * The retreat a dateless destination runs between editions (location.program / getLastEdition):
 * the `program_retreat` field if set, else its latest past or dateless retreat.
 */
function yaww_e_location_program( $loc_id ) {
	$p = (int) yaww_meta( 'program_retreat', $loc_id );
	if ( $p && get_post( $p ) ) { return $p; }
	$ids = get_posts( array( 'post_type' => 'retreat', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'suppress_filters' => false ) );
	// The dateless retreat page first (data/locations.js program, as D's catalog reads it), then the last edition.
	$best = 0; $best_key = -1;
	foreach ( $ids as $id ) {
		if ( (int) yaww_meta( 'location_id', $id ) !== (int) $loc_id ) { continue; }
		$k = yaww_e_datekey( yaww_meta( 'start_date', $id ) );
		if ( ! $k ) { return $id; }
		if ( $k > $best_key ) { $best_key = $k; $best = $id; }
	}
	return $best;
}

/** Location display line for cards: card_name (cardName), else display_name, else title. */
function yaww_e_card_loc( $loc_id ) {
	if ( ! $loc_id ) { return ''; }
	$n = yaww_meta( 'card_name', $loc_id );
	return $n ? $n : yaww_location_line( $loc_id );
}

/** Location gradient placeholder (retreat-cards.js placeholderStyle). */
function yaww_e_placeholder_style( $loc_id ) {
	$h = $loc_id ? yaww_meta( 'placeholder_hue', $loc_id ) : '';
	$h = ( '' === $h || null === $h ) ? 200 : (int) $h;
	return 'background: linear-gradient(155deg, hsl(' . $h . ' 32% 80%), hsl(' . $h . ' 26% 60%));';
}

/** <img> for an attachment with the static markup's attributes (no WP width/height/srcset noise unless asked). */
function yaww_e_img( $att_id, $size, $attrs = array() ) {
	$att_id = (int) $att_id;
	if ( ! $att_id ) { return ''; }
	$src = wp_get_attachment_image_src( $att_id, $size );
	if ( ! $src ) { return ''; }
	$a = array_merge( array( 'src' => $src[0], 'alt' => (string) get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ), $attrs );
	$html = '<img';
	foreach ( $a as $k => $v ) {
		if ( null === $v || false === $v ) { continue; }
		$html .= ' ' . $k . '="' . ( 'src' === $k ? esc_url( $v ) : esc_attr( $v ) ) . '"';
	}
	return $html . '>';
}

/** Image URL of an attachment id ('' when none). */
function yaww_e_img_src( $att_id, $size = 'full' ) {
	$s = $att_id ? wp_get_attachment_image_src( (int) $att_id, $size ) : null;
	return $s ? $s[0] : '';
}

/** 1x/2x srcset when a separate retina attachment exists. */
function yaww_e_srcset( $id1, $id2, $size = 'full' ) {
	$a = yaww_e_img_src( $id1, $size );
	$b = yaww_e_img_src( $id2, $size );
	return ( $a && $b ) ? $a . ' 1x, ' . $b . ' 2x' : null;
}

/* ---------------------------------------------------------------- people -- */

function yaww_e_first_name( $id ) {
	$s = yaww_meta( 'short_name', $id );
	if ( $s ) { return $s; }
	$parts = preg_split( '/\s+/', trim( get_the_title( $id ) ) );
	return $parts ? $parts[0] : '';
}

function yaww_e_initials( $name ) {
	$out = '';
	foreach ( preg_split( '/\s+/', trim( (string) $name ) ) as $w ) {
		if ( '' !== $w ) { $out .= mb_substr( $w, 0, 1 ); }
	}
	return mb_strtoupper( mb_substr( $out, 0, 2 ) );
}

/** Portrait or monogram (people-cards.js photo()). */
function yaww_e_person_photo( $id, $cls, $lazy ) {
	$name  = get_the_title( $id );
	$thumb = (int) get_post_thumbnail_id( $id );
	if ( ! $thumb ) {
		return '<div class="' . esc_attr( $cls . ' ' . $cls . '--empty' ) . '" role="img" aria-label="' . esc_attr( $name . ', photo coming soon' ) . '"><span class="monogram">' . esc_html( yaww_e_initials( $name ) ) . '</span></div>';
	}
	$pos = yaww_meta( 'photo_position', $id );
	return '<div class="' . esc_attr( $cls ) . '">' . yaww_e_img( $thumb, 'full', array(
		'alt'     => $name,
		'loading' => $lazy ? 'lazy' : null,
		'width'   => '672',
		'height'  => '888',
		'style'   => $pos ? 'object-position:' . $pos : null,
	) ) . '</div>';
}

/** People of a type in the data files' order (menu_order, then title); facilitators only when listed. */
function yaww_e_people( $type ) {
	$ids = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ), 'suppress_filters' => false ) );
	if ( 'facilitator' === $type ) {
		// team.js: listed defaults to true; only an explicit false (stored '0') hides the card.
		$ids = array_values( array_filter( $ids, function ( $id ) {
			return '0' !== (string) yaww_meta( 'listed', $id );
		} ) );
	}
	return $ids;
}

/** Spotify URL/URI/id -> embed src (people-cards.js spotifyEmbed). */
function yaww_e_spotify_embed( $v ) {
	$v = trim( (string) $v );
	if ( preg_match( '#(playlist|album|track|show|episode|artist)[/:]([A-Za-z0-9]+)#', $v, $m ) ) { $kind = $m[1]; $id = $m[2]; } else { $kind = 'playlist'; $id = $v; }
	return 'https://open.spotify.com/embed/' . $kind . '/' . rawurlencode( $id ) . '?utm_source=generator&theme=0';
}

/** YouTube id/URL -> youtube-nocookie embed src (people-cards.js youtubeEmbed). */
function yaww_e_youtube_embed( $v ) {
	$v = trim( (string) $v );
	if ( preg_match( '#(?:youtu\.be/|[?&]v=|/embed/|/shorts/|/live/)([A-Za-z0-9_-]{11})#', $v, $m ) ) { return 'https://www.youtube-nocookie.com/embed/' . $m[1]; }
	if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $v ) ) { return 'https://www.youtube-nocookie.com/embed/' . $v; }
	return preg_match( '#^https://(player\.vimeo\.com|www\.youtube(-nocookie)?\.com)/#', $v ) ? $v : '';
}

function yaww_e_instagram_url( $v ) {
	$v = trim( (string) $v );
	if ( '' === $v ) { return ''; }
	if ( preg_match( '#^https?://#', $v ) ) { return $v; }
	return 'https://www.instagram.com/' . rtrim( ltrim( $v, '@' ), '/' ) . '/';
}

/** Number as a capitalised word for the locations lede (build_location.py words). */
function yaww_e_count_word( $n ) {
	$w = array( 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen' );
	return isset( $w[ $n ] ) ? $w[ $n ] : (string) $n;
}

/* --------------------------------------------------------------- journal -- */

/** Journal topics: post categories minus the umbrella "Journal" and "Uncategorized", A to Z. */
function yaww_e_journal_topics() {
	$cats = get_categories( array( 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
	return array_values( array_filter( $cats, function ( $c ) { return ! in_array( $c->slug, array( 'journal', 'uncategorized' ), true ); } ) );
}

/** A post's topic terms, in name order, without the umbrella categories. */
function yaww_e_post_topics( $post_id ) {
	$cats = get_the_category( $post_id );
	$cats = array_values( array_filter( $cats, function ( $c ) { return ! in_array( $c->slug, array( 'journal', 'uncategorized' ), true ); } ) );
	usort( $cats, function ( $a, $b ) { return strcmp( $a->name, $b->name ); } );
	return $cats;
}

/** Author name: the author_name field (journal.js author.name); else the WP author unless it is an admin account. */
function yaww_e_author_name( $post_id ) {
	$n = yaww_meta( 'author_name', $post_id );
	if ( $n ) { return $n; }
	$uid = (int) get_post_field( 'post_author', $post_id );
	if ( $uid && ! user_can( $uid, 'manage_options' ) ) {
		$u = get_userdata( $uid );
		return $u ? $u->display_name : '';
	}
	return '';
}

/**
 * Published posts. $order 'listing': journal.html's order (menu_order, then newest first).
 * 'date': data/journal.js order, newest first (ties by ID), used by "More from the journal".
 */
function yaww_e_journal_posts( $exclude = 0, $limit = -1, $order = 'listing' ) {
	return get_posts( array(
		'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $limit, 'fields' => 'ids',
		'orderby' => 'date' === $order ? array( 'date' => 'DESC', 'ID' => 'ASC' ) : array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'exclude' => $exclude ? array( $exclude ) : array(),
		'suppress_filters' => false,
	) );
}

/**
 * A journal post's body in journal-post.js bodyHTML() markup, from the imported blocks:
 * a group with class post__text => <div class="post__text"><p>…</p></div>; an image => <figure class="post__figure"><img></figure>.
 * Anything else (posts written later in the editor) goes through the usual block rendering.
 */
function yaww_e_journal_body( $post_id ) {
	$raw = (string) get_post_field( 'post_content', $post_id );
	if ( ! has_blocks( $raw ) ) {
		return '<div class="post__text">' . apply_filters( 'the_content', $raw ) . '</div>';
	}
	$out = '';
	foreach ( parse_blocks( $raw ) as $b ) {
		$cls = isset( $b['attrs']['className'] ) ? $b['attrs']['className'] : '';
		if ( 'core/group' === $b['blockName'] && false !== strpos( $cls, 'post__text' ) ) {
			$out .= '<div class="post__text">';
			foreach ( $b['innerBlocks'] as $ib ) {
				if ( preg_match( '#<p[^>]*>(.*?)</p>#s', $ib['innerHTML'], $m ) ) { $out .= '<p>' . wp_kses_post( trim( $m[1] ) ) . '</p>'; }
				elseif ( trim( $ib['innerHTML'] ) ) { $out .= render_block( $ib ); }
			}
			$out .= '</div>';
		} elseif ( 'core/image' === $b['blockName'] && ! empty( $b['attrs']['id'] ) ) {
			$src = yaww_e_img_src( (int) $b['attrs']['id'], 'full' );
			if ( $src ) { $out .= '<figure class="post__figure"><img src="' . esc_url( $src ) . '" alt="" loading="lazy" decoding="async"></figure>'; }
		} elseif ( $b['blockName'] ) {
			$out .= render_block( $b );
		}
	}
	return $out;
}

/** A post's category names as journal.js lists them: A to Z, the umbrella "Journal" included. */
function yaww_e_post_cat_names( $post_id ) {
	$n = array();
	foreach ( get_the_category( $post_id ) as $c ) { if ( 'uncategorized' !== $c->slug ) { $n[] = $c->name; } }
	sort( $n, SORT_STRING );
	return $n;
}
