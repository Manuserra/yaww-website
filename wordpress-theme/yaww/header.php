<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

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

<?php /* Sibling of <header>, not nested: position:sticky needs the whole page as its containing block (see static pages). */ ?>
<nav class="site-header__nav" aria-label="Primary">
  <div class="container">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo site-header__logo--nav" aria-label="Yoga Adventures Worldwide — home">
      <svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg>
    </a>
    <button class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="Menu">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <ul class="nav-list" id="primary-nav">
      <?php foreach ( yaww_nav_items() as $item ) : ?>
        <?php if ( empty( $item['sub'] ) ) : ?>
          <li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
        <?php else : ?>
          <li class="nav-item nav-item--has-sub">
            <a href="<?php echo esc_url( $item['url'] ); ?>" aria-haspopup="true" aria-expanded="false"><?php echo esc_html( $item['label'] ); ?></a>
            <ul class="nav-sub">
              <?php foreach ( $item['sub'] as $s ) : ?>
                <li><a href="<?php echo esc_url( $s[1] ); ?>"><?php echo esc_html( $s[0] ); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
</nav>

<main>
