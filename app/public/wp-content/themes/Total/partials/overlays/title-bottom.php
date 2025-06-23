<?php

/**
 * Overlay: Title Botom.
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

// Title is required.
if ( ! $title ) {
	return;
}

?>

<div class="overlay-title-bottom theme-overlay wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-py-10 wpex-px-20 wpex-text-white wpex-text-md wpex-text-center">
	<?php echo apply_filters( 'wpex_overlay_content_title-bottom', esc_html( $title ) ); ?>
</div>
