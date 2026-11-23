<?php
/**
 * "In our guests' words" (index.html section 4): three guest quotes with their photos, a
 * rail with arrows on desktop (wired by assets/js-theme/home.js). Verbatim from the static page.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
  <!-- =========================================================
       4 · IN OUR GUESTS' WORDS
       ========================================================= -->
  <section class="quotes">
    <h2 class="section-title">In our guests' words</h2>
    <div class="rail rail--quotes" id="quotesRail">
      <button class="rail__arrow rail__arrow--prev is-hidden" type="button" aria-label="Previous testimonial" aria-hidden="true" tabindex="-1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
      </button>
      <div class="rail__track" id="quotesTrack" tabindex="0" aria-label="Guest testimonials">
        <figure class="quote-card">
          <div class="quote-card__text">
            <blockquote class="quote-card__quote">&ldquo;This is my fourth trip with YAWW and I plan on doing more. The perfect combination of retreat and vacation. The yoga teachers are phenomenal and the facilitators are akin to having a personal concierge.&rdquo;</blockquote>
            <figcaption class="quote-card__by">
              <p class="quote-card__name">Amanda S.</p>
              <p class="quote-card__detail">Portugal Retreat</p>
            </figcaption>
          </div>
          <span class="quote-card__divider" aria-hidden="true"></span>
          <picture><source media="(max-width: 899px)" srcset="<?php echo esc_url( yaww_asset_url( 'images/quote-amanda-wide.jpg' ) ); ?>"><img class="quote-card__photo" src="<?php echo esc_url( yaww_asset_url( 'images/quote-amanda.jpg' ) ); ?>" alt="Amanda smiling on a boat, being handed a plate of fresh oysters"></picture>
        </figure>
        <figure class="quote-card">
          <div class="quote-card__text">
            <blockquote class="quote-card__quote">&ldquo;This retreat was an unforgettable experience! Every detail was planned and I felt very cared for. I met amazing people, felt immersed in the local culture, saw incredible sites, and had amazing food.&rdquo;</blockquote>
            <figcaption class="quote-card__by">
              <p class="quote-card__name">Marney B.</p>
              <p class="quote-card__detail">Morocco Retreat</p>
            </figcaption>
          </div>
          <span class="quote-card__divider" aria-hidden="true"></span>
          <picture><source media="(max-width: 899px)" srcset="<?php echo esc_url( yaww_asset_url( 'images/quote-marney-wide.jpg' ) ); ?>"><img class="quote-card__photo" src="<?php echo esc_url( yaww_asset_url( 'images/quote-marney.jpg' ) ); ?>" alt="Marney enjoying a meal on a sunlit terrace"></picture>
        </figure>
        <figure class="quote-card">
          <div class="quote-card__text">
            <blockquote class="quote-card__quote">&ldquo;Answering every question and concern and making sure everything is perfect for us is beyond anything I expect from a travel company. The teachers and facilitators are simply the best of the best!&rdquo;</blockquote>
            <figcaption class="quote-card__by">
              <p class="quote-card__name">Mimi S.</p>
              <p class="quote-card__detail">Montenegro Retreat</p>
            </figcaption>
          </div>
          <span class="quote-card__divider" aria-hidden="true"></span>
          <picture><source media="(max-width: 899px)" srcset="<?php echo esc_url( yaww_asset_url( 'images/quote-mimi-wide.jpg' ) ); ?>"><img class="quote-card__photo" src="<?php echo esc_url( yaww_asset_url( 'images/quote-mimi.jpg' ) ); ?>" alt="Mimi laughing outdoors in a green dress"></picture>
        </figure>
      </div>
      <button class="rail__arrow rail__arrow--next" type="button" aria-label="Next testimonial">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
      </button>
    </div>
  </section>
