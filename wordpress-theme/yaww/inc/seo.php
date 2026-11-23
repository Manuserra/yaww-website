<?php
/**
 * SEO head, no plugin. Port of retreats/_tools/seo.py (same titles, descriptions, Open Graph,
 * Twitter card and JSON-LD) onto WordPress data.
 *
 * - Title: pre_get_document_title ("core | Yoast..." is replaced by "core | Yoga Adventures Worldwide",
 *   or "core | YAWW" when the long form passes 60 characters, as seo.py does).
 * - Description: the `seo_description` field, else the manual excerpt, else a generated line
 *   (retreats) or the field that carries the intro (locations, teachers, facilitators, journal).
 * - Canonical, Open Graph, Twitter and JSON-LD (Organization + WebSite, Event, TouristTrip,
 *   TouristDestination, Person, BlogPosting, BreadcrumbList) in wp_head.
 * - Robots: noindex for search, 404, attachments, leads, and any post whose `seo_noindex` meta is 1
 *   (the static site noindexed drafts, variants and the retired team pages).
 * - Sitemap: WordPress core (/wp-sitemap.xml) with users removed and noindex posts left out.
 *
 * Loaded from functions.php: require_once YAWW_DIR . '/inc/seo.php';
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

const YAWW_SEO_SITE  = 'Yoga Adventures Worldwide';
const YAWW_SEO_SHORT = 'YAWW';

/* ------------------------------------------------------------------ helpers -- */

/** Plain text from markup, entities and runs of spaces collapsed. */
function yaww_seo_clean( $s ) {
	$s = wp_strip_all_tags( (string) $s );
	$s = html_entity_decode( $s, ENT_QUOTES, 'UTF-8' );
	return trim( preg_replace( '/\s+/u', ' ', $s ) );
}

/** "core | Yoga Adventures Worldwide" when it fits in 60 characters, else "core | YAWW". */
function yaww_seo_title_of( $core ) {
	$long = $core . ' | ' . YAWW_SEO_SITE;
	return mb_strlen( $long ) <= 60 ? $long : $core . ' | ' . YAWW_SEO_SHORT;
}

/** Cut text at a sentence end inside lo..hi characters (as seo.py fit()); otherwise a word-safe cut at hi. */
function yaww_seo_fit( $text, $lo = 140, $hi = 160 ) {
	$text = yaww_seo_clean( $text );
	if ( mb_strlen( $text ) <= $hi ) { return $text; }
	$cut = 0;
	if ( preg_match_all( '/[.!?](?=\s|$)/u', $text, $m, PREG_OFFSET_CAPTURE ) ) {
		foreach ( $m[0] as $hit ) {
			$end = mb_strlen( substr( $text, 0, $hit[1] ) ) + 1;
			if ( $end <= $hi ) { $cut = $end; }
		}
	}
	if ( $cut >= $lo ) { return mb_substr( $text, 0, $cut ); }
	$t = mb_substr( $text, 0, $hi - 1 );
	return rtrim( preg_replace( '/\s+\S*$/u', '', $t ), " ,;:-" ) . '…';
}

function yaww_seo_months() { return array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ); }
function yaww_seo_months_long() { return array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ); }

/** "Nov 7–9, 2026" / "Nov 28–Dec 3, 2026". */
function yaww_seo_date_short( $a, $b ) {
	$M = yaww_seo_months();
	list( $y1, $m1, $d1 ) = array_map( 'intval', explode( '-', $a ) );
	list( $y2, $m2, $d2 ) = array_map( 'intval', explode( '-', $b ) );
	return $m1 === $m2 ? "{$M[$m1-1]} {$d1}–{$d2}, {$y1}" : "{$M[$m1-1]} {$d1}–{$M[$m2-1]} {$d2}, {$y2}";
}

/** "November 7–9, 2026" / "November 28 – December 3, 2026". */
function yaww_seo_date_long( $a, $b ) {
	$M = yaww_seo_months_long();
	list( $y1, $m1, $d1 ) = array_map( 'intval', explode( '-', $a ) );
	list( $y2, $m2, $d2 ) = array_map( 'intval', explode( '-', $b ) );
	return $m1 === $m2 ? "{$M[$m1-1]} {$d1}–{$d2}, {$y1}" : "{$M[$m1-1]} {$d1} – {$M[$m2-1]} {$d2}, {$y2}";
}

function yaww_seo_valid_date( $d ) { return is_string( $d ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $d ); }

function yaww_seo_home() { return home_url( '/' ); }

/** Absolute URL of the theme's fallback share image (the static site's og image per page). */
function yaww_seo_default_image( $file = 'hero-home-poster.jpg' ) {
	return YAWW_URI . '/assets/images/' . $file;
}

/** Best image for the current post: featured, then the field images, then a default. */
function yaww_seo_post_image( $post_id, $size = 'yaww-hero', $fallback = 'hero-home-poster.jpg' ) {
	foreach ( array( 'hero_image', 'card_image', 'avatar' ) as $key ) {
		$id = (int) yaww_meta( $key, $post_id );
		if ( $id ) {
			$src = wp_get_attachment_image_src( $id, $size );
			if ( $src ) { return $src[0]; }
		}
	}
	$id = (int) get_post_thumbnail_id( $post_id );
	if ( $id ) {
		$src = wp_get_attachment_image_src( $id, $size );
		if ( $src ) { return $src[0]; }
	}
	return yaww_seo_default_image( $fallback );
}

function yaww_seo_crumbs( $items ) {
	$list = array();
	foreach ( array_values( $items ) as $i => $it ) {
		$list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $it[0], 'item' => $it[1] );
	}
	return array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list );
}

function yaww_seo_org_ref() { return array( '@id' => yaww_seo_home() . '#organization' ); }

/* ------------------------------------------------------------ static pages -- */

/** Pages nothing edits: title core, description, share image, breadcrumb label. Same text as seo.py STATIC. */
function yaww_seo_static( $key ) {
	$T = array(
		'home' => array( 'Yoga Retreats Around the World', 'Small-group yoga retreats since 2008. Hand-picked teachers, thoughtful hotels, local food and real cultural experiences in destinations from Iceland to Oaxaca.', 'hero-home-poster.jpg', '' ),
		'retreats' => array( 'Upcoming Yoga Retreats', 'Browse every upcoming Yoga Adventures Worldwide retreat by destination, date and collection, from short Deep Dives to the Signature Collection.', 'hero-home-poster.jpg', 'Retreats' ),
		'locations' => array( 'Yoga Retreat Destinations', '{n} destinations we return to year after year, with the hotels, teachers and guides we trust. Pick a place to see when we go and what a week looks like.', 'hero-home-poster.jpg', 'Locations' ),
		'about' => array( 'About Us', 'Since 2008, Christina Thomas and her team have taken small groups inside the places they love, with a hand-picked teacher and yoga woven into every day.', 'about-hero.jpg', 'About' ),
		'teachers' => array( 'Our Yoga Teachers', 'Meet the teachers who lead our retreats, from restorative and yoga nidra to strength and alignment, each paired with a place and a group they suit.', 'about-teachers.jpg', 'Teachers' ),
		'facilitators' => array( 'Our Retreat Facilitators', 'Meet the facilitators who run our retreats on the ground. They look after the details so you can arrive, practice, eat well and rest, wherever we are.', 'about-how-team.jpg', 'Facilitators' ),
		'journal' => array( 'Journal', 'Stories, guides and reflections from our retreats and travels: yoga, food, astrology and life on the road, written by the Yoga Adventures Worldwide team.', 'hero-home-poster.jpg', 'Journal' ),
		'contact' => array( 'Contact', 'Questions about a retreat, a private trip for your group, or anything else: write to the Yoga Adventures Worldwide team and a real person will reply.', 'lead-yoga-stretch.jpg', 'Contact' ),
		'teach' => array( 'Lead a Yoga Retreat With Us', 'Lead a yoga retreat with Yoga Adventures Worldwide. We plan the itinerary, book the hotels, run the logistics and help fill your spots, so you can teach.', 'lead-yoga-stretch.jpg', 'Teach with us' ),
	);
	return isset( $T[ $key ] ) ? $T[ $key ] : null;
}

/** Which static page is this view, or ''. */
function yaww_seo_static_key() {
	if ( is_front_page() ) { return 'home'; }
	if ( is_post_type_archive( 'retreat' ) ) { return 'retreats'; }
	if ( is_post_type_archive( 'location' ) ) { return 'locations'; }
	if ( is_post_type_archive( 'teacher' ) ) { return 'teachers'; }
	if ( is_post_type_archive( 'facilitator' ) ) { return 'facilitators'; }
	if ( is_home() ) { return 'journal'; }
	if ( is_page_template( 'page-templates/about.php' ) ) { return 'about'; }
	if ( is_page_template( 'page-templates/contact.php' ) ) { return 'contact'; }
	if ( is_page_template( 'page-templates/teach-with-us.php' ) ) { return 'teach'; }
	return '';
}

/* ------------------------------------------------------------------ context -- */

/** Everything the head needs for the current request. Cached per request. */
function yaww_seo_context() {
	static $ctx = null;
	if ( null !== $ctx ) { return $ctx; }

	$ctx = array( 'title' => '', 'desc' => '', 'image' => '', 'og' => 'website', 'canonical' => '', 'noindex' => false, 'ld' => array(), 'published' => '', 'modified' => '' );
	$home = yaww_seo_home();

	// Views that stay out of the index.
	if ( is_search() || is_404() || is_attachment() || is_tag() || is_date() || is_author() ) { $ctx['noindex'] = true; }

	$key = yaww_seo_static_key();
	if ( $key ) {
		$p = yaww_seo_static( $key );
		$n = '';
		if ( 'locations' === $key ) {
			$count = (int) wp_count_posts( 'location' )->publish;
			$words = array( 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen' );
			$n = isset( $words[ $count ] ) ? $words[ $count ] : (string) $count;
		}
		$ctx['title'] = yaww_seo_title_of( $p[0] );
		$ctx['desc']  = str_replace( '{n}', $n, $p[1] );
		$ctx['image'] = yaww_seo_default_image( $p[2] );
		if ( 'home' === $key ) {
			$ctx['canonical'] = $home;
			$ctx['ld'][] = array( '@context' => 'https://schema.org', '@type' => 'Organization', '@id' => $home . '#organization', 'name' => YAWW_SEO_SITE, 'url' => $home,
				'description' => 'Small-group yoga retreats around the world, hosted by people who know the place.', 'foundingDate' => '2008',
				'founder' => array( '@type' => 'Person', 'name' => 'Christina Thomas' ) );
			$ctx['ld'][] = array( '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => YAWW_SEO_SITE, 'url' => $home, 'publisher' => yaww_seo_org_ref() );
		} else {
			if ( is_home() ) {
				$pid = (int) get_option( 'page_for_posts' );
				$ctx['canonical'] = $pid ? get_permalink( $pid ) : home_url( '/journal/' );
			} elseif ( is_post_type_archive() ) {
				$ctx['canonical'] = get_post_type_archive_link( get_query_var( 'post_type' ) );
			} else {
				$ctx['canonical'] = get_permalink( get_queried_object_id() );
			}
			$ctx['ld'][] = yaww_seo_crumbs( array( array( 'Home', $home ), array( $p[3], $ctx['canonical'] ) ) );
		}
	} elseif ( is_singular( 'retreat' ) ) {
		yaww_seo_fill_retreat( $ctx, get_queried_object_id() );
	} elseif ( is_singular( 'location' ) ) {
		yaww_seo_fill_location( $ctx, get_queried_object_id() );
	} elseif ( is_singular( array( 'teacher', 'facilitator' ) ) ) {
		yaww_seo_fill_person( $ctx, get_queried_object_id() );
	} elseif ( is_singular( 'post' ) ) {
		yaww_seo_fill_journal( $ctx, get_queried_object_id() );
	} elseif ( is_tax( 'retreat_tier' ) ) {
		$t = get_queried_object();
		$ctx['title'] = yaww_seo_title_of( $t->name . ' Yoga Retreats' );
		$ctx['desc']  = $t->description ? yaww_seo_fit( $t->description ) : 'Upcoming ' . $t->name . ' yoga retreats from Yoga Adventures Worldwide: small groups, hand-picked teachers and thoughtful hotels.';
		$ctx['image'] = yaww_seo_default_image();
		$ctx['canonical'] = get_term_link( $t );
		$ctx['ld'][] = yaww_seo_crumbs( array( array( 'Home', $home ), array( 'Retreats', get_post_type_archive_link( 'retreat' ) ), array( $t->name, $ctx['canonical'] ) ) );
	} elseif ( is_category() ) {
		$t = get_queried_object();
		$ctx['title'] = yaww_seo_title_of( $t->name . ' Journal' );
		$ctx['desc']  = $t->description ? yaww_seo_fit( $t->description ) : 'Journal stories filed under ' . $t->name . ', from the Yoga Adventures Worldwide team.';
		$ctx['image'] = yaww_seo_default_image();
		$ctx['canonical'] = get_term_link( $t );
	} elseif ( is_singular() ) {
		// Plain pages (policies, FAQ, press ...).
		$id = get_queried_object_id();
		$ctx['title'] = yaww_seo_title_of( get_the_title( $id ) );
		$ctx['desc']  = yaww_seo_manual_desc( $id ) ?: yaww_seo_fit( get_post_field( 'post_content', $id ) );
		$ctx['image'] = yaww_seo_post_image( $id );
		$ctx['canonical'] = get_permalink( $id );
		$ctx['ld'][] = yaww_seo_crumbs( array( array( 'Home', $home ), array( get_the_title( $id ), $ctx['canonical'] ) ) );
	} elseif ( is_404() ) {
		$ctx['title'] = yaww_seo_title_of( 'Page not found' );
		$ctx['image'] = yaww_seo_default_image();
	} else {
		$ctx['title'] = yaww_seo_title_of( 'Yoga Retreats Around the World' );
		$ctx['image'] = yaww_seo_default_image();
	}

	// Per-post switch for the team: custom field `seo_noindex` = 1.
	if ( is_singular() && (int) get_post_meta( get_queried_object_id(), 'seo_noindex', true ) ) { $ctx['noindex'] = true; }
	if ( is_singular() && 'lead' === get_post_type() ) { $ctx['noindex'] = true; }
	if ( is_paged() && $ctx['canonical'] && ! is_wp_error( $ctx['canonical'] ) ) {
		$ctx['canonical'] = trailingslashit( $ctx['canonical'] ) . 'page/' . (int) get_query_var( 'paged' ) . '/';
	}
	if ( ! $ctx['desc'] ) { $ctx['desc'] = 'Small-group yoga retreats around the world with Yoga Adventures Worldwide.'; }
	return $ctx;
}

/** seo_description field, then the manual excerpt. */
function yaww_seo_manual_desc( $id ) {
	$d = yaww_seo_clean( yaww_meta( 'seo_description', $id ) );
	if ( $d ) { return $d; }
	$post = get_post( $id );
	return ( $post && $post->post_excerpt ) ? yaww_seo_fit( $post->post_excerpt ) : '';
}

function yaww_seo_teacher_names( $id ) {
	$names = array();
	foreach ( array_filter( (array) yaww_meta( 'teacher_ids', $id ) ) as $tid ) { $names[] = get_the_title( $tid ); }
	return $names;
}

function yaww_seo_fill_retreat( &$ctx, $id ) {
	$home  = yaww_seo_home();
	$url   = get_permalink( $id );
	$name  = get_the_title( $id );
	$lid   = (int) yaww_meta( 'location_id', $id );
	$place = $lid ? yaww_location_line( $lid ) : '';
	$start = yaww_meta( 'start_date', $id );
	$end   = yaww_meta( 'end_date', $id );
	$dated = yaww_seo_valid_date( $start ) && yaww_seo_valid_date( $end );
	$who   = yaww_seo_teacher_names( $id );
	$nights = (int) yaww_meta( 'nights', $id );
	$image = yaww_seo_post_image( $id );
	$status = yaww_meta( 'status', $id );

	$prices = array();
	foreach ( yaww_rows( 'rooms', $id ) as $room ) {
		if ( isset( $room['price_usd'] ) && (int) $room['price_usd'] > 0 ) { $prices[] = (int) $room['price_usd']; }
	}

	if ( $dated ) {
		// seo.py uses the location's short name (locations.js `name`, e.g. "Oaxaca, Mexico").
		$dest = $lid ? ( yaww_meta( 'short_name', $lid ) ?: get_the_title( $lid ) ) : $name;
		list( $y, $m ) = array_map( 'intval', explode( '-', $start ) );
		$M = yaww_seo_months();
		$base = "$dest Yoga Retreat, {$M[$m-1]} $y";
		// Unique per site: when two editions of a place share a month, name the exact days.
		$same = $lid ? get_posts( array( 'post_type' => 'retreat', 'post_status' => 'publish', 'fields' => 'ids', 'numberposts' => 5, 'meta_query' => array(
			'relation' => 'AND', array( 'key' => 'location_id', 'value' => $lid ), array( 'key' => 'start_date', 'value' => '^' . sprintf( '%04d-?%02d', $y, $m ), 'compare' => 'REGEXP' ) ) ) ) : array(); // ACF stores dates as Ymd, native meta as Y-m-d.
		if ( count( $same ) > 1 ) {
			$ctx['title'] = "$dest Yoga Retreat, " . yaww_seo_date_short( $start, $end ) . ' | ' . YAWW_SEO_SHORT;
		} else {
			$ctx['title'] = yaww_seo_title_of( $base );
		}
		$when = yaww_seo_date_long( $start, $end );
		$head = ( $nights ? "$nights-night yoga retreat" : 'Yoga retreat' ) . ( $place ? " in $place" : '' ) . ", $when" . ( $who ? ', with ' . implode( ' & ', $who ) : '' ) . '.';
		$manual = yaww_seo_manual_desc( $id );
		$ctx['desc'] = $manual ? $manual : $head;

		$ev = array( '@context' => 'https://schema.org', '@type' => 'Event', 'name' => $name, 'description' => $ctx['desc'],
			'startDate' => $start, 'endDate' => $end,
			'eventStatus' => 'https://schema.org/EventScheduled', 'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
			'location' => array( '@type' => 'Place', 'name' => $place ? $place : $name,
				'address' => array( '@type' => 'PostalAddress', 'addressCountry' => $lid ? (string) yaww_meta( 'country', $lid ) : '' ) ),
			'organizer' => yaww_seo_org_ref(), 'url' => $url, 'image' => array( $image ) );
		if ( $who ) { $ev['performer'] = array_map( function ( $w ) { return array( '@type' => 'Person', 'name' => $w ); }, $who ); }
		if ( $prices ) {
			$ev['offers'] = array( '@type' => 'AggregateOffer', 'priceCurrency' => 'USD', 'lowPrice' => min( $prices ), 'highPrice' => max( $prices ), 'url' => $url,
				'availability' => 'sold_out' === $status ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock' );
		}
		$ctx['ld'][] = $ev;
	} else {
		// Waitlist page: a trip, not an event.
		// seo.py WAITLIST_TITLE: the program's own name as a search title; others fall back to the name before ':'.
		$wait = array(
			'france' => 'Paris & Champagne Yoga Retreat', 'iceland' => 'The Iceland Experience', 'morocco' => 'Mesmerized in Morocco Yoga Retreat',
			'sardinia' => 'Majestic Sardinia Yoga Retreat', 'spain-basque-country' => 'Basque Country Yoga & Gastronomy Retreat',
			'turkey' => 'Istanbul & Cappadocia Yoga Retreat',
		);
		$slug = get_post_field( 'post_name', $id );
		$ctx['title'] = yaww_seo_title_of( isset( $wait[ $slug ] ) ? $wait[ $slug ] : trim( explode( ':', $name )[0] ) );
		$manual = yaww_seo_manual_desc( $id );
		$ctx['desc'] = $manual ? $manual : ( $name . ': ' . ( $nights ? "$nights-night " : '' ) . 'yoga retreat' . ( $place ? " in $place" : '' ) . '. Join the waitlist for the next dates.' );
		$trip = array( '@context' => 'https://schema.org', '@type' => 'TouristTrip', 'name' => $name, 'description' => $ctx['desc'], 'url' => $url,
			'provider' => yaww_seo_org_ref(), 'touristType' => 'Yoga retreat travellers', 'image' => array( $image ) );
		$ctx['ld'][] = $trip;
	}
	$ctx['image'] = $image;
	$ctx['canonical'] = $url;
	$ctx['ld'][] = yaww_seo_crumbs( array( array( 'Home', $home ), array( 'Retreats', get_post_type_archive_link( 'retreat' ) ), array( $name, $url ) ) );
}

function yaww_seo_fill_location( &$ctx, $id ) {
	$home = yaww_seo_home();
	$url  = get_permalink( $id );
	$name = get_the_title( $id );
	$ctx['title'] = yaww_seo_title_of( "$name Yoga Retreats" );
	$desc = yaww_seo_manual_desc( $id );
	if ( ! $desc ) {
		$intro = yaww_meta( 'intro', $id );
		$desc = $intro ? yaww_seo_fit( $intro . ' See dates and how to get there.' ) : yaww_seo_fit( yaww_body( $id ) );
	}
	$ctx['desc']  = $desc;
	$ctx['image'] = yaww_seo_post_image( $id );
	$ctx['canonical'] = $url;
	$ctx['ld'][] = array( '@context' => 'https://schema.org', '@type' => 'TouristDestination', 'name' => $name, 'description' => $desc, 'url' => $url, 'touristType' => 'Yoga retreat travellers' );
	$ctx['ld'][] = yaww_seo_crumbs( array( array( 'Home', $home ), array( 'Locations', get_post_type_archive_link( 'location' ) ), array( $name, $url ) ) );
}

function yaww_seo_fill_person( &$ctx, $id ) {
	$home = yaww_seo_home();
	$type = get_post_type( $id );
	$url  = get_permalink( $id );
	$name = get_the_title( $id );
	$role = (string) yaww_meta( 'role', $id );
	$label = ( 'teacher' === $type ) ? ( $role ? $role : 'Yoga Teacher' ) : 'Retreat Facilitator';
	$core  = "$name, $label";
	$ctx['title'] = ( mb_strlen( $core . ' | ' . YAWW_SEO_SITE ) <= 60 ) ? $core . ' | ' . YAWW_SEO_SITE : $name . ' | ' . YAWW_SEO_SITE;
	$short = yaww_seo_clean( yaww_meta( 'short_bio', $id ) );
	$desc  = yaww_seo_manual_desc( $id ) ?: yaww_seo_fit( $short ? $short : yaww_body( $id ) );
	$ctx['desc']  = $desc;
	$ctx['image'] = yaww_seo_post_image( $id, 'yaww-person', 'about-teachers.jpg' );
	$ctx['og']    = 'profile';
	$ctx['canonical'] = $url;
	$me = array( '@context' => 'https://schema.org', '@type' => 'Person', 'name' => $name, 'jobTitle' => $label, 'description' => $short ? $short : $desc,
		'url' => $url, 'worksFor' => yaww_seo_org_ref(), 'image' => $ctx['image'] );
	$spec = yaww_meta( 'specialties', $id );
	if ( 'teacher' === $type && $spec ) {
		$spec = is_array( $spec ) ? $spec : array_map( 'trim', explode( ',', (string) $spec ) );
		$me['knowsAbout'] = array_values( array_filter( array_map( function ( $s ) { return is_array( $s ) ? ( isset( $s['value'] ) ? $s['value'] : '' ) : $s; }, $spec ) ) );
	}
	$ctx['ld'][] = $me;
	$ctx['ld'][] = yaww_seo_crumbs( array( array( 'Home', $home ), array( 'teacher' === $type ? 'Teachers' : 'Facilitators', get_post_type_archive_link( $type ) ), array( $name, $url ) ) );
}

function yaww_seo_fill_journal( &$ctx, $id ) {
	$home = yaww_seo_home();
	$url  = get_permalink( $id );
	$t    = yaww_seo_clean( get_the_title( $id ) );
	$ctx['title'] = ( mb_strlen( $t . ' | ' . YAWW_SEO_SITE ) <= 60 ) ? $t . ' | ' . YAWW_SEO_SITE : ( ( mb_strlen( $t . ' | ' . YAWW_SEO_SHORT ) <= 60 ) ? $t . ' | ' . YAWW_SEO_SHORT : $t );
	// Search title typed by the team (seo.py TITLE_OVERRIDE for titles too long for 60 characters).
	$own = yaww_seo_clean( yaww_meta( 'seo_title', $id ) );
	if ( $own ) { $ctx['title'] = $own; }
	$post = get_post( $id );
	$desc = yaww_seo_manual_desc( $id );
	if ( ! $desc ) { $desc = yaww_seo_fit( $post->post_excerpt ? $post->post_excerpt : $post->post_content ); }
	$ctx['desc']  = $desc;
	$ctx['image'] = yaww_seo_post_image( $id, 'yaww-journal' );
	$ctx['og']    = 'article';
	$ctx['canonical'] = $url;
	$ctx['published'] = get_post_time( 'c', true, $id );
	$ctx['modified']  = get_post_modified_time( 'c', true, $id );
	$ld = array( '@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $t, 'description' => $desc, 'url' => $url, 'mainEntityOfPage' => $url,
		'publisher' => yaww_seo_org_ref(), 'datePublished' => $ctx['published'], 'dateModified' => $ctx['modified'], 'image' => $ctx['image'] );
	$author = get_the_author_meta( 'display_name', $post->post_author );
	if ( $author ) { $ld['author'] = array( '@type' => 'Person', 'name' => $author ); }
	$jid = (int) get_option( 'page_for_posts' );
	$ctx['ld'][] = $ld;
	$ctx['ld'][] = yaww_seo_crumbs( array( array( 'Home', $home ), array( 'Journal', $jid ? get_permalink( $jid ) : home_url( '/journal/' ) ), array( $t, $url ) ) );
}

/* -------------------------------------------------------------------- hooks -- */

/* Title: the theme declares title-tag support, so this replaces WordPress's own. */
add_filter( 'pre_get_document_title', function ( $title ) {
	$c = yaww_seo_context();
	return $c['title'] ? $c['title'] : $title;
}, 20 );

/* Robots: WordPress prints the tag (and max-image-preview:large); we only add noindex. */
add_filter( 'wp_robots', function ( $robots ) {
	$c = yaww_seo_context();
	if ( $c['noindex'] ) {
		unset( $robots['index'], $robots['follow'] );
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}, 20 );

/* One canonical for everything (core only prints one on singular views). */
remove_action( 'wp_head', 'rel_canonical' );

add_action( 'wp_head', function () {
	$c = yaww_seo_context();
	$e = function ( $s ) { return esc_attr( $s ); };
	echo "\n<!-- SEO: inc/seo.php -->\n";
	if ( $c['desc'] ) { echo '<meta name="description" content="' . $e( $c['desc'] ) . '">' . "\n"; }
	if ( $c['canonical'] && ! is_wp_error( $c['canonical'] ) && ! is_404() ) { echo '<link rel="canonical" href="' . esc_url( $c['canonical'] ) . '">' . "\n"; }
	$locale = str_replace( '-', '_', get_locale() );
	echo '<meta property="og:locale" content="' . $e( $locale ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . $e( YAWW_SEO_SITE ) . '">' . "\n";
	echo '<meta property="og:type" content="' . $e( $c['og'] ) . '">' . "\n";
	echo '<meta property="og:title" content="' . $e( $c['title'] ) . '">' . "\n";
	if ( $c['desc'] ) { echo '<meta property="og:description" content="' . $e( $c['desc'] ) . '">' . "\n"; }
	if ( $c['canonical'] && ! is_wp_error( $c['canonical'] ) ) { echo '<meta property="og:url" content="' . esc_url( $c['canonical'] ) . '">' . "\n"; }
	if ( $c['image'] ) { echo '<meta property="og:image" content="' . esc_url( $c['image'] ) . '">' . "\n"; }
	if ( 'article' === $c['og'] && $c['published'] ) {
		echo '<meta property="article:published_time" content="' . $e( $c['published'] ) . '">' . "\n";
		if ( $c['modified'] ) { echo '<meta property="article:modified_time" content="' . $e( $c['modified'] ) . '">' . "\n"; }
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . $e( $c['title'] ) . '">' . "\n";
	if ( $c['desc'] ) { echo '<meta name="twitter:description" content="' . $e( $c['desc'] ) . '">' . "\n"; }
	if ( $c['image'] ) { echo '<meta name="twitter:image" content="' . esc_url( $c['image'] ) . '">' . "\n"; }
	foreach ( $c['ld'] as $block ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . "</script>\n";
	}
	echo "<!-- /SEO -->\n";
}, 1 );

/* ------------------------------------------------------------------ sitemap -- */

/* No author sitemap (the live site had none). */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );

/* Leave noindex posts out. */
add_filter( 'wp_sitemaps_posts_query_args', function ( $args ) {
	$args['meta_query'] = array(
		'relation' => 'OR',
		array( 'key' => 'seo_noindex', 'compare' => 'NOT EXISTS' ),
		array( 'key' => 'seo_noindex', 'value' => '1', 'compare' => '!=' ),
	);
	return $args;
} );

/* Only what the live site indexed: no attachment pages, no empty tag archives. */
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	unset( $types['attachment'] );
	return $types;
} );
add_filter( 'wp_sitemaps_taxonomies', function ( $tax ) {
	unset( $tax['post_tag'] );
	return $tax;
} );

/* Attachment pages are not a destination: the file's own URL stays at wp-content/uploads. */
add_filter( 'pre_option_wp_attachment_pages_enabled', function () { return '0'; } );

/* robots.txt (virtual): the live one allowed everything, and added Crawl-delay 10, which is dropped.
   WordPress already appends "Sitemap: .../wp-sitemap.xml". Keep search results out of the index. */
add_filter( 'robots_txt', function ( $output, $public ) {
	if ( ! $public ) { return $output; }
	return preg_replace( '/^(User-agent: \*\R)/m', "$1Disallow: /?s=\nDisallow: /search/\n", $output, 1 );
}, 10, 2 );
