<?php
/**
 * Teacher / facilitator card: js/people-cards.js renderPersonCard(). Used by the Teachers and
 * Facilitators grids and the About rails. Args: post_id, compact (bool: no blurb, for the rails).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
$id      = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$compact = ! empty( $args['compact'] );
$bio     = yaww_meta( 'short_bio', $id );

$inner  = yaww_e_person_photo( $id, 'person__image', true );
$inner .= '<h2 class="person__name">' . esc_html( get_the_title( $id ) ) . '</h2>';
if ( ! $compact ) {
	$inner .= $bio
		? '<p class="person__bio">' . esc_html( $bio ) . '</p>'
		: '<p class="person__bio person__bio--pending">Bio coming soon.</p>';
}
$inner .= '<span class="person__more">More about ' . esc_html( yaww_e_first_name( $id ) ) . '</span>';
?>
<article class="person"><a class="person__link" href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above ?></a></article>
