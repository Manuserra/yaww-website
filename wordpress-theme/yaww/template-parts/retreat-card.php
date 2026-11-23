<?php
/**
 * One retreat card, server-rendered: the exact markup of js/retreat-cards.js narrowCardHTML()
 * (photo with the tier · status label, title, location line, date, teacher line, linked to the
 * retreat's page). Pages that render their cards in JS (home rail, listing) use the script itself.
 * Args: post_id (default: current post), current_url (a card never links to the page it is on).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/views.php';

$id  = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$r   = yaww_retreat_record( $id );
$lid = (int) yaww_meta( 'location_id', $id );
$loc = $lid ? yaww_location_record( $lid ) : null;

$alt   = $r['officialName'] . ( $loc ? ' — ' . $loc['displayName'] : '' );
$media = ! empty( $r['image'] ) ? array( $r['image'], isset( $r['image2x'] ) ? $r['image2x'] : null )
	: ( $loc && $loc['image'] ? array( $loc['image'], null ) : null );
$hue   = ( $loc && isset( $loc['placeholderHue'] ) ) ? (int) $loc['placeholderHue'] : 200;

$tiers  = array( 'deep-dives' => 'Deep Dive', 'signature-collection' => 'Signature' );
$sig    = 'signature-collection' === $r['collection'];
$badge  = yaww_status_label( $id );
$label  = '<span class="card__label' . ( $sig ? ' card__label--signature' : '' ) . '">'
	. '<span class="card__label-tier">' . esc_html( isset( $tiers[ $r['collection'] ] ) ? $tiers[ $r['collection'] ] : 'Deep Dive' ) . '</span>'
	. ( $badge ? '<span class="card__label-sep" aria-hidden="true"></span><span class="card__label-status">' . esc_html( $badge ) . '</span>' : '' )
	. '</span>';

/* Teacher line: names, or the catalog's placeholder for an unannounced teacher. */
$names = array();
foreach ( yaww_d_ids( yaww_meta( 'teacher_ids', $id ) ) as $tid ) { $names[] = get_the_title( $tid ); }
$teacher = $names ? implode( ' & ', $names ) : 'a YAWW instructor';

$href = $r['pageUrl'];
if ( ! empty( $args['current_url'] ) && $href === $args['current_url'] ) {
	$href = ( $loc && $loc['pageUrl'] !== $href ) ? $loc['pageUrl'] : '';
}

$inner = '<article class="card card--narrow">'
	. ( $media
		? '<div class="card__media"><img src="' . esc_url( $media[0] ) . '"' . ( $media[1] ? ' srcset="' . esc_url( $media[0] ) . ' 1x, ' . esc_url( $media[1] ) . ' 2x"' : '' ) . ' alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">' . $label . '</div>'
		: '<div class="card__media" style="background: linear-gradient(155deg, hsl(' . $hue . ' 32% 80%), hsl(' . $hue . ' 26% 60%));">' . $label . '</div>' )
	. '<div class="card__body">'
	. '<div class="card__title">' . esc_html( $r['officialName'] ) . '</div>'
	. '<div class="card__loc">' . esc_html( $loc ? ( isset( $loc['cardName'] ) ? $loc['cardName'] : $loc['displayName'] ) : '' ) . '</div>'
	. '<div class="card__date">' . esc_html( yaww_d_date_range( yaww_date_ymd( $r['startDate'] ), yaww_date_ymd( $r['endDate'] ) ) ) . '</div>'
	. '<div class="card__teacher">with ' . esc_html( $teacher ) . '</div>'
	. '</div>'
	. '</article>';

echo $href ? '<a class="result-link" href="' . esc_url( $href ) . '">' . $inner . '</a>' : $inner; // phpcs:ignore WordPress.Security.EscapeOutput
