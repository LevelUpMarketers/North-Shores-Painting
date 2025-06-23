<?php

/**
 * Overlay: Title Date Visibile.
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

// Get post data.
$title = $args['post_title'] ?? get_the_title();
$date  = $args['post_date'] ?? get_the_date();

?>

<div class="overlay-title-date-visible theme-overlay wpex-absolute wpex-inset-0 wpex-flex wpex-items-center wpex-justify-center wpex-text-center">
	<div class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity(); ?>"></div>
	<div class="overlay-content wpex-relative wpex-text-white wpex-p-15 wpex-clr">
		<div class="overlay-title wpex-text-lg"><?php echo esc_html( $title ); ?></div>
		<div class="overlay-date wpex-opacity-80 wpex-italic"><?php echo esc_html( $date ); ?></div>
	</div>
</div>
