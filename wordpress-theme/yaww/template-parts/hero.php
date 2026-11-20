<?php
/** Generic page hero. Args: eyebrow, title, intro, image (url). */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$eyebrow = isset( $args['eyebrow'] ) ? $args['eyebrow'] : '';
$title   = isset( $args['title'] ) ? $args['title'] : get_the_title();
$intro   = isset( $args['intro'] ) ? $args['intro'] : '';
$image   = isset( $args['image'] ) ? $args['image'] : '';
?>
<section class="page-hero"<?php echo $image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : ''; ?>>
  <div class="page-hero__inner">
    <?php if ( $eyebrow ) : ?><p class="kicker"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
    <h1 class="page-hero__title"><?php echo esc_html( $title ); ?></h1>
    <?php if ( $intro ) : ?><p class="page-hero__intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
  </div>
</section>
