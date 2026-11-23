<?php
/**
 * Native custom fields. Reads inc/fields-spec.json (also the source of the
 * acf-json/ export) and registers every key with register_post_meta +
 * show_in_rest, so the REST API and WP-CLI import work without ACF Pro.
 * A basic meta box edits them until ACF Pro takes over (it is skipped when ACF is active).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function yaww_field_spec() {
	static $spec = null;
	if ( null === $spec ) {
		$spec = json_decode( file_get_contents( __DIR__ . '/fields-spec.json' ), true );
	}
	return $spec;
}

/** Spec field -> REST/meta schema. */
function yaww_field_schema( $f ) {
	switch ( $f['type'] ) {
		case 'number': case 'image': case 'post':
			return array( 'type' => 'number' );
		case 'bool':
			return array( 'type' => 'boolean' );
		case 'posts': case 'gallery':
			return array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) );
		case 'list':
			return array( 'type' => 'array', 'items' => array( 'type' => 'string' ) );
		case 'rows':
			$props = array();
			foreach ( $f['sub'] as $s ) {
				$sch = yaww_field_schema( $s );
				$props[ $s['key'] ] = $sch;
			}
			return array( 'type' => 'array', 'items' => array( 'type' => 'object', 'properties' => $props, 'additionalProperties' => true ) );
		default:
			return array( 'type' => 'string' );
	}
}

/** ACF Pro active: it owns storage, the editor UI and REST (field groups have show_in_rest). */
function yaww_acf_active() { return function_exists( 'acf_get_field_group' ); }

add_action( 'init', function () {
	// With ACF, repeaters are stored as row counts plus `rooms_0_name` keys; registering the
	// array schemas natively would make REST reject those values. Native meta is the no-ACF fallback.
	if ( yaww_acf_active() ) { return; }
	foreach ( yaww_field_spec() as $post_type => $fields ) {
		if ( '_' === substr( $post_type, 0, 1 ) ) { continue; }
		foreach ( $fields as $f ) {
			$schema = yaww_field_schema( $f );
			register_post_meta( $post_type, $f['key'], array(
				'single'            => true,
				'type'              => $schema['type'],
				'show_in_rest'      => ( 'array' === $schema['type'] ) ? array( 'schema' => $schema ) : true,
				'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
				'sanitize_callback' => null,
			) );
		}
	}
}, 10 );

/* --- Stop-gap editor: scalar fields as inputs, arrays as JSON. Replaced by ACF Pro. --- */
add_action( 'add_meta_boxes', function () {
	if ( yaww_acf_active() ) { return; }
	foreach ( array_keys( yaww_field_spec() ) as $pt ) {
		if ( '_' === substr( $pt, 0, 1 ) ) { continue; }
		add_meta_box( 'yaww_fields', 'YAWW fields', 'yaww_render_metabox', $pt, 'normal', 'high' );
	}
} );

function yaww_render_metabox( $post ) {
	wp_nonce_field( 'yaww_fields', 'yaww_fields_nonce' );
	foreach ( yaww_field_spec()[ $post->post_type ] as $f ) {
		$v = get_post_meta( $post->ID, $f['key'], true );
		$id = 'yaww_f_' . $f['key'];
		echo '<p><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $f['label'] ) . '</strong> <code>' . esc_html( $f['key'] ) . '</code></label><br>';
		switch ( $f['type'] ) {
			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '"><option value=""></option>';
				foreach ( $f['choices'] as $c ) { printf( '<option value="%1$s"%2$s>%1$s</option>', esc_attr( $c ), selected( $v, $c, false ) ); }
				echo '</select>';
				break;
			case 'bool':
				echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="1"' . checked( (bool) $v, true, false ) . '>';
				break;
			case 'textarea': case 'wysiwyg':
				echo '<textarea class="widefat" rows="4" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '">' . esc_textarea( $v ) . '</textarea>';
				break;
			case 'list': case 'rows': case 'gallery': case 'posts':
				echo '<textarea class="widefat code" rows="5" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" placeholder="JSON">' . esc_textarea( $v ? wp_json_encode( $v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) : '' ) . '</textarea>';
				break;
			default:
				echo '<input class="widefat" type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $v ) . '">';
		}
		echo '</p>';
	}
}

add_action( 'save_post', function ( $post_id ) {
	if ( yaww_acf_active() ) { return; }
	if ( ! isset( $_POST['yaww_fields_nonce'] ) || ! wp_verify_nonce( $_POST['yaww_fields_nonce'], 'yaww_fields' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	$spec = yaww_field_spec();
	$pt = get_post_type( $post_id );
	if ( empty( $spec[ $pt ] ) ) { return; }
	foreach ( $spec[ $pt ] as $f ) {
		$name = 'yaww_f_' . $f['key'];
		$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
		switch ( $f['type'] ) {
			case 'bool':   $val = isset( $_POST[ $name ] ); break;
			case 'number': case 'image': case 'post': $val = ( '' === $raw ) ? '' : (float) $raw; break;
			case 'list': case 'rows': case 'gallery': case 'posts':
				$val = json_decode( $raw, true ); if ( ! is_array( $val ) ) { $val = array(); } break;
			case 'url':    $val = esc_url_raw( $raw ); break;
			case 'wysiwyg': $val = wp_kses_post( $raw ); break;
			default:       $val = sanitize_textarea_field( $raw );
		}
		if ( '' === $val || array() === $val ) { delete_post_meta( $post_id, $f['key'] ); } else { update_post_meta( $post_id, $f['key'], $val ); }
	}
} );

/* Load the acf-json/ field groups from the theme once ACF Pro is installed. */
add_filter( 'acf/settings/load_json', function ( $paths ) { $paths[] = get_template_directory() . '/acf-json'; return $paths; } );
add_filter( 'acf/settings/save_json', function () { return get_template_directory() . '/acf-json'; } );
