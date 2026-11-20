<?php
/**
 * Location page (locationpage-*.html, built from the proposals in locations/). Ported: hero, upcoming
 * dates, intro, travel tips. Gaps: waitlist form (needs forms endpoint), guide/"good to know" blocks,
 * quote, "more destinations".
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	$id   = get_the_ID();
	$hero = yaww_img_url( 'hero_image', 'yaww-hero', $id );
	$up   = new WP_Query( array(
		'post_type' => 'retreat', 'posts_per_page' => -1, 'meta_key' => 'start_date', 'orderby' => 'meta_value', 'order' => 'ASC',
		'meta_query' => array(
			array( 'key' => 'location_id', 'value' => $id ),
			array( 'key' => 'start_date', 'value' => gmdate( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
		),
	) );
	$tips = yaww_rows( 'travel_tips', $id );
	?>
<section class="retreat-hero retreat-hero--overlay loc-hero" data-location-id="<?php echo esc_attr( get_post_field( 'post_name', $id ) ); ?>" data-lead-kind="location">
  <div class="retreat-hero__image" role="img" aria-label="<?php the_title_attribute(); ?>"<?php echo $hero ? ' style="background-image:url(' . esc_url( $hero ) . ')"' : ''; ?>></div>
  <div class="retreat-hero__inner">
    <div class="retreat-hero__copy">
      <p class="loc-hero__eyebrow"><?php echo esc_html( yaww_meta( 'region', $id ) ); ?></p>
      <h1 class="retreat-hero__title"><?php the_title(); ?></h1>
      <p class="retreat-hero__location"><?php echo esc_html( yaww_location_line( $id ) ); ?></p>
    </div>
    <div class="retreat-hero__ctas"><?php yaww_part( 'lead-modal', array( 'source' => 'location-hero', 'class' => 'btn btn--light' ) ); ?></div>
  </div>
</section>

<section class="loc-dates<?php echo $up->have_posts() ? '' : ' loc-dates--waitlist'; ?>" id="dates">
  <div class="loc-dates__head"><h2 class="loc-h">Upcoming dates</h2></div>
  <?php if ( $up->have_posts() ) : ?>
    <div class="loc-dates__grid"><?php while ( $up->have_posts() ) : $up->the_post(); yaww_part( 'retreat-card', array( 'post_id' => get_the_ID() ) ); endwhile; wp_reset_postdata(); ?></div>
  <?php else : ?>
    <?php /* TODO: waitlist form, posts to the forms endpoint (README). */ ?>
    <p class="loc-p">No dates are open here right now.</p>
    <?php yaww_part( 'lead-modal', array( 'source' => 'waitlist', 'label' => 'Join the waitlist', 'class' => 'btn btn--primary' ) ); ?>
  <?php endif; ?>
</section>

<section class="loc-intro"><div class="loc-intro__row"><div class="loc-intro__copy"><?php the_content(); ?></div></div></section>

<?php if ( $tips ) : ?>
<section class="loc-know">
  <div class="loc-know__head"><h2 class="loc-h">Good to know</h2></div>
  <div class="loc-know__grid">
    <?php foreach ( $tips as $g ) : ?>
      <div><h3 class="tips__heading"><?php echo esc_html( $g['heading'] ); ?></h3>
      <ul class="tips__list"><?php foreach ( yaww_lines( isset( $g['items'] ) ? $g['items'] : '' ) as $li ) : ?><li><?php echo esc_html( $li ); ?></li><?php endforeach; ?></ul></div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
<?php yaww_part( 'for-teachers' ); ?>
<?php endwhile;
get_footer();
