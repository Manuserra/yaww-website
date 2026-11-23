<?php
/**
 * Template Name: Contact
 *
 * contact.html, as it is on main: one column, the site's tabs (Join a retreat / Plan a group trip /
 * Press & partners / Teach with us), the teacher application inside its tab. js/contact.js runs it
 * unchanged; the forms post to /wp-json/yaww/v1/lead (inc/forms.php points YAWW_CONTACT there).
 * The retreat select lists the upcoming retreat posts. The public email comes from
 * Settings → YAWW forms; while empty the page shows the [missing data] marker, as the static page does.
 * The teacher application must stay identical to the one in teach-with-us.php.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$retreats_url = yaww_forms_link( 'retreats' );
$teach_url    = yaww_forms_link( 'teach-with-us' );
$public_email = yaww_forms_setting( 'public_email' );
?>
<?php /* header.php opens <main id="main">, footer.php closes it. */ ?>
  <div class="contact">

    <header class="contact__head">
      <h1 class="contact__title">We would love to hear from you</h1>
      <p class="contact__lede">Ask us anything about a retreat, plan a trip for your own group, or just say hello. Someone from our team reads every message and writes back personally.</p>
      <p class="contact__direct">Prefer to talk? <a href="tel:+5219842026072" title="Phone · Mexico" aria-label="Phone, Mexico: +52 1 984 202 6072">+52 1 984 202 6072</a> <span aria-hidden="true">·</span> <?php if ( $public_email ) : ?><a href="mailto:<?php echo esc_attr( $public_email ); ?>" data-contact-email><?php echo esc_html( $public_email ); ?></a><?php else : ?><a href="#" class="is-placeholder" data-contact-email>[missing data: public email address]</a><?php endif; ?></p>
    </header>

    <div class="contact__tabs-wrap">
      <div class="tabs contact__tabs" role="tablist" aria-label="What is it about?" data-contact-tabs>
        <button class="tabs__item is-active" type="button" role="tab" id="tab-retreat" data-contact-tab="retreat" aria-controls="form" aria-selected="true" tabindex="0">Join a retreat</button>
        <button class="tabs__item" type="button" role="tab" id="tab-private-group" data-contact-tab="private-group" aria-controls="form" aria-selected="false" tabindex="-1">Plan a group trip</button>
        <button class="tabs__item" type="button" role="tab" id="tab-press" data-contact-tab="press" aria-controls="form" aria-selected="false" tabindex="-1">Press &amp; partners</button>
        <button class="tabs__item" type="button" role="tab" id="tab-teach" data-contact-tab="teach" aria-controls="teach" aria-selected="false" tabindex="-1">Teach with us</button>
        <span class="tabs__indicator" aria-hidden="true"></span>
      </div>
    </div>

    <section class="contact__main" id="form" role="tabpanel" aria-labelledby="tab-retreat" tabindex="-1" data-yform-wrap>
      <form class="yf" data-yform="contact" data-attribution action="" method="post" novalidate>

        <input type="hidden" name="topic" value="retreat" data-topic-input>

        <div class="yf__field" data-field="retreat" data-show-when="topic=retreat">
          <label class="yf__label" for="c-retreat">Which retreat or place? <span class="yf__opt">(optional)</span></label>
          <select class="yf__input" id="c-retreat" name="retreat" data-retreat-options>
            <option value="not-sure" selected>Not sure yet, help me choose</option>
            <?php foreach ( yaww_contact_retreat_options() as $label ) : ?>
            <option value="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
            <option value="other" data-retreat-last>Something else</option>
          </select>
        </div>

        <p class="contact__group-note" data-show-when="topic=private-group">Friends, family, a studio, a company. Tell us who is coming and roughly when, and we will tell you what we can build.</p>
        <fieldset class="yf__field" data-field="group_size" data-show-when="topic=private-group" data-required data-error="Roughly how many people?">
          <legend class="yf__legend">How many of you?</legend>
          <div class="yf__chips">
            <label class="yf__chip"><input type="radio" name="group_size" value="2 to 5" aria-describedby="c-size-error"><span>2 to 5</span></label>
            <label class="yf__chip"><input type="radio" name="group_size" value="6 to 10" aria-describedby="c-size-error"><span>6 to 10</span></label>
            <label class="yf__chip"><input type="radio" name="group_size" value="11 to 20" aria-describedby="c-size-error"><span>11 to 20</span></label>
            <label class="yf__chip"><input type="radio" name="group_size" value="More than 20" aria-describedby="c-size-error"><span>More than 20</span></label>
          </div>
          <p class="yf__error" id="c-size-error" hidden></p>
        </fieldset>
        <div class="yf__row" data-show-when="topic=private-group">
          <div class="yf__field" data-field="group_when">
            <label class="yf__label" for="c-when">Roughly when? <span class="yf__opt">(optional)</span></label>
            <input class="yf__input" id="c-when" name="group_when" type="text" placeholder="Spring 2027">
          </div>
          <div class="yf__field" data-field="destination" data-show-when="topic=private-group">
            <label class="yf__label" for="c-destination">Anywhere in mind? <span class="yf__opt">(optional)</span></label>
            <input class="yf__input" id="c-destination" name="destination" type="text" placeholder="Mexico, Greece, open to ideas…">
          </div>
        </div>

        <div class="yf__row">
          <div class="yf__field" data-field="first_name">
            <label class="yf__label" for="c-name">First name</label>
            <input class="yf__input" id="c-name" name="first_name" type="text" autocomplete="given-name" required aria-describedby="c-name-error" data-error="What should we call you?">
            <p class="yf__error" id="c-name-error" hidden></p>
          </div>
          <div class="yf__field" data-field="email">
            <label class="yf__label" for="c-email">Email</label>
            <input class="yf__input" id="c-email" name="email" type="email" autocomplete="email" inputmode="email" required aria-describedby="c-email-error" data-error="We need an email to write back to.">
            <p class="yf__error" id="c-email-error" hidden></p>
          </div>
        </div>

        <div class="yf__field" data-field="message">
          <label class="yf__label" for="c-message">Your message</label>
          <textarea class="yf__input" id="c-message" name="message" rows="5" placeholder="First retreat? Travelling alone? Tell us a little about you."
            data-placeholder-retreat="First retreat? Travelling alone? Tell us a little about you."
            data-placeholder-private-group="Who's coming, and what kind of trip do you have in mind?"
            data-placeholder-press="Tell us who you are and what you have in mind." required aria-describedby="c-message-error" data-error="Tell us a little about what you would like to know."></textarea>
          <p class="yf__error" id="c-message-error" hidden></p>
        </div>

        <fieldset class="yf__field yf__field--quiet" data-field="reply_by">
          <legend class="yf__legend">How should we reply? <span class="yf__opt">(optional)</span></legend>
          <div class="yf__radios">
            <label class="yf__radio"><input type="radio" name="reply_by" value="email" checked><span>Email</span></label>
            <label class="yf__radio"><input type="radio" name="reply_by" value="whatsapp"><span>WhatsApp</span></label>
            <label class="yf__radio"><input type="radio" name="reply_by" value="call"><span>A call</span></label>
          </div>
        </fieldset>
        <div class="yf__field" data-field="phone" data-show-when="reply_by=whatsapp|call">
          <label class="yf__label" for="c-phone">Phone, with country code</label>
          <input class="yf__input" id="c-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" placeholder="+1 555 123 4567" required aria-describedby="c-phone-error" data-error="Add the number we should use.">
          <p class="yf__error" id="c-phone-error" hidden></p>
        </div>

        <label class="yf__check" for="c-newsletter">
          <input type="checkbox" id="c-newsletter" name="newsletter" value="yes">
          <span>Also send me news of future retreats</span>
        </label>

        <div class="yf__actions">
          <p class="yf__failed" data-yform-failed role="alert" hidden>Your message did not go through. Please try again, or write to us directly.</p>
          <button class="btn btn--primary yf__submit" type="submit">Send message</button>
          <p class="yf__note">Jenniferlyn writes back within 24 hours. No pressure to book.</p>
        </div>
      </form>

      <div class="yf-done" data-yform-done tabindex="-1" role="status" hidden>
        <p class="yf-done__title">Thank you, <span data-yform-name></span>.</p>
        <p>Jenniferlyn will write back to <strong data-yform-email></strong> within 24 hours.</p>
        <p>In the meantime, have a look at what is coming up.</p>
        <a class="btn btn--secondary" href="<?php echo esc_url( $retreats_url ); ?>">See upcoming retreats</a>
      </div>
    </section>

    <section class="contact__teach" id="teach" role="tabpanel" aria-labelledby="tab-teach" tabindex="-1" hidden data-yform-wrap>
      <p class="contact__teach-intro">Lead a retreat with us. <a href="<?php echo esc_url( $teach_url ); ?>">How it works, what we handle and how to apply&nbsp;<span aria-hidden="true">&rarr;</span></a></p>
      <?php yaww_teach_form(); /* the same application as teach-with-us.php (inc/forms.php) */ ?>
    </section>

  </div>
<?php
get_footer();
