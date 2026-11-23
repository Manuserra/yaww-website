<?php
/**
 * Home: index.html, verbatim (Home is a theme template, not edited content). The retreat rail,
 * the "who comes" postcards and the rails' arrows are the static page's own inline scripts,
 * now assets/js-theme/home.js, over js/retreat-cards.js and the WordPress catalog
 * (inc/views.php). The hero and the guest quotes are template parts.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/views.php';
get_header();
?>
<?php yaww_part( 'hero' ); ?>

  <!-- =========================================================
       2 · WHERE WE'RE GOING NEXT — Producto temprano
       Reuses the retreat-card component; links to the real retreat pages.
       No filters here: the home is a showcase of the next departures and
       hands off to retreats.html, the one place that filters.
       ========================================================= -->
  <section class="home-next home-next--headed" id="next-retreats">

    <div class="home-next__head">
      <p class="kicker home-next__kicker">Upcoming retreats</p>
      <h2 class="section-title">Where we're going next</h2>
    </div>

    <div class="rail" id="retreatsRail">
      <button class="rail__arrow rail__arrow--prev is-hidden" type="button" aria-label="Scroll to previous retreats" aria-hidden="true" tabindex="-1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
      </button>
      <div class="rail__track" id="nextRetreatsTrack" tabindex="0" aria-label="Upcoming retreats"></div>
      <button class="rail__arrow rail__arrow--next" type="button" aria-label="Scroll to more retreats">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
      </button>
    </div>

    <div class="home-next__foot">
      <a class="btn btn--secondary" href="<?php echo yaww_u( 'retreats.html' ); ?>" id="seeAllBtn">See all upcoming dates</a>
    </div>
  </section>

  <!-- =========================================================
       3 · TIERS — Deep Dives / Signature Collection / The Reserve
       ========================================================= -->
  <section class="tiers2">
    <div class="tiers2__head">
      <p class="kicker tiers2__kicker">Our collections</p>
      <h2 class="section-title tiers2__title">Three ways to travel with us</h2>
    </div>
    <div class="tiers2__row">
      <div class="tiers2__card">
        <div class="tiers2__intro">
          <div class="tiers2__image tiers2__image--deep" aria-hidden="true">
            <span class="tiers2__postcard tiers2__postcard--back"></span>
            <span class="tiers2__postcard tiers2__postcard--mid"></span>
            <span class="tiers2__postcard tiers2__postcard--front"></span>
          </div>
          <div class="tiers2__text">
            <p class="kicker tiers2__eyebrow">Deep Dives</p>
            <h3 class="tiers2__headline">Go deeper</h3>
            <p class="tiers2__body">More time on the mat. More space for yourself. Immersive yoga, inspiring teachers, meaningful cultural moments, and plenty of unscheduled time to rest, reflect, or explore at your own pace.</p>
          </div>
        </div>
        <a class="tiers2__link" href="<?php echo yaww_u( 'retreats.html' ); ?>">
          <span class="tiers2__link-text">Explore Deep Dives</span>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.01 11H4V13H16.01V16L20 12L16.01 8V11V11Z" fill="currentColor"/></svg>
        </a>
      </div>
      <div class="tiers2__card">
        <div class="tiers2__intro">
          <div class="tiers2__image tiers2__image--signature" aria-hidden="true">
            <span class="tiers2__postcard tiers2__postcard--back"></span>
            <span class="tiers2__postcard tiers2__postcard--mid"></span>
            <span class="tiers2__postcard tiers2__postcard--front"></span>
          </div>
          <div class="tiers2__text">
            <p class="kicker tiers2__eyebrow">Signature Collection</p>
            <h3 class="tiers2__headline">See the world differently</h3>
            <p class="tiers2__body">Our signature blend of exceptional yoga, fascinating cultures, incredible food, and adventures well beyond the ordinary. Thoughtfully curated for those who want more of the world.</p>
          </div>
        </div>
        <a class="tiers2__link" href="<?php echo yaww_u( 'retreats.html' ); ?>">
          <span class="tiers2__link-text">Explore Signature Collection</span>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.01 11H4V13H16.01V16L20 12L16.01 8V11V11Z" fill="currentColor"/></svg>
        </a>
      </div>
      <div class="tiers2__card">
        <div class="tiers2__intro">
          <div class="tiers2__image tiers2__image--reserve" aria-hidden="true">
            <span class="tiers2__postcard tiers2__postcard--back"></span>
            <span class="tiers2__postcard tiers2__postcard--mid"></span>
            <span class="tiers2__postcard tiers2__postcard--front"></span>
          </div>
          <div class="tiers2__text">
            <p class="kicker tiers2__eyebrow">The Reserve</p>
            <h3 class="tiers2__headline">Beyond the expected</h3>
            <p class="tiers2__body">A new expression of the YAWW experience. Inspired yoga, extraordinary places to stay, private encounters, and exclusive access to some of the world's most fascinating destinations.</p>
          </div>
        </div>
        <a class="tiers2__link" href="<?php echo yaww_u( 'retreats.html' ); ?>">
          <span class="tiers2__link-text">Explore The Reserve</span>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16.01 11H4V13H16.01V16L20 12L16.01 8V11V11Z" fill="currentColor"/></svg>
        </a>
      </div>
    </div>
  </section>

<?php yaww_part( 'testimonials' ); ?>

  <!-- =========================================================
       5 · PILLAR 1 — Handpicked instructors
       ========================================================= -->
  <section class="pillar">
    <div class="pillar__row">
      <div class="pillar__media">
        <img src="<?php echo esc_url( yaww_asset_url( 'images/pillar-1-yoga.jpg' ) ); ?>" alt="A group resting in savasana on an outdoor deck overlooking the water, led by their teacher" loading="lazy">
      </div>
      <div class="pillar__copy">
        <p class="kicker">The people behind the practice</p>
        <h2>Teachers worth traveling for</h2>
        <p>Great yoga begins with great teachers. We bring together inspiring instructors who offer more than exceptional classes. They bring personality, depth, and a genuine love for what they do. Their practice is an essential part of every YAWW journey.</p>
        <a class="btn btn--secondary" href="<?php echo yaww_u( 'retreats.html' ); ?>">Meet our teachers</a>
      </div>
    </div>
  </section>

  <!-- =========================================================
       6 · PILLAR 2 — The experience
       ========================================================= -->
  <section class="pillar pillar--reverse">
    <div class="pillar__row">
      <div class="pillar__copy">
        <p class="kicker">The experience</p>
        <h2>Beyond the guidebook</h2>
        <p>Hunting for truffles in the woods of Croatia. Lunch with shepherds in the mountains of Sardinia. A meditation guided by a monk in Chiang Mai. Our journeys are shaped by the people we've come to know, the relationships we've built over the years, and a genuine respect for the places and communities that welcome us.</p>
        <a class="btn btn--secondary" href="<?php echo yaww_u( 'retreats.html' ); ?>">Explore our destinations</a>
      </div>
      <div class="pillar__media">
        <img src="<?php echo esc_url( yaww_asset_url( 'images/pillar-2-adventure.jpg' ) ); ?>" alt="A small group crouched together in a forest, searching the ground for truffles" loading="lazy">
      </div>
    </div>
  </section>

  <!-- =========================================================
       7 · PILLAR 3 — The shift
       ========================================================= -->
  <section class="pillar">
    <div class="pillar__row">
      <div class="pillar__media">
        <img src="<?php echo esc_url( yaww_asset_url( 'images/pillar-3-food.jpg' ) ); ?>" alt="A long candlelit table of guests laughing together over dinner" loading="lazy">
      </div>
      <div class="pillar__copy">
        <p class="kicker">The way we gather</p>
        <h2>Pull up a chair</h2>
        <p>We follow the food. Local chefs, family-run kitchens, regional specialties, and long meals that have a way of turning strangers into friends. Eating locally is part of how we discover a destination and support the people who call it home.</p>
        <a class="btn btn--secondary" href="<?php echo yaww_u( 'retreats.html' ); ?>">Find your next retreat</a>
      </div>
    </div>
  </section>

  <!-- =========================================================
       8 · THE WAY WE MOVE
       ========================================================= -->
  <section class="movement">
    <div class="movement__intro">
      <p class="kicker movement__kicker">The way we move</p>
      <h2 class="section-title">A practice that grounds the journey</h2>
      <p class="movement__body">Yoga is at the heart of every YAWW retreat. Sometimes energizing, sometimes restorative, our time on the mat offers a chance to reconnect with ourselves before heading out to experience the world.</p>
    </div>
    <div class="movement__gallery">
      <img class="movement__media movement__media--tall" src="<?php echo esc_url( yaww_asset_url( 'images/movement-poolside.jpg' ) ); ?>" alt="Students resting in savasana on a sunlit poolside terrace">
      <img class="movement__media movement__media--short" src="<?php echo esc_url( yaww_asset_url( 'images/movement-beachfront.jpg' ) ); ?>" alt="Practitioners setting up mats on a candlelit, open-air deck facing the beach at dusk">
      <img class="movement__media movement__media--tall" src="<?php echo esc_url( yaww_asset_url( 'images/movement-hillside.jpg' ) ); ?>" alt="A group reaching overhead in a standing stretch on a wooden deck overlooking green hills">
    </div>
  </section>

  <!-- =========================================================
       9 · YOU BOOK, WE HANDLE EVERY DETAIL
       ========================================================= -->
  <section class="booking">
    <div class="booking__intro">
      <p class="kicker booking__kicker">How it works</p>
      <h2 class="section-title booking__title">Ready when you are</h2>
    </div>
    <div class="booking__row">
      <div class="booking__step">
        <span class="booking__icon-box" aria-hidden="true"><img class="booking__icon" src="<?php echo esc_url( yaww_asset_url( 'images/icon_choose.svg' ) ); ?>" alt="" width="80" height="80" loading="lazy"></span>
        <p class="booking__label">Choose your retreat</p>
        <p class="booking__body">Explore our destinations, teachers, and dates. Choose the journey that speaks to you.</p>
      </div>
      <div class="booking__step">
        <span class="booking__icon-box" aria-hidden="true"><img class="booking__icon" src="<?php echo esc_url( yaww_asset_url( 'images/icon_deposit.svg' ) ); ?>" alt="" width="80" height="80" loading="lazy"></span>
        <p class="booking__label">Reserve your place</p>
        <p class="booking__body">Choose your room and complete your reservation. We'll be in touch to confirm the details and welcome you.</p>
      </div>
      <div class="booking__step">
        <span class="booking__icon-box" aria-hidden="true"><img class="booking__icon" src="<?php echo esc_url( yaww_asset_url( 'images/icon_ready.svg' ) ); ?>" alt="" width="80" height="80" loading="lazy"></span>
        <p class="booking__label">Get ready with us</p>
        <p class="booking__body">We'll guide you through everything you need to know before departure, from arrival details to what to pack.</p>
      </div>
      <div class="booking__step">
        <span class="booking__icon-box" aria-hidden="true"><img class="booking__icon" src="<?php echo esc_url( yaww_asset_url( 'images/icon_arrive.svg' ) ); ?>" alt="" width="80" height="80" loading="lazy"></span>
        <p class="booking__label">Arrive and enjoy</p>
        <p class="booking__body">From the planning to the retreat itself, we take care of the details so you can enjoy every moment.</p>
      </div>
    </div>
  </section>

  <!-- =========================================================
       10 · WHO COMES ON OUR RETREATS
       ========================================================= -->
  <section class="who" aria-label="Who comes on our retreats">
    <h2 class="who__title">Who comes on our retreats</h2>
    <div class="who__row">
      <div class="who__profiles" role="tablist" aria-label="Guest profiles" id="whoProfiles"></div>
      <div class="who__postcards">
        <div class="who__stack" aria-live="polite" id="whoStack"></div>
        <div class="who__controls">
          <span class="who__counter" id="whoCounter"></span>
          <button type="button" class="who__play" id="whoPlayBtn"></button>
          <button type="button" class="who__nav who__nav--prev" id="whoPrevBtn" aria-label="Previous postcard">
            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M10 3 5 8 10 13" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <button type="button" class="who__nav who__nav--next" id="whoNextBtn" aria-label="Next postcard">
            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M6 3 11 8 6 13" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- =========================================================
       11 · LEAD A RETREAT WITH US
       ========================================================= -->
  <section class="lead">
    <div class="lead__media">
      <img class="lead__photo" src="<?php echo esc_url( yaww_asset_url( 'images/lead-group-terrace.jpg' ) ); ?>" alt="A teacher leading a discussion with her group seated on a terrace overlooking the bay" loading="lazy">
    </div>
    <div class="lead__copy">
      <p class="kicker lead__kicker">For teachers</p>
      <h2 class="lead__title">Lead a retreat with us</h2>
      <p class="lead__body">Bring your students somewhere new. We take care of everything else, so you can focus on teaching.</p>
      <ul class="lead__list">
        <li>We plan the itinerary</li>
        <li>We book the hotels</li>
        <li>We run the logistics</li>
        <li>We help fill your spots</li>
      </ul>
      <a class="btn btn--secondary" href="<?php echo yaww_u( 'teach-with-us.html' ); ?>">Plan your retreat</a>
    </div>
  </section>

  <!-- =========================================================
       12 · GOOD TO KNOW (FAQ)
       ========================================================= -->
  <section class="faq-section">
    <h2 class="faq-section__title">Good to know</h2>
    <div class="faq-list">
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="home-faq-1">
            <span class="faq__q">Do I need to be an experienced yogi?</span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="home-faq-1" role="region">
            <p>No. Classes are designed for all levels, and teachers adjust each session to the group.</p>
          </div>
        </div>
      </div>
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="home-faq-2">
            <span class="faq__q">Can I come on my own?</span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="home-faq-2" role="region">
            <p>Yes. Many of our guests travel solo, and plenty leave with friends for the next one.</p>
          </div>
        </div>
      </div>
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="home-faq-3">
            <span class="faq__q">What's included?</span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="home-faq-3" role="region">
            <p>Accommodation, daily yoga, most meals, curated excursions and a team member with the group all week. Each retreat page lists exactly what's covered. Flights aren't included unless the retreat page says so.</p>
          </div>
        </div>
      </div>
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="home-faq-4">
            <span class="faq__q">How big are the groups?</span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="home-faq-4" role="region">
            <!-- TODO: real group-size range, e.g. "Between 8 and 14 guests" -->
            <p>Small. [NEED: rango real, ej. "Between 8 and 14 guests"]</p>
          </div>
        </div>
      </div>
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="home-faq-5">
            <span class="faq__q">What if I need to cancel?</span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="home-faq-5" role="region">
            <!-- TODO: one-line cancellation policy + link to Terms -->
            <p>[NEED: política de cancelación en una línea + link a Terms]</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- =========================================================
       13 · CTA BAND (full-bleed parallax photo)
       ========================================================= -->
  <section class="home-cta">
    <div class="home-cta__bg" aria-hidden="true"></div>
    <div class="home-cta__content">
      <h2 class="home-cta__title">Your next week away starts here</h2>
      <p class="home-cta__desc">Pick a place you've always wanted to know. We'll take care of the rest.</p>
      <div class="home-cta__btns">
        <a class="btn btn--primary" href="<?php echo yaww_u( 'retreats.html' ); ?>">Explore upcoming retreats</a>
        <!-- TODO: route "Questions? Talk to us" to a contact/sales flow -->
        <a class="btn btn--light" href="#">Questions? Talk to us</a>
      </div>
    </div>
  </section>


<?php get_footer();
