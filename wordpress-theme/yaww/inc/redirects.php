<?php
/**
 * 301 redirects from every URL of the live site (WordPress + Yoast, 2026-11-23) and of the static
 * prototype to their equivalents here. Docs: docs/wp-url-map.md (generated from the same data).
 *
 * Rules
 * - Runs on template_redirect (priority 1) and ONLY when WordPress would answer 404. A URL that
 *   exists (a journal post that kept its slug, a page created with an old slug) is never touched,
 *   so "same URL" costs nothing and there can be no loops.
 * - Each map entry lists candidate targets; the first one that exists wins, the last one is the
 *   fallback and is used without a check. Matching is case-insensitive and ignores trailing slashes.
 * - Attachment pages (the live sitemap has 6,345 of them, /retreat/<slug>/<file>/, /team/<file>/ ...)
 *   follow their parent: the last segment is dropped until a rule matches.
 * - The query string is kept (UTM tags).
 *
 * Loaded from functions.php: require_once YAWW_DIR . '/inc/redirects.php';
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Live site paths (lowercase, no slashes) => candidate new paths. Generated; edit docs/wp-url-map.md's source or add rows here. */
function yaww_redirect_map() {
	return array(
	'faqs' => array( '/contact/' ),
	'salsa-retreats' => array( '/retreats/' ),
	'test-template' => array( '/' ),
	'creative-expression' => array( '/retreats/' ),
	'power-retreats' => array( '/retreats/' ),
	'tribe-test' => array( '/' ),
	'press' => array( '/about/' ),
	'privacy_policies' => array( '/contact/' ),
	'yoga-food-and-wine' => array( '/retreats/' ),
	'thank-you-contact' => array( '/contact/' ),
	'give-the-gift-of-yoga' => array( '/contact/' ),
	'yoga' => array( '/retreats/' ),
	'contact-about-yoga-retreats' => array( '/contact/' ),
	'gives-global-program' => array( '/about/' ),
	'yoga-teacher-training' => array( '/retreats/' ),
	'termsandconditions' => array( '/contact/' ),
	'about-our-yoga-retreats' => array( '/about/' ),
	'cancellation_policies' => array( '/contact/' ),
	'team' => array( '/facilitators/' ),
	'all-upcoming-dates' => array( '/retreats/' ),
	'journals' => array( '/journal/' ),
	'the-smart-yogis-travel-guide-to-cuba-september-2018' => array( '/the-smart-yogis-travel-guide-to-cuba-september-2018/', '/journal/' ),
	'the-utopian-newsletter-art-issue-july-11-2018' => array( '/the-utopian-newsletter-art-issue-july-11-2018/', '/journal/' ),
	'summer-solstice-2018' => array( '/summer-solstice-2018/', '/journal/' ),
	'travel_and_yoga' => array( '/synergy-of-travel-and-retreats/', '/journal/' ),
	'following-your-heart' => array( '/following-my-heart/', '/journal/' ),
	'how-to-meditate' => array( '/yogis-guide-how-to-meditate/', '/journal/' ),
	'why-we-love-santorini' => array( '/why-we-love-santorini/', '/journal/' ),
	'namaste-and-mean-it' => array( '/namaste-and-mean-it/', '/journal/' ),
	'gay-havana' => array( '/i-include-myself-gay-havana/', '/journal/' ),
	'the-utopian-newsletter-interior-design-issue-september-12-2018' => array( '/the-utopian-newsletter-interior-design-issue-september-12-2018/', '/journal/' ),
	'libra-full-moon' => array( '/libra-full-moon/', '/journal/' ),
	'listening-to-joy' => array( '/listening-to-joy/', '/journal/' ),
	'the-utopian-newsletter-gives-global-issue-august-1-2018' => array( '/the-utopian-newsletter-gives-global-issue-august-1-2018/', '/journal/' ),
	'the-utopian-newsletter-eat-drink-be-merry-issue-august-8-2018' => array( '/the-utopian-newsletter-eat-drink-be-merry-issue-august-8-2018/', '/journal/' ),
	'virgo-full-moon' => array( '/virgo-full-moon/', '/journal/' ),
	'harness-the-overwhelm' => array( '/harness-the-overwhelm/', '/journal/' ),
	'the-utopian-newsletter-henna-issue-july-18-2018' => array( '/the-utopian-newsletter-henna-issue-july-18-2018/', '/journal/' ),
	'the-utopian-newsletter-summer-issue-2018' => array( '/the-utopian-newsletter-summer-issue-2018/', '/journal/' ),
	'the-utopian-newsletter-shopping-issue-august-22-2018' => array( '/the-utopian-newsletter-shopping-issue-august-22-2018/', '/journal/' ),
	'stong_hearts' => array( '/on-strong-hearts/', '/journal/' ),
	'tacos-are-life' => array( '/tacos-are-life/', '/journal/' ),
	'the-utopian-newsletter-cuban-revolution-issue-july-25-2018' => array( '/the-utopian-newsletter-cuban-revolution-issue/', '/journal/' ),
	'yoga-retreat-travel-musts' => array( '/yoga-retreat-travel-musts/', '/journal/' ),
	'new-moon-in-aquarius' => array( '/new-moon-in-aquarius/', '/journal/' ),
	'open-letter-to-all-cuba-retreat-goers' => array( '/open-letter-to-all-cuba-retreat-goers/', '/journal/' ),
	'why-go-on-a-yoga-retreat' => array( '/why-go-on-a-yoga-retreat/', '/journal/' ),
	'getting-to-know-you-amy-appel' => array( '/getting-to-know-you-amy-appel/', '/journal/' ),
	'travel-makes-you-happy' => array( '/travel-makes-you-happy/', '/journal/' ),
	'finding-stillness' => array( '/finding-stillness-a-practice/', '/journal/' ),
	'the-gift-that-yoga-offers' => array( '/the-gift-that-yoga-offers/', '/journal/' ),
	'the-shifting' => array( '/the-shifting/', '/journal/' ),
	'a-love-letter-to-basque-country' => array( '/a-love-letter-to-basque-country/', '/journal/' ),
	'sitting-with-death-the-beauty-of-dia-de-muertos' => array( '/sitting-with-death-the-beauty-of-dia-de-muertos/', '/journal/' ),
	'a-somatic-journey-in-morocco' => array( '/a-somatic-journey-in-morocco/', '/journal/' ),
	'category/asana' => array( '/journal/' ),
	'category/astrology-tarot' => array( '/journal/' ),
	'category/foodies' => array( '/journal/' ),
	'category/guides' => array( '/journal/' ),
	'category/inspo' => array( '/journal/' ),
	'category/lifestyle' => array( '/journal/' ),
	'category/newsletter' => array( '/journal/' ),
	'category/travel' => array( '/journal/' ),
	'category/yoga' => array( '/journal/' ),
	'journals/all' => array( '/journal/' ),
	'journals/asana' => array( '/journal/' ),
	'journals/astrology-tarot' => array( '/journal/' ),
	'journals/community' => array( '/journal/' ),
	'journals/guides' => array( '/journal/' ),
	'journals/inspiration' => array( '/journal/' ),
	'journals/lifestyle' => array( '/journal/' ),
	'journals/newsletters' => array( '/journal/' ),
	'journals/practice' => array( '/journal/' ),
	'journals/travel' => array( '/journal/' ),
	'journals/witchy' => array( '/journal/' ),
	'retreats/archived' => array( '/retreats/' ),
	'retreats/art-creative' => array( '/retreats/' ),
	'retreats/custom-retreats' => array( '/contact/#private-groups' ),
	'retreats/family-retreats' => array( '/retreats/' ),
	'retreats/power-retreats' => array( '/retreats/' ),
	'retreats/signature-retreats' => array( '/tier/signature-collection/', '/retreats/' ),
	'retreats/yoga' => array( '/retreats/' ),
	'retreats/yoga-food-and-wine' => array( '/retreats/' ),
	'retreat/yoga-and-pilates-retreat' => array( '/retreats/' ),
	'retreat/creative-retreat-in-cuba' => array( '/retreats/' ),
	'retreat/rockin-havana-with-sadie-nardini' => array( '/retreats/' ),
	'retreat/the-lush-life' => array( '/retreats/' ),
	'retreat/cuban-salsa-retreats' => array( '/retreats/' ),
	'retreat/guatemala-yoga-art-retreat' => array( '/retreats/' ),
	'retreat/guatemala-yoga-aerial-silks-retreat' => array( '/retreats/' ),
	'retreat/yoga-retreat-colombia' => array( '/retreats/' ),
	'retreat/power-yoga-r3bar-holbox-island' => array( '/retreats/' ),
	'retreat/tulum-yoga-art-retreat' => array( '/retreats/' ),
	'retreat/farm-to-table-retreats' => array( '/retreats/' ),
	'retreat/yoga-retreat-holbox-island' => array( '/retreats/' ),
	'retreat/cuba-yoga-retreat-art' => array( '/retreats/' ),
	'retreat/sup-yoga-retreat-holbox-island' => array( '/retreats/' ),
	'retreat/yoga-retreat-tulum-mexico' => array( '/retreats/' ),
	'retreat/beach-yoga-retreat-tulum' => array( '/retreats/' ),
	'retreat/yoga-retreat-in-oaxaca' => array( '/locations/oaxaca/', '/retreats/' ),
	'retreat/yoga-teacher-training-mexico' => array( '/retreats/' ),
	'retreat/yoga-retreat-little-corn-island' => array( '/retreats/' ),
	'retreat/napa-valley-wine-yoga-retreats' => array( '/retreats/' ),
	'retreat/custom-retreats' => array( '/contact/#private-groups' ),
	'retreat/yoga-retreat-in-costa-rica' => array( '/retreats/' ),
	'retreat/croatia-family-yoga-retreat' => array( '/retreats/' ),
	'retreat/power_yoga_barre_boxing' => array( '/retreats/' ),
	'retreat/yoga-astrology-retreats' => array( '/retreats/' ),
	'retreat/yoga-retreat-cuba' => array( '/retreats/' ),
	'retreat/basque-country-spain-yoga-gastronomy-retreat' => array( '/retreats/spain-basque-country/', '/locations/spain-basque-country/', '/retreats/' ),
	'retreat/thailand-yoga-reverence-retreat' => array( '/locations/thailand/', '/retreats/' ),
	'retreat/croatia-yoga-food-wine-retreat' => array( '/retreats/' ),
	'retreat/peru-machu-picchu-yoga-retreat' => array( '/retreats/' ),
	'retreat/istanbul-yoga-retreat-art' => array( '/retreats/turkey/', '/locations/turkey/', '/retreats/' ),
	'retreat/embodied-the-art-of-being-seen' => array( '/retreats/el-cuyo-feb-2027-morin-gray/', '/locations/el-cuyo/', '/retreats/' ),
	'retreat/the-paris-champagne-experience' => array( '/retreats/france/', '/locations/france/', '/retreats/' ),
	'retreat/yoga-food-wine-adventure-sardinia' => array( '/retreats/sardinia/', '/locations/sardinia/', '/retreats/' ),
	'retreat/yoga-retreat-in-iceland' => array( '/retreats/iceland/', '/locations/iceland/', '/retreats/' ),
	'retreat/yoga-retreat-day-of-the-dead-oaxaca' => array( '/retreats/oaxaca-oct-2027/', '/locations/oaxaca/', '/retreats/' ),
	'retreat/treasures-of-thailand-a-yoga-wellness-retreat' => array( '/retreats/thailand-dec-2026/', '/locations/thailand/', '/retreats/' ),
	'retreat/yoga-retreat-montenegro' => array( '/locations/montenegro/', '/retreats/' ),
	'retreat/yoga-retreat-argentina' => array( '/retreats/argentina-mar-2027/', '/locations/argentina/', '/retreats/' ),
	'retreat/mini-yoga-retreats-in-paradise' => array( '/retreats/el-cuyo-jan-2027-coffey/', '/locations/el-cuyo/', '/retreats/' ),
	'retreat/camino-de-santiago' => array( '/locations/camino-de-santiago/', '/retreats/' ),
	'retreat/yoga-retreat-morocco' => array( '/retreats/morocco/', '/locations/morocco/' ),
	'retreat/beach-yoga-retreat-mexico' => array( '/locations/el-cuyo/', '/retreats/' ),
	'retreat/yoga-retreat-portugal' => array( '/retreats/portugal-may-2027/', '/locations/portugal/', '/retreats/' ),
	'retreat/yoga-retreat-greece' => array( '/locations/santorini/', '/retreats/' ),
	'retreat/morocco-yoga-retreat-deep-dive' => array( '/retreats/morocco/', '/locations/morocco/' ),
		// Yoast XML sitemaps, still submitted in Search Console.
		'sitemap_index.xml'            => array( '/wp-sitemap.xml' ),
		'post-sitemap.xml'             => array( '/wp-sitemap.xml' ),
		'page-sitemap.xml'             => array( '/wp-sitemap.xml' ),
		'retreat-sitemap.xml'          => array( '/wp-sitemap.xml' ),
		'category-sitemap.xml'         => array( '/wp-sitemap.xml' ),
		'journal_categories-sitemap.xml' => array( '/wp-sitemap.xml' ),
		'retreat_categories-sitemap.xml' => array( '/wp-sitemap.xml' ),
		'attachment-sitemap.xml'       => array( '/wp-sitemap.xml' ),
		// Whole old sections that are not listed above (new slugs of the old retreat/category bases).
		'category'                     => array( '/journal/' ),
		'retreat'                      => array( '/retreats/' ),
		'home'                         => array( '/' ),
	);
}

/** Does this site path exist (published)? Fragments and query are ignored. */
function yaww_redirect_exists( $path ) {
	$path = strtok( $path, '#?' );
	if ( '/' === $path || '/wp-sitemap.xml' === $path ) { return true; }
	if ( preg_match( '#^/tier/([^/]+)/$#', $path, $m ) ) { return (bool) get_term_by( 'slug', $m[1], 'retreat_tier' ); }
	if ( in_array( $path, array( '/retreats/', '/locations/', '/teachers/', '/facilitators/', '/journal/' ), true ) ) { return true; }
	return url_to_postid( home_url( $path ) ) > 0;
}

/** First candidate that exists (the last one is unconditional). */
function yaww_redirect_pick( $candidates ) {
	$last = count( $candidates ) - 1;
	foreach ( $candidates as $i => $c ) {
		if ( $i === $last || yaww_redirect_exists( $c ) ) { return $c; }
	}
	return null;
}

/** Static-prototype URLs (retreatpage-*.html ...) => candidates, or null. */
function yaww_redirect_static( $file ) {
	if ( ! preg_match( '#\.html$#', $file ) ) { return null; }
	$f = substr( $file, 0, -5 );
	$locations = array( 'el-cuyo', 'oaxaca', 'argentina', 'santorini', 'camino-de-santiago', 'montenegro', 'portugal', 'iceland', 'france', 'sardinia', 'spain-basque-country', 'morocco', 'thailand', 'turkey' );
	$top = array( 'retreats' => '/retreats/', 'retreats-full' => '/retreats/', 'locations' => '/locations/', 'about' => '/about/', 'teachers' => '/teachers/',
		'facilitators' => '/facilitators/', 'team' => '/facilitators/', 'journal' => '/journal/', 'contact' => '/contact/', 'teach-with-us' => '/teach-with-us/', 'index' => '/' );
	if ( isset( $top[ $f ] ) ) { return array( $top[ $f ] ); }
	if ( preg_match( '#^retreatpage-(.+)$#', $f, $m ) ) {
		$id = ( 'iceland-oct-2026' === $m[1] ) ? 'iceland' : $m[1];   // same retreat, canonical was retreatpage-iceland.html
		$c  = array( '/retreats/' . $id . '/' );
		foreach ( $locations as $l ) {
			if ( 0 === strpos( $id, $l ) ) { $c[] = '/locations/' . $l . '/'; break; }
		}
		if ( 'elcuyo' === $id ) { $c = array( '/locations/el-cuyo/' ); }
		$c[] = '/retreats/';
		return $c;
	}
	if ( preg_match( '#^locationpage-(.+)$#', $f, $m ) ) { return array( '/locations/' . $m[1] . '/', '/locations/' ); }
	if ( preg_match( '#^teacher-(.+)$#', $f, $m ) )      { return array( '/teachers/' . $m[1] . '/', '/teachers/' ); }
	if ( preg_match( '#^(?:facilitator|team)-(.+)$#', $f, $m ) ) { return array( '/facilitators/' . $m[1] . '/', '/teachers/' . $m[1] . '/', '/facilitators/' ); }
	return null;
}

/** Target path for a request path (lowercase, trimmed), or null. */
function yaww_redirect_resolve( $key ) {
	$map  = yaww_redirect_map();
	$segs = '' === $key ? array() : explode( '/', $key );
	// journal/<slug>.html of the static site
	if ( 2 === count( $segs ) && 'journal' === $segs[0] && preg_match( '#^(.+)\.html$#', $segs[1], $m ) ) {
		// 7 posts kept the live site's slug: find the post by its static id first.
		$ids = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'fields' => 'ids', 'numberposts' => 1, 'meta_key' => '_yaww_source_id', 'meta_value' => $m[1] ) );
		if ( $ids ) { return array( wp_make_link_relative( get_permalink( $ids[0] ) ) ); }
		return array( '/' . $m[1] . '/', '/journal/' );
	}
	// full path first, then the parent (attachment pages, deep old URLs)
	for ( $n = count( $segs ); $n >= 1; $n-- ) {
		$k = implode( '/', array_slice( $segs, 0, $n ) );
		if ( isset( $map[ $k ] ) ) { return $map[ $k ]; }
		if ( 1 === $n ) {
			$s = yaww_redirect_static( $k );
			if ( $s ) { return $s; }
		}
	}
	// Old base with an unknown slug: send to the section.
	if ( $segs ) {
		$first = $segs[0];
		if ( 'retreat' === $first )                                { return array( '/retreats/' ); }
		if ( in_array( $first, array( 'journals', 'category' ), true ) ) { return array( '/journal/' ); }
	}
	return null;
}

add_action( 'template_redirect', function () {
	if ( ! is_404() ) { return; }
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( '/' !== $base && 0 === strpos( $path, $base ) ) { $path = '/' . substr( $path, strlen( $base ) ); }
	$key = strtolower( trim( rawurldecode( $path ), '/' ) );
	if ( preg_match( '#^(wp-admin|wp-json|wp-content|wp-includes|xmlrpc)#', $key ) ) { return; }

	$cands = yaww_redirect_resolve( $key );
	if ( ! $cands ) { return; }
	$to = yaww_redirect_pick( $cands );
	if ( ! $to || trim( strtok( $to, '#' ), '/' ) === $key ) { return; }

	$url = home_url( $to );
	$qs  = wp_parse_url( $uri, PHP_URL_QUERY );
	if ( $qs ) {
		$hash = '';
		if ( false !== ( $p = strpos( $url, '#' ) ) ) { $hash = substr( $url, $p ); $url = substr( $url, 0, $p ); }
		$url .= ( false === strpos( $url, '?' ) ? '?' : '&' ) . $qs . $hash;
	}
	header( 'X-Redirect-By: yaww-redirects' );
	wp_redirect( $url, 301, 'YAWW' );
	exit;
}, 1 );
