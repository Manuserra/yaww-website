<?php
/**
 * "I want more info": an opener for the retreat pop-up (docs/lead-form.md).
 *
 * js/lead-modal.js builds the <dialog> itself on the first click, exactly as on the static
 * retreat pages, reading the page's .retreat-hero for the context (title, dates, photo,
 * data-retreat-id / data-location-id + data-lead-kind="location"). So only the opener lives here.
 * On submit, the bridge in inc/forms.php points the form at /wp-json/yaww/v1/lead, adds form=lead
 * and, when the hero has no data-retreat-id, fills retreat_id with yaww_lead_retreat_id() (the
 * static edition id when imported as source_id/static_id, the slug otherwise). Templates that
 * print .retreat-hero should still add data-retreat-id="<?php echo esc_attr( yaww_lead_retreat_id() ); ?>".
 *
 * Args: source (data-lead-source: hero, sticky-bar, rooms, room-card, waitlist, hosts…),
 *       label, class, room (data-lead-room), intent ("reserve" for the Reserve wording).
 * Without JavaScript the link still leads to the contact form.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$source = isset( $args['source'] ) ? $args['source'] : 'page';
$label  = isset( $args['label'] ) ? $args['label'] : 'I want more info';
$class  = isset( $args['class'] ) ? $args['class'] : 'btn btn--ghost';
$room   = isset( $args['room'] ) ? $args['room'] : '';
$intent = isset( $args['intent'] ) ? $args['intent'] : '';
$href   = function_exists( 'yaww_forms_link' ) ? yaww_forms_link( 'contact' ) : home_url( '/contact/' );
?>
<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $href . '#general-inquiry' ); ?>" data-lead-open data-lead-source="<?php echo esc_attr( $source ); ?>"<?php if ( $room ) : ?> data-lead-room="<?php echo esc_attr( $room ); ?>"<?php endif; ?><?php if ( 'reserve' === $intent ) : ?> data-lead-intent="reserve"<?php endif; ?>><?php echo esc_html( $label ); ?></a>
