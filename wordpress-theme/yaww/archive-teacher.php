<?php
/** Teachers grid (teachers.html): js/people-cards.js output, rendered on the server. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'people', array( 'cta-parallax' ) );
get_header(); ?>
  <section class="people">
    <div class="people__head">
      <h1 class="section-title">Teachers</h1>
      <?php /* window.YAWW_TEACHERS_INTRO (data/teachers.js). */ ?>
      <p class="people__lede" data-people-lede="teachers">Our teachers bring grounded guidance and soulful presence to every retreat. Each one offers thoughtful practices that honor the body, steady the mind and invite deeper connection along the way.</p>
    </div>
    <div class="people-grid" data-people-grid="teachers"><?php foreach ( yaww_e_people( 'teacher' ) as $pid ) { yaww_part( 'person-card', array( 'post_id' => $pid ) ); } ?></div>
  </section>

<?php yaww_part( 'e-closing-cta' ); ?>
<?php get_footer();
