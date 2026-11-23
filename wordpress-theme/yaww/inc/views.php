<?php
/**
 * Views owned by agent D (home, retreats listing, retreat page, header, footer).
 *
 * The static site is the spec: these views load the same css/ and js/ files, in the
 * same order, as index.html, retreats.html and retreatpage-*.html, and the behaviours
 * those pages run inline live in assets/js-theme/ (copied verbatim, data pulled out).
 *
 * The card components (js/retreat-cards.js), the nav (js/nav.js) and the listing read
 * window.YAWW_LOCATIONS / YAWW_TEACHERS / YAWW_RETREATS. yaww_data_js() (inc/helpers.php) writes
 * those from WordPress posts in the shape of data/*.js, so every script runs unchanged.
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
			$su = (string) get_post_meta( $id, 'static_url', true );   // the importer's record of the static page
			$map[ $su ? basename( $su ) : $prefix . yaww_static_id( $id ) . '.html' ] = get_permalink( $id );
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
	wp_add_inline_script( 'yaww-catalog', ( wp_script_is( 'yaww-data', 'enqueued' ) ? '' : yaww_data_js( array( 'locations', 'regions', 'teachers', 'retreats' ) ) ) . yaww_links_js() );
	wp_enqueue_script( 'yaww-wp-links', yaww_theme_js_url( 'wp-links.js' ), array( 'yaww-catalog' ), yaww_theme_js_ver( 'wp-links.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
	wp_enqueue_script( 'yaww-nav', yaww_asset_url( 'js/nav.js' ), array( 'yaww-wp-links' ), yaww_asset_ver( 'js/nav.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
	/* functions.php may have registered nav.js first (its fallback): it must still run after wp-links.js. */
	$reg = wp_scripts()->query( 'yaww-nav', 'registered' );
	if ( $reg && ! in_array( 'yaww-wp-links', $reg->deps, true ) ) { $reg->deps[] = 'yaww-wp-links'; }

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
		$wait = ( 'waitlist' === yaww_meta( 'status', $id ) ) || ! yaww_meta( 'start_date', $id );
		/* Static order: booking-bar, retreat-rooms, lead-modal (+ hosts on a dated page), all defer. */
		foreach ( $wait ? array( 'booking-bar', 'retreat-rooms', 'lead-modal' ) : array( 'booking-bar', 'retreat-rooms', 'lead-modal', 'hosts' ) as $js ) {
			wp_enqueue_script( 'yaww-r-' . $js, yaww_asset_url( 'js/' . $js . '.js' ), array( 'yaww-retreat-page' ), yaww_asset_ver( 'js/' . $js . '.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		}
		if ( $wait ) {
			/* The waitlist page adds js/retreat-cards.js and wires its form (build_page.py --waitlist). */
			wp_enqueue_script( 'yaww-r-retreat-cards', yaww_asset_url( 'js/retreat-cards.js' ), array( 'yaww-retreat-page' ), yaww_asset_ver( 'js/retreat-cards.js' ), true );
			wp_add_inline_script( 'yaww-r-retreat-cards', "YAWW.wireWaitlistForm(document.querySelector('[data-waitlist-form]'));" );
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
	foreach ( function_exists( 'yaww_data' ) ? yaww_data( 'locations' ) : array() as $l ) {   // A's catalog first: one source
		if ( $l['pageUrl'] === get_permalink( $lid ) ) { return $l; }
	}
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
	foreach ( function_exists( 'yaww_data' ) ? yaww_data( 'retreats' ) : array() as $r ) {    // A's catalog first: one source
		if ( $r['pageUrl'] === get_permalink( $rid ) ) { return $r; }
	}
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

/* --------------------------------------------------------- retreat page -- */

/** "November 7 – 14, 2026": retreats/_tools/build_page.py date_range() (the hero's format). */
function yaww_d_date_range( $a, $b ) {
	$s = $a ? date_create( $a ) : false;
	$e = $b ? date_create( $b ) : false;
	if ( ! $s ) { return ''; }
	if ( ! $e ) { return $s->format( 'F j, Y' ); }
	if ( $s->format( 'Y' ) !== $e->format( 'Y' ) ) { return $s->format( 'F j, Y' ) . ' – ' . $e->format( 'F j, Y' ); }
	if ( $s->format( 'n' ) !== $e->format( 'n' ) ) { return $s->format( 'F j' ) . ' – ' . $e->format( 'F j, Y' ); }
	return $s->format( 'F j' ) . ' – ' . $e->format( 'j, Y' );
}

/** An image / gallery value (ids, ACF arrays, WP_Post) as attachment ids. */
function yaww_d_ids( $v ) {
	$out = array();
	if ( is_string( $v ) && false !== strpos( $v, ',' ) ) { $v = explode( ',', $v ); }
	foreach ( (array) $v as $x ) {
		if ( is_array( $x ) ) { $x = isset( $x['ID'] ) ? $x['ID'] : ( isset( $x['id'] ) ? $x['id'] : 0 ); }
		if ( is_object( $x ) ) { $x = $x->ID; }
		if ( is_numeric( $x ) && (int) $x ) { $out[] = (int) $x; }
	}
	return $out;
}

/** Gallery -> [ {src, alt} ] at full size: the Media Library holds the very files the static pages serve. */
function yaww_d_gallery( $v ) {
	$out = array();
	foreach ( yaww_d_ids( $v ) as $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) { $out[] = array( 'src' => $src, 'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ); }
	}
	return $out;
}

function yaww_d_urls( $v ) {
	return array_map( function ( $p ) { return $p['src']; }, yaww_d_gallery( $v ) );
}

/** Paragraphs of a content / wysiwyg value: inner HTML of each <p>, else blank-line separated blocks. */
function yaww_d_paragraphs( $html ) {
	$html = trim( (string) $html );
	if ( '' === $html ) { return array(); }
	$html = preg_replace( '/<!--.*?-->/s', '', $html );
	if ( preg_match_all( '~<p[^>]*>(.*?)</p>~s', $html, $m ) ) {
		return array_values( array_filter( array_map( 'trim', $m[1] ), 'strlen' ) );
	}
	return array_values( array_filter( array_map( 'trim', preg_split( '/\n\s*\n|\r\n\s*\r\n/', $html ) ), 'strlen' ) );
}

/** The hosts block's people, in the builder's order: teachers, then the facilitator. */
function yaww_d_hosts( $rid ) {
	$hosts = array();
	$first = function ( $n ) { $p = preg_split( '/\s+/', trim( $n ) ); return $p[0]; };
	foreach ( yaww_d_ids( yaww_meta( 'teacher_ids', $rid ) ) as $i => $tid ) {
		$name    = get_the_title( $tid );
		$hosts[] = array(
			'kind' => 'teacher',
			'key'  => 0 === $i ? 'teacher' : yaww_static_id( $tid ),
			'tab'  => yaww_meta( 'role', $tid ) ?: 'Teacher',
			'name' => $name,
			'photo' => get_the_post_thumbnail_url( $tid, 'full' ),
			'pos'  => yaww_meta( 'photo_position', $tid ),
			'bio'  => yaww_meta( 'host_bio', $tid ) ?: yaww_meta( 'short_bio', $tid ),
			'href' => get_permalink( $tid ),
			'link' => 'Meet ' . $first( $name ),
		);
	}
	if ( ! $hosts ) {
		$hosts[] = array(
			'kind' => 'tba', 'key' => 'teacher', 'tab' => 'Teacher', 'name' => 'To be announced', 'photo' => '', 'pos' => '',
			'bio'  => 'The teacher for these dates has not been announced yet. Ask us and we’ll let you know as soon as it’s confirmed.',
			'href' => yaww_static_url( 'contact.html#general-inquiry' ), 'link' => 'Ask about these dates',
			'attrs' => ' data-lead-open data-lead-source="hosts"',
		);
	}
	foreach ( yaww_d_ids( yaww_meta( 'facilitator_ids', $rid ) ) as $fid ) {
		$name    = get_the_title( $fid );
		$hosts[] = array(
			'kind' => 'facilitator', 'key' => 'facilitator', 'tab' => 'Facilitator', 'name' => $name,
			'photo' => get_the_post_thumbnail_url( $fid, 'full' ),
			'pos'  => yaww_meta( 'photo_position', $fid ),
			'bio'  => yaww_meta( 'short_bio', $fid ),
			'href' => get_permalink( $fid ),
			'link' => 'Meet ' . $first( $name ),
		);
		break; // one facilitator per retreat, as on the static pages
	}
	return $hosts;
}

/** One host, as build_page.py host_html() writes it. */
function yaww_d_host_html( $h ) {
	$in  = '        ';
	$pos = $h['pos'] ? ' style="object-position: ' . esc_attr( $h['pos'] ) . '"' : '';
	echo $in . ( $h['photo']
		? '<img class="host__photo" src="' . esc_url( $h['photo'] ) . '" alt="' . esc_attr( $h['name'] ) . '"' . $pos . ' loading="lazy">'
		: '<div class="host__photo host__photo--empty" aria-hidden="true"></div>' ) . "\n";
	echo $in . '<div class="host">' . "\n";
	echo $in . '<h3 class="host__name">' . esc_html( $h['name'] ) . '</h3>' . "\n";
	if ( $h['bio'] ) { echo $in . '<p class="host__bio">' . esc_html( $h['bio'] ) . '</p>' . "\n"; }
	if ( $h['href'] ) {
		echo $in . '<a class="host__link" href="' . esc_url( $h['href'] ) . '"' . ( isset( $h['attrs'] ) ? $h['attrs'] : '' ) . '>' . esc_html( $h['link'] ) . ' <span aria-hidden="true">&rarr;</span></a>' . "\n";
	}
	echo $in . '</div>' . "\n";
}

/** window.YAWW_RETREAT: the data literals of the static page's inline script (see js-theme/retreat-page.js). */
function yaww_retreat_page_js( $rid ) {
	$num = function ( $v ) {
		if ( null === $v || '' === $v || false === $v ) { return null; }
		$f = (float) $v;
		return floor( $f ) === $f ? (int) $f : $f;
	};
	$hotels = array();
	foreach ( yaww_rows( 'stays', $rid ) as $s ) {
		$hotels[] = array(
			'name'    => (string) ( isset( $s['name'] ) ? $s['name'] : '' ),
			'tab'     => (string) ( ! empty( $s['tab'] ) ? $s['tab'] : ( isset( $s['name'] ) ? $s['name'] : '' ) ),
			'gallery' => yaww_d_urls( isset( $s['gallery'] ) ? $s['gallery'] : array() ),
			'text'    => array_map( 'esc_html', yaww_lines( isset( $s['text'] ) ? $s['text'] : '' ) ),
		);
	}
	$extras = array();
	foreach ( yaww_rows( 'extras', $rid ) as $x ) {
		$price = isset( $x['price'] ) && '' !== $x['price'] ? $x['price'] : ( isset( $x['price_usd'] ) && '' !== $x['price_usd'] ? (int) $x['price_usd'] . ' USD' : '' );
		$extras[] = array(
			'name'    => (string) ( isset( $x['name'] ) ? $x['name'] : '' ),
			'tab'     => (string) ( ! empty( $x['tab'] ) ? $x['tab'] : ( isset( $x['name'] ) ? $x['name'] : '' ) ),
			'desc'    => esc_html( isset( $x['desc'] ) ? $x['desc'] : '' ),
			'price'   => (string) $price,
			'unit'    => (string) ( ! empty( $x['unit'] ) ? $x['unit'] : 'each' ),
			'gallery' => yaww_d_urls( isset( $x['gallery'] ) ? $x['gallery'] : array() ),
		);
	}
	$tiers = array();
	foreach ( yaww_rows( 'rooms', $rid ) as $r ) {
		$tiers[] = array(
			'name'      => (string) ( isset( $r['name'] ) ? $r['name'] : '' ),
			'priceUSD'  => $num( isset( $r['price_usd'] ) ? $r['price_usd'] : null ),
			'priceUnit' => isset( $r['price_unit'] ) && '' !== $r['price_unit'] ? $r['price_unit'] : null,
			'desc'      => isset( $r['description'] ) && '' !== $r['description'] ? $r['description'] : null,
			'pills'     => yaww_lines( isset( $r['pills'] ) ? $r['pills'] : '' ),
			'gallery'   => yaww_d_urls( isset( $r['gallery'] ) ? $r['gallery'] : array() ),
		);
	}
	$quotes = array();
	foreach ( yaww_rows( 'testimonials', $rid ) as $q ) {
		$quotes[] = array( 'quote' => (string) $q['quote'], 'name' => (string) $q['name'], 'detail' => (string) ( isset( $q['detail'] ) ? $q['detail'] : '' ) );
	}
	$st  = yaww_meta( 'status', $rid );
	$urg = null;
	if ( 'sold_out' === $st ) { $urg = array( 'type' => 'soldout' ); }
	elseif ( 'waitlist' === $st || ! yaww_meta( 'start_date', $rid ) ) { $urg = array( 'type' => 'scarce', 'label' => 'Waitlist' ); }
	elseif ( in_array( $st, array( 'filling_fast', 'new', 'spaces_left' ), true ) ) {
		$l = yaww_status_label( $rid );
		if ( $l ) { $urg = array( 'type' => 'scarce', 'label' => $l ); }
	}
	$data = array(
		'hotels'       => $hotels,
		'extras'       => $extras,
		'tiers'        => $tiers,
		'roomVenues'   => array_values( yaww_list( 'room_venues', $rid ) ),
		'urgency'      => $urg,
		'testimonials' => $quotes,
	);
	$js  = 'window.YAWW_RETREAT = ' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ';';
	$url = yaww_meta( 'wetravel_url', $rid );
	if ( $url ) { $js .= "\nwindow.YAWW_WETRAVEL = " . wp_json_encode( array( 'url' => esc_url_raw( $url ) ) ) . ';'; }
	return $js;
}
