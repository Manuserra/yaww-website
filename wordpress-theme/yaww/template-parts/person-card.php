<?php
/** Teacher / facilitator card. Args: post_id. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
?>
<a class="person-card" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
  <div class="person-card__photo"><?php echo get_the_post_thumbnail( $id, 'yaww-person', array( 'loading' => 'lazy' ) ); // phpcs:ignore ?></div>
  <h3 class="person-card__name"><?php echo esc_html( get_the_title( $id ) ); ?></h3>
  <p class="person-card__bio"><?php echo esc_html( yaww_meta( 'short_bio', $id ) ); ?></p>
</a>
