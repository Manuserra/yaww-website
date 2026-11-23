<?php
/**
 * YAWW theme bootstrap.
 *
 * Styles and scripts are the static site's own css/ and js/, copied into
 * assets/ by bin/sync-assets.sh, so the theme and the static site cannot drift.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'YAWW_VERSION', '0.1.0' );
define( 'YAWW_DIR', get_template_directory() );
define( 'YAWW_URI', get_template_directory_uri() );

require_once YAWW_DIR . '/inc/helpers.php';
require_once YAWW_DIR . '/inc/post-types.php';
require_once YAWW_DIR . '/inc/fields.php';
/* Other agents' includes (BRIEF-WP ownership); each is optional so a missing file never breaks the site. */
foreach ( array( 'redirects', 'seo', 'settings', 'forms', 'views', 'e-helpers', 'admin' ) as $yaww_inc ) {
	if ( file_exists( YAWW_DIR . '/inc/' . $yaww_inc . '.php' ) ) { require_once YAWW_DIR . '/inc/' . $yaww_inc . '.php'; }
}
unset( $yaww_inc );

/* Imported photos keep their own file and name: no "-scaled" copy (photo-focus.css keys on names),
   and no 1536/2048 sizes nobody uses. */
add_filter( 'big_image_size_threshold', '__return_false' );
add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {
	unset( $sizes['1536x1536'], $sizes['2048x2048'] );
	return $sizes;
} );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array(
		'primary' => __( 'Primary', 'yaww' ),
		'footer'  => __( 'Footer', 'yaww' ),
	) );

	/* Sizes mirror the static site's frames. Retina variants are the same
	   frame at 2x, as in data/retreats.js (image / image2x). */
	add_image_size( 'yaww-card',      312, 408, true );   // retreat card, 3:4
	add_image_size( 'yaww-card-2x',   624, 816, true );
	add_image_size( 'yaww-loc-card',  600, 750, true );   // location card, 4:5
	add_image_size( 'yaww-hero',     1920, 1080, true );  // retreat / location hero, 16:9
	add_image_size( 'yaww-person',    800, 1000, true );  // teacher / facilitator portrait, 4:5
	add_image_size( 'yaww-avatar',    160, 160, true );   // 1:1 circle
	add_image_size( 'yaww-gallery',  1200, 0, false );    // free ratio, as the slider uses --r per photo
	add_image_size( 'yaww-journal',  1200, 750, true );   // journal hero, 16:10
} );

/*
 * Assets. Every static page loads, in this order:
 *   Google Fonts, [Hugeicons], base.css, <page css...>, type.css, [rooms.css], attribution.js,
 *   cards.css, nav.css, photo-focus.css, nav.js, [data + page scripts], Lenis
 * (retreats/_tools/chrome.py LAYERS_CSS / LAYERS_JS). yaww_page_assets() is that table.
 * D's views (inc/views.php: home, listing, retreat) and E's views (inc/e-helpers.php: location,
 * people, about, journal, post, page) print their own chain at priority 20; this baseline covers
 * what is left (contact, teach-with-us, 404, search) and the fonts everywhere.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'yaww-fonts',
		'https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Raleway:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap',
		array(), null );

	$a = yaww_page_assets();
	if ( ! $a ) { return; }
	if ( $a['icons'] ) { wp_enqueue_style( 'yaww-hugeicons', 'https://use.hugeicons.com/font/icons.css', array(), null ); }
	$prev = array( 'yaww-fonts' );
	foreach ( $a['css'] as $css ) {
		$h = 'yaww-css-' . $css;
		wp_enqueue_style( $h, YAWW_URI . '/assets/css/' . $css . '.css', $prev, yaww_asset_ver( 'css/' . $css . '.css' ) );
		$prev = array( $h );
	}
	wp_enqueue_script( 'yaww-attribution', YAWW_URI . '/assets/js/attribution.js', array(), yaww_asset_ver( 'js/attribution.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
	if ( ! wp_script_is( 'yaww-nav', 'enqueued' ) && ! wp_script_is( 'yaww-nav', 'registered' ) ) {
		// inc/views.php normally enqueues nav.js with its URL bridge; this is the fallback.
		wp_enqueue_script( 'yaww-nav', YAWW_URI . '/assets/js/nav.js', array(), yaww_asset_ver( 'js/nav.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
	}
	$prevjs = array();
	if ( $a['data'] ) {
		wp_register_script( 'yaww-data', false, array(), null, false );
		wp_enqueue_script( 'yaww-data' );
		wp_add_inline_script( 'yaww-data', yaww_data_js( $a['data'] ) );
		$prevjs = array( 'yaww-data' );
	}
	foreach ( $a['js'] as $js ) {
		$h = 'yaww-js-' . $js;
		wp_enqueue_script( $h, YAWW_URI . '/assets/js/' . $js . '.js', $prevjs, yaww_asset_ver( 'js/' . $js . '.js' ), true );
		$prevjs = array( $h );
	}
	if ( in_array( 'contact', $a['js'], true ) ) {
		wp_add_inline_script( 'yaww-js-contact', 'window.YAWW_CONTACT = window.YAWW_CONTACT || ' . wp_json_encode( yaww_contact_config() ) . ';', 'before' );
	}
	wp_enqueue_script( 'yaww-lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js', array(), '1.1.18', true );
} );

/**
 * The static page's asset list for the current request, or null when D's / E's views own it.
 * css = the full chain in order (base … photo-focus); data = window.YAWW_* sets; js = page scripts.
 */
function yaww_page_assets() {
	$d_view = function_exists( 'yaww_view' ) && '' !== yaww_view();
	$e_view = is_singular( array( 'location', 'teacher', 'facilitator', 'post' ) ) || is_post_type_archive( array( 'location', 'teacher', 'facilitator' ) )
		|| is_home() || is_page_template( 'page-templates/about.php' );
	if ( $d_view || ( $e_view && function_exists( 'yaww_e_assets' ) ) ) { return null; }

	$tail = array( 'type', 'cards', 'nav', 'photo-focus' );
	$out  = array( 'icons' => true, 'css' => array_merge( array( 'base' ), $tail ), 'data' => array(), 'js' => array() );
	if ( is_page_template( 'page-templates/contact.php' ) ) {                     // contact.html
		$out['css']  = array_merge( array( 'base', 'contact' ), $tail );
		$out['data'] = array( 'locations', 'regions', 'retreats' );   // data/locations.js also sets YAWW_REGIONS (nav panel order)
		$out['js']   = array( 'contact' );
	} elseif ( is_page_template( 'page-templates/teach-with-us.php' ) ) {         // teach-with-us.html
		$out['css'] = array_merge( array( 'base', 'lead', 'contact', 'teach' ), $tail );
		$out['js']  = array( 'contact' );
	} elseif ( is_front_page() ) {                                                // index.html (fallback)
		$out['css']  = array_merge( array( 'base', 'home', 'lead' ), $tail );
		$out['data'] = array( 'locations', 'regions', 'teachers', 'retreats' );
		$out['js']   = array( 'retreat-cards' );
	} elseif ( is_singular( 'retreat' ) ) {                                       // retreatpage-*.html (fallback)
		$out['icons'] = false;
		$out['css']   = array( 'base', 'retreat', 'lead-modal', 'type', 'rooms', 'cards', 'nav', 'photo-focus' );
		$out['js']    = array( 'booking-bar', 'retreat-rooms', 'lead-modal', 'hosts' );
	} elseif ( is_post_type_archive( 'retreat' ) || is_tax( 'retreat_tier' ) ) {  // retreats.html (fallback)
		$out['icons'] = false;
		$out['css']   = array_merge( array( 'base', 'filters', 'retreats' ), $tail );
		$out['data']  = array( 'locations', 'regions', 'teachers', 'retreats' );
		$out['js']    = array( 'retreat-cards' );
	}
	return $out;
}

/** Cache-busting by file mtime; falls back to the theme version. */
function yaww_asset_ver( $rel ) {
	$f = YAWW_DIR . '/assets/' . $rel;
	return file_exists( $f ) ? (string) filemtime( $f ) : YAWW_VERSION;
}

/** Same contract as data/contact.js. Endpoints stay null until the backend exists. */
function yaww_contact_config() {
	return array(
		'endpoints' => array( 'contact' => null, 'teach' => null ),
		'email'     => null,
		'phone'     => array( 'label' => '+52 1 984 202 6072', 'href' => 'tel:+5219842026072' ),
		'instagram' => null,
	);
}

/** Journal categories are the built-in `category` taxonomy; nothing else to register. */

/* The static site shows emoji as text; WordPress's emoji script swapped them for twemoji <img> (QA, journal). */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
