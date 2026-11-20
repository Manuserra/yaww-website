<?php /** "Lead a retreat with us" band (index.html .lead). */ if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<section class="lead">
  <div class="lead__media">
    <img class="lead__photo" src="<?php echo esc_url( YAWW_URI . '/assets/images/lead-group-terrace.jpg' ); ?>" alt="A teacher leading a discussion with her group seated on a terrace overlooking the bay" loading="lazy">
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
