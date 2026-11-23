<?php
/**
 * Template Name: Teach with us
 *
 * teach-with-us.html, as it is on main: hero (the homepage "For teachers" module as H1), why YAWW,
 * how it works, who does what, two teacher quotes, FAQ, and the application (yaww_teach_form(),
 * shared with the Contact tab). js/contact.js runs the form; it posts to /wp-json/yaww/v1/lead.
 * CSS: teach.css (page CSS) + lead.css and contact.css (enqueued in inc/forms.php).
 * Copy is the static page's; [missing data] markers stay visible until Christina answers.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

/** A teacher's link and photo: the teacher post when it exists, the theme copy of the static image otherwise. */
$yaww_tq = function ( $slug ) {
	$p   = get_page_by_path( $slug, OBJECT, 'teacher' );
	$url = $p ? get_permalink( $p ) : home_url( '/teachers/' . $slug . '/' );
	$img = $p ? get_the_post_thumbnail_url( $p, 'large' ) : '';
	if ( ! $img ) { $img = YAWW_URI . '/assets/images/teachers/' . $slug . '.jpg'; }
	return array( $url, $img );
};
list( $gm_url, $gm_img ) = $yaww_tq( 'genevieve-morin' );
list( $jc_url, $jc_img ) = $yaww_tq( 'jenniferlyn-chiemingo' );
$chev = '<svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
$faqs = array(
	array( 'Do I need to have led a retreat before?', '<p>Not necessarily. Tell us about your experience in the application and we will talk it through on the call. The production side is ours either way.</p>' ),
	array( 'Do I need to bring my own students?', '<p>Your community is the heart of the group, and we promote every retreat to our own guests too, to help fill the spots. <span class="teach-missing">[missing data: minimum number of students a teacher is expected to bring, if any]</span></p>' ),
	array( 'How are teachers paid?', '<p>We go through it with you on the first call. <span class="teach-missing">[missing data: how teacher compensation works, e.g. fee, per-guest share, travel and room covered]</span></p>' ),
	array( 'Can I choose the destination?', '<p>Yes. Pick one of the places we already know well, or suggest your own. We will tell you honestly whether we can do it well there.</p>' ),
	array( 'How far ahead should we plan?', '<p>The earlier the better: it gives your students time to save and book flights. <span class="teach-missing">[missing data: typical lead time, e.g. 9 to 12 months]</span></p>' ),
	array( 'Who handles bookings and payments?', '<p>We do. Your guests book and pay through us, and our team answers their questions before the trip, so you are not chasing deposits.</p>' ),
);
?>
<?php /* header.php opens <main id="main">, footer.php closes it. */ ?>
  <section class="lead teach-hero">
    <div class="lead__media">
      <img class="lead__photo" src="<?php echo esc_url( YAWW_URI . '/assets/images/lead-group-terrace.jpg' ); ?>" alt="A teacher leading a discussion with her group seated on a terrace overlooking the bay" fetchpriority="high">
    </div>
    <div class="lead__copy">
      <p class="kicker lead__kicker">For teachers</p>
      <h1 class="lead__title teach-hero__title">Lead a retreat with us</h1>
      <p class="lead__body">Bring your students somewhere new. We take care of everything else, so you can focus on teaching.</p>
      <ul class="lead__list">
        <li>We plan the itinerary</li>
        <li>We book the hotels</li>
        <li>We run the logistics</li>
        <li>We help fill your spots</li>
      </ul>
      <div class="teach-hero__ctas">
        <a class="btn btn--primary" href="#apply">Apply to lead a retreat</a>
        <a class="teach-link" href="#how">How it works</a>
      </div>
    </div>
  </section>

  <section class="teach-section teach-why" aria-labelledby="why-title">
    <div class="teach-inner">
      <div class="teach-head">
        <p class="kicker teach-kicker">Why YAWW</p>
        <h2 class="section-title" id="why-title">You teach. We produce the rest.</h2>
        <p class="teach-lede">Planning a retreat on your own means contracts, deposits, transfers, menus and a hundred emails. We have been doing that work since 2008, so your energy can go to your students.</p>
      </div>
      <dl class="teach-stats">
        <div><dt>Hosting retreats since</dt><dd>2008</dd></div>
        <div><dt>Retreats hosted</dt><dd>550+</dd></div>
        <div><dt>Yogis who have travelled with us</dt><dd>4,500</dd></div>
      </dl>
      <ul class="teach-why__grid">
        <li>
          <h3>Places worth the trip</h3>
          <p>We look for destinations that are not overdone, and we add new ones every year, so you always have somewhere fresh to offer your students.</p>
        </li>
        <li>
          <h3>Days that feel effortless</h3>
          <p>Boutique hotels, food we would travel for and excursions with local guides, woven around your classes. Everything is optional for your guests.</p>
        </li>
        <li>
          <h3>Someone with you all week</h3>
          <p>A YAWW facilitator travels with the group, so you are never the one sorting out a room change or a late transfer between classes.</p>
        </li>
      </ul>
    </div>
  </section>

  <section class="teach-section teach-steps-wrap" id="how" aria-labelledby="how-title">
    <div class="teach-inner">
      <div class="teach-head">
        <p class="kicker teach-kicker">How it works</p>
        <h2 class="section-title" id="how-title">From first message to last class</h2>
      </div>
      <ol class="teach-steps">
        <li>
          <span class="teach-steps__n" aria-hidden="true">01</span>
          <h3>Tell us about you</h3>
          <p>Fill in the short application below: where you teach, who your students are and where you would love to take them.</p>
        </li>
        <li>
          <span class="teach-steps__n" aria-hidden="true">02</span>
          <h3>We talk</h3>
          <p>A call to get to know each other and look at the destinations and dates that suit your community.</p>
        </li>
        <li>
          <span class="teach-steps__n" aria-hidden="true">03</span>
          <h3>We build your retreat</h3>
          <p>Itinerary, hotels, meals, excursions and pricing. You shape the yoga program; we shape everything around it.</p>
        </li>
        <li>
          <span class="teach-steps__n" aria-hidden="true">04</span>
          <h3>We open booking</h3>
          <p>Your retreat gets its own page on our site. We take the bookings and payments, answer guests' questions and promote it alongside you.</p>
        </li>
        <li>
          <span class="teach-steps__n" aria-hidden="true">05</span>
          <h3>You teach</h3>
          <p>Arrive, teach and enjoy your group. We look after the rest, from the first transfer to the last goodbye.</p>
        </li>
      </ol>
    </div>
  </section>

  <section class="teach-section teach-split" aria-labelledby="split-title">
    <div class="teach-inner">
      <h2 class="section-title" id="split-title">Who does what</h2>
      <div class="teach-split__cols">
        <div class="teach-split__col">
          <p class="kicker teach-kicker">We take care of</p>
          <ul class="teach-list">
            <li>The itinerary, day by day</li>
            <li>Boutique hotels and rooms</li>
            <li>Transfers, meals and excursions</li>
            <li>Bookings, payments and guests' questions</li>
            <li>Promotion to help fill your spots</li>
            <li>A facilitator with the group all week</li>
          </ul>
        </div>
        <div class="teach-split__col">
          <p class="kicker teach-kicker">You bring</p>
          <ul class="teach-list">
            <li>Your teaching and the yoga program</li>
            <li>Your community, and a word to your students</li>
            <li>Your presence with the group</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="teach-section teach-quotes" aria-labelledby="quotes-title">
    <div class="teach-inner">
      <div class="teach-head">
        <p class="kicker teach-kicker">From our teachers</p>
        <h2 class="section-title" id="quotes-title">In their words</h2>
      </div>
      <div class="teach-quotes__grid">
        <figure class="tq">
          <img class="tq__photo" src="<?php echo esc_url( $gm_img ); ?>" alt="Genevieve Morin" loading="lazy" width="672" height="888">
          <div class="tq__text">
            <blockquote class="tq__quote">&ldquo;As a yoga instructor, I've had the pleasure of leading over 30 international retreats, many of which I produced independently. Partnering with Christina and Yoga &amp; Adventures Worldwide has significantly eased the process for me. Christina is proactive and supportive in filling spots, making the entire experience seamless for instructors and participants.&rdquo;</blockquote>
            <figcaption class="tq__by">
              <a class="tq__name" href="<?php echo esc_url( $gm_url ); ?>">Genevieve Morin</a>
              <span class="tq__detail">Led our Basque Country retreat</span>
            </figcaption>
          </div>
        </figure>
        <figure class="tq">
          <img class="tq__photo" src="<?php echo esc_url( $jc_img ); ?>" alt="Jenniferlyn Chiemingo" loading="lazy" width="672" height="888">
          <div class="tq__text">
            <blockquote class="tq__quote">&ldquo;Working with Christina and Yoga &amp; Adventures Worldwide has been a dream. She curates the most decadent, beautiful trips and takes care of every detail so I can focus on my students &amp; yoga. She always has my back as a teacher and I trust her with my best clients.&rdquo;</blockquote>
            <figcaption class="tq__by">
              <a class="tq__name" href="<?php echo esc_url( $jc_url ); ?>">Jenniferlyn Chiemingo</a>
              <span class="tq__detail">Yoga teacher for more than 20 years</span>
            </figcaption>
          </div>
        </figure>
      </div>
    </div>
  </section>

  <section class="faq-section teach-faq" aria-labelledby="faq-title">
    <h2 class="faq-section__title" id="faq-title">Questions teachers ask us</h2>
    <div class="faq-list">
      <?php foreach ( $faqs as $i => $f ) : $n = $i + 1; ?>
      <div class="faq">
        <h3 class="faq__heading"><button class="faq__trigger" type="button" aria-expanded="false" aria-controls="tfaq-<?php echo (int) $n; ?>"><span class="faq__q"><?php echo esc_html( $f[0] ); ?></span><?php echo $chev; // phpcs:ignore WordPress.Security.EscapeOutput ?></button></h3>
        <div class="faq__panel"><div class="faq__answer" id="tfaq-<?php echo (int) $n; ?>" role="region"><?php echo wp_kses_post( $f[1] ); ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="teach-section teach-apply" id="apply" aria-labelledby="apply-title">
    <div class="teach-inner teach-apply__grid">
      <div class="teach-apply__intro">
        <p class="kicker teach-kicker">Apply</p>
        <h2 class="teach-apply__title" id="apply-title">Tell us about your teaching</h2>
        <p class="teach-lede">It takes about three minutes. We read every application ourselves and write back to set up a first call.</p>
        <p class="teach-apply__aside">Not a teacher? <a class="teach-link" href="<?php echo esc_url( yaww_forms_link( 'contact' ) ); ?>">Write to us here</a></p>
      </div>

      <div class="teach-apply__form" data-yform-wrap>
        <?php yaww_teach_form(); ?>
      </div>
    </div>
  </section>
<?php
get_footer();
