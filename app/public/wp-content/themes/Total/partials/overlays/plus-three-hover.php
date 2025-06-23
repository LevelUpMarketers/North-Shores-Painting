<?php

/**
 * Overlay: Plus Three Hover.
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

<div class="overlay-plus-three-hover overlay-hide theme-overlay wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo totaltheme_get_overlay_speed(); ?> wpex-text-accent wpex-flex wpex-items-center wpex-justify-center wpex-text-6xl" aria-hidden="true">
	<span class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color( 'black' ); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity( '70' ); ?>"></span>
    <?php echo totaltheme_get_icon( 'plus-circle', 'overlay-content overlay-transform wpex-relative wpex-leading-none wpex-translate-y-50 wpex-transition-all wpex-duration-300' ); ?>
</div>
