<?php
/**
 * Template helpers. Templates never call get_post_meta / get_field directly:
 * they call yaww_meta(), so the day ACF Pro arrives nothing in the templates changes.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Meta value, ACF first (when present and non-empty), native post meta otherwise. */
function yaww_meta( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
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
			$u = wp_get_attachment_image_url( (int) $gid, 'yaww-gallery' );
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
	$js  = 'var TIERS = ' . wp_json_encode( $tiers ) . '; var ROOM_VENUES = [];';
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
