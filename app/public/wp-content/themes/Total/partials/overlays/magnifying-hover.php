<?php

/**
 * Overlay: Magnifying Hover.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( 'inside_link' !== $position ) {
	return;
}

?>

<div class="magnifying-hover theme-overlay overlay-hide wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo totaltheme_get_overlay_speed(); ?> wpex-text-white wpex-text-2xl wpex-flex wpex-items-center wpex-justify-center" aria-hidden="true">
	<span class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity(); ?>"></span>
	<?php echo totaltheme_get_icon( 'search', 'wpex-relative' ); ?>
</div>
