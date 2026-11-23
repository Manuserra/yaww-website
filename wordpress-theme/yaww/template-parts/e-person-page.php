<?php
/**
 * Teacher / facilitator profile: js/people-cards.js renderPersonPage(), the "Upcoming retreats with"
 * rail and the "Listen & watch" band, rendered on the server. Args: kind ('teacher' | 'team').
 *
 * Fields: featured image (portrait), photo_position, role, short_name, quote, specialties,
 * bio = post content (one <p> per paragraph), youtube (or video_url), spotify_url,
 * website, website_label, instagram. Every optional slot renders only when its field is set.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
require_once get_template_directory() . '/inc/e-helpers.php';
$id   = get_the_ID();
$kind = isset( $args['kind'] ) ? $args['kind'] : 'teacher';
$name = get_the_title( $id );
$first = yaww_e_first_name( $id );
$role = yaww_meta( 'role', $id );
$role = $role ? $role : ( 'team' === $kind ? 'Facilitator' : 'Teacher' );
$bio  = yaww_e_paragraphs( $id );
$quote = 'team' !== $kind ? yaww_meta( 'quote', $id ) : '';
$spec  = 'team' !== $kind ? yaww_list( 'specialties', $id ) : array();

// Website and Instagram: plain links under the bio, new tab.
$links = array();
$web   = yaww_meta( 'website', $id );
if ( $web ) {
	$wl = yaww_meta( 'website_label', $id );
	$links[] = '<li><a href="' . esc_url( $web ) . '" target="_blank" rel="noopener">' . esc_html( $wl ? $wl : 'Website' ) . '</a></li>';
}
$ig = yaww_e_instagram_url( yaww_meta( 'instagram', $id ) );
if ( $ig ) { $links[] = '<li><a href="' . esc_url( $ig ) . '" target="_blank" rel="noopener">Instagram</a></li>'; }

// Retreats and media: teachers only.
$retreats = 'team' !== $kind ? yaww_e_retreats_by_teacher( $id ) : array();
$yt = '';
$sp = '';
if ( 'team' !== $kind ) {
	$yv = yaww_meta( 'youtube', $id );
	$yv = $yv ? $yv : yaww_meta( 'video_url', $id );
	$yt = $yv ? yaww_e_youtube_embed( $yv ) : '';
	$sv = yaww_meta( 'spotify_url', $id );
	$sp = $sv ? yaww_e_spotify_embed( $sv ) : '';
}
?>
  <section class="person-page" data-person="<?php echo esc_attr( get_post_field( 'post_name', $id ) ); ?>" data-person-kind="<?php echo esc_attr( $kind ); ?>"><div class="profile">
    <?php echo yaww_e_person_photo( $id, 'profile__photo', false ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    <div class="profile__text">
      <p class="kicker profile__kicker"><?php echo esc_html( $role ); ?></p>
      <h1 class="profile__name"><?php echo esc_html( $name ); ?></h1>
      <?php if ( $quote ) : ?><blockquote class="profile__quote">&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;</blockquote><?php endif; ?>
      <?php if ( $bio ) : ?>
      <div class="profile__bio"><?php foreach ( $bio as $p ) { echo '<p>' . esc_html( $p ) . '</p>'; } ?></div>
      <?php else : ?>
      <div class="profile__pending"><p class="profile__pending-title">Bio coming soon</p><p>We are still writing this one. In the meantime you can see <?php echo esc_html( $first ); ?>&rsquo;s upcoming retreats below.</p></div>
      <?php endif; ?>
      <?php if ( $spec ) : ?>
      <div class="profile__facts"><h2 class="profile__facts-title">Specialties</h2><ul class="profile__tags"><?php foreach ( $spec as $s ) { echo '<li>' . esc_html( $s ) . '</li>'; } ?></ul></div>
      <?php endif; ?>
      <?php if ( $links ) : ?><ul class="profile__links"><?php echo implode( '', $links ); // phpcs:ignore ?></ul><?php endif; ?>
    </div>
  </div></section>

<?php if ( 'team' !== $kind ) : /* facilitator pages have neither band */ ?>
  <section class="person-retreats" data-person-retreats<?php echo $retreats ? '' : ' hidden'; ?>>
    <h2 class="person-retreats__title" data-person-retreats-title><?php echo $retreats ? 'Upcoming retreats with ' . esc_html( $first ) : 'Upcoming retreats'; ?></h2>
    <div class="person-retreats__grid" data-person-retreats-grid><?php foreach ( $retreats as $rid ) { yaww_part( 'retreat-card', array( 'post_id' => $rid ) ); } ?></div>
  </section>

  <section class="profile-media" data-person-media<?php echo ( $yt || $sp ) ? '' : ' hidden'; ?>><?php if ( $yt || $sp ) : ?><h2 class="section-title profile-media__title">Listen &amp; watch</h2><div class="profile-media__grid<?php echo ( $yt && $sp ) ? ' profile-media__grid--two' : ''; ?>"><?php
	if ( $yt ) {
		echo '<div><p class="kicker profile-media__label">A class with ' . esc_html( $first ) . '</p><div class="profile-media__video"><iframe src="' . esc_url( $yt ) . '" title="' . esc_attr( 'A class with ' . $first ) . '" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="encrypted-media; picture-in-picture" allowfullscreen></iframe></div></div>';
	}
	if ( $sp ) {
		echo '<div><p class="kicker profile-media__label">On ' . esc_html( $first ) . '&rsquo;s playlist</p><div class="profile-media__playlist"><iframe src="' . esc_url( $sp ) . '" title="' . esc_attr( $first ) . '&rsquo;s playlist on Spotify" loading="lazy" allow="encrypted-media"></iframe></div></div>';
	}
	?></div><?php endif; ?></section>

<?php endif; ?>

<?php yaww_part( 'e-closing-cta' ); ?>
