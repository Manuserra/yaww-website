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

/* The static pages load these two families from Google Fonts. */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'yaww-fonts',
		'https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Raleway:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap',
		array(), null );
	wp_enqueue_style( 'yaww-base', YAWW_URI . '/assets/css/base.css', array( 'yaww-fonts' ), yaww_asset_ver( 'css/base.css' ) );
	wp_enqueue_style( 'yaww-lead-modal', YAWW_URI . '/assets/css/lead-modal.css', array( 'yaww-base' ), yaww_asset_ver( 'css/lead-modal.css' ) );

	$page_css = yaww_page_css();
	if ( $page_css ) {
		wp_enqueue_style( 'yaww-page', YAWW_URI . '/assets/css/' . $page_css . '.css', array( 'yaww-base' ), yaww_asset_ver( 'css/' . $page_css . '.css' ) );
	}

	wp_enqueue_script( 'yaww-lenis', 'https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js', array(), '1.1.18', true );
	wp_enqueue_script( 'yaww-theme', YAWW_URI . '/assets/theme.js', array(), YAWW_VERSION, true );
	wp_enqueue_script( 'yaww-lead-modal', YAWW_URI . '/assets/js/lead-modal.js', array(), yaww_asset_ver( 'js/lead-modal.js' ), true );

	if ( is_singular( 'retreat' ) ) {
		wp_enqueue_script( 'yaww-booking-bar', YAWW_URI . '/assets/js/booking-bar.js', array(), yaww_asset_ver( 'js/booking-bar.js' ), true );
		wp_enqueue_script( 'yaww-retreat-rooms', YAWW_URI . '/assets/js/retreat-rooms.js', array( 'yaww-theme' ), yaww_asset_ver( 'js/retreat-rooms.js' ), true );
		// retreat-rooms.js reads the globals TIERS / ROOM_VENUES; the static page defines them inline.
		wp_add_inline_script( 'yaww-theme', yaww_rooms_js_globals( get_the_ID() ), 'before' );
	}
	if ( is_singular( 'post' ) ) {
		wp_enqueue_script( 'yaww-journal-post', YAWW_URI . '/assets/js/journal-post.js', array(), yaww_asset_ver( 'js/journal-post.js' ), true );
	}
	if ( is_page_template( 'page-templates/contact.php' ) || is_page_template( 'page-templates/teach-with-us.php' ) ) {
		wp_enqueue_script( 'yaww-contact', YAWW_URI . '/assets/js/contact.js', array(), yaww_asset_ver( 'js/contact.js' ), true );
		wp_add_inline_script( 'yaww-contact', 'window.YAWW_CONTACT = ' . wp_json_encode( yaww_contact_config() ) . ';', 'before' );
	}
} );

/** Which css/<name>.css belongs to the current view (same mapping as the static pages' <link>). */
function yaww_page_css() {
	if ( is_front_page() ) { return 'home'; }
	if ( is_singular( 'retreat' ) ) { return 'retreat'; }
	if ( is_post_type_archive( 'retreat' ) ) { return 'retreats'; }
	if ( is_singular( 'location' ) ) { return 'location'; }
	if ( is_post_type_archive( 'location' ) ) { return 'locations'; }
	if ( is_singular( array( 'teacher', 'facilitator' ) ) || is_post_type_archive( 'teacher' ) || is_post_type_archive( 'facilitator' ) ) { return 'people'; }
	if ( is_home() ) { return 'journal'; }
	if ( is_singular( 'post' ) ) { return 'journal-post'; }
	if ( is_page_template( 'page-templates/about.php' ) ) { return 'about'; }
	if ( is_page_template( 'page-templates/contact.php' ) ) { return 'contact'; }
	if ( is_page_template( 'page-templates/teach-with-us.php' ) ) { return 'teach'; }
	return '';
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
