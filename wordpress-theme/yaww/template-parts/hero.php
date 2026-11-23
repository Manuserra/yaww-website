<?php
/**
 * Home hero (index.html section 1): the looping footage with its poster, the promise, the
 * proof row and the scroll cue. Verbatim from the static page; the clip and poster are the
 * theme's synced assets (assets/media/hero-home.mp4, assets/images/hero-home-poster.jpg).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

  <!-- =========================================================
       1 · HERO — Promise
       ========================================================= -->
  <section class="home-hero home-hero--overlay">
    <div class="home-hero__media home-hero__media--video">
      <!-- Decorative background footage: muted, looping, no controls,
           and playsinline so iOS doesn't take it fullscreen. preload
           is auto (not lazy like the photo is on index.html) because
           this is the first thing on the page — there is nothing to
           defer it behind. The <img> inside is the fallback for a
           browser that won't play the file at all. -->
      <video
        src="<?php echo esc_url( yaww_asset_url( 'media/hero-home.mp4' ) ); ?>"
        poster="<?php echo esc_url( yaww_asset_url( 'images/hero-home-poster.jpg' ) ); ?>"
        autoplay muted loop playsinline preload="auto"
        aria-hidden="true" tabindex="-1">
        <img src="<?php echo esc_url( yaww_asset_url( 'images/hero-home-poster.jpg' ) ); ?>" alt="Guests on a YAWW retreat swimming in open water beside two anchored sailboats">
      </video>
      <!-- Copy sits on the footage, behind a light scrim (see
           .home-hero--overlay in home.css) so it stays legible on the
           brightest frames without darkening the whole clip. -->
      <div class="home-hero__copy">
        <h1 class="home-hero__title">Travel that moves you</h1>
        <p class="home-hero__desc">Thoughtfully curated journeys for curious travelers. <br>Exceptional yoga, authentic cultural experiences, incredible food, <br>and good company in some of the world's most inspiring destinations.</p>
        <div class="home-hero__proof">
          <div class="home-hero__proof-item">
            <span class="home-hero__proof-value">Since 2008</span>
            <span class="home-hero__proof-label">Exploring together</span>
          </div>
          <div class="home-hero__proof-item">
            <span class="home-hero__proof-value">★ 4.9</span>
            <span class="home-hero__proof-label">Google Reviews</span>
          </div>
          <div class="home-hero__proof-item">
            <span class="home-hero__proof-value">600+</span>
            <span class="home-hero__proof-label">Retreats hosted</span>
          </div>
        </div>
      </div>
      <!-- Scroll cue, ported from evaneos.es's hero: a 48px button with
           Material's keyboard_arrow_down filling it, a 4s loop that
           fades in near the top and fades out drifting down, paused on
           hover; a click scrolls one viewport down, smoothly. -->
      <button type="button" class="home-hero__cue" aria-label="Scroll down">
        <svg viewBox="0 -960 960 960" fill="currentColor" aria-hidden="true"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
      </button>
    </div>
  </section>
