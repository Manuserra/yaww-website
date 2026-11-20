<?php
/**
 * Template Name: Teach with us
 * teach-with-us.html: hero, why, steps, split, quotes, FAQ, application form (data-yform="teach").
 * Ported lean: hero + editor content + application form shell. Gap: steps / split / FAQ sections.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); ?>
<section class="lead teach-hero">
  <div class="lead__copy">
    <p class="kicker lead__kicker">For teachers</p>
    <h1 class="teach-hero__title"><?php the_title(); ?></h1>
    <div class="lead__body"><?php the_content(); ?></div>
    <div class="teach-hero__ctas"><a class="btn btn--primary" href="#apply">Apply to teach</a></div>
  </div>
</section>
<section class="teach-section teach-apply" id="apply" aria-labelledby="apply-title">
  <h2 class="teach-apply__title" id="apply-title">Tell us about your retreat</h2>
  <form class="yf" data-yform="teach" action="" method="post" novalidate>
    <?php /* TODO: port the field set from teach-with-us.html. */ ?>
    <label>Name <input name="name" type="text" required></label>
    <label>Email <input name="email" type="email" required></label>
    <label>Tell us about your retreat idea <textarea name="message" rows="5"></textarea></label>
    <button class="btn btn--primary" type="submit">Send</button>
  </form>
</section>
<?php endwhile;
get_footer();
