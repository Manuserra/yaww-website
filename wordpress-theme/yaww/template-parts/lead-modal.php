<?php
/**
 * "I want more info" trigger. js/lead-modal.js builds the <dialog> itself on first click, reading the page's
 * .retreat-hero for context, so only the trigger lives in PHP. Args: source (data-lead-source), label, class.
 * Form endpoint: window.YAWW_CONTACT.endpoints (see functions.php); null until the backend exists.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$source = isset( $args['source'] ) ? $args['source'] : 'page';
$label  = isset( $args['label'] ) ? $args['label'] : 'I want more info';
$class  = isset( $args['class'] ) ? $args['class'] : 'btn btn--ghost';
?>
<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( home_url( '/contact/#general-inquiry' ) ); ?>" data-lead-open data-lead-source="<?php echo esc_attr( $source ); ?>"><?php echo esc_html( $label ); ?></a>
