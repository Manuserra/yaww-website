<?php
/**
 * Location page (locationpage-*.html, retreats/_tools/build_location.py): hero, upcoming dates or the
 * waitlist, "Why we go", "Our guide", "Good to know", a guest quote, "More places we love", closing band.
 *
 * Fields (location): eyebrow, tagline, facts (rows: label, value), hero_image, intro_title, intro_image,
 * post content = the "Why we go" paragraphs, guide (rows: kicker, title, text, image; placeholder = label when there is no photo yet),
 * know (rows: label, value), quote_text, quote_name, quote_source, program_retreat (optional),
 * region, display_name / card_name, placeholder_hue. Anything missing shows as [missing data: …].
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'location', array( 'lenis-anchors', 'cta-parallax', 'location-waitlist' ) );
get_header();
while ( have_posts() ) : the_post();
	$id      = get_the_ID();
	$slug    = get_post_field( 'post_name', $id );
	$place   = get_the_title( $id );
	$contact = home_url( '/contact/#general-inquiry' );
	$hero_id = (int) yaww_meta( 'hero_image', $id );
	$hero    = yaww_e_img_src( $hero_id, 'yaww-hero' );
	if ( ! $hero ) { $hero = yaww_img_url( 'hero_image', 'yaww-hero', $id ); }
	$hero_alt = $hero_id ? (string) get_post_meta( $hero_id, '_wp_attachment_image_alt', true ) : '';
	$list    = yaww_e_retreats_by_location( $id );
	$dates   = (bool) $list;
	$facts   = yaww_rows( 'facts', $id );
	$m       = function ( $v, $what ) { return '' !== trim( (string) $v ) ? yaww_e_t( $v ) : esc_html( yaww_e_missing( $what ) ); };
	?>

  <section class="retreat-hero retreat-hero--overlay loc-hero" data-location-id="<?php echo esc_attr( $slug ); ?>" data-lead-kind="location">
    <div class="retreat-hero__image" role="img" aria-label="<?php echo esc_attr( $hero_alt ? $hero_alt : $place ); ?>"<?php if ( $hero ) : ?> style="background-image: url('<?php echo esc_url( $hero ); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"<?php endif; ?>></div>

    <div class="retreat-hero__inner">
      <div class="retreat-hero__copy">
        <p class="loc-hero__eyebrow"><?php echo $m( yaww_meta( 'eyebrow', $id ), 'eyebrow' ); // phpcs:ignore ?></p>
        <h1 class="retreat-hero__title"><?php echo yaww_e_t( $place ); // phpcs:ignore ?></h1>
        <p class="retreat-hero__location"><?php echo str_replace( 'One of our most sought-after destinations', 'One of our most <span class="tx-nowrap">sought-after</span> destinations', $m( yaww_meta( 'tagline', $id ), 'tagline' ) ); /* build_location.py keeps the compound together */ // phpcs:ignore ?></p>
        <?php if ( $facts ) : ?><dl class="loc-hero__facts"><?php foreach ( $facts as $f ) { echo '<div><dt>' . yaww_e_t( isset( $f['label'] ) ? $f['label'] : '' ) . '</dt><dd>' . yaww_e_t( isset( $f['value'] ) ? $f['value'] : '' ) . '</dd></div>'; } // phpcs:ignore ?></dl><?php endif; ?>
      </div>

      <div class="retreat-hero__ctas">
        <?php if ( $dates ) : ?><a class="btn btn--light" href="#retreats">See the retreats</a><?php else : ?><a class="btn btn--light" href="#waitlist">Join the waitlist</a><?php endif; ?>
        <a class="btn btn--ghost" href="<?php echo esc_url( $contact ); ?>" data-lead-open data-lead-source="hero">Ask us about <?php echo yaww_e_t( $place ); // phpcs:ignore ?></a>
      </div>
    </div>
  </section>

<?php if ( $dates ) :
	$n = count( $list ); ?>
  <section class="loc-dates<?php echo $n < 4 ? ' loc-dates--few' : ''; ?>" id="retreats" aria-labelledby="loc-dates-title">
    <div class="loc-wrap">
      <div class="loc-dates__head">
        <div>
          <p class="loc-kicker">Upcoming dates</p>
          <h2 class="loc-h2" id="loc-dates-title">Retreats in <?php echo yaww_e_t( $place ); // phpcs:ignore ?></h2>
          <p class="loc-p loc-dates__count" data-loc-count><?php echo 1 === $n ? 'One date open' : esc_html( $n . ' dates open' ); ?></p>
        </div>
        <p class="loc-dates__aside"><button class="loc-link" type="button" data-lead-open data-lead-source="dates">Ask us which one suits you</button></p>
      </div>
      <div class="loc-dates__grid" data-loc-grid<?php echo $n < 4 ? ' style="--n: ' . (int) $n . ';"' : ''; ?>><?php foreach ( $list as $rid ) { yaww_part( 'retreat-card', array( 'post_id' => $rid ) ); } ?></div>
    </div>
  </section>
<?php else :
	$prog      = yaww_e_location_program( $id );
	$prog_name = $prog ? get_the_title( $prog ) : 'our retreat in ' . $place;
	?>
  <section class="loc-dates loc-dates--waitlist" id="waitlist" aria-labelledby="loc-wait-title">
    <div class="loc-wrap loc-wait">
      <?php if ( $prog ) :
		$coll   = yaww_meta( 'collection', $prog );
		$tiers  = array( 'deep-dives' => 'Deep Dive', 'signature-collection' => 'Signature' );
		$tier   = isset( $tiers[ $coll ] ) ? $tiers[ $coll ] : '';
		$sig    = 'signature-collection' === $coll;
		$where  = yaww_e_card_loc( $id );
		$img_id = (int) yaww_meta( 'card_image', $prog );
		if ( ! $img_id ) { $img_id = (int) yaww_meta( 'card_image', $id ); }
		$label  = '<span class="card__label' . ( $sig ? ' card__label--signature' : '' ) . '">'
			. ( $tier ? '<span class="card__label-tier">' . esc_html( $tier ) . '</span><span class="card__label-sep" aria-hidden="true"></span>' : '' )
			. '<span class="card__label-status">Waitlist</span></span>';
		$img = $img_id ? yaww_e_img( $img_id, 'yaww-card', array( 'srcset' => yaww_e_srcset( $img_id, (int) yaww_meta( 'card_image_2x', $prog ) ), 'alt' => $prog_name . ' — ' . yaww_location_line( $id ), 'loading' => 'lazy', 'decoding' => 'async' ) ) : '';
		?>
      <div class="loc-wait__card" data-loc-wait-card><a class="result-link" href="<?php echo esc_url( get_permalink( $prog ) ); ?>" data-waitlist-dest="<?php echo esc_attr( $slug ); ?>"><article class="card card--narrow card--waitlist"><?php
		echo $img ? '<div class="card__media">' . $img . $label . '</div>' : '<div class="card__media" style="' . esc_attr( yaww_e_placeholder_style( $id ) ) . '">' . $label . '</div>'; // phpcs:ignore
		?><div class="card__body"><div class="card__title"><?php echo esc_html( $prog_name ); ?></div><div class="card__loc"><?php echo esc_html( $where ); ?></div><div class="card__date">See the itinerary</div></div></article></a></div>
      <?php endif; ?>
      <div class="loc-wait__panel">
        <div class="loc-wait__form-wrap" data-loc-wait-body>
          <p class="loc-kicker">Join the waitlist</p>
          <h2 class="loc-h2" id="loc-wait-title">Our next dates in <?php echo yaww_e_t( $place ); // phpcs:ignore ?> go to the waitlist first</h2>
          <p class="loc-p">Leave your name and email and we will write to you as soon as the dates for <?php echo yaww_e_t( $prog_name ); // phpcs:ignore ?> are set, before they are published anywhere else.<?php if ( $prog ) : ?> The full itinerary is on the <a class="loc-inline" href="<?php echo esc_url( get_permalink( $prog ) ); ?>">retreat page</a>.<?php endif; ?></p>
          <form class="lead-form loc-wait__form" data-waitlist-form data-waitlist-success="loc-wait-done" novalidate>
            <div class="lead-form__row">
              <div class="lead-form__field">
                <label class="lead-form__label" for="wl-first">First name</label>
                <input class="lead-form__input" id="wl-first" name="first_name" type="text" autocomplete="given-name" required aria-required="true" aria-describedby="wl-first-err">
                <p class="lead-form__error" id="wl-first-err" data-error-for="first_name"></p>
              </div>
              <div class="lead-form__field">
                <label class="lead-form__label" for="wl-email">Email</label>
                <input class="lead-form__input" id="wl-email" name="email" type="email" autocomplete="email" required aria-required="true" aria-describedby="wl-email-err">
                <p class="lead-form__error" id="wl-email-err" data-error-for="email"></p>
              </div>
            </div>
            <input type="hidden" name="location" value="<?php echo esc_attr( $slug ); ?>">
            <div class="loc-wait__actions">
              <button class="btn btn--primary" type="submit">Join the waitlist</button>
              <a class="btn btn--secondary" href="<?php echo esc_url( $contact ); ?>" data-lead-open data-lead-source="waitlist">Ask us about <?php echo yaww_e_t( $place ); // phpcs:ignore ?></a>
            </div>
            <p class="loc-wait__fine">We only use your details to tell you about these dates.</p>
          </form>
        </div>
        <div class="loc-wait__done" id="loc-wait-done" hidden>
          <svg class="loc-wait__tick" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22.5"/><path d="M15 24.5l6 6 12-13"/></svg>
          <h2 class="loc-h2" data-wait-done-title>You are on the list</h2>
          <p class="loc-p" data-wait-done-text>We will write to you as soon as the dates for <?php echo yaww_e_t( $place ); // phpcs:ignore ?> are set.</p>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php
	$intro_img = yaww_e_img( (int) yaww_meta( 'intro_image', $id ), 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	$paras     = yaww_e_paragraphs( $id );
	if ( ! $paras ) { $paras = yaww_list( 'intro', $id ); }
	if ( ! $paras && is_string( yaww_meta( 'intro', $id ) ) ) { $paras = yaww_e_lines( yaww_meta( 'intro', $id ) ); }
	?>
  <section class="loc-intro">
    <div class="loc-wrap loc-intro__row">
      <figure class="loc-intro__fig"><?php echo $intro_img ? $intro_img : '<div class="loc-ph">' . esc_html( yaww_e_missing( 'intro photo' ) ) . '</div>'; // phpcs:ignore ?></figure>
      <div class="loc-intro__copy">
        <p class="loc-kicker">Why we go</p>
        <h2 class="loc-h2"><?php echo $m( yaww_meta( 'intro_title', $id ), 'intro title' ); // phpcs:ignore ?></h2>
<?php if ( $paras ) { foreach ( $paras as $p ) { echo '        <p class="loc-p">' . yaww_e_t( $p ) . "</p>\n"; } } else { echo '        <p class="loc-p">' . esc_html( yaww_e_missing( 'intro' ) ) . "</p>\n"; } // phpcs:ignore ?>
      </div>
    </div>
  </section>

<?php $guide = yaww_rows( 'guide', $id ); ?>
  <section class="loc-guide" aria-labelledby="loc-guide-title">
    <div class="loc-wrap">
      <div class="loc-guide__head">
        <p class="loc-kicker">Our guide</p>
        <h2 class="loc-h2" id="loc-guide-title"><?php echo yaww_e_t( $place ); // phpcs:ignore ?>, the way we know it</h2>
      </div>
      <ul class="loc-guide__grid">
<?php if ( ! $guide ) { echo '        <li class="loc-guide__item"><p class="loc-p">' . esc_html( yaww_e_missing( 'guide' ) ) . "</p></li>\n"; }
foreach ( $guide as $g ) :
	$gi  = isset( $g['image'] ) ? (int) $g['image'] : 0;
	$gph = isset( $g['placeholder'] ) ? $g['placeholder'] : '';
	$fig = $gi ? yaww_e_img( $gi, 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ) : '';
	$fig = $fig ? $fig : '<div class="loc-ph" role="img" aria-label="Photo to come: ' . esc_attr( $gph ) . '">[photo: ' . yaww_e_t( $gph ) . ']</div>';
	?>
        <li class="loc-guide__item">
          <figure class="loc-guide__fig"><?php echo $fig; // phpcs:ignore ?></figure>
          <p class="loc-kicker"><?php echo yaww_e_t( isset( $g['kicker'] ) ? $g['kicker'] : '' ); // phpcs:ignore ?></p>
          <h3 class="loc-guide__title"><?php echo yaww_e_t( isset( $g['title'] ) ? $g['title'] : '' ); // phpcs:ignore ?></h3>
          <p class="loc-p"><?php echo yaww_e_t( isset( $g['text'] ) ? $g['text'] : '' ); // phpcs:ignore ?></p>
        </li>
<?php endforeach; ?>
      </ul>
    </div>
  </section>

<?php $know = yaww_rows( 'know', $id ); ?>
  <section class="loc-know" aria-labelledby="loc-know-title">
    <div class="loc-wrap">
      <div class="loc-know__head">
        <h2 class="loc-h2" id="loc-know-title">Good to know</h2>
        <button class="loc-link" type="button" data-lead-open data-lead-source="good-to-know">Ask us anything else</button>
      </div>
      <dl class="loc-know__grid"><?php
		if ( ! $know ) { echo '<div><dt>' . esc_html( yaww_e_missing( 'good to know' ) ) . '</dt><dd></dd></div>'; }
		foreach ( $know as $k ) { echo '<div><dt>' . yaww_e_t( isset( $k['label'] ) ? $k['label'] : '' ) . '</dt><dd>' . yaww_e_t_missing( isset( $k['value'] ) ? $k['value'] : ( isset( $k['text'] ) ? $k['text'] : '' ) ) . '</dd></div>'; } // phpcs:ignore
		?></dl>
    </div>
  </section>

<?php $qt = yaww_meta( 'quote_text', $id ); ?>
  <section class="loc-quote">
    <div class="loc-wrap">
      <figure>
        <blockquote>&ldquo;<?php echo $m( $qt, 'guest quote' ); // phpcs:ignore ?>&rdquo;</blockquote>
        <figcaption><?php echo $m( yaww_meta( 'quote_name', $id ), 'name' ); // phpcs:ignore ?> <span>· <?php echo $m( yaww_meta( 'quote_source', $id ), 'source' ); // phpcs:ignore ?></span></figcaption>
      </figure>
    </div>
  </section>

<?php
	// Three more: same region first, then the rest, in the data's order (menu_order).
	$all    = get_posts( array( 'post_type' => 'location', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ), 'suppress_filters' => false ) );
	$region = yaww_meta( 'region', $id );
	$same   = array();
	$other  = array();
	foreach ( $all as $lid ) {
		if ( (int) $lid === (int) $id ) { continue; }
		if ( yaww_meta( 'region', $lid ) === $region ) { $same[] = $lid; } else { $other[] = $lid; }
	}
	$more = array_slice( array_merge( $same, $other ), 0, 3 );
	?>
  <section class="loc-more" aria-labelledby="loc-more-title">
    <div class="loc-wrap">
      <div class="loc-more__head">
        <h2 class="loc-h2" id="loc-more-title">More places we love</h2>
        <a class="loc-link" href="<?php echo esc_url( get_post_type_archive_link( 'location' ) ); ?>">All locations</a>
      </div>
      <div class="locations-grid">
<?php foreach ( $more as $lid ) { yaww_part( 'e-location-card', array( 'post_id' => $lid, 'tag' => 'h3' ) ); } ?>
      </div>
    </div>
  </section>

  <section class="home-cta">
    <div class="home-cta__bg" aria-hidden="true" style="background-image: linear-gradient(rgba(40, 49, 57, 0.5), rgba(40, 49, 57, 0.5))<?php echo $hero ? ", url('" . esc_url( $hero ) . "')" : ''; ?>;"></div>
    <div class="home-cta__content">
      <h2 class="home-cta__title">Your week in <?php echo yaww_e_t( $place ); // phpcs:ignore ?> starts here</h2>
      <p class="home-cta__desc">Leave the planning to us. All you need to do is arrive.</p>
      <div class="home-cta__btns">
        <?php if ( $dates ) : ?><a class="btn btn--primary" href="#retreats">See the retreats</a><?php else : ?><a class="btn btn--primary" href="#waitlist">Join the waitlist</a><?php endif; ?>
        <a class="btn btn--light" href="<?php echo esc_url( $contact ); ?>" data-lead-open data-lead-source="closing">Ask us about <?php echo yaww_e_t( $place ); // phpcs:ignore ?></a>
      </div>
    </div>
  </section>

<?php endwhile;
get_footer();
