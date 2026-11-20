<?php
/**
 * Template Name: Contact
 * contact.html. The form posts through js/contact.js to window.YAWW_CONTACT.endpoints.contact
 * (null until the forms endpoint exists: submit then logs and shows the success state, as on the static site).
 * Field names are the static form's snake_case names; copy the <form> from contact.html when wiring the endpoint.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$c = yaww_contact_config();
while ( have_posts() ) : the_post(); ?>
<section class="contact">
  <div class="contact__intro">
    <h1 class="contact__title"><?php the_title(); ?></h1>
    <div class="contact__lede"><?php the_content(); ?></div>
    <p class="contact__direct"><span class="contact__direct-k">Phone</span> <a href="<?php echo esc_url( $c['phone']['href'] ); ?>"><?php echo esc_html( $c['phone']['label'] ); ?></a></p>
  </div>
  <section class="contact__main" id="form" aria-labelledby="contact-form-title" data-yform-wrap>
    <h2 class="contact__form-title" id="contact-form-title">Ask a question</h2>
    <form class="yf" data-yform="contact" action="" method="post" novalidate>
      <?php /* TODO: port the field set from contact.html (name, email, topic, message, honeypot). */ ?>
      <label>Name <input name="name" type="text" autocomplete="name" required></label>
      <label>Email <input name="email" type="email" autocomplete="email" required></label>
      <label>Message <textarea name="message" rows="5" required></textarea></label>
      <button class="btn btn--primary" type="submit">Send</button>
    </form>
  </section>
</section>
<?php endwhile;
get_footer();
