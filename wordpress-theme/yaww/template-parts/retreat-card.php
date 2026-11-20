<?php
/** One retreat card. Markup = js/retreat-cards.js cardHTML(). Args: post_id (default: current post). */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$id    = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$loc   = yaww_retreat_location( $id );
$badge = yaww_status_label( $id );
$sig   = ( 'signature-collection' === yaww_meta( 'collection', $id ) );
$img   = yaww_img( 'card_image', 'yaww-card', array( 'alt' => get_the_title( $id ), 'loading' => 'lazy' ), $id );
if ( ! $img && $loc ) { $img = yaww_img( 'card_image', 'yaww-card', array( 'alt' => '', 'loading' => 'lazy' ), $loc ); }
$hue = $loc ? (int) yaww_meta( 'placeholder_hue', $loc ) : 200;
?>
<a class="result-link" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
  <article class="card card--narrow">
    <?php if ( $img ) : ?>
      <div class="card__media"><?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
    <?php else : ?>
      <div class="card__media" style="background:linear-gradient(160deg,hsl(<?php echo (int) $hue; ?> 35% 62%),hsl(<?php echo (int) $hue; ?> 30% 38%))"></div>
    <?php endif; ?>
    <div class="card__body">
      <p class="card__label<?php echo $sig ? ' card__label--signature' : ''; ?>"><span class="card__label-tier"><?php echo esc_html( yaww_tier_label( $id ) ); ?></span><?php if ( $badge ) : ?><span class="card__label-sep" aria-hidden="true"></span><span class="card__label-status"><?php echo esc_html( $badge ); ?></span><?php endif; ?></p>
      <div class="card__title"><?php echo esc_html( get_the_title( $id ) ); ?></div>
      <div class="card__loc"><?php echo esc_html( yaww_location_line( $loc ) ); ?></div>
      <div class="card__date"><?php echo esc_html( yaww_retreat_dates( $id ) ); ?></div>
      <?php if ( yaww_teacher_line( $id ) ) : ?><div class="card__teacher">with <?php echo esc_html( yaww_teacher_line( $id ) ); ?></div><?php endif; ?>
    </div>
  </article>
</a>
