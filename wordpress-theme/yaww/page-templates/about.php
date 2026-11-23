<?php
/**
 * Template Name: About
 * about.html, ported as code (the page's editor content is not used): intro, our story, founder,
 * how we work, "It takes a village" 2x2, teacher and facilitator rails (from the posts), guest quotes,
 * review badges, the For-teachers module and the closing band. Photos are the static site's
 * images/ (bin/sync-assets.sh copies them into assets/images/).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'about', array( 'people-rail', 'cta-parallax', 'about-reveal' ) );
get_header();
?>
  <section class="about-intro">
    <p class="kicker">About us</p>
    <h1 class="about-intro__title">Small-group yoga journeys,<br>hosted by people who know the place</h1>
    <p class="about-body about-intro__lede">Since 2008, Christina Thomas and her team have been taking small groups inside the places they love, with a hand-picked teacher and yoga woven into every day.</p>
    <figure class="about-intro__figure">
      <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/about-hero.jpg' ) ); ?>" alt="A Yoga Adventures group together on a city street" loading="lazy">
    </figure>
  </section>
  <section class="about-section">
    <div class="about-inner about-grid">
      <figure class="about-split__media">
        <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/about-story.jpg' ) ); ?>" alt="A retreat group practising together in a wooden studio" loading="lazy">
      </figure>
      <div class="about-split__text">
        <div class="about-split__head">
          <p class="kicker">Since 2008</p>
          <h2 class="section-title">Our story</h2>
        </div>
        <div class="about-prose">
          <p class="about-body">Yoga Adventures Worldwide started in 2008 on the beaches of Tulum, Mexico. Back then, it was just a simple idea: to bring like-minded people together in beautiful places, with space to move, rest, and reconnect. We didn’t have much, no Wi-Fi, barely any electricity, but what we did have was community, spontaneity, and a sense that something meaningful was happening.</p>
          <p class="about-body">Over the years, our retreats grew organically. Word spread, and so did our destinations. From Portugal to Morocco to Croatia, we kept following what felt right—places with character, depth, and a bit of magic.</p>
          <p class="about-body">Today, we still do things the same way: thoughtfully, intentionally, and with a focus on quality over quantity. The group is small, the details matter, and the experience always feels personal. We’re not here to sell the dream, we’re here to live it, together.</p>
        </div>
      </div>
    </div>
  </section>
  <section class="about-section">
    <div class="about-inner about-grid about-split--mirror">
      <div class="about-split__text">
        <div class="about-split__head">
          <p class="kicker">Founder</p>
          <h2 class="section-title">Christina Thomas</h2>
        </div>
        <div class="about-prose">
          <p class="about-body">In 2008 Christina left her fancy, big city Interior Design career to follow her heart in the Caribbean of Tulum, Mexico. After many (bumpy) serendipitous turns, she founded Utopia Guesthouse &amp; Yoga Studios in Tulum, Havana and now El Cuyo, Yucatan. She started Yoga Adventures Tulum in 2008 and Yoga &amp; Adventures Worldwide in 2010, offering unique, affordable retreats in some of the world’s most fascinating locations.</p>
          <p class="about-body">Her passion for yoga began in 1994 with a college Kundalini course. Since then, she has studied with many well-known masters and aspiring ones.</p>
          <p class="about-body">She acquired her RYT-200 in 2012 from the Institute of Interdisciplinary Yoga Studies, with a focus on Iyengar, Ashtanga, and Viniyoga traditions. Although she greatly enjoys leading asana practices, she still prefers to be on the mat as a student. Hence, she is the creator and (sometimes) host during her bespoke, lovingly crafted retreats.</p>
          <p class="about-body">She has been planning and hosting retreats across the globe since 2012, and traveling solo since she was just 5 years old. Each destination she chooses holds a deeply special place in her heart. Join us to discover why!</p>
        </div>
      </div>
      <figure class="about-split__media">
        <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/about-founder.jpg' ) ); ?>" alt="Christina with guests at a local flower market" loading="lazy">
      </figure>
    </div>
  </section>
  <section class="about-section about-how">
    <div class="about-team__head">
      <p class="kicker">Our approach</p>
      <h2 class="section-title">How we work</h2>
    </div>
    <div class="about-how__grid">

      <article class="about-card">
        <figure class="about-card__media">
          <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/about-how-places.jpg' ) ); ?>" alt="Guests riding camels on a desert track" loading="lazy">
        </figure>
        <h3 class="about-card__title">The places</h3>
        <p class="about-card__text">Destinations away from the overdone routes, chosen for their people, their food and their culture.</p>
        <a class="btn btn--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'location' ) ); ?>">Explore locations</a>
      </article>

      <article class="about-card">
        <figure class="about-card__media">
          <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/about-how-people.jpg' ) ); ?>" alt="A guest being wrapped in a scarf by a local host" loading="lazy">
        </figure>
        <h3 class="about-card__title">The people we know there</h3>
        <p class="about-card__text">Lunch with shepherds in Sardinia. The Day of the Dead with local families in Oaxaca. A monk chat in Chiang Mai.</p>
        <a class="btn btn--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'retreat' ) ); ?>">Explore retreats</a>
      </article>

      <article class="about-card">
        <figure class="about-card__media">
          <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/about-how-teachers.jpg' ) ); ?>" alt="A teacher sitting on a terrace above the water" loading="lazy">
        </figure>
        <h3 class="about-card__title">The teachers</h3>
        <p class="about-card__text">Chosen one by one: teachers who read the room and adapt every class to the group, from first-timers to teachers themselves.</p>
        <a class="btn btn--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'teacher' ) ); ?>">See all teachers</a>
      </article>

      <article class="about-card">
        <figure class="about-card__media">
          <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/about-how-team.jpg' ) ); ?>" alt="Two facilitators standing in a doorway" loading="lazy">
        </figure>
        <h3 class="about-card__title">Our team, with you</h3>
        <p class="about-card__text">A facilitator travels with every group, so you never have to think about logistics. Just be there.</p>
        <a class="btn btn--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'facilitator' ) ); ?>">See all facilitators</a>
      </article>

    </div>
  </section>
  <section class="about-section about-team">
    <div class="about-team__head">
      <p class="kicker">Team</p>
      <h2 class="section-title">It takes a village</h2>
    </div>
    <div class="about-team__grid">

      <article class="about-person">
        <figure class="about-person__media">
          <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/team/sofi-von-linden.jpg' ) ); ?>" alt="Sofi von Linden sitting on the sand by the sea" loading="lazy" width="672" height="888">
        </figure>
        <p class="about-person__role">Operations</p>
        <h3 class="about-person__name">Sofi von Linden</h3>
        <p class="about-person__text">Makes sure every detail works, and travels with many of our groups.</p>
      </article>

      <article class="about-person">
        <figure class="about-person__media">
          <img class="about-img" src="<?php echo esc_url( yaww_e_static_img( 'images/team/jenniferlyn-chiemingo.jpg' ) ); ?>" alt="Jenniferlyn Chiemingo outdoors" loading="lazy" width="672" height="888">
        </figure>
        <p class="about-person__role">Guest care</p>
        <h3 class="about-person__name">Jenniferlyn Chiemingo</h3>
        <p class="about-person__text">Answers your questions, from your first email to your packing list.</p>
      </article>
      <article class="about-person">
        <figure class="about-person__media about-person__media--missing">
          <span>[missing photo: Manu]</span>
        </figure>
        <p class="about-person__role">[missing data: role]</p>
        <h3 class="about-person__name">Manu</h3>
        <p class="about-person__text">[missing data: one line on what Manu does for our guests]</p>
      </article>
      <article class="about-person">
        <figure class="about-person__media about-person__media--missing">
          <span>[missing photo: Maggie]</span>
        </figure>
        <p class="about-person__role">[missing data: role]</p>
        <h3 class="about-person__name">Maggie</h3>
        <p class="about-person__text">[missing data: one line on what Maggie does for our guests]</p>
      </article>

    </div>
  </section>
  <section class="about-section about-rails">
    <div class="people-rail" data-people-rail="teachers">
      <div class="people-rail__head">
        <div class="about-split__head">
          <p class="kicker">Who teaches</p>
          <h2 class="section-title">Our teachers</h2>
        </div>
        <div class="people-rail__controls">
          <a class="people-rail__all" href="<?php echo esc_url( get_post_type_archive_link( 'teacher' ) ); ?>">See all teachers</a>
          <div class="people-rail__nav">
            <button class="people-rail__btn" type="button" data-rail-prev aria-label="Previous teachers"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
            <button class="people-rail__btn" type="button" data-rail-next aria-label="More teachers"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>
      <div class="people-rail__track" data-rail-track><?php foreach ( yaww_e_people( 'teacher' ) as $pid ) { yaww_part( 'person-card', array( 'post_id' => $pid, 'compact' => true ) ); } ?></div>
    </div>

    <div class="people-rail" data-people-rail="facilitators">
      <div class="people-rail__head">
        <div class="about-split__head">
          <p class="kicker">Who travels with you</p>
          <h2 class="section-title">Our facilitators</h2>
        </div>
        <div class="people-rail__controls">
          <a class="people-rail__all" href="<?php echo esc_url( get_post_type_archive_link( 'facilitator' ) ); ?>">See all facilitators</a>
          <div class="people-rail__nav">
            <button class="people-rail__btn" type="button" data-rail-prev aria-label="Previous facilitators"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
            <button class="people-rail__btn" type="button" data-rail-next aria-label="More facilitators"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>
      <div class="people-rail__track" data-rail-track><?php foreach ( yaww_e_people( 'facilitator' ) as $pid ) { yaww_part( 'person-card', array( 'post_id' => $pid, 'compact' => true ) ); } ?></div>
    </div>
  </section>
  <section class="about-section about-quotes">
    <div class="about-team__head">
      <p class="kicker">Reviews</p>
      <h2 class="section-title">What our guests say</h2>
    </div>
    <div class="about-quotes__grid">

      <figure class="about-quote">
        <blockquote class="about-quote__text">&ldquo;YAWW always hits it out of the park. The yoga component is also great, but the curated experience includes incredible food and wine experiences, top-notch accommodations, and the opportunity to make meaningful connections with like-minded people.&rdquo;</blockquote>
        <figcaption class="about-quote__by">
          <span class="about-quote__avatar" aria-hidden="true">MR</span>
          <div>
            <p class="about-quote__name">Mallory R.</p>
            <p class="about-quote__where">Google review</p>
          </div>
        </figcaption>
      </figure>
      <figure class="about-quote">
        <blockquote class="about-quote__text">&ldquo;I had never been interested in group organized travel prior to going on a retreat with Yoga Adventures Worldwide and now I can&rsquo;t imagine traveling without Christina and her wonderful crew.&rdquo;</blockquote>
        <figcaption class="about-quote__by">
          <span class="about-quote__avatar" aria-hidden="true">ES</span>
          <div>
            <p class="about-quote__name">Elizabeth S.</p>
            <p class="about-quote__where">Tripadvisor review</p>
          </div>
        </figcaption>
      </figure>
      <figure class="about-quote">
        <blockquote class="about-quote__text">&ldquo;Christina and her crew are magic travel masters. I would think of something and it would appear. Kind, organized and creative they offer great yoga, great food, great friends and great adventures.&rdquo;</blockquote>
        <figcaption class="about-quote__by">
          <span class="about-quote__avatar" aria-hidden="true">JS</span>
          <div>
            <p class="about-quote__name">Jessica S.</p>
            <p class="about-quote__where">Google review</p>
          </div>
        </figcaption>
      </figure>

    </div>
    <div class="review-badges">
      <a class="review-badge" data-review-widget="google" href="#" rel="noopener">
        <span class="review-badge__source">Google</span>
        <span class="review-badge__stars" role="img" aria-label="4.9 out of 5 stars"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg></span>
        <span class="review-badge__score">4.9</span>
        <span class="review-badge__cta">Read our Google reviews</span>
      </a>
      <a class="review-badge" data-review-widget="tripadvisor" href="#" rel="noopener">
        <span class="review-badge__source">Tripadvisor</span>
        <span class="review-badge__stars" role="img" aria-label="4.8 out of 5 stars"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.6 5.5 6 .8-4.4 4.2 1.1 6-5.3-2.9-5.3 2.9 1.1-6L1.4 7.8l6-.8z"/></svg></span>
        <span class="review-badge__score">4.8</span>
        <span class="review-badge__cta">Read our Tripadvisor reviews</span>
      </a>
    </div>
  </section>
  <section class="lead">
    <div class="lead__media">
      <img class="lead__photo" src="<?php echo esc_url( yaww_e_static_img( 'images/lead-group-terrace.jpg' ) ); ?>" alt="A teacher leading a discussion with her group seated on a terrace overlooking the bay" loading="lazy">
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
      <a class="btn btn--secondary" href="<?php echo esc_url( home_url( '/teach-with-us/' ) ); ?>">Plan your retreat</a>
    </div>
  </section>

<?php yaww_part( 'e-closing-cta' ); ?>
<?php get_footer();
