<?php
/** Facilitators grid (facilitators.html). Only listed people get a card; unlisted ones keep their page. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'people', array( 'cta-parallax' ) );
get_header(); ?>
  <section class="people">
    <div class="people__head">
      <h1 class="section-title">Facilitators</h1>
      <?php /* window.YAWW_TEAM_INTRO (data/team.js). */ ?>
      <p class="people__lede" data-people-lede="facilitators">A facilitator from our team travels with every group. They know the place, handle every detail and look after each guest, so all you have to do is be there.</p>
    </div>
    <div class="people-grid" data-people-grid="facilitators"><?php foreach ( yaww_e_people( 'facilitator' ) as $pid ) { yaww_part( 'person-card', array( 'post_id' => $pid ) ); } ?></div>
  </section>

<?php yaww_part( 'e-closing-cta' ); ?>
<?php get_footer();
