<?php
/**
 * Forms: one endpoint for every form on the site.
 *
 *   POST /wp-json/yaww/v1/lead   (JSON, the payload the static JS already builds)
 *
 * form = contact (contact.html / Contact page)   js/contact.js
 *        teach   (teacher application)           js/contact.js
 *        lead    (retreat / location pop-up)     js/lead-modal.js
 *        waitlist (location waitlist)            js/retreat-cards.js + the bridge below
 *        newsletter (footer)                     the bridge below
 *
 * Each accepted submission goes through four independent steps; a failure in one never
 * stops the others, and each result is written to the lead's `_lead_delivery` meta:
 *   1. stored as a private `lead` post (Leads in wp-admin, columns + filter by form);
 *   2. emailed to Settings → YAWW forms recipients (reply-to = the sender), plain text;
 *   3. posted to the Slack incoming webhook (skipped when empty);
 *   4. WeTravel: its public API has no "create lead" call (only list/read), so nothing is
 *      sent; do_action( 'yaww_lead_created', $lead_id, $data ) is the hook for any CRM.
 *
 * Spam: same-origin check, a signed page token (yaww_ts, also a 2 s minimum fill time),
 * a honeypot (yaww_hp) and a rate limit per hashed IP.
 *
 * The static files are not changed: the bridge printed in the footer points
 * YAWW_CONTACT.endpoints and the pop-up's data-endpoint here, and posts the waitlist and
 * newsletter forms (which have no sender in the static site).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once __DIR__ . '/settings.php';

/* ------------------------------------------------------------------ schema */

/** Form keys and their names in wp-admin, the email and Slack. */
function yaww_forms_types() {
	return array(
		'contact'    => 'Contact',
		'lead'       => 'Retreat pop-up',
		'teach'      => 'Teacher application',
		'waitlist'   => 'Waitlist',
		'newsletter' => 'Newsletter',
	);
}

/** Required fields per form (the same as the front-end validation). */
function yaww_forms_required( $form ) {
	$r = array(
		'contact'    => array( 'first_name', 'email', 'message' ),
		'lead'       => array( 'first_name', 'email' ),
		'teach'      => array( 'first_name', 'last_name', 'email', 'teaches_at', 'about' ),
		'waitlist'   => array( 'first_name', 'email' ),
		'newsletter' => array( 'email' ),
	);
	return isset( $r[ $form ] ) ? $r[ $form ] : array( 'email' );
}

/**
 * Every accepted field => [ label, kind ]. Kinds: text, area (multi-line), list (array), url, email.
 * Order here is the order in the email and the lead screen. Anything else in the payload is dropped.
 */
function yaww_forms_fields() {
	return array(
		'first_name'         => array( 'First name', 'text' ),
		'last_name'          => array( 'Last name', 'text' ),
		'email'              => array( 'Email', 'email' ),
		'phone'              => array( 'Phone', 'text' ),
		'topic'              => array( 'Topic', 'text' ),
		'retreat'            => array( 'Retreat or place', 'text' ),
		'retreat_name'       => array( 'Retreat', 'text' ),
		'retreat_dates'      => array( 'Dates', 'text' ),
		'retreat_id'         => array( 'Retreat id', 'text' ),
		'location_id'        => array( 'Location id', 'text' ),
		'teacher_id'         => array( 'Teacher id', 'text' ),
		'room'               => array( 'Room', 'text' ),
		'intent'             => array( 'Intent', 'text' ),
		'travel_party'       => array( 'Who is coming', 'text' ),
		'topics'             => array( 'Would like to know', 'list' ),
		'group_size'         => array( 'Group size', 'text' ),
		'group_when'         => array( 'Roughly when', 'text' ),
		'destination'        => array( 'Anywhere in mind', 'text' ),
		'teaches_at'         => array( 'Teaches at', 'text' ),
		'links'              => array( 'Website or Instagram', 'text' ),
		'retreat_experience' => array( 'Led a retreat before', 'text' ),
		'destinations'       => array( 'Would love to go', 'list' ),
		'timing'             => array( 'When', 'text' ),
		'message'            => array( 'Message', 'area' ),
		'about'              => array( 'Teaching and students', 'area' ),
		'reply_by'           => array( 'Reply by', 'text' ),
		'contact_method'     => array( 'Reach by', 'text' ),
		'newsletter'         => array( 'Newsletter', 'text' ),
		'marketing_opt_in'   => array( 'News of future retreats', 'text' ),
		'source'             => array( 'Opened from', 'text' ),
		'page'               => array( 'Page', 'text' ),
		'page_url'           => array( 'Page URL', 'url' ),
		// Attribution (js/attribution.js, docs/lead-form.md "Attribution").
		'utm_source'         => array( 'utm_source', 'text' ),
		'utm_medium'         => array( 'utm_medium', 'text' ),
		'utm_campaign'       => array( 'utm_campaign', 'text' ),
		'utm_term'           => array( 'utm_term', 'text' ),
		'utm_content'        => array( 'utm_content', 'text' ),
		'referrer'           => array( 'Referrer', 'text' ),
		'landing_page'       => array( 'Landing page', 'text' ),
		'first_utm_source'   => array( 'First utm_source', 'text' ),
		'first_utm_medium'   => array( 'First utm_medium', 'text' ),
		'first_utm_campaign' => array( 'First utm_campaign', 'text' ),
		'first_utm_term'     => array( 'First utm_term', 'text' ),
		'first_utm_content'  => array( 'First utm_content', 'text' ),
		'first_referrer'     => array( 'First referrer', 'text' ),
		'first_landing_page' => array( 'First landing page', 'text' ),
		'first_seen'         => array( 'First seen', 'text' ),
	);
}

/** Attribution keys, grouped apart in the email and the lead screen. */
function yaww_forms_is_attribution( $key ) {
	return 0 === strpos( $key, 'utm_' ) || 0 === strpos( $key, 'first_' ) && 'first_name' !== $key || in_array( $key, array( 'referrer', 'landing_page' ), true );
}

/** Human value for a stored one (topic keys, chips, yes). */
function yaww_forms_pretty( $key, $value ) {
	$maps = array(
		'topic'              => array( 'retreat' => 'Join a retreat', 'private-group' => 'Plan a group trip', 'press' => 'Press & partners' ),
		'retreat'            => array( 'not-sure' => 'Not sure yet, help me choose', 'other' => 'Something else' ),
		'travel_party'       => array( 'solo' => 'Just me', 'two' => 'Two of us', 'group' => 'A group' ),
		'retreat_experience' => array( 'not-yet' => 'Not yet', '1-3' => 'Once or a few times', 'many' => 'Many times' ),
		'group_size'         => array( 'under-5' => 'Under 5', '5-10' => '5 to 10', '10-20' => '10 to 20', '20-plus' => 'More than 20', 'not-sure' => 'Not sure yet' ),
		'timing'             => array( '2027' => 'In 2027', '2028' => 'In 2028', 'flexible' => 'Flexible' ),
		'reply_by'           => array( 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'call' => 'A call' ),
		'contact_method'     => array( 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'phone' => 'Phone call' ),
		'topics'             => array( 'rooms' => 'Rooms and prices', 'level' => 'Right for my level?', 'travel' => 'Getting there', 'other' => 'Something else', 'dates' => 'Upcoming dates' ),
	);
	if ( is_array( $value ) ) {
		$out = array();
		foreach ( $value as $v ) { $out[] = yaww_forms_pretty( $key, $v ); }
		return implode( ', ', $out );
	}
	if ( isset( $maps[ $key ][ $value ] ) ) { return $maps[ $key ][ $value ]; }
	if ( 'destinations' === $key ) { return ucwords( str_replace( '-', ' ', $value ) ); }
	return (string) $value;
}

/**
 * Clean a raw payload: known keys only, sanitized and clipped. Accepts the waitlist's
 * camelCase names (firstName, locationId, teacherId) too.
 */
function yaww_forms_clean( $raw ) {
	$raw     = is_array( $raw ) ? $raw : array();
	$aliases = array( 'firstName' => 'first_name', 'lastName' => 'last_name', 'locationId' => 'location_id', 'teacherId' => 'teacher_id', 'location' => 'location_id', 'teacher' => 'teacher_id' );
	foreach ( $aliases as $from => $to ) {
		if ( isset( $raw[ $from ] ) && ! isset( $raw[ $to ] ) ) { $raw[ $to ] = $raw[ $from ]; }
	}
	$out = array();
	foreach ( yaww_forms_fields() as $key => $def ) {
		if ( ! isset( $raw[ $key ] ) ) { continue; }
		$v = $raw[ $key ];
		switch ( $def[1] ) {
			case 'list':
				$list = array();
				foreach ( array_slice( (array) $v, 0, 20 ) as $item ) {
					if ( is_scalar( $item ) ) {
						$item = mb_substr( sanitize_text_field( (string) $item ), 0, 100 );
						if ( '' !== $item ) { $list[] = $item; }
					}
				}
				$v = $list;
				break;
			case 'area':
				$v = is_scalar( $v ) ? mb_substr( sanitize_textarea_field( (string) $v ), 0, 5000 ) : '';
				break;
			case 'email':
				$v = is_scalar( $v ) ? sanitize_email( (string) $v ) : '';
				break;
			case 'url':
				$v = is_scalar( $v ) ? esc_url_raw( mb_substr( (string) $v, 0, 500 ) ) : '';
				break;
			default:
				$v = is_scalar( $v ) ? mb_substr( sanitize_text_field( (string) $v ), 0, 200 ) : '';
		}
		if ( '' !== $v && array() !== $v ) { $out[ $key ] = $v; }
	}
	return $out;
}

/** Field errors (key => message), empty when the lead is valid. */
function yaww_forms_validate( $form, $data ) {
	$errors = array();
	foreach ( yaww_forms_required( $form ) as $k ) {
		if ( empty( $data[ $k ] ) ) { $errors[ $k ] = 'Please fill this in.'; }
	}
	if ( ! empty( $data['email'] ) && ! is_email( $data['email'] ) ) {
		$errors['email'] = "That email doesn't look right.";
	}
	// A phone is required when the person asked for WhatsApp or a call.
	$wants_phone = ( isset( $data['reply_by'] ) && in_array( $data['reply_by'], array( 'whatsapp', 'call' ), true ) )
		|| ( isset( $data['contact_method'] ) && in_array( $data['contact_method'], array( 'whatsapp', 'phone' ), true ) );
	if ( $wants_phone && ( empty( $data['phone'] ) || strlen( preg_replace( '/\D/', '', $data['phone'] ) ) < 7 ) ) {
		$errors['phone'] = 'Please add a number we can reach, with the country code.';
	}
	return $errors;
}

/* ------------------------------------------------------------- post type */

add_action( 'init', function () {
	register_post_type( 'lead', array(
		'labels'              => array(
			'name'          => 'Leads',
			'singular_name' => 'Lead',
			'menu_name'     => 'Leads',
			'all_items'     => 'All leads',
			'edit_item'     => 'Lead',
			'search_items'  => 'Search leads',
			'not_found'     => 'No leads yet.',
		),
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_rest'        => false,
		'menu_position'       => 26,
		'menu_icon'           => 'dashicons-email-alt',
		'supports'            => array( 'title' ),
		'capability_type'     => 'post',
		'capabilities'        => array( 'create_posts' => 'do_not_allow' ),   // leads come from the forms only
		'map_meta_cap'        => true,
		'rewrite'             => false,
		'query_var'           => false,
	) );
} );

/* ----------------------------------------------------------- the endpoint */

add_action( 'rest_api_init', function () {
	register_rest_route( 'yaww/v1', '/lead', array(
		'methods'             => 'POST',
		'callback'            => 'yaww_forms_rest',
		'permission_callback' => '__return_true',   // public form; checks are in the callback
	) );
} );

function yaww_forms_endpoint() { return rest_url( 'yaww/v1/lead' ); }

/** Signed page token: "<time>.<hmac>". Proves the post came from one of our pages and how long it took. */
function yaww_forms_token() {
	$t = (string) time();
	return $t . '.' . substr( hash_hmac( 'sha256', $t, wp_salt( 'nonce' ) ), 0, 20 );
}

function yaww_forms_token_age( $token ) {
	if ( ! is_string( $token ) || ! preg_match( '/^(\d{9,11})\.([a-f0-9]{20})$/', $token, $m ) ) { return false; }
	$good = substr( hash_hmac( 'sha256', $m[1], wp_salt( 'nonce' ) ), 0, 20 );
	return hash_equals( $good, $m[2] ) ? time() - (int) $m[1] : false;
}

function yaww_forms_same_origin( WP_REST_Request $req ) {
	$site   = wp_parse_url( home_url(), PHP_URL_HOST );
	$origin = $req->get_header( 'origin' );
	if ( $origin ) { return wp_parse_url( $origin, PHP_URL_HOST ) === $site; }
	$ref = $req->get_header( 'referer' );
	return $ref ? wp_parse_url( $ref, PHP_URL_HOST ) === $site : false;
}

/** True when this IP has sent too much in the last 10 minutes (8 posts). The IP is stored only as a hash. */
function yaww_forms_rate_limited() {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'yaww_rl_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ), 0, 24 );
	$n   = (int) get_transient( $key );
	if ( $n >= (int) apply_filters( 'yaww_forms_rate_limit', 8 ) ) { return true; }
	set_transient( $key, $n + 1, 10 * MINUTE_IN_SECONDS );
	return false;
}

function yaww_forms_rest( WP_REST_Request $req ) {
	$raw = $req->get_json_params();
	if ( ! is_array( $raw ) ) { $raw = $req->get_body_params(); }
	if ( ! is_array( $raw ) ) { $raw = array(); }

	if ( ! yaww_forms_same_origin( $req ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'origin' ), 403 );
	}
	// Honeypot or too fast: look successful, keep nothing.
	$age = yaww_forms_token_age( isset( $raw['yaww_ts'] ) ? $raw['yaww_ts'] : '' );
	if ( false === $age ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'token', 'message' => 'Please reload the page and try again.' ), 403 );
	}
	if ( ! empty( $raw['yaww_hp'] ) || $age < 2 ) {
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}
	if ( yaww_forms_rate_limited() ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'rate', 'message' => 'Too many messages from here. Please try again in a few minutes.' ), 429 );
	}

	$r = yaww_forms_process( $raw );
	if ( ! empty( $r['errors'] ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'invalid', 'fields' => $r['errors'] ), 400 );
	}
	// Success when it reached the team at least one way (stored, emailed or in Slack).
	if ( empty( $r['delivered'] ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'delivery' ), 500 );
	}
	return new WP_REST_Response( array( 'ok' => true, 'id' => (int) $r['lead_id'] ), 200 );
}

/* ------------------------------------------------------------- pipeline */

/**
 * Validate, store, email, Slack, hook. Returns [ errors, lead_id, log, delivered ].
 * Also used by the settings page's test button ( $opts['test'] ).
 */
function yaww_forms_process( $raw, $opts = array() ) {
	$types = yaww_forms_types();
	$form  = isset( $raw['form'] ) && is_string( $raw['form'] ) && isset( $types[ $raw['form'] ] ) ? $raw['form'] : '';
	if ( ! $form ) {
		return array( 'errors' => array( 'form' => 'Unknown form.' ), 'lead_id' => 0, 'log' => array(), 'delivered' => false );
	}
	$data = yaww_forms_clean( $raw );
	$data = yaww_forms_fill_context( $form, $data );
	$errors = yaww_forms_validate( $form, $data );
	if ( $errors ) {
		return array( 'errors' => $errors, 'lead_id' => 0, 'log' => array(), 'delivered' => false );
	}
	$data = array( 'form' => $form ) + $data;
	$test = ! empty( $opts['test'] );

	$log = array();
	$delivered = false;

	// 1. Store.
	$lead_id = 0;
	try {
		$lead_id = yaww_forms_store( $form, $data, $test );
		$log['stored'] = $lead_id ? 'saved as lead #' . $lead_id : 'failed';
		$delivered = $delivered || (bool) $lead_id;
	} catch ( \Throwable $e ) {
		$log['stored'] = 'failed: ' . $e->getMessage();
	}

	// 2. Email.
	try {
		$sent = yaww_forms_email( $data, $lead_id, $test );
		$log['email'] = $sent;
		$delivered = $delivered || 0 === strpos( $sent, 'sent' );
	} catch ( \Throwable $e ) {
		$log['email'] = 'failed: ' . $e->getMessage();
	}

	// 3. Slack.
	try {
		$slack = yaww_forms_slack( $data, $lead_id, $test );
		$log['slack'] = $slack;
		$delivered = $delivered || 0 === strpos( $slack, 'sent' );
	} catch ( \Throwable $e ) {
		$log['slack'] = 'failed: ' . $e->getMessage();
	}

	// 4. WeTravel / any CRM: no create-lead API today, so a hook.
	try {
		do_action( 'yaww_lead_created', $lead_id, $data );
		$log['wetravel'] = 'not sent: WeTravel has no create-lead API (hook yaww_lead_created fired)';
	} catch ( \Throwable $e ) {
		$log['wetravel'] = 'hook failed: ' . $e->getMessage();
	}

	if ( $lead_id ) { update_post_meta( $lead_id, '_lead_delivery', $log ); }
	return array( 'errors' => array(), 'lead_id' => $lead_id, 'log' => $log, 'delivered' => $delivered );
}

/** Fill retreat_id / retreat_name from the page the lead was sent from, when the page did not carry them. */
function yaww_forms_fill_context( $form, $data ) {
	if ( 'lead' !== $form || ! empty( $data['retreat_id'] ) || empty( $data['page_url'] ) ) { return $data; }
	$pid = url_to_postid( $data['page_url'] );
	if ( $pid && 'retreat' === get_post_type( $pid ) ) {
		$data['retreat_id'] = yaww_lead_retreat_id( $pid );
		if ( empty( $data['retreat_name'] ) ) { $data['retreat_name'] = get_the_title( $pid ); }
	}
	return $data;
}

/**
 * The id a lead carries for a retreat: the static site's edition id when the import kept it
 * (meta source_id / static_id), the post slug otherwise.
 */
function yaww_lead_retreat_id( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	foreach ( array( 'source_id', 'static_id' ) as $k ) {
		$v = get_post_meta( $post_id, $k, true );
		if ( is_string( $v ) && '' !== $v ) { return $v; }
	}
	return (string) get_post_field( 'post_name', $post_id );
}

function yaww_forms_person( $data ) {
	$name = trim( ( isset( $data['first_name'] ) ? $data['first_name'] : '' ) . ' ' . ( isset( $data['last_name'] ) ? $data['last_name'] : '' ) );
	return '' !== $name ? $name : ( isset( $data['email'] ) ? $data['email'] : 'Someone' );
}

/** The retreat or place the lead is about, in words (for columns, subject, Slack). */
function yaww_forms_about( $data ) {
	if ( ! empty( $data['retreat_name'] ) ) {
		return $data['retreat_name'] . ( ! empty( $data['retreat_dates'] ) ? ' · ' . $data['retreat_dates'] : '' );
	}
	if ( ! empty( $data['retreat'] ) ) { return yaww_forms_pretty( 'retreat', $data['retreat'] ); }
	if ( ! empty( $data['location_id'] ) ) { return ucwords( str_replace( '-', ' ', $data['location_id'] ) ); }
	return '';
}

function yaww_forms_store( $form, $data, $test ) {
	$types = yaww_forms_types();
	$title = ( $test ? 'Test · ' : '' ) . $types[ $form ] . ' · ' . yaww_forms_person( $data ) . ' · ' . wp_date( 'Y-m-d H:i' );
	$id = wp_insert_post( array(
		'post_type'   => 'lead',
		'post_status' => 'private',
		'post_title'  => $title,
	), true );
	if ( is_wp_error( $id ) ) { throw new \RuntimeException( $id->get_error_message() ); }
	foreach ( $data as $k => $v ) {
		update_post_meta( $id, 'lead_' . $k, $v );
	}
	update_post_meta( $id, 'lead_about', yaww_forms_about( $data ) );
	if ( $test ) { update_post_meta( $id, 'lead_test', 1 ); }
	return (int) $id;
}

/** Plain-text summary: the person's answers, then where the visit came from. */
function yaww_forms_summary( $data, $lead_id ) {
	$types  = yaww_forms_types();
	$fields = yaww_forms_fields();
	$lines  = array( $types[ $data['form'] ] . ' · ' . wp_date( 'j F Y, H:i' ), '' );
	$attr   = array();
	foreach ( $fields as $k => $def ) {
		if ( ! isset( $data[ $k ] ) ) { continue; }
		$v = yaww_forms_pretty( $k, $data[ $k ] );
		if ( yaww_forms_is_attribution( $k ) ) { $attr[] = $def[0] . ': ' . $v; continue; }
		if ( 'area' === $def[1] ) { $lines[] = $def[0] . ':'; $lines[] = $v; $lines[] = ''; continue; }
		$lines[] = $def[0] . ': ' . $v;
	}
	if ( $attr ) { $lines[] = ''; $lines[] = 'Where they came from'; $lines = array_merge( $lines, $attr ); }
	if ( $lead_id ) { $lines[] = ''; $lines[] = 'In WordPress: ' . admin_url( 'post.php?post=' . $lead_id . '&action=edit' ); }
	return implode( "\n", $lines );
}

function yaww_forms_email( $data, $lead_id, $test ) {
	$types   = yaww_forms_types();
	$about   = yaww_forms_about( $data );
	$subject = ( $test ? '[Test] ' : '' ) . $types[ $data['form'] ] . ': ' . yaww_forms_person( $data ) . ( $about ? ' · ' . $about : '' );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( ! empty( $data['email'] ) && is_email( $data['email'] ) ) {
		$name      = str_replace( array( '"', "\r", "\n" ), '', yaww_forms_person( $data ) );
		$headers[] = 'Reply-To: "' . $name . '" <' . $data['email'] . '>';
	}
	$err = null;
	$catch = function ( $e ) use ( &$err ) { $err = $e; };
	add_action( 'wp_mail_failed', $catch );
	$ok = wp_mail( yaww_forms_recipients(), $subject, yaww_forms_summary( $data, $lead_id ), $headers );
	remove_action( 'wp_mail_failed', $catch );
	if ( $ok ) { return 'sent to ' . implode( ', ', yaww_forms_recipients() ); }
	return 'failed' . ( $err instanceof WP_Error ? ': ' . $err->get_error_message() : '' );
}

function yaww_forms_slack( $data, $lead_id, $test ) {
	$hook = yaww_forms_setting( 'slack_webhook' );
	if ( ! $hook ) { return 'skipped (no webhook in Settings → YAWW forms)'; }
	$types = yaww_forms_types();
	$esc   = function ( $s ) { return str_replace( array( '&', '<', '>' ), array( '&amp;', '&lt;', '&gt;' ), (string) $s ); };
	$text  = ( $test ? '[Test] ' : '' ) . '*' . $types[ $data['form'] ] . '*: ' . $esc( yaww_forms_person( $data ) );
	if ( ! empty( $data['email'] ) ) { $text .= ' · ' . $esc( $data['email'] ); }
	$about = yaww_forms_about( $data );
	if ( $about ) { $text .= "\n" . $esc( $about ); }
	if ( ! empty( $data['topic'] ) ) { $text .= "\n" . $esc( yaww_forms_pretty( 'topic', $data['topic'] ) ); }
	if ( $lead_id ) { $text .= "\n<" . admin_url( 'post.php?post=' . $lead_id . '&action=edit' ) . '|Open the lead in WordPress>'; }
	$res = wp_remote_post( $hook, array(
		'timeout' => 5,
		'headers' => array( 'Content-Type' => 'application/json' ),
		'body'    => wp_json_encode( array( 'text' => $text ) ),
	) );
	if ( is_wp_error( $res ) ) { return 'failed: ' . $res->get_error_message(); }
	$code = wp_remote_retrieve_response_code( $res );
	return ( $code >= 200 && $code < 300 ) ? 'sent' : 'failed: HTTP ' . $code . ' ' . substr( wp_remote_retrieve_body( $res ), 0, 100 );
}

/* ---------------------------------------------------------------- admin */

add_filter( 'manage_lead_posts_columns', function () {
	return array(
		'cb'           => '<input type="checkbox" />',
		'title'        => 'Lead',
		'lead_form'    => 'Form',
		'lead_topic'   => 'Topic',
		'lead_retreat' => 'Retreat or place',
		'lead_email'   => 'Email',
		'lead_status'  => 'Delivery',
		'date'         => 'Date',
	);
} );

add_action( 'manage_lead_posts_custom_column', function ( $col, $id ) {
	switch ( $col ) {
		case 'lead_form':
			$t = yaww_forms_types();
			$f = get_post_meta( $id, 'lead_form', true );
			echo esc_html( isset( $t[ $f ] ) ? $t[ $f ] : $f );
			break;
		case 'lead_topic':
			$v = get_post_meta( $id, 'lead_topic', true );
			if ( ! $v ) { $v = get_post_meta( $id, 'lead_topics', true ); $v = $v ? yaww_forms_pretty( 'topics', (array) $v ) : ''; }
			else { $v = yaww_forms_pretty( 'topic', $v ); }
			echo esc_html( $v );
			break;
		case 'lead_retreat':
			echo esc_html( get_post_meta( $id, 'lead_about', true ) );
			break;
		case 'lead_email':
			$e = get_post_meta( $id, 'lead_email', true );
			if ( $e ) { echo '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>'; }
			break;
		case 'lead_status':
			$log = (array) get_post_meta( $id, '_lead_delivery', true );
			$bad = array();
			foreach ( array( 'email', 'slack' ) as $s ) {
				if ( isset( $log[ $s ] ) && 0 === strpos( $log[ $s ], 'failed' ) ) { $bad[] = $s; }
			}
			echo $bad ? '<span style="color:#b32d2e">' . esc_html( implode( ', ', $bad ) . ' failed' ) . '</span>' : 'OK';
			break;
	}
}, 10, 2 );

/* Leads are all private; the "— Private" tag on every row says nothing. */
add_filter( 'display_post_states', function ( $states, $post ) {
	if ( 'lead' === $post->post_type ) { unset( $states['private'] ); }
	return $states;
}, 10, 2 );

/* Filter by form. */
add_action( 'restrict_manage_posts', function ( $type ) {
	if ( 'lead' !== $type ) { return; }
	$cur = isset( $_GET['lead_form'] ) ? sanitize_key( wp_unslash( $_GET['lead_form'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	echo '<select name="lead_form"><option value="">All forms</option>';
	foreach ( yaww_forms_types() as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $cur, $k, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
} );

add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() || 'lead' !== $q->get( 'post_type' ) ) { return; }
	$cur = isset( $_GET['lead_form'] ) ? sanitize_key( wp_unslash( $_GET['lead_form'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( $cur ) { $q->set( 'meta_query', array( array( 'key' => 'lead_form', 'value' => $cur ) ) ); }
	// Search leads by email and name too (the title has the name; meta holds the email).
	if ( $q->get( 's' ) && is_email( $q->get( 's' ) ) ) {
		$q->set( 'meta_query', array( array( 'key' => 'lead_email', 'value' => $q->get( 's' ) ) ) );
		$q->set( 's', '' );
	}
} );

/* Leads are read-only: one box with every answer and what happened to it. */
add_action( 'add_meta_boxes_lead', function () {
	remove_meta_box( 'submitdiv', 'lead', 'side' );
	remove_meta_box( 'slugdiv', 'lead', 'normal' );
	add_meta_box( 'yaww-lead', 'What they sent', 'yaww_forms_lead_box', 'lead', 'normal', 'high' );
	add_meta_box( 'yaww-lead-delivery', 'Delivery', 'yaww_forms_delivery_box', 'lead', 'side', 'high' );
} );

function yaww_forms_lead_box( $post ) {
	$fields = yaww_forms_fields();
	$types  = yaww_forms_types();
	$form   = get_post_meta( $post->ID, 'lead_form', true );
	echo '<p><strong>' . esc_html( isset( $types[ $form ] ) ? $types[ $form ] : $form ) . '</strong> · ' . esc_html( get_the_date( 'j F Y, H:i', $post ) ) . '</p>';
	echo '<table class="widefat striped" style="border:0"><tbody>';
	$attr = '';
	foreach ( $fields as $k => $def ) {
		$v = get_post_meta( $post->ID, 'lead_' . $k, true );
		if ( '' === $v || array() === $v ) { continue; }
		$shown = yaww_forms_pretty( $k, $v );
		if ( 'email' === $k ) { $cell = '<a href="mailto:' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a>'; }
		elseif ( 'page_url' === $k ) { $cell = '<a href="' . esc_url( $v ) . '" target="_blank" rel="noopener">' . esc_html( $v ) . '</a>'; }
		else { $cell = nl2br( esc_html( $shown ) ); }
		$row = '<tr><th style="width:200px">' . esc_html( $def[0] ) . '</th><td>' . $cell . '</td></tr>';
		if ( yaww_forms_is_attribution( $k ) ) { $attr .= $row; } else { echo $row; } // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</tbody></table>';
	if ( $attr ) {
		echo '<h4 style="margin:16px 0 6px">Where they came from</h4><table class="widefat striped" style="border:0"><tbody>' . $attr . '</tbody></table>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '<style>#post-body-content #titlediv input#title{pointer-events:none;background:#f6f7f7}</style>';
}

function yaww_forms_delivery_box( $post ) {
	$log = (array) get_post_meta( $post->ID, '_lead_delivery', true );
	echo '<ul style="margin:0">';
	foreach ( $log as $step => $msg ) {
		$bad = 0 === strpos( (string) $msg, 'failed' );
		echo '<li><strong>' . esc_html( ucfirst( $step ) ) . '</strong>: <span' . ( $bad ? ' style="color:#b32d2e"' : '' ) . '>' . esc_html( $msg ) . '</span></li>';
	}
	echo '</ul>';
	echo '<p><a class="button-link-delete" href="' . esc_url( get_delete_post_link( $post->ID ) ) . '">Move to Trash</a></p>';
}

/* ---------------------------------------------------- front end: assets */

add_action( 'wp_enqueue_scripts', function () {
	// attribution.js on every page, as on the static site (retreats/_tools/chrome.py).
	if ( file_exists( YAWW_DIR . '/assets/js/attribution.js' ) ) {
		wp_enqueue_script( 'yaww-attribution', YAWW_URI . '/assets/js/attribution.js', array(), yaww_asset_ver( 'js/attribution.js' ), array( 'strategy' => 'defer', 'in_footer' => false ) );
	}
	// teach-with-us.html also loads lead.css (the hero) and contact.css (the form): page CSS covers teach.css only.
	if ( is_page_template( 'page-templates/teach-with-us.php' ) ) {
		wp_enqueue_style( 'yaww-forms-lead', YAWW_URI . '/assets/css/lead.css', array( 'yaww-base' ), yaww_asset_ver( 'css/lead.css' ) );
		wp_enqueue_style( 'yaww-forms-contact', YAWW_URI . '/assets/css/contact.css', array( 'yaww-base' ), yaww_asset_ver( 'css/contact.css' ) );
	}
}, 20 );

/** What the bridge needs: the endpoint, a fresh token and, on a retreat page, the retreat. */
function yaww_forms_front_config() {
	$c = array( 'endpoint' => yaww_forms_endpoint(), 'token' => yaww_forms_token(), 'context' => new stdClass() );
	if ( is_singular( 'retreat' ) ) {
		$c['context'] = array( 'retreat_id' => yaww_lead_retreat_id( get_queried_object_id() ), 'retreat_name' => get_the_title( get_queried_object_id() ) );
	}
	return $c;
}

/*
 * The bridge. Printed after the footer scripts, so contact.js, lead-modal.js and
 * retreat-cards.js are already loaded; nothing in them changes.
 * - contact / teach: YAWW_CONTACT.endpoints.{contact,teach} = the endpoint.
 * - pop-up: on submit (capture phase, before its own handler) data-endpoint = the endpoint,
 *   hidden form=lead, and the retreat id when the page did not provide one.
 * - waitlist: posts after retreat-cards.js validated it (yaww:track waitlist_submit); if no
 *   script wired the form, validates and shows the thank-you itself.
 * - newsletter: validates the email, posts, says thank you.
 * - every form: yaww_ts (page token) and a honeypot yaww_hp.
 */
add_action( 'wp_footer', function () {
	$cfg = wp_json_encode( yaww_forms_front_config() );
	?>
<script id="yaww-forms-bridge">
(function (w, d) {
  "use strict";
  var C = w.YAWW_FORMS = <?php echo $cfg; // phpcs:ignore WordPress.Security.EscapeOutput ?>;
  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  var SELECTOR = "form[data-yform], form[data-waitlist-form], .footer-news__form";

  function endpoints() {
    if (!w.YAWW_CONTACT) return;
    w.YAWW_CONTACT.endpoints = w.YAWW_CONTACT.endpoints || {};
    w.YAWW_CONTACT.endpoints.contact = C.endpoint;
    w.YAWW_CONTACT.endpoints.teach = C.endpoint;
  }
  endpoints();
  w.YAWW = w.YAWW || {};
  w.YAWW.WAITLIST_ENDPOINT = C.endpoint;

  function hidden(form, name, value) {
    var el = form.querySelector('input[type="hidden"][name="' + name + '"]');
    if (!el) { el = d.createElement("input"); el.type = "hidden"; el.name = name; form.appendChild(el); }
    if (value !== undefined) el.value = value;
    return el;
  }
  function honeypot(form) {
    if (form.querySelector('[name="yaww_hp"]')) return;
    var box = d.createElement("div");
    box.setAttribute("aria-hidden", "true");
    box.style.cssText = "position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden";
    box.innerHTML = '<label>Leave this empty <input type="text" name="yaww_hp" tabindex="-1" autocomplete="off"></label>';
    form.appendChild(box);
  }
  function attribution() { return (w.YAWW_ATTRIBUTION && w.YAWW_ATTRIBUTION.get) ? w.YAWW_ATTRIBUTION.get() : {}; }
  function post(data) {
    data.yaww_ts = C.token;
    return fetch(C.endpoint, { method: "POST", headers: { "Content-Type": "application/json" }, credentials: "same-origin", body: JSON.stringify(data) })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r; });
  }
  function base(form, key) {
    var a = attribution(), out = { form: key, page_url: location.href.split("#")[0] };
    Object.keys(a).forEach(function (k) { out[k] = a[k]; });
    var hp = form.querySelector('[name="yaww_hp"]');
    out.yaww_hp = hp ? hp.value : "";
    return out;
  }

  function prepare() { Array.prototype.forEach.call(d.querySelectorAll(SELECTOR), honeypot); }
  if (d.readyState === "loading") d.addEventListener("DOMContentLoaded", prepare); else prepare();

  /* Capture phase: runs before each form's own submit handler reads its fields. */
  var lastWaitlist = null;
  d.addEventListener("submit", function (e) {
    var f = e.target;
    if (!f || !f.matches) return;
    if (f.matches("form[data-yform]")) { endpoints(); hidden(f, "yaww_ts", C.token); honeypot(f); }
    if (f.matches(".lead-modal form")) {
      f.setAttribute("data-endpoint", C.endpoint);
      hidden(f, "form", "lead");
      hidden(f, "yaww_ts", C.token);
      var rid = f.elements.retreat_id;
      if (rid && !rid.value && C.context && C.context.retreat_id) rid.value = C.context.retreat_id;
      var rn = f.elements.retreat_name;
      if (rn && !rn.value && C.context && C.context.retreat_name) rn.value = C.context.retreat_name;
    }
    if (f.matches("form[data-waitlist-form]")) { lastWaitlist = f; f.__yawwTracked = false; }
  }, true);

  /* Waitlist, wired by js/retreat-cards.js: it announces a valid submit. */
  function waitlistData(f) {
    var data = base(f, "waitlist");
    data.first_name = (f.elements.first_name && f.elements.first_name.value || "").trim();
    data.email = (f.elements.email && f.elements.email.value || "").trim();
    data.location_id = f.elements.location ? f.elements.location.value : "";
    data.teacher_id = f.elements.teacher ? f.elements.teacher.value : "";
    return data;
  }
  d.addEventListener("yaww:track", function (e) {
    if (!e.detail || e.detail.event !== "waitlist_submit" || !lastWaitlist) return;
    lastWaitlist.__yawwTracked = true;
    post(waitlistData(lastWaitlist)).catch(function (err) { if (w.console) console.warn("[yaww] waitlist not sent", err); });
  });

  /* Bubble phase: forms nobody else handles. */
  d.addEventListener("submit", function (e) {
    var f = e.target;
    if (!f || !f.matches) return;

    if (f.matches("form[data-waitlist-form]") && !f.dataset.yawwWired) {
      e.preventDefault();
      var bad = null;
      ["first_name", "email"].forEach(function (n) {
        var el = f.elements[n]; if (!el) return;
        var v = el.value.trim(), msg = "";
        if (n === "first_name" && !v) msg = "Please tell us your first name.";
        if (n === "email") msg = !v ? "We need an email to write to you." : (!EMAIL.test(v) ? "That email looks incomplete. Could you check it?" : "");
        var out = f.querySelector('[data-error-for="' + n + '"]');
        el.setAttribute("aria-invalid", msg ? "true" : "false");
        var wrap = el.closest(".lead-form__field"); if (wrap) wrap.classList.toggle("is-invalid", !!msg);
        if (out) out.textContent = msg;
        if (msg && !bad) bad = el;
      });
      if (bad) { bad.focus(); return; }
      var btn = f.querySelector('[type="submit"]'); if (btn) btn.disabled = true;
      post(waitlistData(f)).then(function () {
        var ok = d.getElementById(f.getAttribute("data-waitlist-success") || "");
        if (ok) {
          var t = ok.querySelector("[data-wait-done-title]");
          if (t) t.textContent = "You are on the list, " + f.elements.first_name.value.trim() + ".";
          var body = f.closest("[data-loc-wait-body]"); if (body) body.hidden = true;
          f.hidden = true; ok.hidden = false; ok.setAttribute("tabindex", "-1"); ok.focus();
        }
      }).catch(function () {
        var err = f.querySelector("[data-waitlist-error]") || f.querySelector('[data-error-for="email"]');
        if (err) { err.textContent = "Something went wrong sending this. Please try again."; err.hidden = false; }
      }).then(function () { if (btn) btn.disabled = false; });
      return;
    }

    if (f.matches(".footer-news__form")) {
      e.preventDefault();
      var input = f.elements.email, v = input ? input.value.trim() : "";
      var status = f.parentNode.querySelector("[data-news-status]");
      if (!status) {
        status = d.createElement("p");
        status.className = "footer-news__sub";
        status.setAttribute("data-news-status", "");
        status.setAttribute("role", "status");
        f.parentNode.insertBefore(status, f.nextSibling);
      }
      if (!EMAIL.test(v)) {
        input.setAttribute("aria-invalid", "true");
        status.textContent = "That email doesn't look right. Check for a typo?";
        input.focus();
        return;
      }
      input.removeAttribute("aria-invalid");
      var b = f.querySelector('[type="submit"]'); if (b) b.disabled = true;
      var data = base(f, "newsletter"); data.email = v; data.newsletter = "yes";
      post(data).then(function () {
        f.hidden = true;
        status.textContent = "Thank you. You are on the list.";
        d.dispatchEvent(new CustomEvent("yaww:track", { detail: { event: "newsletter_submit" } }));
      }).catch(function () {
        status.textContent = "Something went wrong. Please try again in a moment.";
      }).then(function () { if (b) b.disabled = false; });
    }
  });
})(window, document);
</script>
	<?php
}, 100 );

/* ------------------------------------------------- template helpers */

/**
 * Contact's "Which retreat or place?" options: upcoming retreats as "El Cuyo · Jan 2027"
 * (location name · month year), soonest first, without duplicates. Same rule as the
 * inline script at the end of contact.html.
 */
function yaww_contact_retreat_options() {
	$today = wp_date( 'Y-m-d' );
	$rows  = array();
	$ids   = get_posts( array( 'post_type' => 'retreat', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true ) );
	foreach ( $ids as $id ) {
		$start = (string) yaww_meta( 'start_date', $id );
		if ( preg_match( '/^(\d{4})(\d{2})(\d{2})$/', $start, $m ) ) { $start = $m[1] . '-' . $m[2] . '-' . $m[3]; }   // ACF stores Ymd
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}/', $start ) || $start < $today ) { continue; }
		$loc  = (int) yaww_meta( 'location_id', $id );
		$name = $loc ? get_the_title( $loc ) : get_the_title( $id );
		$rows[] = array( $start, $name . ' · ' . date_i18n( 'M Y', strtotime( $start ) ) );
	}
	usort( $rows, function ( $a, $b ) { return strcmp( $a[0], $b[0] ); } );
	$out = array();
	foreach ( $rows as $r ) { $out[ $r[1] ] = $r[1]; }
	return array_values( $out );
}

/** A site URL by page slug or post type archive, for the static pages' links (contact.html → /contact/). */
function yaww_forms_link( $what ) {
	if ( 'retreats' === $what ) {
		$u = get_post_type_archive_link( 'retreat' );
		return $u ? $u : home_url( '/retreats/' );
	}
	$p = get_page_by_path( $what );
	return $p ? get_permalink( $p ) : home_url( '/' . $what . '/' );
}

/**
 * The teacher application (form + thank-you). One copy for both places it appears:
 * teach-with-us.php (#apply) and the "Teach with us" tab on Contact, as the static
 * pages require ("the two copies must match").
 */
function yaww_teach_form() {
	$retreats_url = yaww_forms_link( 'retreats' );
	?>
      <form class="yf" data-yform="teach" data-attribution action="" method="post" novalidate>
        <div class="yf__row">
          <div class="yf__field" data-field="first_name">
            <label class="yf__label" for="t-first">First name</label>
            <input class="yf__input" id="t-first" name="first_name" type="text" autocomplete="given-name" required aria-describedby="t-first-error" data-error="Please add your first name.">
            <p class="yf__error" id="t-first-error" hidden></p>
          </div>
          <div class="yf__field" data-field="last_name">
            <label class="yf__label" for="t-last">Last name</label>
            <input class="yf__input" id="t-last" name="last_name" type="text" autocomplete="family-name" required aria-describedby="t-last-error" data-error="Please add your last name.">
            <p class="yf__error" id="t-last-error" hidden></p>
          </div>
        </div>
        <div class="yf__row">
          <div class="yf__field" data-field="email">
            <label class="yf__label" for="t-email">Email</label>
            <input class="yf__input" id="t-email" name="email" type="email" autocomplete="email" inputmode="email" required aria-describedby="t-email-error" data-error="We need an email to reply to.">
            <p class="yf__error" id="t-email-error" hidden></p>
          </div>
          <div class="yf__field" data-field="phone">
            <label class="yf__label" for="t-phone">Phone <span class="yf__opt">(optional)</span></label>
            <input class="yf__input" id="t-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" placeholder="+1 555 123 4567">
          </div>
        </div>
        <div class="yf__row">
          <div class="yf__field" data-field="teaches_at">
            <label class="yf__label" for="t-where">Where do you teach?</label>
            <input class="yf__input" id="t-where" name="teaches_at" type="text" required placeholder="City and studio, or online" aria-describedby="t-where-error" data-error="A city or a studio is enough.">
            <p class="yf__error" id="t-where-error" hidden></p>
          </div>
          <div class="yf__field" data-field="links">
            <label class="yf__label" for="t-links">Website or Instagram <span class="yf__opt">(optional)</span></label>
            <input class="yf__input" id="t-links" name="links" type="text" placeholder="@yourname">
          </div>
        </div>

        <fieldset class="yf__field" data-field="retreat_experience" data-required data-error="Choose the one closest to you.">
          <legend class="yf__legend">Have you led a retreat before?</legend>
          <div class="yf__chips">
            <label class="yf__chip"><input type="radio" name="retreat_experience" value="not-yet" aria-describedby="t-exp-error"><span>Not yet</span></label>
            <label class="yf__chip"><input type="radio" name="retreat_experience" value="1-3" aria-describedby="t-exp-error"><span>Once or a few times</span></label>
            <label class="yf__chip"><input type="radio" name="retreat_experience" value="many" aria-describedby="t-exp-error"><span>Many times</span></label>
          </div>
          <p class="yf__error" id="t-exp-error" hidden></p>
        </fieldset>

        <fieldset class="yf__field" data-field="group_size" data-required data-error="A rough guess is fine.">
          <legend class="yf__legend">How many students might travel with you?</legend>
          <div class="yf__chips">
            <label class="yf__chip"><input type="radio" name="group_size" value="under-5" aria-describedby="t-size-error"><span>Under 5</span></label>
            <label class="yf__chip"><input type="radio" name="group_size" value="5-10" aria-describedby="t-size-error"><span>5 to 10</span></label>
            <label class="yf__chip"><input type="radio" name="group_size" value="10-20" aria-describedby="t-size-error"><span>10 to 20</span></label>
            <label class="yf__chip"><input type="radio" name="group_size" value="20-plus" aria-describedby="t-size-error"><span>More than 20</span></label>
            <label class="yf__chip"><input type="radio" name="group_size" value="not-sure" aria-describedby="t-size-error"><span>Not sure yet</span></label>
          </div>
          <p class="yf__error" id="t-size-error" hidden></p>
        </fieldset>

        <fieldset class="yf__field" data-field="destinations">
          <legend class="yf__legend">Where would you love to go? <span class="yf__opt">(optional, pick any)</span></legend>
          <div class="yf__chips">
            <?php foreach ( array( 'mexico' => 'Mexico', 'greece' => 'Greece', 'morocco' => 'Morocco', 'portugal' => 'Portugal', 'spain' => 'Spain', 'france' => 'France', 'italy' => 'Italy', 'iceland' => 'Iceland', 'montenegro' => 'Montenegro', 'thailand' => 'Thailand', 'somewhere-new' => 'Somewhere new' ) as $v => $l ) : ?>
            <label class="yf__chip"><input type="checkbox" name="destinations" value="<?php echo esc_attr( $v ); ?>"><span><?php echo esc_html( $l ); ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>

        <div class="yf__field" data-field="timing">
          <label class="yf__label" for="t-when">When are you thinking? <span class="yf__opt">(optional)</span></label>
          <select class="yf__input" id="t-when" name="timing">
            <option value="">Choose</option>
            <option value="2027">In 2027</option>
            <option value="2028">In 2028</option>
            <option value="flexible">I'm flexible</option>
          </select>
        </div>

        <div class="yf__field" data-field="about">
          <label class="yf__label" for="t-about">Your teaching and your students</label>
          <p class="yf__hint" id="t-about-hint">Your style, how long you have been teaching, and the kind of trip your students would love.</p>
          <textarea class="yf__input" id="t-about" name="about" rows="6" required aria-describedby="t-about-hint t-about-error" data-error="A few lines about your teaching helps us prepare the call."></textarea>
          <p class="yf__error" id="t-about-error" hidden></p>
        </div>

        <div class="yf__actions">
          <p class="yf__failed" data-yform-failed role="alert" hidden>Your application did not go through. Please try again in a moment.</p>
          <button class="btn btn--primary yf__submit" type="submit">Send application</button>
        </div>
      </form>

      <div class="yf-done" data-yform-done tabindex="-1" role="status" hidden>
        <p class="yf-done__title">Thank you, <span data-yform-name></span></p>
        <p>Your application is with us. We will write to <strong data-yform-email></strong> to set up a first call.</p>
        <p>While you wait, see where our teachers are taking their students this year.</p>
        <a class="btn btn--secondary" href="<?php echo esc_url( $retreats_url ); ?>">See upcoming retreats</a>
      </div>
	<?php
}
