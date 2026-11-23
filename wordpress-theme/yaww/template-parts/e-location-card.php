<?php
/**
 * One destination card (locations.html grid and "More places we love"): build_location.py place_card().
 * Args: post_id, tag ('h2' on the grid, 'h3' in "More places we love").
 * Fields: card_image (alt = the attachment's alt text), blurb (falls back to intro).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
$id    = (int) $args['post_id'];
$tag   = ( isset( $args['tag'] ) && 'h3' === $args['tag'] ) ? 'h3' : 'h2';
$title = get_the_title( $id );
$blurb = yaww_meta( 'blurb', $id );
$blurb = $blurb ? $blurb : yaww_meta( 'intro', $id );
$img_id = (int) yaww_meta( 'card_image', $id );
$img   = yaww_e_img( $img_id, 'yaww-loc-card', array( 'width' => '672', 'height' => '888', 'loading' => 'lazy' ) );
if ( $img && '' === (string) get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ) {
	$img = str_replace( 'alt=""', 'alt="' . esc_attr( $title ) . '"', $img );
}
?>
      <article class="location">
        <a class="location__link" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
          <div class="location__image">
            <?php echo $img ? $img : '<div class="loc-ph" role="img" aria-label="' . esc_attr( $title ) . '" style="' . esc_attr( yaww_e_placeholder_style( $id ) ) . 'width:100%;height:100%"></div>'; // phpcs:ignore ?>
          </div>
          <<?php echo $tag; ?> class="location__title"><?php echo yaww_e_t( $title ); // phpcs:ignore ?></<?php echo $tag; ?>>
          <p class="location__caption"><?php echo $blurb ? yaww_e_t( $blurb ) : esc_html( yaww_e_missing( 'blurb' ) ); // phpcs:ignore ?></p>
        </a>
      </article>
