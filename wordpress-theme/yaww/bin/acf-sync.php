<?php
/**
 * Loads acf-json/group_yaww_*.json into the database (same as Custom Fields > Sync in wp-admin).
 * Updates the existing group by its key, never creates a second copy. ACF re-saves the JSON afterwards.
 * Run: bash wordpress-theme/yaww/bin/wp.sh eval-file wordpress-theme/yaww/bin/acf-sync.php
 */
if ( ! function_exists( 'acf_import_field_group' ) ) { echo "ACF Pro is not active\n"; return; }
foreach ( glob( get_template_directory() . '/acf-json/group_yaww_*.json' ) as $file ) {
	$g   = json_decode( file_get_contents( $file ), true );
	$ids = get_posts( array( 'post_type' => 'acf-field-group', 'post_status' => 'any', 'name' => $g['key'], 'fields' => 'ids', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) );
	if ( $ids ) { $g['ID'] = $ids[0]; }
	$r = acf_import_field_group( $g );
	echo $g['key'] . ' -> #' . $r['ID'] . ' (' . count( acf_get_fields( $r['key'] ) ) . " fields)\n";
}
