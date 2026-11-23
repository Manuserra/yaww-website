<?php
/**
 * wp-admin for the team: structured edit screens, useful list tables, a tidy menu and dashboard.
 *
 * - Retreat, location, teacher and facilitator use the classic edit screen (no block editor, no main
 *   text box): the title, then the ACF tabs right under it (acf-json/, from inc/fields-spec.json).
 *   Their body text lives in a field (yaww_body() in inc/helpers.php; bin/migrate-body.php moved it).
 * - On save the duplicates the site still reads are kept in step: featured image = hero photo
 *   (retreat, location), Tiers term = Collection (retreat), excerpt = card blurb (location, people).
 * - List tables with dates, place, people, status pills, counts, filters and a date order.
 * - Menu order, icons, dashboard widgets "Upcoming retreats" and "Latest leads".
 *
 * Nothing here runs on the front end except the post type labels/supports, which no template reads.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Post types with the structured (classic + tabs) edit screen. */
function yaww_admin_types() { return array( 'retreat', 'location', 'teacher', 'facilitator' ); }

/* ------------------------------------------------------------ edit screens */

add_filter( 'use_block_editor_for_post_type', function ( $use, $type ) {
	return in_array( $type, yaww_admin_types(), true ) ? false : $use;
}, 10, 2 );

add_action( 'init', function () {
	foreach ( yaww_admin_types() as $t ) {
		remove_post_type_support( $t, 'editor' );    // body text = a field in the first tabs
		remove_post_type_support( $t, 'excerpt' );   // = the card blurb field, synced on save
	}
	remove_post_type_support( 'retreat', 'thumbnail' );   // = Hero photo, synced on save
	remove_post_type_support( 'location', 'thumbnail' );
	remove_post_type_support( 'retreat', 'page-attributes' );   // retreats are ordered by date
}, 20 );

/* Labels and icons (admin only: the front end never reads them). */
add_filter( 'register_post_type_args', function ( $args, $type ) {
	if ( ! is_admin() ) { return $args; }
	$icons = array( 'retreat' => 'dashicons-palmtree', 'location' => 'dashicons-location-alt', 'teacher' => 'dashicons-universal-access',
		'facilitator' => 'dashicons-id-alt', 'post' => 'dashicons-book-alt' );
	if ( isset( $icons[ $type ] ) ) { $args['menu_icon'] = $icons[ $type ]; }
	$labels = isset( $args['labels'] ) ? (array) $args['labels'] : array();
	if ( in_array( $type, array( 'teacher', 'facilitator' ), true ) ) {
		$labels += array( 'featured_image' => 'Portrait photo', 'set_featured_image' => 'Choose the portrait photo',
			'remove_featured_image' => 'Remove the portrait photo', 'use_featured_image' => 'Use as portrait photo' );
	}
	$plural = array( 'retreat' => 'retreats', 'location' => 'locations', 'teacher' => 'teachers', 'facilitator' => 'facilitators' );
	if ( isset( $plural[ $type ] ) ) { $labels += array( 'edit_item' => 'Edit ' . rtrim( $plural[ $type ], 's' ), 'view_item' => 'View ' . rtrim( $plural[ $type ], 's' ) . ' page',
		'item_updated' => ucfirst( rtrim( $plural[ $type ], 's' ) ) . ' saved.', 'item_published' => ucfirst( rtrim( $plural[ $type ], 's' ) ) . ' published.' ); }
	if ( isset( $plural[ $type ] ) ) { $labels += array( 'search_items' => 'Search ' . $plural[ $type ], 'not_found' => 'No ' . $plural[ $type ] . ' found.' ); }
	if ( 'post' === $type ) {
		$labels = array_merge( $labels, array( 'name' => 'Journal', 'singular_name' => 'Journal post', 'menu_name' => 'Journal',
			'all_items' => 'All journal posts', 'add_new_item' => 'Add journal post', 'edit_item' => 'Edit journal post',
			'new_item' => 'New journal post', 'view_item' => 'View journal post', 'search_items' => 'Search the journal',
			'featured_image' => 'Top photo', 'set_featured_image' => 'Choose the top photo', 'remove_featured_image' => 'Remove the top photo' ) );
	}
	$args['labels'] = $labels;
	return $args;
}, 10, 2 );

/* The Tiers box duplicates the Collection field: the field is the one source, the term follows it. */
add_filter( 'register_taxonomy_args', function ( $args, $tax ) {
	if ( 'retreat_tier' === $tax ) {
		$args['meta_box_cb']        = false;
		$args['show_in_quick_edit'] = false;
		$args['show_admin_column']  = false;
		$args['show_in_menu']       = false;
	}
	return $args;
}, 10, 2 );

/* The "All dates" box filters on the publish date, which means nothing for these lists. */
add_filter( 'disable_months_dropdown', function ( $off, $type ) {
	return in_array( $type, yaww_admin_types(), true ) ? true : $off;
}, 10, 2 );

add_filter( 'enter_title_here', function ( $text, $post ) {
	$t = array( 'retreat' => 'Retreat name, e.g. Mesmerized in Morocco', 'location' => 'Place name, e.g. Thailand',
		'teacher' => 'Full name', 'facilitator' => 'Full name' );
	return isset( $t[ $post->post_type ] ) ? $t[ $post->post_type ] : $text;
}, 10, 2 );

/* Keep the copies the templates and SEO read in step with the fields (after ACF has saved). */
add_action( 'acf/save_post', function ( $post_id ) {
	if ( ! is_numeric( $post_id ) || wp_is_post_revision( $post_id ) ) { return; }
	$type = get_post_type( $post_id );
	if ( in_array( $type, array( 'retreat', 'location' ), true ) ) {
		$hero = (int) get_post_meta( $post_id, 'hero_image', true );
		if ( $hero ) { set_post_thumbnail( $post_id, $hero ); } else { delete_post_thumbnail( $post_id ); }
	}
	if ( 'retreat' === $type ) {
		$c = (string) get_post_meta( $post_id, 'collection', true );
		wp_set_object_terms( $post_id, $c ? array( $c ) : array(), 'retreat_tier' );
	}
	$blurb = array( 'location' => 'blurb', 'teacher' => 'short_bio', 'facilitator' => 'short_bio' );
	if ( isset( $blurb[ $type ] ) ) {
		global $wpdb;   // direct update: wp_update_post would run save_post (and ACF) again
		$wpdb->update( $wpdb->posts, array( 'post_excerpt' => (string) get_post_meta( $post_id, $blurb[ $type ], true ) ), array( 'ID' => $post_id ) );
		clean_post_cache( $post_id );
	}
}, 20 );

/* ------------------------------------------------------------ assets */

add_action( 'admin_enqueue_scripts', function () {
	$dir = YAWW_DIR . '/assets/admin/';
	wp_enqueue_style( 'yaww-admin', YAWW_URI . '/assets/admin/admin.css', array(), (string) filemtime( $dir . 'admin.css' ) );
	$screen = get_current_screen();
	if ( $screen && 'post' === $screen->base && in_array( $screen->post_type, yaww_admin_types(), true ) ) {
		wp_enqueue_script( 'yaww-admin', YAWW_URI . '/assets/admin/admin.js', array( 'jquery', 'acf-input' ), (string) filemtime( $dir . 'admin.js' ), true );
	}
} );

/* Journal posts keep the block editor; open the panel with their fields under the text by default. */
add_action( 'enqueue_block_editor_assets', function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->post_type ) { return; }
	wp_add_inline_script( 'wp-preferences', "wp.domReady(function(){ wp.data.dispatch('core/preferences').setDefaults('core/edit-post', { metaBoxesMainIsOpen: true, metaBoxesMainOpenHeight: Math.round( window.innerHeight * 0.45 ) }); });" );
} );

/* ------------------------------------------------------------ list tables */

/** Every retreat's people, place, dates and status, read once per request (for columns and counts). */
function yaww_admin_retreat_index() {
	static $idx = null;
	if ( null !== $idx ) { return $idx; }
	$idx = array();
	foreach ( get_posts( array( 'post_type' => 'retreat', 'post_status' => array( 'publish', 'draft', 'pending', 'future', 'private' ), 'numberposts' => -1, 'fields' => 'ids' ) ) as $id ) {
		$idx[ $id ] = array(
			'start'        => (string) get_post_meta( $id, 'start_date', true ),
			'location'     => (int) get_post_meta( $id, 'location_id', true ),
			'teachers'     => array_map( 'intval', (array) get_post_meta( $id, 'teacher_ids', true ) ),
			'facilitators' => array_map( 'intval', (array) get_post_meta( $id, 'facilitator_ids', true ) ),
			'status'       => (string) get_post_meta( $id, 'status', true ),
		);
	}
	return $idx;
}

/** Retreats linked to a place or person: [upcoming, all] (waitlist pages count as upcoming). */
function yaww_admin_retreat_count( $post_id, $what ) {
	$today = current_time( 'Ymd' );
	$up = 0; $all = 0;
	foreach ( yaww_admin_retreat_index() as $r ) {
		$hit = ( 'location' === $what ) ? $r['location'] === (int) $post_id : in_array( (int) $post_id, $r[ $what ], true );
		if ( ! $hit ) { continue; }
		$all++;
		if ( '' === $r['start'] || $r['start'] >= $today ) { $up++; }
	}
	return array( $up, $all );
}

function yaww_admin_status_labels() {
	return array( 'available' => 'Available', 'filling_fast' => 'Filling fast', 'spaces_left' => 'Spaces left',
		'sold_out' => 'Sold out', 'new' => 'New retreat', 'waitlist' => 'Waitlist' );
}

function yaww_admin_pill( $status ) {
	$l = yaww_admin_status_labels();
	$status = $status ? $status : 'available';
	return '<span class="yaww-pill yaww-pill--' . esc_attr( $status ) . '">' . esc_html( isset( $l[ $status ] ) ? $l[ $status ] : $status ) . '</span>';
}

function yaww_admin_thumb( $att_id, $round = false ) {
	$att_id = (int) $att_id;
	if ( ! $att_id ) { return '<span class="yaww-thumb yaww-thumb--empty' . ( $round ? ' yaww-thumb--round' : '' ) . '"></span>'; }
	return wp_get_attachment_image( $att_id, 'thumbnail', false, array( 'class' => 'yaww-thumb' . ( $round ? ' yaww-thumb--round' : '' ), 'alt' => '', 'loading' => 'lazy' ) );
}

function yaww_admin_dates( $id ) {
	$s = yaww_date_ymd( get_post_meta( $id, 'start_date', true ) );
	if ( ! $s ) { return '<span class="yaww-muted">No dates (waitlist)</span>'; }
	$e   = yaww_date_ymd( get_post_meta( $id, 'end_date', true ) );
	$out = esc_html( yaww_date_range( $s, $e ) );
	if ( str_replace( '-', '', $e ? $e : $s ) < current_time( 'Ymd' ) ) { $out = '<span class="yaww-muted">' . $out . ' · past</span>'; }
	return $out;
}

/* Retreats */
add_filter( 'manage_retreat_posts_columns', function ( $cols ) {
	return array( 'cb' => $cols['cb'], 'yaww_thumb' => '<span class="screen-reader-text">Photo</span>', 'title' => 'Retreat',
		'yaww_dates' => 'Dates', 'yaww_location' => 'Location', 'yaww_teachers' => 'Teacher(s)', 'yaww_collection' => 'Collection',
		'yaww_status' => 'Status', 'yaww_spaces' => 'Spaces left' );
} );
add_action( 'manage_retreat_posts_custom_column', function ( $col, $id ) {
	switch ( $col ) {
		case 'yaww_thumb':
			echo yaww_admin_thumb( get_post_meta( $id, 'card_image', true ) ?: get_post_meta( $id, 'hero_image', true ) ); // phpcs:ignore
			break;
		case 'yaww_dates':
			echo yaww_admin_dates( $id ); // phpcs:ignore
			break;
		case 'yaww_location':
			$l = (int) get_post_meta( $id, 'location_id', true );
			echo $l ? '<a href="' . esc_url( admin_url( 'edit.php?post_type=retreat&yaww_location=' . $l ) ) . '">' . esc_html( get_the_title( $l ) ) . '</a>' : '<span class="yaww-muted">none</span>';
			break;
		case 'yaww_teachers':
			$names = array();
			foreach ( array_filter( (array) get_post_meta( $id, 'teacher_ids', true ) ) as $t ) { $names[] = esc_html( get_the_title( $t ) ); }
			echo $names ? implode( '<br>', $names ) : '<span class="yaww-muted">to be announced</span>'; // phpcs:ignore
			break;
		case 'yaww_collection':
			$c = get_post_meta( $id, 'collection', true );
			echo esc_html( 'signature-collection' === $c ? 'Signature Collection' : ( 'deep-dives' === $c ? 'Deep Dives' : '' ) );
			break;
		case 'yaww_status':
			echo yaww_admin_pill( get_post_meta( $id, 'status', true ) ); // phpcs:ignore
			break;
		case 'yaww_spaces':
			$st = get_post_meta( $id, 'status', true );
			$n  = get_post_meta( $id, 'spaces_left', true );
			echo ( 'spaces_left' === $st && '' !== (string) $n ) ? (int) $n : '<span class="yaww-muted">–</span>'; // phpcs:ignore
			break;
	}
}, 10, 2 );
add_filter( 'manage_edit-retreat_sortable_columns', function ( $cols ) {
	$cols['yaww_dates']  = array( 'yaww_start', false );
	$cols['yaww_status'] = 'yaww_status';
	return $cols;
} );

/* Filters above the retreat list: location, status, collection. */
add_action( 'restrict_manage_posts', function ( $type ) {
	if ( 'retreat' !== $type ) { return; }
	$get = function ( $k ) { return isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : ''; }; // phpcs:ignore WordPress.Security.NonceVerification
	echo '<select name="yaww_location"><option value="">All locations</option>';
	foreach ( get_posts( array( 'post_type' => 'location', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'any' ) ) as $l ) {
		echo '<option value="' . (int) $l->ID . '"' . selected( $get( 'yaww_location' ), (string) $l->ID, false ) . '>' . esc_html( $l->post_title ) . '</option>';
	}
	echo '</select><select name="yaww_status"><option value="">All statuses</option>';
	foreach ( yaww_admin_status_labels() as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $get( 'yaww_status' ), $k, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select><select name="yaww_collection"><option value="">All collections</option>';
	foreach ( array( 'signature-collection' => 'Signature Collection', 'deep-dives' => 'Deep Dives' ) as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $get( 'yaww_collection' ), $k, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
} );

add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() ) { return; }
	$type = $q->get( 'post_type' );
	if ( in_array( $type, array( 'location', 'teacher', 'facilitator' ), true ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );   // the order the site shows them in
	}
	if ( 'retreat' !== $type ) { return; }
	$get = function ( $k ) { return isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : ''; }; // phpcs:ignore WordPress.Security.NonceVerification
	$mq  = array();
	if ( $get( 'yaww_location' ) ) { $mq[] = array( 'key' => 'location_id', 'value' => (int) $get( 'yaww_location' ) ); }
	if ( $get( 'yaww_collection' ) ) { $mq[] = array( 'key' => 'collection', 'value' => $get( 'yaww_collection' ) ); }
	if ( $get( 'yaww_status' ) ) {
		$mq[] = 'available' === $get( 'yaww_status' )
			? array( 'relation' => 'OR', array( 'key' => 'status', 'value' => 'available' ), array( 'key' => 'status', 'value' => '' ), array( 'key' => 'status', 'compare' => 'NOT EXISTS' ) )
			: array( 'key' => 'status', 'value' => $get( 'yaww_status' ) );
	}
	if ( $mq ) { $q->set( 'meta_query', $mq ); }
	$ob = $q->get( 'orderby' );
	if ( 'yaww_status' === $ob ) { $q->set( 'meta_key', 'status' ); $q->set( 'orderby', 'meta_value' ); }
	if ( ! $ob || 'yaww_start' === $ob ) { $q->set( 'yaww_date_order', $ob ? strtoupper( $q->get( 'order' ) ) : 'DEFAULT' ); }
} );

/* Date order. Default: upcoming soonest first, then waitlist (no dates), then past (latest first). */
add_filter( 'posts_clauses', function ( $c, $q ) {
	$mode = $q->get( 'yaww_date_order' );
	if ( ! $mode ) { return $c; }
	global $wpdb;
	$c['join'] .= " LEFT JOIN {$wpdb->postmeta} yaww_sd ON ( yaww_sd.post_id = {$wpdb->posts}.ID AND yaww_sd.meta_key = 'start_date' )";
	$today = esc_sql( current_time( 'Ymd' ) );
	$empty = "( yaww_sd.meta_value IS NULL OR yaww_sd.meta_value = '' )";
	if ( 'DEFAULT' === $mode ) {
		$c['orderby'] = "CASE WHEN $empty THEN 1 WHEN yaww_sd.meta_value >= '$today' THEN 0 ELSE 2 END ASC,"
			. " CASE WHEN yaww_sd.meta_value >= '$today' THEN yaww_sd.meta_value END ASC, yaww_sd.meta_value DESC, {$wpdb->posts}.post_title ASC";
	} else {
		$dir = 'DESC' === $mode ? 'DESC' : 'ASC';
		$c['orderby'] = "$empty ASC, yaww_sd.meta_value $dir, {$wpdb->posts}.post_title ASC";
	}
	return $c;
}, 10, 2 );

/* Locations */
add_filter( 'manage_location_posts_columns', function ( $cols ) {
	return array( 'cb' => $cols['cb'], 'yaww_thumb' => '<span class="screen-reader-text">Photo</span>', 'title' => 'Location',
		'yaww_region' => 'Region', 'yaww_country' => 'Country', 'yaww_upcoming' => 'Upcoming retreats' );
} );
add_action( 'manage_location_posts_custom_column', function ( $col, $id ) {
	if ( 'yaww_thumb' === $col ) { echo yaww_admin_thumb( get_post_meta( $id, 'card_image', true ) ?: get_post_meta( $id, 'hero_image', true ) ); } // phpcs:ignore
	if ( 'yaww_region' === $col ) { echo esc_html( get_post_meta( $id, 'region', true ) ); }
	if ( 'yaww_country' === $col ) { echo esc_html( get_post_meta( $id, 'country', true ) ); }
	if ( 'yaww_upcoming' === $col ) {
		list( $up, $all ) = yaww_admin_retreat_count( $id, 'location' );
		$url = admin_url( 'edit.php?post_type=retreat&yaww_location=' . (int) $id );
		echo $all ? '<a href="' . esc_url( $url ) . '">' . (int) $up . '</a> <span class="yaww-muted">of ' . (int) $all . '</span>' : '<span class="yaww-muted">0</span>'; // phpcs:ignore
	}
}, 10, 2 );
add_filter( 'manage_edit-location_sortable_columns', function ( $cols ) { $cols['yaww_region'] = 'yaww_region'; return $cols; } );
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && 'yaww_region' === $q->get( 'orderby' ) ) { $q->set( 'meta_key', 'region' ); $q->set( 'orderby', 'meta_value' ); }
} );

/* Teachers and facilitators */
foreach ( array( 'teacher', 'facilitator' ) as $yaww_t ) {
	add_filter( "manage_{$yaww_t}_posts_columns", function ( $cols ) use ( $yaww_t ) {
		$out = array( 'cb' => $cols['cb'], 'yaww_thumb' => '<span class="screen-reader-text">Photo</span>', 'title' => 'Name', 'yaww_retreats' => 'Retreats' );
		$out[ 'facilitator' === $yaww_t ? 'yaww_listed' : 'yaww_ready' ] = 'facilitator' === $yaww_t ? 'On Facilitators page' : 'Profile';
		return $out;
	} );
	add_action( "manage_{$yaww_t}_posts_custom_column", function ( $col, $id ) use ( $yaww_t ) {
		if ( 'yaww_thumb' === $col ) { echo yaww_admin_thumb( get_post_thumbnail_id( $id ), true ); } // phpcs:ignore
		if ( 'yaww_ready' === $col ) { echo get_post_meta( $id, 'needs_content', true ) ? '<span class="yaww-pill yaww-pill--filling_fast">Not ready</span>' : 'Ready'; } // phpcs:ignore
		if ( 'yaww_retreats' === $col ) {
			list( $up, $all ) = yaww_admin_retreat_count( $id, 'teacher' === $yaww_t ? 'teachers' : 'facilitators' );
			echo $all ? (int) $up . ' upcoming <span class="yaww-muted">of ' . (int) $all . '</span>' : '<span class="yaww-muted">0</span>'; // phpcs:ignore
		}
		if ( 'yaww_listed' === $col ) { echo get_post_meta( $id, 'listed', true ) ? 'Yes' : '<span class="yaww-muted">No</span>'; } // phpcs:ignore
	}, 10, 2 );
}
unset( $yaww_t );

/* ------------------------------------------------------------ menu */

add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', function ( $order ) {
	$first = array( 'index.php', 'separator1', 'edit.php?post_type=retreat', 'edit.php?post_type=location', 'edit.php?post_type=teacher',
		'edit.php?post_type=facilitator', 'edit.php', 'edit.php?post_type=lead', 'separator2', 'edit.php?post_type=page', 'upload.php' );
	$first = array_values( array_intersect( $first, $order ) );
	return array_merge( $first, array_values( array_diff( $order, $first ) ) );
} );

add_filter( 'admin_footer_text', function () { return 'Yoga Adventures Worldwide · website admin'; } );

/* ------------------------------------------------------------ dashboard */

add_action( 'wp_dashboard_setup', function () {
	foreach ( array( 'dashboard_primary' => 'side', 'dashboard_quick_press' => 'side', 'dashboard_site_health' => 'normal',
		'dashboard_activity' => 'normal', 'dashboard_right_now' => 'normal', 'dashboard_php_nag' => 'normal' ) as $id => $ctx ) {
		remove_meta_box( $id, 'dashboard', $ctx );
	}
	wp_add_dashboard_widget( 'yaww_upcoming', 'Upcoming retreats', 'yaww_admin_widget_upcoming' );
	wp_add_dashboard_widget( 'yaww_leads', 'Latest leads', 'yaww_admin_widget_leads', null, null, 'side' );
} );
add_action( 'admin_init', function () { remove_action( 'welcome_panel', 'wp_welcome_panel' ); } );

/* The site has no comments: no Comments menu or admin-bar bubble. */
add_action( 'admin_menu', function () { remove_menu_page( 'edit-comments.php' ); }, 99 );
add_action( 'admin_bar_menu', function ( $bar ) { $bar->remove_node( 'comments' ); }, 99 );

function yaww_admin_widget_upcoming() {
	$ids = get_posts( array( 'post_type' => 'retreat', 'post_status' => array( 'publish', 'draft', 'future', 'pending' ), 'numberposts' => 5, 'fields' => 'ids',
		'meta_key' => 'start_date', 'orderby' => 'meta_value', 'order' => 'ASC',
		'meta_query' => array( array( 'key' => 'start_date', 'value' => current_time( 'Ymd' ), 'compare' => '>=' ) ) ) );
	if ( ! $ids ) { echo '<p>No upcoming retreats.</p>'; return; }
	echo '<table class="yaww-dash"><tbody>';
	foreach ( $ids as $id ) {
		$loc = (int) get_post_meta( $id, 'location_id', true );
		echo '<tr><td><a href="' . esc_url( get_edit_post_link( $id ) ) . '"><strong>' . esc_html( get_the_title( $id ) ) . '</strong></a>'
			. ( 'publish' !== get_post_status( $id ) ? ' <span class="yaww-muted">(' . esc_html( get_post_status( $id ) ) . ')</span>' : '' )
			. '<br><span class="yaww-muted">' . yaww_admin_dates( $id ) . ( $loc ? ' · ' . esc_html( get_the_title( $loc ) ) : '' ) . '</span></td>' // phpcs:ignore
			. '<td class="yaww-dash__status">' . yaww_admin_pill( get_post_meta( $id, 'status', true ) )
			. ( 'spaces_left' === get_post_meta( $id, 'status', true ) ? '<br><span class="yaww-muted">' . (int) get_post_meta( $id, 'spaces_left', true ) . ' left</span>' : '' ) . '</td></tr>';
	}
	echo '</tbody></table><p class="yaww-dash__more"><a href="' . esc_url( admin_url( 'edit.php?post_type=retreat' ) ) . '">All retreats</a> · <a href="' . esc_url( admin_url( 'post-new.php?post_type=retreat' ) ) . '">Add a retreat</a></p>';
}

function yaww_admin_widget_leads() {
	if ( ! post_type_exists( 'lead' ) ) { echo '<p>Leads are not set up.</p>'; return; }
	$leads = get_posts( array( 'post_type' => 'lead', 'post_status' => 'any', 'numberposts' => 5 ) );
	if ( ! $leads ) { echo '<p>No leads yet. Every form on the site lands here.</p>'; return; }
	$types = function_exists( 'yaww_forms_types' ) ? yaww_forms_types() : array();
	echo '<table class="yaww-dash"><tbody>';
	foreach ( $leads as $l ) {
		$form  = get_post_meta( $l->ID, 'lead_form', true );
		$email = get_post_meta( $l->ID, 'lead_email', true );
		$about = get_post_meta( $l->ID, 'lead_about', true );
		$name  = trim( explode( ' · ', $l->post_title )[1] ?? $l->post_title );
		echo '<tr><td><a href="' . esc_url( get_edit_post_link( $l->ID ) ) . '"><strong>' . esc_html( $name ) . '</strong></a>'
			. '<br><span class="yaww-muted">' . esc_html( ( isset( $types[ $form ] ) ? $types[ $form ] : $form ) . ( $about ? ' · ' . $about : '' ) ) . '</span></td>'
			. '<td class="yaww-dash__status"><span class="yaww-muted">' . esc_html( human_time_diff( get_post_time( 'U', true, $l ), time() ) ) . ' ago</span>'
			. ( $email ? '<br><a href="mailto:' . esc_attr( $email ) . '">Email</a>' : '' ) . '</td></tr>';
	}
	echo '</tbody></table><p class="yaww-dash__more"><a href="' . esc_url( admin_url( 'edit.php?post_type=lead' ) ) . '">All leads</a></p>';
}
