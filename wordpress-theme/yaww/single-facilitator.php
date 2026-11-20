<?php
/**
 * Single facilitator (facilitator-*.html / team-*.html). Name, photo, bio paragraphs (post content), facts/quote.
 * Gap: "retreats with her" rail and video/playlist media blocks (people.css .profile-media).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); $id = get_the_ID(); ?>
<section class="person-page" data-person="<?php echo esc_attr( get_post_field( 'post_name', $id ) ); ?>" data-person-kind="facilitator">
  <div class="profile">
    <div class="profile__photo"><?php echo get_the_post_thumbnail( $id, 'yaww-person' ); // phpcs:ignore ?></div>
    <div class="profile__text">
      <p class="kicker"><?php echo esc_html( yaww_meta( 'role', $id ) ?: 'Facilitator' ); ?></p>
      <h1 class="profile__name"><?php the_title(); ?></h1>
      <?php if ( yaww_meta( 'quote', $id ) ) : ?><blockquote class="profile__quote"><?php echo esc_html( yaww_meta( 'quote', $id ) ); ?></blockquote><?php endif; ?>
      <div class="profile__bio"><?php the_content(); ?></div>
      <?php $spec = yaww_list( 'specialties', $id ); if ( $spec ) : ?><ul class="profile__tags"><?php foreach ( $spec as $s ) : ?><li><?php echo esc_html( $s ); ?></li><?php endforeach; ?></ul><?php endif; ?>
    </div>
  </div>
</section>
<?php if ( function_exists( 'wp_oembed_get' ) && yaww_meta( 'video_url', $id ) ) : ?>
<section class="profile-media"><div class="profile-media__video"><?php echo wp_oembed_get( esc_url( yaww_meta( 'video_url', $id ) ) ); // phpcs:ignore ?></div></section>
<?php endif; ?>
<?php endwhile;
get_footer();
