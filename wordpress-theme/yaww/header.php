<?php
/**
 * Site header: the static pages' markup (skip link, logo sprite, brand row, nav row) with
 * WordPress URLs. js/nav.js enhances the nav row (dropdown panels, mobile sheet) exactly as
 * on the static site; assets/js-theme/wp-links.js bridges its .html links (see that file).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/views.php';

/* The section the current page belongs to: its top nav link gets aria-current="page". */
$yaww_section = '';
if ( is_post_type_archive( 'retreat' ) || is_tax( 'retreat_tier' ) ) { $yaww_section = 'retreats'; }
elseif ( is_post_type_archive( 'location' ) ) { $yaww_section = 'locations'; }
elseif ( is_page( 'about' ) ) { $yaww_section = 'about'; }
elseif ( is_home() ) { $yaww_section = 'journal'; }
elseif ( is_page( 'contact' ) ) { $yaww_section = 'contact'; }
$yaww_cur = function ( $key ) use ( $yaww_section ) { return $key === $yaww_section ? ' aria-current="page"' : ''; };
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>

<?php get_template_part( 'template-parts/logo-symbol' ); ?>

<header class="site-header">
  <div class="site-header__brand">
    <div class="container">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" aria-label="Yoga Adventures Worldwide — home">
        <svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg>
      </a>
    </div>
  </div>
</header>

<!-- A sibling of <header>, not nested inside it: position:sticky only stays
     pinned while part of its containing block is in the scrollport. -->
<nav class="site-header__nav" aria-label="Primary">
    <div class="container">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo site-header__logo--nav" aria-label="Yoga Adventures Worldwide — home">
        <svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg>
      </a>
      <button class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="Menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
          <path d="M3 6h18M3 12h18M3 18h18"/>
        </svg>
      </button>
      <ul class="nav-list" id="primary-nav">
        <li><a href="<?php echo yaww_u( 'retreats.html' ); ?>"<?php echo $yaww_cur( 'retreats' ); ?>>Retreats</a></li>
        <li><a href="<?php echo yaww_u( 'locations.html' ); ?>"<?php echo $yaww_cur( 'locations' ); ?>>Locations</a></li>
        <li class="nav-item nav-item--has-sub">
        <a href="<?php echo yaww_u( 'about.html' ); ?>" aria-haspopup="true" aria-expanded="false"<?php echo $yaww_cur( 'about' ); ?>>About</a>
        <ul class="nav-sub">
          <li><a href="<?php echo yaww_u( 'about.html' ); ?>">About Us</a></li>
          <li><a href="<?php echo yaww_u( 'teachers.html' ); ?>">Teachers</a></li>
          <li><a href="<?php echo yaww_u( 'facilitators.html' ); ?>">Facilitators</a></li>
        </ul>
      </li>
        <li><a href="<?php echo yaww_u( 'journal.html' ); ?>"<?php echo $yaww_cur( 'journal' ); ?>>Journal</a></li>
        <li class="nav-item nav-item--has-sub">
        <a href="<?php echo yaww_u( 'contact.html' ); ?>" aria-haspopup="true" aria-expanded="false"<?php echo $yaww_cur( 'contact' ); ?>>Contact</a>
        <ul class="nav-sub">
          <li><a href="<?php echo yaww_u( 'contact.html' ); ?>">Ask a question</a></li>
          <li><a href="<?php echo yaww_u( 'contact.html#private-groups' ); ?>">Private groups</a></li>
          <li><a href="<?php echo yaww_u( 'teach-with-us.html' ); ?>">Teach with us</a></li>
        </ul>
      </li>
      </ul>
    </div>
  </nav>

<main id="main">
