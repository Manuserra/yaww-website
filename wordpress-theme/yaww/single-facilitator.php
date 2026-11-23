<?php
/** Single facilitator (facilitator-*.html): profile, upcoming retreats, Listen & watch, closing band. See template-parts/e-person-page.php. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
yaww_e_assets( 'people', array( 'cta-parallax' ) );
get_header();
while ( have_posts() ) : the_post();
	yaww_part( 'e-person-page', array( 'kind' => 'team' ) );
endwhile;
get_footer();
