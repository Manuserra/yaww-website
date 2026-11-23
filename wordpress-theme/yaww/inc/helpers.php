<?php
/**
 * Template helpers. Templates never call get_post_meta / get_field directly:
 * they call yaww_meta(), so the day ACF Pro arrives nothing in the templates changes.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Meta value, ACF first (when present and non-empty), native post meta otherwise. */
function yaww_meta( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$alias = array( 'video_url' => 'youtube', 'intro' => '' );   // renamed fields (2026-11-23)
	if ( isset( $alias[ $key ] ) ) {
		if ( '' === $alias[ $key ] ) { return get_post_field( 'post_content', $post_id ); }   // location intro = content
		$key = $alias[ $key ];
	}
	if ( function_exists( 'get_field' ) ) {
		$v = get_field( $key, $post_id );
		if ( null !== $v && false !== $v && '' !== $v && array() !== $v ) { return $v; }
	}
	return get_post_meta( $post_id, $key, true );
}

/** Echo escaped text meta. */
function yaww_the_meta( $key, $post_id = null ) { echo esc_html( yaww_meta( $key, $post_id ) ); }

/** A "list" field as plain strings (ACF repeater returns [{value}] rows, native returns strings). */
function yaww_list( $key, $post_id = null ) {
	$out = array();
	foreach ( (array) yaww_meta( $key, $post_id ) as $row ) {
		$s = is_array( $row ) ? ( isset( $row['value'] ) ? $row['value'] : '' ) : $row;
		if ( '' !== trim( (string) $s ) ) { $out[] = (string) $s; }
	}
	return $out;
}

/** A "rows" field as an array of assoc arrays. */
function yaww_rows( $key, $post_id = null ) {
	$v = yaww_meta( $key, $post_id );
	return is_array( $v ) ? array_values( array_filter( $v, 'is_array' ) ) : array();
}

/** A textarea "one per line" value as an array. */
function yaww_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}

/** Image field (attachment id) -> <img>; empty string when unset. */
function yaww_img( $key_or_id, $size = 'large', $attr = array(), $post_id = null ) {
	$id = is_numeric( $key_or_id ) ? (int) $key_or_id : (int) yaww_meta( $key_or_id, $post_id );
	return $id ? wp_get_attachment_image( $id, $size, false, $attr ) : '';
}

/** Image field -> URL (for CSS backgrounds). Falls back to the featured image. */
function yaww_img_url( $key, $size = 'large', $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$id = (int) yaww_meta( $key, $post_id );
	if ( ! $id ) { $id = (int) get_post_thumbnail_id( $post_id ); }
	$src = $id ? wp_get_attachment_image_src( $id, $size ) : null;
	return $src ? $src[0] : '';
}

/** "September 4-8, 2026" / "Nov 28 - Dec 3, 2026" from two Y-m-d dates (port of YAWW.formatDateRange). */
function yaww_date_range( $start, $end ) {
	$s = $start ? date_create( $start ) : false;
	$e = $end ? date_create( $end ) : false;
	if ( ! $s ) { return ''; }
	if ( ! $e || $s == $e ) { return $s->format( 'F j, Y' ); }
	if ( $s->format( 'Y-m' ) === $e->format( 'Y-m' ) ) { return $s->format( 'F j' ) . '-' . $e->format( 'j, Y' ); }
	if ( $s->format( 'Y' ) === $e->format( 'Y' ) ) { return $s->format( 'M j' ) . ' - ' . $e->format( 'M j, Y' ); }
	return $s->format( 'M j, Y' ) . ' - ' . $e->format( 'M j, Y' );
}

function yaww_retreat_dates( $post_id = null ) {
	return yaww_date_range( yaww_meta( 'start_date', $post_id ), yaww_meta( 'end_date', $post_id ) );
}

/** Status badge label, or '' for "available". Same enum as js/retreat-cards.js. */
function yaww_status_label( $post_id = null ) {
	$st = yaww_meta( 'status', $post_id );
	if ( 'filling_fast' === $st ) { return 'Filling fast'; }
	if ( 'sold_out' === $st ) { return 'Sold out'; }
	if ( 'new' === $st ) { return 'New retreat'; }
	if ( 'spaces_left' === $st ) {
		$n = (int) yaww_meta( 'spaces_left', $post_id );
		return $n ? ( 1 === $n ? '1 space left' : $n . ' spaces left' ) : '';
	}
	return '';
}

function yaww_tier_label( $post_id = null ) {
	return ( 'signature-collection' === yaww_meta( 'collection', $post_id ) ) ? 'Signature' : 'Deep Dive';
}

/** Linked location post id of a retreat. */
function yaww_retreat_location( $post_id = null ) { return (int) yaww_meta( 'location_id', $post_id ); }

/** Italic location line: the location's display_name, falling back to its title. */
function yaww_location_line( $loc_id ) {
	if ( ! $loc_id ) { return ''; }
	$n = yaww_meta( 'display_name', $loc_id );
	return $n ? $n : get_the_title( $loc_id );
}

/** "Renata Centi & Sue Berger" from the retreat's teacher ids. */
function yaww_teacher_line( $post_id = null ) {
	$names = array();
	foreach ( array_filter( (array) yaww_meta( 'teacher_ids', $post_id ) ) as $tid ) { $names[] = get_the_title( $tid ); }
	return implode( ' & ', $names );
}

/**
 * Hotel confidentiality (BRIEF s.0/s.12): a hotel name must never reach a page. Only the generic
 * label is public. Utopia is the one exception, and it is ours, so it is not special-cased here:
 * write it in the `hotel_label` field itself.
 */
function yaww_hotel_label( $post_id = null ) {
	$l = yaww_meta( 'hotel_label', $post_id );
	return $l ? $l : '';
}

/** window.TIERS / ROOM_VENUES for js/retreat-rooms.js. Hotel names are never emitted. */
function yaww_rooms_js_globals( $post_id ) {
	$tiers = array();
	foreach ( yaww_rows( 'rooms', $post_id ) as $r ) {
		$gal = array();
		foreach ( (array) ( isset( $r['gallery'] ) ? $r['gallery'] : array() ) as $gid ) {
			$u = wp_get_attachment_image_url( (int) $gid, 'full' );
			if ( $u ) { $gal[] = $u; }
		}
		$tiers[] = array(
			'name'      => isset( $r['name'] ) ? $r['name'] : '',
			'priceUSD'  => isset( $r['price_usd'] ) && '' !== $r['price_usd'] ? (float) $r['price_usd'] : null,
			'priceUnit' => isset( $r['price_unit'] ) ? $r['price_unit'] : '',
			'desc'      => isset( $r['description'] ) ? $r['description'] : '',
			'pills'     => yaww_lines( isset( $r['pills'] ) ? $r['pills'] : '' ),
			'gallery'   => $gal,
		);
	}
	$js  = 'var TIERS = ' . wp_json_encode( $tiers ) . '; var ROOM_VENUES = ' . wp_json_encode( yaww_list( 'room_venues', $post_id ) ) . ';';
	$url = yaww_meta( 'wetravel_url', $post_id );
	if ( $url ) { $js .= ' window.YAWW_WETRAVEL = ' . wp_json_encode( array( 'url' => esc_url_raw( $url ) ) ) . ';'; }
	return $js;
}

/** Load a template part from template-parts/ with args. */
function yaww_part( $name, $args = array() ) { get_template_part( 'template-parts/' . $name, null, $args ); }

/** Nav structure shared by header and footer (labels from the static site; contact page's labels win). */
function yaww_nav_items() {
	return array(
		array( 'label' => 'Retreats',  'url' => get_post_type_archive_link( 'retreat' ) ),
		array( 'label' => 'Locations', 'url' => get_post_type_archive_link( 'location' ) ),
		array( 'label' => 'About', 'url' => home_url( '/about/' ), 'sub' => array(
			array( 'About Us', home_url( '/about/' ) ),
			array( 'Teachers', get_post_type_archive_link( 'teacher' ) ),
			array( 'Facilitators', get_post_type_archive_link( 'facilitator' ) ),
		) ),
		array( 'label' => 'Journal', 'url' => home_url( '/journal/' ) ),
		array( 'label' => 'Contact', 'url' => home_url( '/contact/' ), 'sub' => array(
			array( 'Ask a question', home_url( '/contact/' ) ),
			array( 'Private groups', home_url( '/contact/#private-groups' ) ),
			array( 'Teach with us', home_url( '/teach-with-us/' ) ),
		) ),
	);
}

/* ------------------------------------------------------------------------------------------
 * Images. bin/import.php keeps every file under its original name in uploads/yaww/<path under
 * images/>, e.g. uploads/yaww/retreat-photos/argentina/yoga-retreat-argentina-....webp, so the
 * static css/photo-focus.css selectors ([src$="/<name>.webp"]) match when the template prints
 * the 'full' size. A retina twin (<name>@2x.webp) is registered on the 1x attachment as the
 * size '2x', so wp_get_attachment_image() adds it to srcset and yaww_img_2x() gives its URL.
 * ---------------------------------------------------------------------------------------- */

/** URL of the attachment's @2x twin, or ''. */
function yaww_img_2x( $id ) {
	$meta = wp_get_attachment_metadata( (int) $id );
	if ( empty( $meta['sizes']['2x']['file'] ) ) { return ''; }
	$src = wp_get_attachment_image_src( (int) $id, '2x' );
	return $src ? $src[0] : '';
}

/** Static-site markup: <img src="1x" srcset="1x 1x, 2x 2x" alt="">, the way the static pages write card images. */
function yaww_img_1x2x( $id, $attr = array() ) {
	$id  = (int) $id;
	$src = $id ? wp_get_attachment_image_url( $id, 'full' ) : '';
	if ( ! $src ) { return ''; }
	$two = yaww_img_2x( $id );
	$a   = array_merge( array( 'src' => $src, 'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ), $attr );
	if ( $two ) { $a['srcset'] = $src . ' 1x, ' . $two . ' 2x'; }
	$out = '<img';
	foreach ( $a as $k => $v ) {
		if ( false === $v || null === $v ) { continue; }
		$out .= ' ' . $k . '="' . esc_attr( $v ) . '"';
	}
	return $out . '>';
}

/** Gallery / rows image ids as URLs (full size, original file names). */
function yaww_img_urls( $ids, $size = 'full' ) {
	$out = array();
	foreach ( (array) $ids as $gid ) {
		$u = wp_get_attachment_image_url( (int) ( is_array( $gid ) ? ( isset( $gid['ID'] ) ? $gid['ID'] : 0 ) : $gid ), $size );
		if ( $u ) { $out[] = $u; }
	}
	return $out;
}

/** Retina twins are larger than 'full' on purpose; let srcset use them. */
add_filter( 'max_srcset_image_width', function ( $w ) { return max( (int) $w, 3200 ); } );

/** Post id of a CPT entry by its static-site id (the `_yaww_source_id` meta set by bin/import.php). */
function yaww_by_source( $post_type, $source_id ) {
	$q = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids',
		'meta_key' => '_yaww_source_id', 'meta_value' => $source_id ) );
	return $q ? (int) $q[0] : 0;
}

/* ------------------------------------------------------------------------------------------
 * window.YAWW_* globals in the exact shape of the static site's data/*.js, built from the
 * database. The static scripts (js/retreat-cards.js, people-cards.js, nav.js, journal-post.js)
 * then run unchanged: URLs are absolute permalinks / Media Library URLs instead of *.html and
 * images/... paths. yaww_data_js( array( 'locations', 'retreats', ... ) ) returns the <script> body.
 * ---------------------------------------------------------------------------------------- */
function yaww_data( $what ) {
	static $cache = array();
	if ( isset( $cache[ $what ] ) ) { return $cache[ $what ]; }
	$slug = function ( $id ) { return $id ? get_post_field( 'post_name', (int) $id ) : null; };
	$out = array();
	switch ( $what ) {
		case 'regions':
			$out = (array) get_option( 'yaww_regions', array() );
			break;
		case 'locations':
			foreach ( get_posts( array( 'post_type' => 'location', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $p ) {
				$l = array(
					'id' => $p->post_name, 'displayName' => yaww_meta( 'display_name', $p->ID ), 'name' => yaww_meta( 'short_name', $p->ID ) ?: $p->post_title,
					'region' => yaww_meta( 'region', $p->ID ), 'country' => yaww_meta( 'country', $p->ID ), 'pageUrl' => get_permalink( $p ),
					'image' => wp_get_attachment_image_url( (int) yaww_meta( 'card_image', $p->ID ), 'full' ) ?: null,
					'placeholderHue' => ( '' !== (string) yaww_meta( 'placeholder_hue', $p->ID ) ) ? (int) yaww_meta( 'placeholder_hue', $p->ID ) : null,
				);
				if ( yaww_meta( 'card_name', $p->ID ) ) { $l['cardName'] = yaww_meta( 'card_name', $p->ID ); }
				$prog = (int) yaww_meta( 'program_retreat', $p->ID );
				if ( $prog && 'publish' === get_post_status( $prog ) ) {
					$img = (int) yaww_meta( 'card_image', $prog );
					$l['program'] = array( 'name' => get_post_field( 'post_title', $prog ), 'pageUrl' => get_permalink( $prog ),
						'collection' => yaww_meta( 'collection', $prog ) ?: null,
						'image' => $img ? wp_get_attachment_image_url( $img, 'full' ) : null, 'image2x' => $img ? ( yaww_img_2x( $img ) ?: null ) : null );
				}
				$out[] = $l;
			}
			break;
		case 'teachers':
			foreach ( get_posts( array( 'post_type' => 'teacher', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $p ) {
				$t = array( 'id' => $p->post_name, 'name' => $p->post_title, 'slug' => $p->post_name,
					'photo' => get_the_post_thumbnail_url( $p, 'full' ) ?: null,
					'avatar' => wp_get_attachment_image_url( (int) yaww_meta( 'avatar', $p->ID ), 'full' ) ?: null,
					'pageUrl' => get_permalink( $p ), 'shortBio' => yaww_meta( 'short_bio', $p->ID ),
					'bio' => yaww_paragraphs( $p->post_content ) );
				foreach ( array( 'photoPosition' => 'photo_position', 'shortName' => 'short_name', 'role' => 'role', 'quote' => 'quote',
					'youtube' => 'youtube', 'spotifyPlaylist' => 'spotify_url', 'instagram' => 'instagram' ) as $js => $key ) {
					$v = yaww_meta( $key, $p->ID );
					if ( '' !== (string) $v ) { $t[ $js ] = $v; }
				}
				if ( yaww_meta( 'website', $p->ID ) ) {
					$t['website'] = yaww_meta( 'website_label', $p->ID ) ? array( 'url' => yaww_meta( 'website', $p->ID ), 'label' => yaww_meta( 'website_label', $p->ID ) ) : yaww_meta( 'website', $p->ID );
				}
				if ( $s = yaww_list( 'specialties', $p->ID ) ) { $t['specialties'] = $s; }
				if ( yaww_meta( 'needs_content', $p->ID ) ) { $t['needsContent'] = true; }
				$out[] = $t;
			}
			$out[] = array( 'id' => 'tbd', 'name' => null, 'placeholderLabel' => 'a YAWW instructor', 'unannounced' => true );
			break;
		case 'team':
			foreach ( get_posts( array( 'post_type' => 'facilitator', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $p ) {
				$t = array( 'id' => $p->post_name, 'name' => $p->post_title, 'slug' => $p->post_name,
					'photo' => get_the_post_thumbnail_url( $p, 'full' ) ?: null, 'pageUrl' => get_permalink( $p ),
					'shortBio' => yaww_meta( 'short_bio', $p->ID ), 'bio' => yaww_paragraphs( $p->post_content ) );
				if ( yaww_meta( 'photo_position', $p->ID ) ) { $t['photoPosition'] = yaww_meta( 'photo_position', $p->ID ); }
				if ( yaww_meta( 'role', $p->ID ) ) { $t['role'] = yaww_meta( 'role', $p->ID ); }
				if ( ! yaww_meta( 'listed', $p->ID ) ) { $t['listed'] = false; }
				$out[] = $t;
			}
			break;
		case 'retreats':
			$q = get_posts( array( 'post_type' => 'retreat', 'numberposts' => -1, 'meta_key' => 'start_date', 'orderby' => 'meta_value', 'order' => 'ASC' ) );
			foreach ( $q as $p ) {
				if ( 'waitlist' === yaww_meta( 'status', $p->ID ) || ! yaww_meta( 'start_date', $p->ID ) ) { continue; }
				$img = (int) yaww_meta( 'card_image', $p->ID );
				$tids = array_values( array_filter( array_map( $slug, (array) yaww_meta( 'teacher_ids', $p->ID ) ) ) );
				$r = array( 'id' => $p->post_name, 'slug' => $p->post_name, 'pageUrl' => get_permalink( $p ), 'officialName' => $p->post_title,
					'locationId' => $slug( yaww_meta( 'location_id', $p->ID ) ), 'teacherIds' => $tids ? $tids : array( 'tbd' ),
					'startDate' => yaww_date_ymd( yaww_meta( 'start_date', $p->ID ) ), 'endDate' => yaww_date_ymd( yaww_meta( 'end_date', $p->ID ) ),
					'status' => yaww_meta( 'status', $p->ID ) ?: 'available', 'collection' => yaww_meta( 'collection', $p->ID ) ?: null,
					'url' => wp_make_link_relative( get_permalink( $p ) ) );
				if ( 'spaces_left' === $r['status'] ) { $r['spacesLeft'] = (int) yaww_meta( 'spaces_left', $p->ID ); }
				if ( $img ) { $r['image'] = wp_get_attachment_image_url( $img, 'full' ); if ( yaww_img_2x( $img ) ) { $r['image2x'] = yaww_img_2x( $img ); } }
				$out[] = $r;
			}
			break;
		case 'journal':
			foreach ( get_posts( array( 'post_type' => 'post', 'numberposts' => -1 ) ) as $p ) {
				$hero = (int) get_post_thumbnail_id( $p );
				$ap   = (int) yaww_meta( 'author_photo', $p->ID );
				$ri   = (int) yaww_meta( 'retreat_image', $p->ID );
				$person = (int) yaww_meta( 'author_person', $p->ID );
				$j = array( 'slug' => $p->post_name, 'title' => $p->post_title, 'excerpt' => $p->post_excerpt,
					'categories' => wp_list_pluck( yaww_post_categories( $p->ID ), 'name' ), 'date' => yaww_meta( 'date_label', $p->ID ) ?: null,
					'dateISO' => yaww_meta( 'date_label', $p->ID ) ? get_the_date( 'Y-m-d', $p ) : null, 'url' => get_permalink( $p ),
					'hero' => $hero ? array( 'src' => wp_get_attachment_image_url( $hero, 'full' ), 'src2x' => yaww_img_2x( $hero ) ?: null ) : null,
					'author' => yaww_meta( 'author_name', $p->ID ) ? array( 'name' => yaww_meta( 'author_name', $p->ID ),
						'photo' => $ap ? wp_get_attachment_image_url( $ap, 'full' ) : null, 'photo2x' => $ap ? ( yaww_img_2x( $ap ) ?: null ) : null,
						'pageUrl' => $person ? get_permalink( $person ) : null, 'bio' => yaww_lines( yaww_meta( 'author_bio', $p->ID ) ) ) : null,
					'retreat' => yaww_meta( 'retreat_title', $p->ID ) ? array( 'title' => yaww_meta( 'retreat_title', $p->ID ),
						'subtitle' => yaww_meta( 'retreat_subtitle', $p->ID ), 'text' => yaww_meta( 'retreat_text', $p->ID ),
						'locationId' => $slug( yaww_meta( 'retreat_location', $p->ID ) ),
						'image' => $ri ? wp_get_attachment_image_url( $ri, 'full' ) : null, 'image2x' => $ri ? ( yaww_img_2x( $ri ) ?: null ) : null ) : null,
					'blocks' => array() );   // the body is post_content; render it with the_content()
				$out[] = $j;
			}
			break;
	}
	return $cache[ $what ] = $out;
}

/** <script> body defining window.YAWW_* for the given data sets (locations, regions, teachers, team, retreats, journal). */
function yaww_data_js( $sets ) {
	$names = array( 'locations' => 'YAWW_LOCATIONS', 'regions' => 'YAWW_REGIONS', 'teachers' => 'YAWW_TEACHERS', 'team' => 'YAWW_TEAM',
		'retreats' => 'YAWW_RETREATS', 'journal' => 'YAWW_JOURNAL' );
	$js = '';
	foreach ( $sets as $s ) {
		if ( isset( $names[ $s ] ) ) { $js .= 'window.' . $names[ $s ] . ' = ' . wp_json_encode( yaww_data( $s ) ) . ";\n"; }
	}
	if ( in_array( 'teachers', $sets, true ) ) { $js .= 'window.YAWW_TEACHERS_INTRO = ' . wp_json_encode( (string) get_option( 'yaww_teachers_intro', '' ) ) . ";\n"; }
	if ( in_array( 'team', $sets, true ) ) { $js .= 'window.YAWW_TEAM_INTRO = ' . wp_json_encode( (string) get_option( 'yaww_team_intro', '' ) ) . ";\n"; }
	return $js;
}

/** Post content as an array of paragraph strings (bios are stored as <p> paragraphs). */
function yaww_paragraphs( $content ) {
	preg_match_all( '#<p[^>]*>(.*?)</p>#s', (string) $content, $m );
	return array_values( array_filter( array_map( function ( $p ) { return trim( html_entity_decode( wp_strip_all_tags( $p ), ENT_QUOTES, 'UTF-8' ) ); }, $m[1] ), 'strlen' ) );
}

/** ACF date (Ymd or Y-m-d) as Y-m-d. */
function yaww_date_ymd( $v ) {
	$v = (string) $v;
	return preg_match( '/^\d{8}$/', $v ) ? substr( $v, 0, 4 ) . '-' . substr( $v, 4, 2 ) . '-' . substr( $v, 6, 2 ) : $v;
}

/** Journal categories in the static site's order (the `_yaww_cat_order` list set by the import), "Journal" first. */
function yaww_post_categories( $post_id ) {
	$cats  = get_the_category( $post_id );
	$order = (array) get_post_meta( $post_id, '_yaww_cat_order', true );
	usort( $cats, function ( $a, $b ) use ( $order ) {
		$ia = array_search( $a->name, $order, true ); $ib = array_search( $b->name, $order, true );
		return ( false === $ia ? 99 : $ia ) - ( false === $ib ? 99 : $ib );
	} );
	return $cats;
}

/**
 * Static-site URL -> WordPress URL, for js/nav.js (the theme's copy reads window.YAWW_URLMAP, see
 * bin/sync-assets.sh) and for any template that still holds a *.html link.
 */
function yaww_url_map() {
	$m = array(
		'index.html' => home_url( '/' ), 'retreats.html' => get_post_type_archive_link( 'retreat' ),
		'locations.html' => get_post_type_archive_link( 'location' ), 'teachers.html' => get_post_type_archive_link( 'teacher' ),
		'facilitators.html' => get_post_type_archive_link( 'facilitator' ), 'team.html' => get_post_type_archive_link( 'facilitator' ),
		'about.html' => home_url( '/about/' ), 'journal.html' => home_url( '/journal/' ), 'contact.html' => home_url( '/contact/' ),
		'teach-with-us.html' => home_url( '/teach-with-us/' ), 'policies.html' => home_url( '/policies/' ),
	);
	foreach ( array( 'retreat' => 'retreatpage-', 'location' => 'locationpage-', 'teacher' => 'teacher-', 'facilitator' => 'facilitator-' ) as $pt => $prefix ) {
		foreach ( get_posts( array( 'post_type' => $pt, 'numberposts' => -1 ) ) as $p ) {
			$m[ $prefix . $p->post_name . '.html' ] = get_permalink( $p );
		}
	}
	foreach ( get_posts( array( 'post_type' => 'post', 'numberposts' => -1 ) ) as $p ) {
		$m[ 'journal/' . $p->post_name . '.html' ] = get_permalink( $p );
		// The static site links the post by its own id; 7 posts kept the live site's different slug.
		$src = (string) get_post_meta( $p->ID, '_yaww_source_id', true );
		if ( $src && $src !== $p->post_name ) { $m[ 'journal/' . $src . '.html' ] = get_permalink( $p ); }
	}
	return $m;
}
