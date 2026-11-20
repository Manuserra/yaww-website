<?php
/**
 * Guest quotes. Source: inc/testimonials.json (lifted from index.html), filterable via 'yaww_testimonials'.
 * Args: title.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$title  = isset( $args['title'] ) ? $args['title'] : "In our guests' words";
$quotes = apply_filters( 'yaww_testimonials', json_decode( file_get_contents( YAWW_DIR . '/inc/testimonials.json' ), true ) );
if ( empty( $quotes ) ) { return; }
?>
<section class="quotes">
  <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
  <div class="rail rail--quotes" id="quotesRail">
    <div class="rail__track" id="quotesTrack" tabindex="0" aria-label="Guest testimonials">
      <?php foreach ( $quotes as $q ) : ?>
        <figure class="quote-card">
          <div class="quote-card__text">
            <blockquote class="quote-card__quote">&ldquo;<?php echo esc_html( $q['quote'] ); ?>&rdquo;</blockquote>
            <figcaption class="quote-card__by">
              <p class="quote-card__name"><?php echo esc_html( $q['name'] ); ?></p>
              <p class="quote-card__detail"><?php echo esc_html( $q['detail'] ); ?></p>
            </figcaption>
          </div>
          <span class="quote-card__divider" aria-hidden="true"></span>
          <?php if ( $q['photo'] ) : ?><img class="quote-card__photo" src="<?php echo esc_url( YAWW_URI . '/assets/' . $q['photo'] ); ?>" alt="<?php echo esc_attr( $q['alt'] ); ?>" loading="lazy"><?php endif; ?>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
