<?php
/**
 * Single retreat. Ported section by section from retreatpage-elcuyo.html (the template the static
 * generator, retreats/_tools/build_page.py, fills in). Every value comes through yaww_meta() / helpers.
 *
 * Hotel confidentiality: no hotel name is read or printed anywhere here, only the generic `hotel_label`.
 * Gaps (see README): accommodation tabs + extras tabs JS, itinerary progress rail, gallery slider and
 * lightbox behaviours (inline scripts in the static page), FAQ placeholder fallbacks, carbon-offset copy.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

while ( have_posts() ) :
	the_post();
	$id        = get_the_ID();
	$loc_id    = yaww_retreat_location( $id );
	$loc_line  = yaww_location_line( $loc_id );
	$teachers  = array_filter( (array) yaww_meta( 'teacher_ids', $id ) );
	$tier      = yaww_meta( 'collection', $id );
	$sig       = ( 'signature-collection' === $tier );
	$badge     = yaww_status_label( $id );
	$hero_url  = yaww_img_url( 'hero_image', 'yaww-hero', $id );
	$includes  = yaww_list( 'includes', $id );
	$excluded  = yaww_list( 'not_included', $id );
	$itinerary = yaww_rows( 'itinerary', $id );
	$rooms     = yaww_rows( 'rooms', $id );
	$extras    = yaww_rows( 'extras', $id );
	$tips      = yaww_rows( 'travel_tips', $id );
	$faqs      = yaww_rows( 'faqs', $id );
	$gallery   = array_filter( (array) yaww_meta( 'gallery', $id ) );
	$hotel     = yaww_hotel_label( $id );
	$deposit   = (float) yaww_meta( 'deposit_usd', $id );
	$balance   = (int) yaww_meta( 'balance_due_days', $id );
	?>

<?php /* ===== HERO ===== */ ?>
<section class="retreat-hero retreat-hero--overlay">
  <div class="retreat-hero__image" role="img" aria-label="<?php echo esc_attr( get_the_title() . ( $loc_line ? ' — ' . $loc_line : '' ) ); ?>"<?php echo $hero_url ? ' style="background-image:url(' . esc_url( $hero_url ) . ')"' : ''; ?>></div>
  <div class="retreat-hero__inner">
    <div class="retreat-hero__copy">
      <p class="retreat-hero__label card__label<?php echo $sig ? ' card__label--signature' : ''; ?>"><span class="card__label-tier"><?php echo esc_html( yaww_tier_label( $id ) ); ?></span><?php if ( $badge ) : ?><span class="card__label-sep" aria-hidden="true"></span><span class="card__label-status"><?php echo esc_html( $badge ); ?></span><?php endif; ?></p>
      <h1 class="retreat-hero__title"><?php the_title(); ?></h1>
      <?php if ( $loc_line ) : ?><p class="retreat-hero__location"><?php echo esc_html( $loc_line ); ?></p><?php endif; ?>
      <p class="retreat-hero__meta">
        <span><?php echo esc_html( yaww_retreat_dates( $id ) ); ?></span>
        <?php if ( yaww_teacher_line( $id ) ) : ?>
          <span class="retreat-hero__meta-dot" aria-hidden="true">·</span>
          <span>with <?php echo esc_html( yaww_teacher_line( $id ) ); ?></span>
        <?php endif; ?>
      </p>
    </div>
    <div class="retreat-hero__ctas">
      <a class="btn btn--light" href="#rooms">Book now</a>
      <?php yaww_part( 'lead-modal', array( 'source' => 'hero' ) ); ?>
    </div>
  </div>
</section>

<?php /* ===== ABOUT (post content) ===== */ ?>
<section class="retreat-about">
  <h2 class="retreat-about__title">About this retreat</h2>
  <div class="retreat-about__body"><?php the_content(); ?></div>
</section>

<?php /* ===== TEACHERS ===== */ ?>
<?php foreach ( $teachers as $tid ) : ?>
<section class="retreat-teacher">
  <h2 class="retreat-teacher__title">Yoga with <?php echo esc_html( get_the_title( $tid ) ); ?></h2>
  <div class="retreat-teacher__row">
    <div class="retreat-teacher__photo" role="img" aria-label="<?php echo esc_attr( get_the_title( $tid ) ); ?>"<?php $tp = get_the_post_thumbnail_url( $tid, 'yaww-person' ); echo $tp ? ' style="background-image:url(' . esc_url( $tp ) . ')"' : ''; ?>></div>
    <div class="retreat-teacher__text">
      <?php echo wp_kses_post( wpautop( yaww_meta( 'short_bio', $tid ) ) ); ?>
      <p><a href="<?php echo esc_url( get_permalink( $tid ) ); ?>">More about <?php echo esc_html( yaww_meta( 'short_name', $tid ) ?: get_the_title( $tid ) ); ?></a></p>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php /* ===== INCLUDES ===== */ ?>
<?php if ( $includes || $excluded ) : ?>
<section class="retreat-includes">
  <div class="retreat-includes__bg" aria-hidden="true"></div>
  <div class="retreat-includes__card">
    <h2 class="retreat-includes__title">Includes</h2>
    <div class="tabs tabs--fixed retreat-includes__tabs" role="tablist" aria-label="Includes">
      <button class="tabs__item retreat-includes__tab is-active" data-panel="includes" role="tab" aria-selected="true">What Includes</button>
      <?php if ( $excluded ) : ?><button class="tabs__item retreat-includes__tab" data-panel="not-included" role="tab" aria-selected="false">What&rsquo;s Not Included</button><?php endif; ?>
    </div>
    <div class="retreat-includes__panels">
      <div class="retreat-includes__panel" data-panel="includes" role="tabpanel">
        <ul class="retreat-includes__list"><?php foreach ( $includes as $li ) : ?><li><?php echo esc_html( $li ); ?></li><?php endforeach; ?></ul>
      </div>
      <?php if ( $excluded ) : ?>
      <div class="retreat-includes__panel is-hidden" data-panel="not-included" role="tabpanel" aria-hidden="true" hidden>
        <ul class="retreat-includes__list"><?php foreach ( $excluded as $li ) : ?><li><?php echo esc_html( $li ); ?></li><?php endforeach; ?></ul>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php /* ===== ITINERARY ===== */ ?>
<?php if ( $itinerary ) : ?>
<section class="retreat-itinerary">
  <h2 class="retreat-itinerary__title">Itinerary</h2>
  <div class="retreat-itinerary__timeline">
    <span class="retreat-itinerary__progress" aria-hidden="true"></span>
    <ol class="retreat-itinerary__list">
      <?php foreach ( $itinerary as $day ) :
        $imgs = array_filter( (array) ( isset( $day['images'] ) ? $day['images'] : array() ) ); ?>
      <li class="retreat-itinerary__day">
        <div class="retreat-itinerary__rail">
          <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
          <span class="retreat-itinerary__dot" aria-hidden="true"></span>
          <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
        </div>
        <div class="retreat-itinerary__content">
          <div class="retreat-itinerary__text">
            <div class="retreat-itinerary__head"><span class="retreat-itinerary__label"><?php echo esc_html( isset( $day['label'] ) ? $day['label'] : '' ); ?></span><?php if ( ! empty( $day['title'] ) ) : ?> <span class="retreat-itinerary__day-title"><?php echo esc_html( $day['title'] ); ?></span><?php endif; ?></div>
            <ul class="retreat-itinerary__bullets">
              <?php foreach ( yaww_lines( isset( $day['bullets'] ) ? $day['bullets'] : '' ) as $b ) : ?><li><?php echo esc_html( $b ); ?></li><?php endforeach; ?>
            </ul>
          </div>
          <?php if ( $imgs ) : ?>
          <div class="photo-slider retreat-itinerary__slider" role="group" aria-roledescription="carousel" aria-label="<?php echo esc_attr( ( isset( $day['label'] ) ? $day['label'] : 'Day' ) . ' photos' ); ?>">
            <div class="photo-slider__track">
              <?php foreach ( $imgs as $aid ) : ?><div class="photo-slider__slide"><?php echo yaww_img( (int) $aid, 'yaww-gallery', array( 'loading' => 'lazy' ) ); // phpcs:ignore ?></div><?php endforeach; ?>
            </div>
            <button class="photo-slider__arrow photo-slider__arrow--prev" aria-label="Previous photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
            <button class="photo-slider__arrow photo-slider__arrow--next" aria-label="Next photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
            <div class="photo-slider__dots"><?php foreach ( array_values( $imgs ) as $i => $x ) : ?><button class="photo-slider__dot<?php echo 0 === $i ? ' is-active' : ''; ?>" aria-label="Photo <?php echo (int) ( $i + 1 ); ?>"></button><?php endforeach; ?></div>
          </div>
          <?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<?php /* ===== ACCOMMODATIONS: generic label only. Tabs appear with 2+ stays (BRIEF s.12 item 7). ===== */ ?>
<?php if ( $hotel ) : ?>
<section class="retreat-accom">
  <h2 class="retreat-accom__title">Accommodations</h2>
  <div class="retreat-accom__panels"><p class="retreat-accom__label"><?php echo esc_html( $hotel ); ?></p></div>
</section>
<?php endif; ?>

<?php yaww_part( 'testimonials', array( 'title' => 'What guests say' ) ); ?>

<?php /* ===== ROOMS PRICING. #rooms-grid is filled by js/retreat-rooms.js from the TIERS global (functions.php). ===== */ ?>
<?php if ( $rooms ) : ?>
<section class="rooms" id="rooms">
  <h2 class="rooms__title">Rooms pricing</h2>
  <div class="rooms__inner">
    <div class="rooms__list" id="rooms-grid" aria-label="Room types"></div>
    <aside class="rooms__aside" aria-labelledby="how-to-book">
      <h3 class="rooms__aside-title" id="how-to-book">How to book</h3>
      <ol class="rooms__steps">
        <li><strong>Choose your room</strong><span>Every price is per person, in USD.</span></li>
        <?php if ( $deposit ) : ?><li><strong>Secure your space</strong><span>A $<?php echo esc_html( number_format_i18n( $deposit ) ); ?> deposit per person holds your place. It comes off your total &mdash; it is not an extra.</span></li><?php endif; ?>
        <?php if ( $balance ) : ?><li><strong>Pay the balance later</strong><span>The rest is due <?php echo (int) $balance; ?> days before the retreat.</span></li><?php endif; ?>
      </ol>
      <?php if ( yaww_meta( 'roommate_note', $id ) ) : ?><p class="rooms__aside-note"><?php echo esc_html( yaww_meta( 'roommate_note', $id ) ); ?></p><?php endif; ?>
      <div class="rooms__actions">
        <?php if ( yaww_meta( 'wetravel_url', $id ) ) : ?><a class="btn btn--primary" href="<?php echo esc_url( yaww_meta( 'wetravel_url', $id ) ); ?>">Reserve your spot</a><?php endif; ?>
        <?php yaww_part( 'lead-modal', array( 'source' => 'rooms', 'class' => 'btn btn--secondary' ) ); ?>
      </div>
    </aside>
  </div>
</section>
<?php endif; ?>

<?php /* ===== OPTIONAL EXTRAS (static markup is JS-built; here plain lists, tabs JS is a gap) ===== */ ?>
<?php if ( $extras ) : ?>
<section class="extras">
  <h2 class="extras__title">Optional Extras</h2>
  <div class="extras__panels">
    <?php foreach ( $extras as $x ) : ?>
      <div class="extras__panel">
        <h3 class="extras__name"><?php echo esc_html( $x['name'] ); ?></h3>
        <?php if ( isset( $x['price_usd'] ) && '' !== $x['price_usd'] ) : ?><p class="extras__price">$<?php echo esc_html( number_format_i18n( (float) $x['price_usd'] ) ); ?> <?php echo esc_html( isset( $x['unit'] ) ? $x['unit'] : '' ); ?></p><?php endif; ?>
        <?php if ( ! empty( $x['desc'] ) ) : ?><p><?php echo esc_html( $x['desc'] ); ?></p><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php /* ===== TRAVEL TIPS (retreat's own, else the location's) ===== */ ?>
<?php
if ( ! $tips && $loc_id ) { $tips = yaww_rows( 'travel_tips', $loc_id ); }
if ( $tips ) : ?>
<section class="tips">
  <h2 class="tips__title">Travel Tips</h2>
  <div class="tips__body">
    <?php foreach ( $tips as $g ) : ?>
    <div class="tips__group">
      <h3 class="tips__heading"><?php echo esc_html( $g['heading'] ); ?></h3>
      <ul class="tips__list"><?php foreach ( yaww_lines( isset( $g['items'] ) ? $g['items'] : '' ) as $li ) : ?><li><?php echo esc_html( $li ); ?></li><?php endforeach; ?></ul>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php /* ===== GALLERY ===== */ ?>
<?php if ( $gallery ) : ?>
<section class="gallery">
  <div class="gallery__head">
    <h2 class="gallery__title"><?php echo esc_html( yaww_meta( 'gallery_title', $id ) ?: 'Where you’ll be' ); ?></h2>
    <?php if ( yaww_meta( 'gallery_intro', $id ) ) : ?><p class="gallery__intro"><?php echo esc_html( yaww_meta( 'gallery_intro', $id ) ); ?></p><?php endif; ?>
  </div>
  <div class="gallery__track" role="group" aria-roledescription="carousel" aria-label="Photos">
    <div class="gallery__rail">
      <?php foreach ( $gallery as $aid ) :
        $m = wp_get_attachment_metadata( (int) $aid );
        $r = ( $m && ! empty( $m['width'] ) && ! empty( $m['height'] ) ) ? $m['width'] . '/' . $m['height'] : '28/17'; ?>
        <figure class="gallery__slide" style="--r: <?php echo esc_attr( $r ); ?>"><?php echo yaww_img( (int) $aid, 'yaww-gallery', array( 'loading' => 'lazy' ) ); // phpcs:ignore ?></figure>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="gallery__progress" aria-hidden="true"><span class="gallery__progress-bar"></span></div>
</section>
<?php endif; ?>

<?php /* ===== CARBON OFFSET: copy not final (no source copy on the old site). ===== */ ?>

<?php /* ===== ADDITIONAL INFO / FAQ ===== */ ?>
<?php if ( $faqs ) : ?>
<section class="faq-section">
  <h2 class="faq-section__title">Additional info</h2>
  <div class="faq-list">
    <?php foreach ( $faqs as $n => $f ) : ?>
    <div class="faq">
      <h3 class="faq__heading">
        <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="faq-<?php echo (int) $n; ?>">
          <span class="faq__q"><?php echo esc_html( $f['question'] ); ?></span>
          <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </button>
      </h3>
      <div class="faq__panel"><div class="faq__answer" id="faq-<?php echo (int) $n; ?>" role="region"><?php echo wp_kses_post( wpautop( $f['answer'] ) ); ?></div></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<dialog class="lightbox" aria-label="Photo viewer">
  <div class="lightbox__frame">
    <img class="lightbox__img" alt="">
    <p class="lightbox__count" aria-live="polite"></p>
    <button class="lightbox__btn lightbox__btn--prev" aria-label="Previous photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
    <button class="lightbox__btn lightbox__btn--next" aria-label="Next photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
    <button class="lightbox__btn lightbox__btn--close" aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
  </div>
</dialog>

<?php
endwhile;
get_footer();
