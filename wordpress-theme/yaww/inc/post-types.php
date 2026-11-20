<?php
/**
 * Custom post types and taxonomies. Kept in an mu-style include so it can be
 * lifted into wp-content/mu-plugins/ unchanged (content model should outlive
 * the theme); `if ( ! function_exists )` guards make the double load harmless.
 *
 * Journal = the built-in `post` type (home.php / single.php).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function () {
	$common = array(
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => true,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
	);

	register_post_type( 'retreat', array_merge( $common, array(
		'labels'    => array( 'name' => 'Retreats', 'singular_name' => 'Retreat', 'add_new_item' => 'Add retreat' ),
		'rewrite'   => array( 'slug' => 'retreats', 'with_front' => false ),
		'menu_icon' => 'dashicons-palmtree',
		'taxonomies' => array( 'retreat_tier' ),
	) ) );

	register_post_type( 'location', array_merge( $common, array(
		'labels'    => array( 'name' => 'Locations', 'singular_name' => 'Location', 'add_new_item' => 'Add location' ),
		'rewrite'   => array( 'slug' => 'locations', 'with_front' => false ),
		'menu_icon' => 'dashicons-location-alt',
	) ) );

	register_post_type( 'teacher', array_merge( $common, array(
		'labels'    => array( 'name' => 'Teachers', 'singular_name' => 'Teacher', 'add_new_item' => 'Add teacher' ),
		'rewrite'   => array( 'slug' => 'teachers', 'with_front' => false ),
		'menu_icon' => 'dashicons-groups',
	) ) );

	register_post_type( 'facilitator', array_merge( $common, array(
		'labels'    => array( 'name' => 'Facilitators', 'singular_name' => 'Facilitator', 'add_new_item' => 'Add facilitator' ),
		'rewrite'   => array( 'slug' => 'facilitators', 'with_front' => false ),
		'menu_icon' => 'dashicons-businessperson',
	) ) );

	/* Tier = the retreat collections: Signature Collection, The Reserve, Deep Dives. */
	register_taxonomy( 'retreat_tier', array( 'retreat' ), array(
		'labels'       => array( 'name' => 'Tiers', 'singular_name' => 'Tier' ),
		'public'       => true,
		'hierarchical' => false,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'tier', 'with_front' => false ),
	) );
}, 5 );

/* Journal lives at /journal/ (the static site's URL), not the blog front. */
add_filter( 'pre_option_page_for_posts', function ( $v ) {
	$p = get_page_by_path( 'journal' );
	return $p ? $p->ID : $v;
} );

/* Seed the three tiers once so the filter UI has terms to show. */
add_action( 'init', function () {
	if ( get_option( 'yaww_tiers_seeded' ) ) { return; }
	foreach ( array( 'signature-collection' => 'Signature Collection', 'the-reserve' => 'The Reserve', 'deep-dives' => 'Deep Dives' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'retreat_tier' ) ) { wp_insert_term( $name, 'retreat_tier', array( 'slug' => $slug ) ); }
	}
	update_option( 'yaww_tiers_seeded', 1 );
}, 20 );

add_action( 'after_switch_theme', function () { flush_rewrite_rules(); } );
