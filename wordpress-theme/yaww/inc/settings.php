<?php
/**
 * Settings → YAWW forms: who gets the form emails, the Slack webhook, the WeTravel key,
 * the public email on Contact, and a button that sends a test lead through every step.
 *
 * One option, `yaww_forms`, read with yaww_forms_setting( $key ). Empty values mean "skip".
 * The forms pipeline itself is inc/forms.php.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** One setting, or '' when unset. Keys: recipients, slack_webhook, wetravel_key, public_email. */
function yaww_forms_setting( $key ) {
	$o = get_option( 'yaww_forms', array() );
	return ( is_array( $o ) && isset( $o[ $key ] ) ) ? $o[ $key ] : '';
}

/** Valid recipient addresses; falls back to the site admin email so a lead is never mailed to nobody. */
function yaww_forms_recipients() {
	$out = array();
	foreach ( preg_split( '/[\s,;]+/', (string) yaww_forms_setting( 'recipients' ) ) as $e ) {
		$e = sanitize_email( $e );
		if ( $e && is_email( $e ) ) { $out[] = $e; }
	}
	if ( ! $out ) { $out[] = get_option( 'admin_email' ); }
	return array_values( array_unique( $out ) );
}

add_action( 'admin_init', function () {
	register_setting( 'yaww_forms', 'yaww_forms', array(
		'type'              => 'array',
		'sanitize_callback' => 'yaww_forms_sanitize_settings',
		'default'           => array(),
	) );
} );

function yaww_forms_sanitize_settings( $in ) {
	$in  = is_array( $in ) ? $in : array();
	$old = get_option( 'yaww_forms', array() );
	$out = array();

	$emails = array();
	foreach ( preg_split( '/[\s,;]+/', isset( $in['recipients'] ) ? (string) $in['recipients'] : '' ) as $e ) {
		$e = sanitize_email( $e );
		if ( $e && is_email( $e ) ) { $emails[] = $e; }
	}
	$out['recipients'] = implode( ', ', array_unique( $emails ) );

	$hook = isset( $in['slack_webhook'] ) ? trim( (string) $in['slack_webhook'] ) : '';
	if ( '' !== $hook && 0 !== strpos( $hook, 'https://' ) ) {
		add_settings_error( 'yaww_forms', 'slack', 'The Slack webhook must start with https://. It was not saved.' );
		$hook = isset( $old['slack_webhook'] ) ? $old['slack_webhook'] : '';
	}
	$out['slack_webhook'] = esc_url_raw( $hook );

	// The key field is shown empty; leaving it empty keeps the saved key, "-" clears it.
	$key = isset( $in['wetravel_key'] ) ? trim( (string) $in['wetravel_key'] ) : '';
	if ( '-' === $key ) { $key = ''; }
	elseif ( '' === $key ) { $key = isset( $old['wetravel_key'] ) ? $old['wetravel_key'] : ''; }
	$out['wetravel_key'] = sanitize_text_field( $key );

	$pub = isset( $in['public_email'] ) ? sanitize_email( $in['public_email'] ) : '';
	$out['public_email'] = ( $pub && is_email( $pub ) ) ? $pub : '';

	return $out;
}

add_action( 'admin_menu', function () {
	add_options_page( 'YAWW forms', 'YAWW forms', 'manage_options', 'yaww-forms', 'yaww_forms_settings_page' );
} );

function yaww_forms_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$key_set = '' !== yaww_forms_setting( 'wetravel_key' );
	?>
	<div class="wrap">
		<h1>YAWW forms</h1>
		<p>Every form on the site (Contact, the retreat pop-up, the teacher application, the waitlist and the newsletter) is saved under <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=lead' ) ); ?>">Leads</a>, emailed to the addresses below and posted to Slack. Anything left empty is skipped.</p>
		<?php settings_errors( 'yaww_forms' ); ?>
		<?php if ( isset( $_GET['yaww_test'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification
			$r = get_transient( 'yaww_forms_test_result_' . get_current_user_id() );
			if ( is_array( $r ) ) : ?>
			<div class="notice notice-info is-dismissible"><p><strong>Test lead sent.</strong>
				<?php if ( ! empty( $r['lead_id'] ) ) : ?><a href="<?php echo esc_url( get_edit_post_link( $r['lead_id'] ) ); ?>">Open it</a>.<?php endif; ?></p>
				<ul style="list-style:disc;margin-left:20px">
				<?php foreach ( (array) $r['log'] as $step => $msg ) : ?>
					<li><?php echo esc_html( ucfirst( $step ) . ': ' . $msg ); ?></li>
				<?php endforeach; ?>
				</ul></div>
			<?php endif;
		endif; ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'yaww_forms' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="yf-recipients">Send form emails to</label></th>
					<td><textarea id="yf-recipients" name="yaww_forms[recipients]" rows="3" class="large-text code"><?php echo esc_textarea( yaww_forms_setting( 'recipients' ) ); ?></textarea>
					<p class="description">One or more addresses, separated by commas or new lines. Empty: the site admin email (<?php echo esc_html( get_option( 'admin_email' ) ); ?>). Replies go straight to the person who wrote.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="yf-slack">Slack incoming webhook</label></th>
					<td><input id="yf-slack" type="url" name="yaww_forms[slack_webhook]" value="<?php echo esc_attr( yaww_forms_setting( 'slack_webhook' ) ); ?>" class="large-text code" placeholder="https://hooks.slack.com/services/…">
					<p class="description">Slack → Apps → Incoming Webhooks → Add to a channel. Empty: Slack is skipped.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="yf-wetravel">WeTravel API key</label></th>
					<td><input id="yf-wetravel" type="password" name="yaww_forms[wetravel_key]" value="" class="regular-text code" autocomplete="new-password" placeholder="<?php echo $key_set ? esc_attr( 'Saved. Leave empty to keep it, type - to remove it.' ) : ''; ?>">
					<p class="description">Stored for later. WeTravel's public API (developer.wetravel.com) can list and read the leads captured on WeTravel, but has no call to create one, so website leads are not sent there yet. Developers can hook <code>yaww_lead_created</code>.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="yf-public">Public email on Contact</label></th>
					<td><input id="yf-public" type="email" name="yaww_forms[public_email]" value="<?php echo esc_attr( yaww_forms_setting( 'public_email' ) ); ?>" class="regular-text">
					<p class="description">Shown under the Contact lede. Empty: the page shows "[missing data: public email address]".</p></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<hr>
		<h2>Test</h2>
		<p>Sends a test lead ("Test · YAWW forms") through every step: saved under Leads, emailed, posted to Slack. Save your changes first.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="yaww_forms_test">
			<?php wp_nonce_field( 'yaww_forms_test' ); ?>
			<?php submit_button( 'Send a test lead', 'secondary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}

add_action( 'admin_post_yaww_forms_test', function () {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die( 'Not allowed.', 403 ); }
	check_admin_referer( 'yaww_forms_test' );
	$user = wp_get_current_user();
	$r    = yaww_forms_process( array(
		'form'       => 'contact',
		'topic'      => 'retreat',
		'retreat'    => 'not-sure',
		'first_name' => 'Test',
		'email'      => $user->user_email ? $user->user_email : get_option( 'admin_email' ),
		'message'    => 'A test lead sent from Settings → YAWW forms by ' . $user->display_name . '.',
		'reply_by'   => 'email',
		'page'       => 'wp-admin',
	), array( 'test' => true ) );
	set_transient( 'yaww_forms_test_result_' . get_current_user_id(), $r, 300 );
	wp_safe_redirect( admin_url( 'options-general.php?page=yaww-forms&yaww_test=1' ) );
	exit;
} );
