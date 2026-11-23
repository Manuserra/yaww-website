<?php
/**
 * One-off, idempotent: copies the post content of retreats, locations, teachers and facilitators into
 * their body field (retreat "About this retreat" = about, location "Intro text" = intro, teacher and
 * facilitator "Full bio" = bio). Those edit screens have no main text box any more (inc/admin.php).
 * A post whose field already exists is skipped, so edits made in wp-admin are never overwritten.
 * The post content is left as it is (a frozen copy; nothing reads it once the field exists).
 * Run: bash wordpress-theme/yaww/bin/wp.sh eval-file wordpress-theme/yaww/bin/migrate-body.php
 */
if ( ! function_exists( 'update_field' ) ) { echo "ACF Pro is not active\n"; return; }
$done = 0; $skipped = 0;
foreach ( array( 'retreat' => 'about', 'location' => 'intro', 'teacher' => 'bio', 'facilitator' => 'bio' ) as $type => $key ) {
	foreach ( get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'numberposts' => -1 ) ) as $p ) {
		if ( metadata_exists( 'post', $p->ID, $key ) ) { $skipped++; continue; }
		update_field( 'field_yaww_' . $type . '_' . $key, $p->post_content, $p->ID );
		$saved = (string) get_post_meta( $p->ID, $key, true );
		echo ( $saved === $p->post_content ? 'copied   ' : 'CHANGED  ' ) . "$type {$p->post_name}\n";
		$done++;
	}
}
echo "$done copied, $skipped already had the field\n";
