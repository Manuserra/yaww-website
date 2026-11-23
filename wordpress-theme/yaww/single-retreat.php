<?php
/**
 * Retreat page: retreatpage-<id>.html as retreats/_tools/build_page.py writes it, fed by
 * the retreat post. Same markup, same order, same class names; the regions the builder
 * fills (hero, about, hosts, includes, itinerary, rooms steps, tips, gallery, FAQ) are
 * written here, and the ones its inline script renders (stays, extras, rooms, quotes) by
 * assets/js-theme/retreat-page.js from window.YAWW_RETREAT (inc/views.php).
 *
 * A dateless retreat (status "waitlist", a destination between editions) is the builder's
 * --waitlist page: "Dates to be announced", no hosts, the waitlist form in place of rooms.
 *
 * Hotel names never appear: stays carry the generic label only (see BRIEF-WP).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/views.php';
get_header();
the_post();

$id       = get_the_ID();
$name     = get_the_title();
$loc      = (int) yaww_meta( 'location_id' );
/* The hero's place line is the retreat's own (data.json location_label, e.g. "Marrakech & Essaouira,
   Morocco"), which can differ from the location's card line; that line is the fallback. */
$place    = yaww_meta( 'location_label' ) ?: ( $loc ? yaww_location_line( $loc ) : '' );
$status   = yaww_meta( 'status' );
$waitlist = ( 'waitlist' === $status ) || ! yaww_meta( 'start_date' );
$dates    = $waitlist ? 'Dates to be announced' : yaww_d_date_range( yaww_date_ymd( yaww_meta( 'start_date' ) ), yaww_date_ymd( yaww_meta( 'end_date' ) ) );
$hosts    = $waitlist ? array() : yaww_d_hosts( $id );
$who      = implode( ' & ', array_map( function ( $h ) { return $h['name']; }, array_filter( $hosts, function ( $h ) { return 'teacher' === $h['kind']; } ) ) );

/* Hero label: tier and status, worded as on the cards (YAWW.cardLabelHTML). */
$tier  = array( 'signature-collection' => 'Signature', 'deep-dives' => 'Deep Dive' );
$coll  = yaww_meta( 'collection' );
$label = array();
if ( isset( $tier[ $coll ] ) ) { $label[] = '<span class="card__label-tier">' . esc_html( $tier[ $coll ] ) . '</span>'; }
$st = $waitlist ? 'Waitlist' : yaww_status_label( $id );
if ( $st ) {
	if ( $label ) { $label[] = '<span class="card__label-sep" aria-hidden="true"></span>'; }
	$label[] = '<span class="card__label-status">' . esc_html( $st ) . '</span>';
}

$hero_id  = (int) yaww_meta( 'hero_image' );
$hero_src = $hero_id ? wp_get_attachment_image_url( $hero_id, 'full' ) : '';
$hero_alt = $hero_id ? get_post_meta( $hero_id, '_wp_attachment_image_alt', true ) : '';
if ( ! $hero_alt ) { $hero_alt = $name . ' — ' . $place; }

$arrow_prev = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>';
$arrow_next = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>';
?>

  <!-- =========================================================
       HERO
       ========================================================= -->
  <section class="retreat-hero retreat-hero--overlay" data-retreat-id="<?php echo esc_attr( yaww_static_id( $id ) ); ?>">
    <div class="retreat-hero__image" role="img" aria-label="<?php echo esc_attr( $hero_alt ); ?>"<?php if ( $hero_src ) : ?> style="background-image: url('<?php echo esc_url( $hero_src ); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"<?php endif; ?>></div>

    <div class="retreat-hero__inner">
      <div class="retreat-hero__copy">
<?php if ( $label ) : ?>
        <p class="retreat-hero__label card__label<?php echo 'signature-collection' === $coll ? ' card__label--signature' : ''; ?>"><?php echo implode( '', $label ); // escaped above ?></p>
<?php endif; ?>
        <h1 class="retreat-hero__title"><?php echo esc_html( $name ); ?></h1>
        <p class="retreat-hero__location"><?php echo esc_html( $place ); ?></p>
        <p class="retreat-hero__meta">
<?php if ( $waitlist ) : ?>
          <span>Dates to be announced</span>
<?php else : ?>
          <span><?php echo esc_html( $dates ); ?></span>
          <span class="retreat-hero__meta-dot" aria-hidden="true">·</span>
          <span><?php echo $who ? 'with ' . esc_html( $who ) : 'Teacher to be announced'; ?></span>
<?php endif; ?>
        </p>
      </div>

      <div class="retreat-hero__ctas">
<?php if ( $waitlist ) : ?>
        <a class="btn btn--light" href="#waitlist">Join the waitlist</a>
<?php else : ?>
        <a class="btn btn--light" href="#rooms">Book now</a>
<?php endif; ?>
        <a class="btn btn--ghost" href="<?php echo yaww_u( 'contact.html#general-inquiry' ); ?>" data-lead-open data-lead-source="hero">I want more info</a>
      </div>
    </div>
  </section>

  <!-- =========================================================
       ABOUT
       ========================================================= -->
  <section class="retreat-about">
    <h2 class="retreat-about__title">About this retreat</h2>
    <div class="retreat-about__body">
<?php foreach ( yaww_d_paragraphs( get_post_field( 'post_content', $id ) ) as $p ) : ?>
      <p><?php echo wp_kses_post( $p ); ?></p>
<?php endforeach; ?>
    </div>
  </section>

<?php if ( $waitlist ) : ?>
  <!-- Hosts: announced with the next dates. -->
<?php else : ?>
  <!-- =========================================================
       HOSTS
       ========================================================= -->
  <section class="retreat-teacher" aria-labelledby="hosts-title">
    <h2 class="retreat-teacher__title" id="hosts-title"><?php echo count( $hosts ) > 1 ? 'Your hosts' : ( 'teacher' === $hosts[0]['key'] ? 'Your teacher' : 'Your host' ); ?></h2>
    <div class="hosts" data-hosts>
<?php if ( count( $hosts ) > 1 ) : ?>
      <div class="tabs tabs--hug hosts__tabs" role="tablist" aria-label="Your hosts">
<?php foreach ( $hosts as $i => $h ) : ?>
        <button class="tabs__item hosts__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" type="button" role="tab" data-host-tab="<?php echo esc_attr( $h['key'] ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>"><?php echo esc_html( $h['tab'] ); ?></button>
<?php endforeach; ?>
      </div>
<?php endif; ?>
      <div class="hosts__slot" data-host-slot>
<?php yaww_d_host_html( $hosts[0] ); ?>
      </div>
<?php if ( count( $hosts ) > 1 ) : foreach ( $hosts as $h ) : ?>
      <template data-host="<?php echo esc_attr( $h['key'] ); ?>">
<?php yaww_d_host_html( $h ); ?>
      </template>
<?php endforeach; endif; ?>
    </div>
  </section>
<?php endif; ?>

  <!-- =========================================================
       INCLUDES
       ========================================================= -->
<?php
$inc_img = (int) yaww_meta( 'includes_image' );
$inc_src = $inc_img ? wp_get_attachment_image_url( $inc_img, 'full' ) : '';
?>
  <section class="retreat-includes">
    <div class="retreat-includes__bg" aria-hidden="true"<?php if ( $inc_src ) : ?> style="background-image: url('<?php echo esc_url( $inc_src ); ?>'), linear-gradient(155deg, hsl(150 20% 68%), hsl(150 24% 38%));"<?php endif; ?>></div>
    <div class="retreat-includes__card">
      <h2 class="retreat-includes__title">Includes</h2>

      <div class="tabs tabs--fixed retreat-includes__tabs" role="tablist" aria-label="Includes">
        <button class="tabs__item retreat-includes__tab is-active" data-panel="includes" role="tab" aria-selected="true">What Includes</button>
        <button class="tabs__item retreat-includes__tab" data-panel="not-included" role="tab" aria-selected="false">What&rsquo;s Not Included</button>
      </div>

      <div class="retreat-includes__panels">
        <div class="retreat-includes__panel" data-panel="includes" role="tabpanel">
          <ul class="retreat-includes__list">
<?php foreach ( yaww_list( 'includes' ) as $x ) : ?>
            <li><?php echo esc_html( $x ); ?></li>
<?php endforeach; ?>
          </ul>
        </div>

        <div class="retreat-includes__panel is-hidden" data-panel="not-included" role="tabpanel" aria-hidden="true">
          <ul class="retreat-includes__list">
<?php foreach ( yaww_list( 'not_included' ) as $x ) : ?>
            <li><?php echo esc_html( $x ); ?></li>
<?php endforeach; ?>
          </ul>
<?php foreach ( yaww_list( 'includes_notes' ) as $x ) : ?>
          <p class="retreat-includes__footnote"><?php echo esc_html( $x ); ?></p>
<?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- =========================================================
       ITINERARY (day-by-day vertical timeline + photo slider)
       ========================================================= -->
  <section class="retreat-itinerary">
    <h2 class="retreat-itinerary__title">Itinerary</h2>

    <div class="retreat-itinerary__timeline">
      <span class="retreat-itinerary__progress" aria-hidden="true"></span>

      <ol class="retreat-itinerary__list">
<?php foreach ( yaww_rows( 'itinerary' ) as $day ) :
	$pics = yaww_d_gallery( isset( $day['images'] ) ? $day['images'] : array() );
	$lbl  = isset( $day['label'] ) ? $day['label'] : '';
	?>

        <li class="retreat-itinerary__day">
          <div class="retreat-itinerary__rail">
            <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
            <span class="retreat-itinerary__dot" aria-hidden="true"></span>
            <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
          </div>

          <div class="retreat-itinerary__content">
            <div class="retreat-itinerary__text">
              <div class="retreat-itinerary__head"><span class="retreat-itinerary__label"><?php echo esc_html( $lbl ); ?></span></div>

              <ul class="retreat-itinerary__bullets">
<?php if ( ! empty( $day['title'] ) ) : ?>
                <li class="retreat-itinerary__day-title"><?php echo esc_html( $day['title'] ); ?></li>
<?php endif; ?>
<?php foreach ( yaww_lines( isset( $day['bullets'] ) ? $day['bullets'] : '' ) as $b ) : ?>
                <li><?php echo esc_html( $b ); ?></li>
<?php endforeach; ?>
              </ul>
            </div>
<?php if ( $pics ) : ?>

            <div class="photo-slider retreat-itinerary__slider" role="group" aria-roledescription="carousel" aria-label="<?php echo esc_attr( $lbl . ' photos' ); ?>">
              <div class="photo-slider__track">
<?php foreach ( $pics as $p ) : ?>
                <div class="photo-slider__slide"><img src="<?php echo esc_url( $p['src'] ); ?>" alt="<?php echo esc_attr( $p['alt'] ? $p['alt'] : $lbl . ' — ' . $place ); ?>" loading="lazy"></div>
<?php endforeach; ?>
              </div>
              <button class="photo-slider__arrow photo-slider__arrow--prev" aria-label="Previous photo">
                <?php echo $arrow_prev; ?>

              </button>
              <button class="photo-slider__arrow photo-slider__arrow--next" aria-label="Next photo">
                <?php echo $arrow_next; ?>

              </button>
              <div class="photo-slider__dots">
<?php foreach ( $pics as $n => $p ) : ?>
                <button class="photo-slider__dot<?php echo 0 === $n ? ' is-active' : ''; ?>" aria-label="Photo <?php echo (int) $n + 1; ?>"></button>
<?php endforeach; ?>
              </div>
            </div>
<?php endif; ?>
          </div>
        </li>
<?php endforeach; ?>

      </ol>
    </div>
<?php $notes = yaww_list( 'itinerary_notes' ); if ( $notes ) : ?>
    <div class="retreat-itinerary__notes">
<?php foreach ( $notes as $n ) : ?>
      <p class="retreat-itinerary__note"><?php echo esc_html( $n ); ?></p>
<?php endforeach; ?>
    </div>
<?php endif; ?>
  </section>

  <!-- =========================================================
       ACCOMMODATIONS (stay tabs + gallery) — rendered from JS
       ========================================================= -->
  <section class="retreat-accom">
    <h2 class="retreat-accom__title">Accommodations</h2>
    <div class="tabs tabs--hug retreat-accom__tabs" role="tablist" aria-label="Accommodations"></div>
    <div class="retreat-accom__panels"></div>
  </section>

  <!-- =========================================================
       TESTIMONIALS ("What guests say") — rendered from JS
       ========================================================= -->
  <section class="quotes">
    <h2 class="quotes__title">What guests say</h2>
    <div class="quotes__grid" id="quotes-grid"></div>
  </section>

<?php if ( $waitlist ) :
	$loc_name = $loc ? ( yaww_meta( 'short_name', $loc ) ?: get_the_title( $loc ) ) : '';
	$note     = yaww_meta( 'waitlist_note' );
	if ( ! $note ) { $note = 'The itinerary, inclusions and stay on this page are from our last retreat here; the next one may differ, and its dates, teacher and prices come out together.'; }
	?>
  <!-- =========================================================
       WAITLIST (in place of Rooms pricing: no dates yet)
       ========================================================= -->
  <section class="waitlist" id="waitlist" aria-labelledby="waitlist-title">
    <div class="waitlist__inner">
      <h2 class="waitlist__title" id="waitlist-title">Join the waitlist</h2>
      <p class="waitlist__body">We are planning the next retreat in <?php echo esc_html( $loc_name ); ?>. Leave your name and we will write to you first, before the dates go public.</p>
      <form class="waitlist__form" data-waitlist-form data-waitlist-success="waitlist-done" novalidate>
        <label class="waitlist__label-sr" for="waitlist-first">First name</label>
        <input class="waitlist__input" id="waitlist-first" name="first_name" type="text" placeholder="First name" autocomplete="given-name" required>
        <label class="waitlist__label-sr" for="waitlist-email">Email address</label>
        <input class="waitlist__input" id="waitlist-email" name="email" type="email" placeholder="Email address" autocomplete="email" required>
        <input type="hidden" name="location" value="<?php echo esc_attr( $loc ? yaww_static_id( $loc ) : '' ); ?>">
        <button class="btn btn--primary waitlist__submit" type="submit">Join the waitlist</button>
        <p class="waitlist__error" data-waitlist-error role="alert" hidden></p>
      </form>
      <div class="waitlist__done" id="waitlist-done" hidden>
        <p class="waitlist__title">You are on the list</p>
        <p class="waitlist__body">We will write to you as soon as the dates are set.</p>
      </div>
      <p class="waitlist__note"><?php echo esc_html( $note ); ?></p>
      <p class="waitlist__links"><a href="<?php echo yaww_u( 'contact.html#general-inquiry' ); ?>" data-lead-open data-lead-source="waitlist">Questions? Talk to us</a></p>
    </div>
  </section>
<?php else :
	$rooms  = yaww_rows( 'rooms' );
	$priced = array_filter( $rooms, function ( $r ) { return isset( $r['price_usd'] ) && '' !== $r['price_usd'] && null !== $r['price_usd']; } );
	$units  = array_unique( array_map( function ( $r ) { return isset( $r['price_unit'] ) ? $r['price_unit'] : ''; }, $priced ) );
	$step1  = ! $priced ? 'Prices for these dates are coming soon.' : ( array( 'per person' ) === array_values( $units ) ? 'Every price is per person, in USD.' : 'Prices are in USD, per person or for a private room, as marked.' );
	$dep    = (int) yaww_meta( 'deposit_usd' );
	$due    = (int) yaww_meta( 'balance_due_days' );
	?>
  <!-- =========================================================
       ROOMS PRICING (one card per tier) — rendered from JS
       ========================================================= -->
  <section class="rooms" id="rooms">
    <h2 class="rooms__title">Rooms pricing</h2>
    <div class="rooms__inner">
      <div class="rooms__list" id="rooms-grid" aria-label="Room types"></div>

      <aside class="rooms__aside" aria-labelledby="how-to-book">
        <h3 class="rooms__aside-title" id="how-to-book">How to book</h3>
        <ol class="rooms__steps">
          <li>
            <strong>Choose your room</strong>
            <span><?php echo esc_html( $step1 ); ?></span>
          </li>
          <li>
            <strong>Secure your space</strong>
            <span>A <?php echo $dep ? '$' . esc_html( number_format( $dep ) ) . ' ' : ''; ?>deposit per person holds your place. It comes off your total &mdash; it is not an extra.</span>
          </li>
          <li>
            <strong>Pay the balance later</strong>
            <span>The rest is due <?php echo $due ? esc_html( $due ) . ' days ' : ''; ?>before the retreat.</span>
          </li>
        </ol>
        <p class="rooms__aside-note">All payments are non-refundable and non-transferable, so we recommend trip cancellation insurance from the day you pay your deposit.</p>
        <p class="rooms__aside-note">Sharing a room? Book together with your roommate. If you don&rsquo;t have one we&rsquo;ll pair you when we can &mdash; and if nobody is available, we&rsquo;ll move you to a private room.</p>
        <div class="rooms__actions">
          <a class="btn btn--primary" href="<?php echo yaww_u( 'contact.html#general-inquiry' ); ?>" data-lead-open data-lead-intent="reserve" data-lead-source="rooms" data-wetravel>Reserve your spot</a>
          <a class="btn btn--secondary" href="<?php echo yaww_u( 'contact.html#general-inquiry' ); ?>" data-lead-open data-lead-source="rooms">I want more info</a>
        </div>
      </aside>
    </div>
  </section>
<?php endif; ?>

<?php if ( yaww_rows( 'extras' ) ) : ?>
  <!-- =========================================================
       OPTIONAL EXTRAS (tabs + gallery) — rendered from JS
       ========================================================= -->
  <section class="extras">
    <h2 class="extras__title">Optional Extras</h2>
    <div class="tabs tabs--hug extras__tabs" role="tablist" aria-label="Optional extras"></div>
    <div class="extras__panels"></div>
  </section>
<?php else : ?>
  <!-- Optional Extras: none published for this retreat. -->
<?php endif; ?>

<?php
$tips = yaww_rows( 'travel_tips' );
if ( ! $tips && $loc ) { $tips = yaww_rows( 'travel_tips', $loc ); }
if ( $tips ) : ?>
  <!-- =========================================================
       TRAVEL TIPS
       ========================================================= -->
  <section class="tips">
    <h2 class="tips__title">Travel Tips</h2>
    <div class="tips__body">
<?php foreach ( $tips as $g ) : ?>
      <div class="tips__group">
        <h3 class="tips__heading"><?php echo esc_html( isset( $g['heading'] ) ? $g['heading'] : '' ); ?></h3>
        <ul class="tips__list">
<?php foreach ( yaww_lines( isset( $g['items'] ) ? $g['items'] : '' ) as $it ) : ?>
          <li><?php echo esc_html( $it ); ?></li>
<?php endforeach; ?>
        </ul>
      </div>

<?php endforeach; ?>
    </div>
  </section>
<?php else : ?>
  <!-- Travel Tips: none published for this destination. -->
<?php endif; ?>

<?php
$gal    = yaww_d_gallery( yaww_meta( 'gallery' ) );
$ratios = array( '28/17', '1/1', '28/17', '5/4', '28/17', '1/1', '28/17', '5/4', '1/1', '28/17' );
$g_title = yaww_meta( 'gallery_title' );
$g_intro = yaww_meta( 'gallery_intro' );
$g_where = $loc ? ( yaww_meta( 'short_name', $loc ) ?: get_the_title( $loc ) ) : $place;
?>
  <!-- =========================================================
       GALLERY
       ========================================================= -->
  <section class="gallery">
    <div class="gallery__head">
      <h2 class="gallery__title"><?php echo $g_title ? esc_html( $g_title ) : 'Where you&rsquo;ll be'; ?></h2>
      <p class="gallery__intro"><?php echo esc_html( $g_intro ? $g_intro : 'Photographs from past retreats in ' . $place . '.' ); ?></p>
      <div class="gallery__nav">
        <button class="gallery__arrow gallery__arrow--prev" aria-label="Previous photos">
          <?php echo $arrow_prev; ?>

        </button>
        <button class="gallery__arrow gallery__arrow--next" aria-label="More photos">
          <?php echo $arrow_next; ?>

        </button>
      </div>
    </div>

    <div class="gallery__track" role="group" aria-roledescription="carousel" aria-label="<?php echo esc_attr( 'Photos from ' . $g_where ); ?>">
      <div class="gallery__rail">
<?php foreach ( $gal as $i => $p ) : ?>
        <figure class="gallery__slide" style="--r: <?php echo esc_attr( $ratios[ $i % count( $ratios ) ] ); ?>"><img src="<?php echo esc_url( $p['src'] ); ?>" alt="<?php echo esc_attr( $p['alt'] ? $p['alt'] : $place ); ?>" loading="lazy"></figure>
<?php endforeach; ?>
      </div>
    </div>

    <div class="gallery__progress" aria-hidden="true">
      <span class="gallery__progress-bar"></span>
    </div>
  </section>

  <!-- Carbon offset: now the last question in Additional info. -->

  <!-- =========================================================
       ADDITIONAL INFO (FAQ)
       ========================================================= -->
  <section class="faq-section">
    <h2 class="faq-section__title">Additional info</h2>
    <div class="faq-list">
<?php foreach ( yaww_rows( 'faqs' ) as $i => $f ) : $n = $i + 1; ?>
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="faq-<?php echo (int) $n; ?>">
            <span class="faq__q"><?php echo esc_html( isset( $f['question'] ) ? $f['question'] : '' ); ?></span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="faq-<?php echo (int) $n; ?>" role="region">
<?php foreach ( yaww_d_paragraphs( isset( $f['answer'] ) ? $f['answer'] : '' ) as $p ) : ?>
            <p><?php echo wp_kses_post( $p ); ?></p>
<?php endforeach; ?>
          </div>
        </div>
      </div>
<?php endforeach; ?>
    </div>
  </section>

  <dialog class="lightbox" aria-label="Photo viewer">
    <div class="lightbox__frame">
      <img class="lightbox__img" alt="">
      <p class="lightbox__count" aria-live="polite"></p>
      <button class="lightbox__btn lightbox__btn--prev" aria-label="Previous photo">
        <?php echo $arrow_prev; ?>

      </button>
      <button class="lightbox__btn lightbox__btn--next" aria-label="Next photo">
        <?php echo $arrow_next; ?>

      </button>
      <button class="lightbox__btn lightbox__btn--close" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
  </dialog>

<?php get_footer();
