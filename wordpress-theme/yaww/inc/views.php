<?php
/**
 * Views owned by agent D (home, retreats listing, retreat page, header, footer).
 *
 * The static site is the spec: these views load the same css/ and js/ files, in the
 * same order, as index.html, retreats.html and retreatpage-*.html, and the behaviours
 * those pages run inline live in assets/js-theme/ (copied verbatim, data pulled out).
 *
 * The card components (js/retreat-cards.js), the nav (js/nav.js) and the listing read
 * window.YAWW_LOCATIONS / YAWW_TEACHERS / YAWW_RETREATS. yaww_catalog_js() writes those
 * from WordPress posts in the shape of data/*.js, so every script runs unchanged.
 *
 * Loaded by functions.php (require_once) and, defensively, by the templates themselves.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( defined( 'YAWW_D_VIEWS' ) ) { return; }
define( 'YAWW_D_VIEWS', 1 );

/* ------------------------------------------------------------------ urls -- */

/** URL of a file synced into assets/ (css, js, images, media). */
function yaww_asset_url( $rel ) {
	return YAWW_URI . '/assets/' . ltrim( $rel, '/' );
}

/** Theme-owned JS (assets/js-theme/, committed). */
function yaww_theme_js_url( $name ) {
	return YAWW_URI . '/assets/js-theme/' . $name;
}

/** First post of a type whose static id is $id: meta `static_id` if the importer wrote one, else the slug. */
function yaww_post_by_static_id( $type, $id ) {
	static $cache = array();
	$k = $type . ':' . $id;
	if ( array_key_exists( $k, $cache ) ) { return $cache[ $k ]; }
	$q = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => 1, 'meta_key' => 'static_id', 'meta_value' => $id, 'fields' => 'ids', 'suppress_filters' => false ) );
	if ( ! $q ) {
		$q = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => 1, 'name' => $id, 'fields' => 'ids' ) );
	}
	return $cache[ $k ] = $q ? (int) $q[0] : 0;
}

/** The static id of a post (what data/*.js calls `id`): meta `static_id`, else the slug. */
function yaww_static_id( $post_id ) {
	$s = get_post_meta( $post_id, 'static_id', true );
	return $s ? (string) $s : (string) get_post_field( 'post_name', $post_id );
}

/** WordPress URL for a static page name ("retreats.html?tier=x", "contact.html#y", "retreatpage-id.html"). */
function yaww_static_url( $href ) {
	if ( preg_match( '~^(https?:|mailto:|tel:|#)~', $href ) ) { return $href; }
	$frag = '';
	$qs   = '';
	if ( false !== ( $p = strpos( $href, '#' ) ) ) { $frag = substr( $href, $p ); $href = substr( $href, 0, $p ); }
	if ( false !== ( $p = strpos( $href, '?' ) ) ) { $qs = substr( $href, $p ); $href = substr( $href, 0, $p ); }
	$map = yaww_link_map();
	$url = isset( $map[ $href ] ) ? $map[ $href ] : home_url( '/' . $href );
	return $url . $qs . $frag;
}

/** Echo-ready escaped variant. */
function yaww_u( $href ) { return esc_url( yaww_static_url( $href ) ); }

/**
 * Static file name => WordPress URL, for every page the static site links to.
 * Also emitted to JS (window.YAWW_WP_LINKS) so links that scripts build from
 * hard-coded file names (js/nav.js panels) land on WordPress URLs.
 */
function yaww_link_map() {
	static $map = null;
	if ( null !== $map ) { return $map; }
	$arch = function ( $type, $fallback ) {
		$l = get_post_type_archive_link( $type );
		return $l ? $l : home_url( $fallback );
	};
	$map = array(
		'index.html'         => home_url( '/' ),
		''                   => home_url( '/' ),
		'retreats.html'      => $arch( 'retreat', '/retreats/' ),
		'locations.html'     => $arch( 'location', '/locations/' ),
		'teachers.html'      => $arch( 'teacher', '/teachers/' ),
		'facilitators.html'  => $arch( 'facilitator', '/facilitators/' ),
		'about.html'         => home_url( '/about/' ),
		'journal.html'       => home_url( '/journal/' ),
		'contact.html'       => home_url( '/contact/' ),
		'teach-with-us.html' => home_url( '/teach-with-us/' ),
		'policies.html'      => home_url( '/policies/' ),
	);
	$types = array( 'retreat' => 'retreatpage-', 'location' => 'locationpage-', 'teacher' => 'teacher-', 'facilitator' => 'facilitator-' );
	foreach ( $types as $type => $prefix ) {
		foreach ( get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) ) as $id ) {
			$map[ $prefix . yaww_static_id( $id ) . '.html' ] = get_permalink( $id );
		}
	}
	return $map;
}

/* ----------------------------------------------------------------- views -- */

/** Which of D's views is rendering: home | listing | retreat | ''. */
function yaww_view() {
	if ( is_front_page() ) { return 'home'; }
	if ( is_post_type_archive( 'retreat' ) || is_tax( 'retreat_tier' ) ) { return 'listing'; }
	if ( is_singular( 'retreat' ) ) { return 'retreat'; }
	return '';
}

/** The static pages' <link rel=stylesheet> lists, in their order (after Google Fonts). */
function yaww_view_styles( $view ) {
	$tail = array( 'cards', 'nav', 'photo-focus' ); // retreats/_tools/chrome.py LAYERS_CSS
	switch ( $view ) {
		case 'home':    return array_merge( array( 'base', 'home', 'lead', 'type' ), $tail );
		case 'listing': return array_merge( array( 'base', 'filters', 'retreats', 'type' ), $tail );
		case 'retreat': return array_merge( array( 'base', 'retreat', 'lead-modal', 'type', 'rooms' ), $tail );
	}
	return array();
}

/** Is the current request on a page this file styles (D's views)? */
function yaww_is_d_view() { return '' !== yaww_view(); }

add_action( 'wp_enqueue_scripts', function () {
	/* Site-wide scripts every official static page carries (chrome.py LAYERS_JS) and that the
	   header needs. The layer stylesheets (type, cards, nav, photo-focus) come with each view's
	   own chain: below for D's views, yaww_e_assets() for E's. */
	wp_enqueue_script( 'yaww-attribution', yaww_asset_url( 'js/attribution.js' ), array(), yaww_asset_ver( 'js/attribution.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
	/* The catalog (data/*.js equivalent) on every page: nav.js reads it for its panels. */
	wp_register_script( 'yaww-catalog', false, array(), null, false );
	wp_enqueue_script( 'yaww-catalog' );
	wp_add_inline_script( 'yaww-catalog', yaww_catalog_js() . "\n" . yaww_links_js() );
	wp_enqueue_script( 'yaww-wp-links', yaww_theme_js_url( 'wp-links.js' ), array( 'yaww-catalog' ), yaww_theme_js_ver( 'wp-links.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
	wp_enqueue_script( 'yaww-nav', yaww_asset_url( 'js/nav.js' ), array( 'yaww-wp-links' ), yaww_asset_ver( 'js/nav.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );

	$view = yaww_view();
	if ( ! $view ) { return; }

	/* D's views: exactly the static page's stylesheet chain, nothing else. */
	foreach ( array( 'yaww-page', 'yaww-lead-modal', 'yaww-base', 'yaww-type', 'yaww-cards', 'yaww-nav', 'yaww-photo-focus' ) as $h ) {
		wp_dequeue_style( $h );
	}
	$prev = array( 'yaww-fonts' );
	foreach ( yaww_view_styles( $view ) as $css ) {
		$h = 'yaww-v-' . $css;
		wp_enqueue_style( $h, yaww_asset_url( 'css/' . $css . '.css' ), $prev, yaww_asset_ver( 'css/' . $css . '.css' ) );
		$prev = array( $h );
	}
	if ( 'home' === $view ) {
		wp_enqueue_style( 'yaww-hugeicons', 'https://use.hugeicons.com/font/icons.css', array(), null );
	}

	/* theme.js re-implements the nav toggle, tabs, FAQ and Lenis generically; on these
	   views the static pages' own scripts (below) do that, so it would run twice. */
	wp_dequeue_script( 'yaww-theme' );
	wp_dequeue_script( 'yaww-booking-bar' );
	wp_dequeue_script( 'yaww-retreat-rooms' );
	wp_dequeue_script( 'yaww-lead-modal' );

	/* Lenis + the static pages' inline init (same on every page). */
	wp_enqueue_script( 'yaww-lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js', array(), '1.1.18', true );
	wp_enqueue_script( 'yaww-lenis-init', yaww_theme_js_url( 'lenis-init.js' ), array( 'yaww-lenis' ), yaww_theme_js_ver( 'lenis-init.js' ), true );

	if ( 'home' === $view || 'listing' === $view ) {
		/* js/retreat-cards.js is a classic script in the static <head>, after data/*.js. */
		wp_enqueue_script( 'yaww-retreat-cards', yaww_asset_url( 'js/retreat-cards.js' ), array( 'yaww-catalog' ), yaww_asset_ver( 'js/retreat-cards.js' ), false );
	}
	if ( 'home' === $view ) {
		wp_enqueue_script( 'yaww-home', yaww_theme_js_url( 'home.js' ), array( 'yaww-lenis-init', 'yaww-retreat-cards' ), yaww_theme_js_ver( 'home.js' ), true );
	}
	if ( 'listing' === $view ) {
		wp_enqueue_script( 'yaww-listing', yaww_theme_js_url( 'retreats-listing.js' ), array( 'yaww-lenis-init', 'yaww-retreat-cards' ), yaww_theme_js_ver( 'retreats-listing.js' ), true );
		if ( is_tax( 'retreat_tier' ) ) {
			$base = wp_parse_url( yaww_static_url( 'retreats.html' ), PHP_URL_PATH );
			wp_add_inline_script( 'yaww-listing', 'window.YAWW_LISTING_TIER = ' . wp_json_encode( get_queried_object()->slug ) . '; window.YAWW_LISTING_BASE = ' . wp_json_encode( $base ) . ';', 'before' );
		}
	}
	if ( 'retreat' === $view ) {
		$id = get_queried_object_id();
		wp_enqueue_script( 'yaww-retreat-page', yaww_theme_js_url( 'retreat-page.js' ), array( 'yaww-lenis-init' ), yaww_theme_js_ver( 'retreat-page.js' ), true );
		wp_add_inline_script( 'yaww-retreat-page', yaww_retreat_page_js( $id ), 'before' );
		foreach ( array( 'booking-bar', 'retreat-rooms', 'lead-modal', 'hosts' ) as $js ) {
			wp_enqueue_script( 'yaww-r-' . $js, yaww_asset_url( 'js/' . $js . '.js' ), array( 'yaww-retreat-page' ), yaww_asset_ver( 'js/' . $js . '.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		}
	}
}, 20 );

function yaww_theme_js_ver( $name ) {
	$f = YAWW_DIR . '/assets/js-theme/' . $name;
	return file_exists( $f ) ? (string) filemtime( $f ) : YAWW_VERSION;
}

/** window.YAWW_WP_LINKS (static file name => WP URL) and the assets root, for js-theme/wp-links.js. */
function yaww_links_js() {
	return 'window.YAWW_WP_LINKS = ' . wp_json_encode( yaww_link_map() ) . '; window.YAWW_ASSETS = ' . wp_json_encode( yaww_asset_url( '' ) ) . ';';
}

/* -------------------------------------------------------------- catalog -- */

function yaww_attachment_url( $id, $size = 'full' ) {
	$id = (int) $id;
	if ( ! $id ) { return null; }
	$u = wp_get_attachment_image_url( $id, $size );
	return $u ? $u : null;
}

/** Location record in the shape of data/locations.js. */
function yaww_location_record( $lid ) {
	$img = yaww_attachment_url( yaww_meta( 'card_image', $lid ), 'large' );
	if ( ! $img ) { $img = yaww_attachment_url( get_post_thumbnail_id( $lid ), 'large' ); }
	$rec = array(
		'id'          => yaww_static_id( $lid ),
		'displayName' => yaww_meta( 'display_name', $lid ) ?: get_the_title( $lid ),
		'name'        => yaww_meta( 'short_name', $lid ) ?: get_the_title( $lid ),
		'region'      => yaww_meta( 'region', $lid ) ?: '',
		'country'     => yaww_meta( 'country', $lid ) ?: '',
		'pageUrl'     => get_permalink( $lid ),
		'image'       => $img,
	);
	$hue = yaww_meta( 'placeholder_hue', $lid );
	if ( '' !== $hue && null !== $hue && false !== $hue ) { $rec['placeholderHue'] = (int) $hue; }
	$card = yaww_meta( 'card_name', $lid );
	if ( $card ) { $rec['cardName'] = $card; }
	return $rec;
}

/** Retreat record in the shape of data/retreats.js. Dateless retreats are a location's program (waitlist). */
function yaww_retreat_record( $rid ) {
	$loc = (int) yaww_meta( 'location_id', $rid );
	$tids = array();
	foreach ( array_filter( (array) yaww_meta( 'teacher_ids', $rid ) ) as $t ) {
		$t = is_object( $t ) ? $t->ID : (int) $t;
		if ( $t ) { $tids[] = yaww_static_id( $t ); }
	}
	if ( ! $tids ) { $tids = array( 'tbd' ); }
	$rec = array(
		'id'           => yaww_static_id( $rid ),
		'slug'         => yaww_static_id( $rid ),
		'pageUrl'      => get_permalink( $rid ),
		'officialName' => get_the_title( $rid ),
		'locationId'   => $loc ? yaww_static_id( $loc ) : '',
		'teacherIds'   => $tids,
		'startDate'    => (string) yaww_meta( 'start_date', $rid ),
		'endDate'      => (string) yaww_meta( 'end_date', $rid ),
		'status'       => yaww_meta( 'status', $rid ) ?: 'available',
		'collection'   => yaww_meta( 'collection', $rid ) ?: 'deep-dives',
		'url'          => get_permalink( $rid ),
	);
	if ( 'spaces_left' === $rec['status'] ) { $rec['spacesLeft'] = (int) yaww_meta( 'spaces_left', $rid ); }
	$img = yaww_meta( 'card_image', $rid );
	if ( $img ) {
		$rec['image']   = yaww_attachment_url( $img, 'yaww-card' );
		$rec['image2x'] = yaww_attachment_url( $img, 'yaww-card-2x' );
	}
	return $rec;
}

/** Normalise a Y-m-d / Ymd date (ACF returns Ymd unless the field says otherwise). */
function yaww_ymd( $d ) {
	$d = (string) $d;
	if ( preg_match( '/^(\d{4})(\d{2})(\d{2})$/', $d, $m ) ) { return "$m[1]-$m[2]-$m[3]"; }
	if ( preg_match( '~^(\d{2})/(\d{2})/(\d{4})$~', $d, $m ) ) { return "$m[3]-$m[2]-$m[1]"; }
	return $d;
}

/** window.YAWW_LOCATIONS / YAWW_REGIONS / YAWW_TEACHERS / YAWW_RETREATS from WordPress. */
function yaww_catalog_js() {
	$locs = array();
	$loc_ids = get_posts( array( 'post_type' => 'location', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC', 'fields' => 'ids' ) );
	foreach ( $loc_ids as $lid ) { $locs[ $lid ] = yaww_location_record( $lid ); }

	$teachers = array();
	foreach ( get_posts( array( 'post_type' => 'teacher', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC', 'fields' => 'ids' ) ) as $tid ) {
		$photo = yaww_attachment_url( get_post_thumbnail_id( $tid ), 'yaww-person' );
		$teachers[] = array_filter( array(
			'id'            => yaww_static_id( $tid ),
			'name'          => get_the_title( $tid ),
			'slug'          => yaww_static_id( $tid ),
			'shortName'     => yaww_meta( 'short_name', $tid ) ?: null,
			'photo'         => $photo,
			'avatar'        => yaww_attachment_url( yaww_meta( 'avatar', $tid ), 'yaww-avatar' ) ?: $photo,
			'pageUrl'       => get_permalink( $tid ),
			'photoPosition' => yaww_meta( 'photo_position', $tid ) ?: null,
			'shortBio'      => yaww_meta( 'short_bio', $tid ) ?: null,
			'role'          => yaww_meta( 'role', $tid ) ?: null,
			'needsContent'  => yaww_meta( 'needs_content', $tid ) ? true : null,
		), function ( $v ) { return null !== $v; } );
	}
	$teachers[] = array( 'id' => 'tbd', 'name' => null, 'placeholderLabel' => 'a YAWW instructor', 'unannounced' => true );

	$retreats = array();
	foreach ( get_posts( array( 'post_type' => 'retreat', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) ) as $rid ) {
		$rec = yaww_retreat_record( $rid );
		$rec['startDate'] = yaww_ymd( $rec['startDate'] );
		$rec['endDate']   = yaww_ymd( $rec['endDate'] );
		if ( '' === $rec['startDate'] ) {
			/* No dates: the program a destination runs between editions (its waitlist page). */
			$lid = (int) yaww_meta( 'location_id', $rid );
			if ( $lid && isset( $locs[ $lid ] ) ) {
				$p = array( 'name' => $rec['officialName'], 'pageUrl' => $rec['pageUrl'] );
				if ( yaww_meta( 'collection', $rid ) ) { $p['collection'] = yaww_meta( 'collection', $rid ); }
				if ( ! empty( $rec['image'] ) ) { $p['image'] = $rec['image']; $p['image2x'] = $rec['image2x']; }
				$locs[ $lid ]['program'] = $p;
			}
			continue;
		}
		if ( yaww_meta( 'unlisted', $rid ) ) { $rec['unlisted'] = true; }
		$retreats[] = $rec;
	}
	usort( $retreats, function ( $a, $b ) { return strcmp( $a['startDate'], $b['startDate'] ); } );

	return 'window.YAWW_LOCATIONS = ' . wp_json_encode( array_values( $locs ) ) . ";\n"
		. 'window.YAWW_REGIONS = ["Europe", "The Americas", "Africa & Asia"];' . "\n"
		. 'window.YAWW_TEACHERS = ' . wp_json_encode( $teachers ) . ";\n"
		. 'window.YAWW_RETREATS = ' . wp_json_encode( $retreats ) . ';';
}
