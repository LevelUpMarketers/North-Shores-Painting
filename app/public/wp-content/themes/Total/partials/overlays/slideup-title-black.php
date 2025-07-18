<?php

/**
 * Overlay: Slide Up Title Black.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

// Only used for inside position.
if ( 'inside_link' !== $position ) {
	return;
}

if ( 'staff' === get_post_type() ) {
	$content = get_post_meta( get_the_ID(), 'wpex_staff_position', true );
} else {
	$content = $args['post_title'] ?? get_the_title();
}

?>

<div class="overlay-slideup-title overlay-hide theme-overlay wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo totaltheme_get_overlay_speed(); ?> wpex-overflow-hidden wpex-flex wpex-items-center wpex-justify-center">

	<div class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color( 'black' ); ?> wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity( '80' ); ?>"></div>

	<div class="overlay-content overlay-transform wpex-relative wpex-text-md wpex-text-white wpex-text-center wpex-font-semibold wpex-transition-all wpex-duration-300 wpex-px-20 wpex-translate-y-100"><?php echo apply_filters( 'wpex_overlay_content_slideup-title-white',  '<span class="title">' . wp_kses_post( $content ) . '</span>' ); ?></div>

</div>
