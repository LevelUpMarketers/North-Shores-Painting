<?php

/**
 * Overlay: Hover Title
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

// Only used for inside position
if ( 'inside_link' !== $position ) {
	return;
}

$speed = totaltheme_get_overlay_speed();
$title = $args['post_title'] ?? get_the_title();

if ( ! $title ) {
	return;
}

?>

<div class="overlay-hover-title overlay-hide theme-overlay wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo intval( $speed ); ?> wpex-overflow-hidden wpex-flex wpex-items-center wpex-justify-center wpex-text-center">
	<div class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity(); ?>"></div>
	<div class="overlay-content wpex-relative wpex-text-white wpex-p-15">
		<div class="overlay-title wpex-text-xl wpex-text-balance"><?php echo esc_html( $title ); ?></div>
	</div>
</div>
