<?php
/**
 * Import the static site into WordPress. Idempotent: every post carries `_yaww_source_id`
 * and every attachment `_yaww_source` (its repo path), so a re-run updates in place.
 *
 *   bash wordpress-theme/yaww/bin/import.sh              # export-content.py + this file
 *   bash wordpress-theme/yaww/bin/wp.sh eval-file wordpress-theme/yaww/bin/import.php [only=media,locations,...]
 *
 * Reads bin/content.json (written by bin/export-content.py). Needs ACF Pro: values are written
 * with update_field() on the field keys of acf-json/, so ACF's editor shows them as entered.
 *
 * Media: each file is copied once to uploads/yaww/<path under images/>, keeping its file name
 * (css/photo-focus.css matches on names). A retina twin (<name>@2x.<ext>) is copied beside it
 * and registered on the 1x attachment as the image size '2x' (no second Media Library item).
 */
if ( ! defined( 'ABSPATH' ) ) { fwrite( STDERR, "Run through wp eval-file.\n" ); exit( 1 ); }
if ( ! function_exists( 'update_field' ) ) { WP_CLI::error( 'ACF Pro must be active.' ); }
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';

$YAWW_THEME = dirname( __DIR__ );
$YAWW_REPO  = dirname( dirname( $YAWW_THEME ) );
$GLOBALS['YAWW_REPO'] = $YAWW_REPO;   // wp eval-file runs this file inside a function
$C = json_decode( file_get_contents( $YAWW_THEME . '/bin/content.json' ), true );
if ( ! $C ) { WP_CLI::error( 'bin/content.json missing or invalid: run bin/export-content.py first.' ); }

$only = array();
foreach ( (array) ( isset( $args ) ? $args : array() ) as $a ) {
	if ( 0 === strpos( $a, 'only=' ) ) { $only = explode( ',', substr( $a, 5 ) ); }
}
$want = function ( $part ) use ( $only ) { return ! $only || in_array( $part, $only, true ); };

/* B (URLs/SEO): journal posts keep the live site's slugs. */
$JOURNAL_SLUGS = array(
	'following-my-heart' => 'following-your-heart', 'yogis-guide-how-to-meditate' => 'how-to-meditate',
	'i-include-myself-gay-havana' => 'gay-havana', 'synergy-of-travel-and-retreats' => 'travel_and_yoga',
	'on-strong-hearts' => 'stong_hearts', 'finding-stillness-a-practice' => 'finding-stillness',
	'the-utopian-newsletter-cuban-revolution-issue' => 'the-utopian-newsletter-cuban-revolution-issue-july-25-2018',
);

$GLOBALS['stats'] = array( 'media_new' => 0, 'media_reused' => 0 );

/* ------------------------------------------------------------------ media -- */

function yaww_imp_twin( $rel ) {
	$two = preg_replace( '/(\.[a-z0-9]+)$/i', '@2x$1', $rel );
	return $two;
}

/** Attachment id for a repo image path (images/...). $rel2x: retina twin; true = look for <name>@2x. */
function yaww_imp_media( $rel, $alt = '', $rel2x = null ) {
	$YAWW_REPO = $GLOBALS['YAWW_REPO'];
	$stats = &$GLOBALS['stats'];
	if ( ! $rel ) { return 0; }
	$rel = ltrim( preg_replace( '#^\.\./#', '', $rel ), '/' );
	$src = $YAWW_REPO . '/' . $rel;
	if ( ! file_exists( $src ) ) { WP_CLI::warning( "missing file: $rel" ); return 0; }
	if ( true === $rel2x ) { $rel2x = file_exists( $YAWW_REPO . '/' . yaww_imp_twin( $rel ) ) ? yaww_imp_twin( $rel ) : null; }

	$found = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids',
		'meta_key' => '_yaww_source', 'meta_value' => $rel ) );
	$up   = wp_upload_dir();
	$sub  = 'yaww/' . preg_replace( '#^images/#', '', $rel );
	$dest = $up['basedir'] . '/' . $sub;
	if ( $found ) {
		$id = (int) $found[0];
		$stats['media_reused']++;
		if ( ! file_exists( $dest ) || md5_file( $dest ) !== md5_file( $src ) ) {   // file changed in the repo: refresh
			wp_mkdir_p( dirname( $dest ) );
			copy( $src, $dest );
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
		}
	} else {
		wp_mkdir_p( dirname( $dest ) );
		copy( $src, $dest );
		$ft = wp_check_filetype( $dest );
		$id = wp_insert_attachment( array(
			'guid' => $up['baseurl'] . '/' . $sub, 'post_mime_type' => $ft['type'],
			'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $rel ) ), 'post_status' => 'inherit',
		), $dest, 0, true );
		if ( is_wp_error( $id ) ) { WP_CLI::warning( $id->get_error_message() . " ($rel)" ); return 0; }
		update_post_meta( $id, '_yaww_source', $rel );
		update_post_meta( $id, '_yaww_md5', md5_file( $src ) );
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
		$stats['media_new']++;
	}
	if ( $alt && ! get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', wp_strip_all_tags( $alt ) );
	}
	if ( $rel2x ) {
		$meta = wp_get_attachment_metadata( $id );
		$s2   = $YAWW_REPO . '/' . $rel2x;
		$d2   = dirname( $dest ) . '/' . basename( $rel2x );
		if ( file_exists( $s2 ) && ( empty( $meta['sizes']['2x'] ) || ! file_exists( $d2 ) || filesize( $d2 ) !== filesize( $s2 ) ) ) {
			copy( $s2, $d2 );
			$sz = getimagesize( $d2 );
			if ( ! is_array( $meta ) ) { $meta = array(); }
			$meta['sizes']['2x'] = array( 'file' => basename( $d2 ), 'width' => $sz[0], 'height' => $sz[1],
				'mime-type' => $sz['mime'], 'filesize' => filesize( $d2 ) );
			wp_update_attachment_metadata( $id, $meta );
			update_post_meta( $id, '_yaww_source_2x', $rel2x );
		}
	}
	return (int) $id;
}

function yaww_imp_img( $img, $two = null ) {   // {src, alt[, src2x]} or a path
	if ( ! $img ) { return 0; }
	if ( is_string( $img ) ) { return yaww_imp_media( $img, '', $two ); }
	return yaww_imp_media( $img['src'], isset( $img['alt'] ) ? $img['alt'] : '', ! empty( $img['src2x'] ) ? $img['src2x'] : $two );
}

function yaww_imp_gallery( $list ) {
	$ids = array();
	foreach ( (array) $list as $g ) {
		$id = is_array( $g ) ? yaww_imp_img( $g ) : yaww_imp_media( $g );
		if ( $id ) { $ids[] = $id; }
	}
	return $ids;
}

/* ------------------------------------------------------------------ posts -- */

/** Create or update a post keyed by `_yaww_source_id`. Returns its id. */
function yaww_imp_post( $type, $source_id, $data ) {
	$found = get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids',
		'meta_key' => '_yaww_source_id', 'meta_value' => $source_id ) );
	$data = array_merge( array( 'post_type' => $type, 'post_status' => 'publish' ), $data );
	if ( $found ) {
		$data['ID'] = (int) $found[0];
		$id = wp_update_post( wp_slash( $data ), true );
	} else {
		$id = wp_insert_post( wp_slash( $data ), true );
	}
	if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() . " ($type $source_id)" ); }
	update_post_meta( $id, '_yaww_source_id', $source_id );
	return (int) $id;
}

/** ACF write by field key: field_yaww_<post type>_<name>. Empty values clear the field. */
function yaww_imp_fields( $type, $id, $values ) {
	foreach ( $values as $name => $v ) {
		if ( null === $v ) { $v = ''; }
		$r = update_field( 'field_yaww_' . $type . '_' . $name, $v, $id );
		if ( false === $r && '' !== $v && array() !== $v && get_field( 'field_yaww_' . $type . '_' . $name, $id, false ) === null ) {
			WP_CLI::warning( "field not written: $type.$name on $id" );
		}
	}
}

function yaww_imp_list( $a ) { return array_map( function ( $x ) { return array( 'value' => $x ); }, array_values( (array) $a ) ); }
function yaww_imp_paras( $a ) {
	return implode( "\n\n", array_map( function ( $p ) { return '<p>' . esc_html( $p ) . '</p>'; }, (array) $a ) );
}
function yaww_imp_ymd( $d ) { return $d ? str_replace( '-', '', $d ) : ''; }

$ids = array( 'location' => array(), 'teacher' => array(), 'facilitator' => array(), 'retreat' => array() );
foreach ( array_keys( $ids ) as $pt ) {   // existing ids, for partial runs (only=...)
	foreach ( get_posts( array( 'post_type' => $pt, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $pid ) {
		$ids[ $pt ][ get_post_meta( $pid, '_yaww_source_id', true ) ] = $pid;
	}
}

update_option( 'yaww_regions', $C['regions'] );
update_option( 'yaww_teachers_intro', $C['teachers_intro'] );
update_option( 'yaww_team_intro', $C['team_intro'] );

/* -------------------------------------------------------------- locations -- */
if ( $want( 'locations' ) ) {
	foreach ( $C['locations'] as $L ) {
		$id = yaww_imp_post( 'location', $L['source_id'], array( 'post_title' => $L['title'], 'post_name' => $L['slug'],
			'post_content' => yaww_imp_paras( $L['intro'] ), 'post_excerpt' => (string) $L['blurb'], 'menu_order' => $L['menu_order'] ) );
		$ids['location'][ $L['source_id'] ] = $id;
		yaww_imp_fields( 'location', $id, array(
			'display_name' => $L['display_name'], 'card_name' => $L['card_name'], 'short_name' => $L['short_name'],
			'region' => $L['region'], 'country' => $L['country'], 'placeholder_hue' => $L['placeholder_hue'],
			'hero_image' => yaww_imp_img( $L['hero_image'] ), 'card_image' => yaww_imp_img( $L['card_image'] ),
			'eyebrow' => $L['eyebrow'], 'tagline' => $L['tagline'], 'blurb' => $L['blurb'], 'facts' => $L['facts'],
			'intro_title' => $L['intro_title'], 'intro_image' => yaww_imp_img( $L['intro_image'] ),
			'guide' => array_map( function ( $g ) { $g['image'] = yaww_imp_img( $g['image'] ); return $g; }, $L['guide'] ),
			'know' => $L['know'], 'quote_text' => $L['quote_text'], 'quote_name' => $L['quote_name'], 'quote_source' => $L['quote_source'],
		) );
		if ( $L['hero_image'] ) { set_post_thumbnail( $id, yaww_imp_img( $L['hero_image'] ) ); }
		WP_CLI::log( "location  {$L['slug']}" );
	}
}

/* ------------------------------------------------------------ people -- */
if ( $want( 'people' ) ) {
	foreach ( $C['teachers'] as $T ) {
		$id = yaww_imp_post( 'teacher', $T['source_id'], array( 'post_title' => $T['title'], 'post_name' => $T['slug'],
			'post_content' => yaww_imp_paras( $T['bio'] ), 'post_excerpt' => (string) $T['short_bio'], 'menu_order' => $T['menu_order'] ) );
		$ids['teacher'][ $T['source_id'] ] = $id;
		$photo = yaww_imp_media( $T['photo'], $T['title'] );
		if ( $photo ) { set_post_thumbnail( $id, $photo ); } else { delete_post_thumbnail( $id ); }
		yaww_imp_fields( 'teacher', $id, array(
			'short_name' => $T['short_name'], 'role' => $T['role'], 'short_bio' => $T['short_bio'], 'host_bio' => $T['host_bio'],
			'quote' => $T['quote'], 'specialties' => yaww_imp_list( $T['specialties'] ), 'youtube' => $T['youtube'],
			'spotify_url' => $T['spotify_url'], 'website' => $T['website'], 'website_label' => $T['website_label'],
			'instagram' => $T['instagram'], 'avatar' => yaww_imp_media( $T['avatar'], $T['title'] ),
			'photo_position' => $T['photo_position'], 'needs_content' => $T['needs_content'] ? 1 : 0,
		) );
		WP_CLI::log( "teacher   {$T['slug']}" );
	}
	foreach ( $C['facilitators'] as $F ) {
		$id = yaww_imp_post( 'facilitator', $F['source_id'], array( 'post_title' => $F['title'], 'post_name' => $F['slug'],
			'post_content' => yaww_imp_paras( $F['bio'] ), 'post_excerpt' => (string) $F['short_bio'], 'menu_order' => $F['menu_order'] ) );
		$ids['facilitator'][ $F['source_id'] ] = $id;
		$photo = yaww_imp_media( $F['photo'], $F['title'] );
		if ( $photo ) { set_post_thumbnail( $id, $photo ); }
		yaww_imp_fields( 'facilitator', $id, array( 'role' => $F['role'], 'short_bio' => $F['short_bio'],
			'listed' => $F['listed'] ? 1 : 0, 'photo_position' => $F['photo_position'] ) );
		WP_CLI::log( "facilitator {$F['slug']}" );
	}
}

/* ------------------------------------------------------------- retreats -- */
if ( $want( 'retreats' ) ) {
	foreach ( $C['retreats'] as $i => $R ) {
		$id = yaww_imp_post( 'retreat', $R['source_id'], array( 'post_title' => $R['title'], 'post_name' => $R['slug'],
			'post_content' => implode( "\n\n", array_map( function ( $p ) { return '<p>' . $p . '</p>'; }, $R['about'] ) ),
			'menu_order' => $i ) );
		$ids['retreat'][ $R['source_id'] ] = $id;
		$map = function ( $type, $list ) use ( &$ids ) {
			return array_values( array_filter( array_map( function ( $s ) use ( &$ids, $type ) { return isset( $ids[ $type ][ $s ] ) ? $ids[ $type ][ $s ] : 0; }, (array) $list ) ) );
		};
		$card = $R['card_image'] ? yaww_imp_media( $R['card_image'], $R['title'], $R['card_image_2x'] ) : 0;
		$hero = yaww_imp_img( $R['hero'] );
		if ( $hero ) { set_post_thumbnail( $id, $hero ); }
		yaww_imp_fields( 'retreat', $id, array(
			'subtitle' => $R['subtitle'], 'location_label' => $R['location_label'], 'waitlist_note' => $R['waitlist_note'],
			'location_id' => isset( $ids['location'][ $R['location'] ] ) ? $ids['location'][ $R['location'] ] : '',
			'teacher_ids' => $map( 'teacher', $R['teachers'] ), 'facilitator_ids' => $map( 'facilitator', $R['facilitators'] ),
			'start_date' => yaww_imp_ymd( $R['start_date'] ), 'end_date' => yaww_imp_ymd( $R['end_date'] ),
			'status' => $R['status'], 'spaces_left' => $R['spaces_left'], 'collection' => $R['collection'],
			'card_image' => $card, 'hero_image' => $hero, 'nights' => $R['nights'],
			'includes' => yaww_imp_list( $R['includes'] ), 'not_included' => yaww_imp_list( $R['not_included'] ),
			'includes_notes' => yaww_imp_list( $R['includes_notes'] ), 'includes_image' => yaww_imp_media( $R['includes_image'] ),
			'itinerary' => array_map( function ( $d ) {
				return array( 'label' => $d['label'], 'title' => $d['title'], 'bullets' => implode( "\n", $d['bullets'] ), 'images' => yaww_imp_gallery( $d['images'] ) );
			}, $R['itinerary'] ),
			'itinerary_notes' => yaww_imp_list( $R['itinerary_notes'] ),
			'stays' => array_map( function ( $s ) {
				return array( 'name' => $s['name'], 'tab' => $s['tab'], 'text' => implode( "\n", $s['text'] ), 'gallery' => yaww_imp_gallery( $s['gallery'] ) );
			}, $R['stays'] ),
			'room_venues' => yaww_imp_list( $R['room_venues'] ),
			'rooms' => array_map( function ( $r ) {
				return array( 'name' => $r['name'], 'price_usd' => $r['price_usd'], 'price_unit' => $r['price_unit'], 'description' => $r['description'],
					'pills' => implode( "\n", $r['pills'] ), 'hotel_label' => '', 'gallery' => yaww_imp_gallery( $r['gallery'] ) );
			}, $R['rooms'] ),
			'extras' => array_map( function ( $x ) {
				$usd = preg_match( '/^([\d.,]+)\s*USD$/i', trim( $x['price'] ), $m ) ? (float) str_replace( ',', '', $m[1] ) : '';
				return array( 'name' => $x['name'], 'tab' => $x['tab'], 'price' => $x['price'], 'price_usd' => $usd, 'unit' => $x['unit'],
					'desc' => $x['desc'], 'gallery' => yaww_imp_gallery( $x['gallery'] ) );
			}, $R['extras'] ),
			'travel_tips' => array_map( function ( $t ) { return array( 'heading' => $t['heading'], 'items' => implode( "\n", $t['items'] ) ); }, $R['travel_tips'] ),
			'faqs' => array_map( function ( $f ) { return array( 'question' => $f['question'], 'answer' => $f['answer'] ); }, $R['faqs'] ),
			'gallery' => yaww_imp_gallery( $R['gallery'] ), 'gallery_title' => $R['gallery_title'], 'gallery_intro' => $R['gallery_intro'],
			'testimonials' => $R['testimonials'],
			'deposit_usd' => $R['deposit_usd'], 'balance_due_days' => $R['balance_due_days'], 'roommate_note' => $R['roommate_note'],
			'seo_description' => $R['seo_description'],
		) );
		if ( $R['collection'] ) { wp_set_object_terms( $id, $R['collection'], 'retreat_tier' ); }
		if ( $R['waitlist'] && isset( $ids['location'][ $R['location'] ] ) ) {
			yaww_imp_fields( 'location', $ids['location'][ $R['location'] ], array( 'program_retreat' => $id ) );
		}
		WP_CLI::log( "retreat   {$R['slug']}" );
	}
}

/* -------------------------------------------------------------- journal -- */
if ( $want( 'journal' ) ) {
	$J = $C['journal'];
	// Ten posts have no date on the source; they keep their place in the list (newest first)
	// with a date between their neighbours, and an empty date_label so no date line shows.
	$dates = array_map( function ( $j ) { return $j['date']; }, $J );
	$n = count( $dates );
	for ( $i = 0; $i < $n; $i++ ) {
		if ( $dates[ $i ] ) { continue; }
		$prev = null;
		for ( $k = $i - 1; $k >= 0; $k-- ) { if ( $dates[ $k ] ) { $prev = $dates[ $k ]; break; } }
		$dates[ $i ] = $prev ? gmdate( 'Y-m-d', strtotime( $prev ) - DAY_IN_SECONDS ) : '2018-01-01';
	}
	foreach ( $J as $i => $P ) {
		$slug = isset( $JOURNAL_SLUGS[ $P['slug'] ] ) ? $JOURNAL_SLUGS[ $P['slug'] ] : $P['slug'];
		$html = array();
		foreach ( $P['blocks'] as $b ) {
			if ( 'img' === $b['type'] ) {
				$aid = yaww_imp_media( $b['src'], '', ! empty( $b['src2x'] ) ? $b['src2x'] : null );
				if ( ! $aid ) { continue; }
				$url = wp_get_attachment_image_url( $aid, 'full' );
				$html[] = '<!-- wp:image {"id":' . $aid . ',"sizeSlug":"full","linkDestination":"none","className":"post__figure"} -->' . "\n"
					. '<figure class="wp-block-image size-full post__figure"><img src="' . esc_url( $url ) . '" alt="" class="wp-image-' . $aid . '"/></figure>' . "\n"
					. '<!-- /wp:image -->';
			} else {
				$ps = '';
				foreach ( $b['paras'] as $group ) {
					foreach ( $group as $line ) {
						$ps .= '<!-- wp:paragraph -->' . "\n" . '<p>' . esc_html( $line ) . '</p>' . "\n" . '<!-- /wp:paragraph -->' . "\n\n";
					}
				}
				$html[] = '<!-- wp:group {"className":"post__text","layout":{"type":"default"}} -->' . "\n"
					. '<div class="wp-block-group post__text">' . trim( $ps ) . '</div>' . "\n" . '<!-- /wp:group -->';
			}
		}
		$cats = array();
		foreach ( $P['categories'] as $c ) {
			$t = term_exists( $c, 'category' );
			if ( ! $t ) { $t = wp_insert_term( $c, 'category' ); }
			$cats[] = (int) ( is_array( $t ) ? $t['term_id'] : $t );
		}
		$id = yaww_imp_post( 'post', $P['source_id'], array( 'post_title' => $P['title'], 'post_name' => $slug,
			'post_content' => implode( "\n\n", $html ), 'post_excerpt' => $P['excerpt'],
			'post_date' => $dates[ $i ] . ' 12:00:00', 'post_category' => $cats, 'menu_order' => $P['menu_order'] ) );
		update_post_meta( $id, '_yaww_cat_order', $P['categories'] );
		$hero = yaww_imp_img( $P['hero'] );
		if ( $hero ) { set_post_thumbnail( $id, $hero ); }
		$person = 0;
		if ( $P['author_person'] ) {
			list( $ptype, $pslug ) = $P['author_person'];
			$person = isset( $ids[ $ptype ][ $pslug ] ) ? $ids[ $ptype ][ $pslug ] : 0;
		}
		$r = $P['retreat'];
		yaww_imp_fields( 'post', $id, array(
			'date_label' => $P['date_label'], 'author_name' => $P['author_name'], 'author_bio' => implode( "\n", $P['author_bio'] ),
			'author_photo' => yaww_imp_img( $P['author_photo'] ), 'author_person' => $person,
			'retreat_title' => $r ? $r['title'] : '', 'retreat_subtitle' => $r ? $r['subtitle'] : '', 'retreat_text' => $r ? $r['text'] : '',
			'retreat_location' => ( $r && isset( $ids['location'][ $r['location'] ] ) ) ? $ids['location'][ $r['location'] ] : '',
			'retreat_image' => $r ? yaww_imp_img( $r['image'] ) : 0,
			// E's templates read the retina files as their own attachments (same file also sits on the 1x as size '2x').
			'card_image' => yaww_imp_media( $P['card_image'], $P['title'] ), 'card_image_2x' => yaww_imp_media( $P['card_image_2x'], $P['title'] ),
			'hero_image_2x' => ( $P['hero'] && ! empty( $P['hero']['src2x'] ) ) ? yaww_imp_media( $P['hero']['src2x'], $P['title'] ) : 0,
			'author_photo_2x' => ( $P['author_photo'] && ! empty( $P['author_photo']['src2x'] ) ) ? yaww_imp_media( $P['author_photo']['src2x'], $P['author_name'] ) : 0,
			'retreat_image_2x' => ( $r && $r['image'] && ! empty( $r['image']['src2x'] ) ) ? yaww_imp_media( $r['image']['src2x'], $r['title'] ) : 0,
		) );
		// seo.py TITLE_OVERRIDE: two titles too long for 60 characters (field "Search title"; set only when empty).
		$seo_titles = array(
			'the-utopian-newsletter-eat-drink-be-merry-issue-august-8-2018' => 'Utopian Newsletter: Eat, Drink & Be Merry Issue | YAWW',
			'the-utopian-newsletter-interior-design-issue-september-12-2018' => 'Utopian Newsletter: Interior Design Issue | YAWW',
		);
		if ( isset( $seo_titles[ $P['slug'] ] ) && ! yaww_meta( 'seo_title', $id ) ) { yaww_imp_fields( 'post', $id, array( 'seo_title' => $seo_titles[ $P['slug'] ] ) ); }
		WP_CLI::log( "journal   $slug" );
	}
	$unc = get_term_by( 'slug', 'uncategorized', 'category' );
	if ( $unc && ! $unc->count ) { /* keep the default term; it is simply empty */ }
}

/* ----------------------------------------------------------------- site -- */
if ( $want( 'site' ) ) {
	$pages = array(
		'home' => array( 'Home', '' ), 'about' => array( 'About', 'page-templates/about.php' ),
		'contact' => array( 'Contact', 'page-templates/contact.php' ), 'teach-with-us' => array( 'Teach with us', 'page-templates/teach-with-us.php' ),
		'journal' => array( 'Journal', '' ), 'policies' => array( 'Policies', '' ),
	);
	$pid = array();
	foreach ( $pages as $slug => $p ) {
		$id = yaww_imp_post( 'page', 'page:' . $slug, array( 'post_title' => $p[0], 'post_name' => $slug ) );
		update_post_meta( $id, '_wp_page_template', $p[1] ? $p[1] : 'default' );
		$pid[ $slug ] = $id;
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pid['home'] );
	update_option( 'page_for_posts', $pid['journal'] );
	update_option( 'blogname', 'Yoga Adventures Worldwide' );
	update_option( 'posts_per_page', 40 );
	foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $s => $t ) {
		$p = get_page_by_path( $s, OBJECT, $t );
		if ( $p && ! get_post_meta( $p->ID, '_yaww_source_id', true ) ) { wp_trash_post( $p->ID ); }
	}

	/* Menus: the static header nav (labels from the static pages; js/nav.js builds the panels). */
	$menu_items = array(
		array( 'Retreats', get_post_type_archive_link( 'retreat' ), array(
			array( 'All Retreats', get_post_type_archive_link( 'retreat' ) ),
			array( 'Deep Dives', get_term_link( 'deep-dives', 'retreat_tier' ) ),
			array( 'Signature Collection', get_term_link( 'signature-collection', 'retreat_tier' ) ),
		) ),
		array( 'Locations', get_post_type_archive_link( 'location' ), array() ),
		array( 'About', get_permalink( $pid['about'] ), array(
			array( 'About Us', get_permalink( $pid['about'] ) ),
			array( 'Teachers', get_post_type_archive_link( 'teacher' ) ),
			array( 'Facilitators', get_post_type_archive_link( 'facilitator' ) ),
		) ),
		array( 'Journal', get_permalink( $pid['journal'] ), array() ),
		array( 'Contact', get_permalink( $pid['contact'] ), array(
			array( 'Ask a question', get_permalink( $pid['contact'] ) ),
			array( 'Private groups', get_permalink( $pid['contact'] ) . '#private-groups' ),
			array( 'Teach with us', get_permalink( $pid['teach-with-us'] ) ),
		) ),
	);
	$locs = array();
	foreach ( (array) get_theme_mod( 'nav_menu_locations', array() ) as $k => $v ) { $locs[ $k ] = $v; }
	foreach ( array( 'primary' => 'Primary', 'footer' => 'Footer' ) as $loc => $name ) {
		$menu = wp_get_nav_menu_object( $name );
		$mid  = $menu ? $menu->term_id : wp_create_nav_menu( $name );
		foreach ( (array) wp_get_nav_menu_items( $mid ) as $it ) { wp_delete_post( $it->ID, true ); }   // rebuild (menu items only)
		foreach ( $menu_items as $o => $top ) {
			$parent = wp_update_nav_menu_item( $mid, 0, array( 'menu-item-title' => $top[0], 'menu-item-url' => $top[1],
				'menu-item-type' => 'custom', 'menu-item-status' => 'publish', 'menu-item-position' => $o + 1 ) );
			if ( 'primary' !== $loc ) { continue; }
			foreach ( $top[2] as $k => $sub ) {
				if ( is_wp_error( $sub[1] ) ) { continue; }
				wp_update_nav_menu_item( $mid, 0, array( 'menu-item-title' => $sub[0], 'menu-item-url' => $sub[1], 'menu-item-type' => 'custom',
					'menu-item-status' => 'publish', 'menu-item-parent-id' => $parent, 'menu-item-position' => ( $o + 1 ) * 10 + $k ) );
			}
		}
		$locs[ $loc ] = $mid;
	}
	set_theme_mod( 'nav_menu_locations', $locs );
	flush_rewrite_rules();
	WP_CLI::log( 'site      pages, reading settings, menus, rewrite rules' );
}

/* ------------------------------------------------------------------ seo -- */
/* Search descriptions as the static pages print them (retreats/_tools/seo.py: padded, overrides), into
   seo_description when that field is empty, so WordPress's <meta name="description"> matches. */
if ( $want( 'seo' ) || $want( 'locations' ) || $want( 'people' ) || $want( 'journal' ) ) {
	$files = array( 'location' => 'locationpage-%s.html', 'teacher' => 'teacher-%s.html', 'facilitator' => 'facilitator-%s.html', 'post' => 'journal/%s.html' );
	$n = 0;
	foreach ( $files as $type => $pattern ) {
		foreach ( get_posts( array( 'post_type' => $type, 'numberposts' => -1, 'post_status' => 'any' ) ) as $p ) {
			$src  = (string) get_post_meta( $p->ID, '_yaww_source_id', true );
			$file = $GLOBALS['YAWW_REPO'] . '/' . sprintf( $pattern, $src );
			if ( ! $src || ! is_file( $file ) || yaww_meta( 'seo_description', $p->ID ) ) { continue; }
			if ( preg_match( '/<meta name="description" content="([^"]*)"/', file_get_contents( $file ), $m ) ) {
				yaww_imp_fields( $type, $p->ID, array( 'seo_description' => html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' ) ) );
				$n++;
			}
		}
	}
	WP_CLI::log( "seo       $n search descriptions from the static pages" );
}

$count = function ( $t ) { return (int) wp_count_posts( $t )->publish; };
WP_CLI::success( sprintf( 'locations %d, teachers %d, facilitators %d, retreats %d, journal %d, media new %d / reused %d',
	$count( 'location' ), $count( 'teacher' ), $count( 'facilitator' ), $count( 'retreat' ), $count( 'post' ),
	$GLOBALS['stats']['media_new'], $GLOBALS['stats']['media_reused'] ) );
