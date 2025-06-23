<?php

/**
 * Overlay: Video Icon #4
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( 'inside_link' !== $position ) {
	return;
}

?>

<div class="overlay-video-icon_4 theme-overlay overlay-transform wpex-absolute wpex-inset-0 wpex-flex wpex-items-center wpex-justify-center" aria-hidden="true">
	<span class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity( '20' ); ?>"></span><svg class="overlay__video-svg wpex-transition-transform wpex-duration-300 wpex-max-w-20 wpex-relative" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="60px" viewBox="0 0 24 24" width="60px" fill="#FFFFFF"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M9.5,14.67V9.33c0-0.79,0.88-1.27,1.54-0.84 l4.15,2.67c0.61,0.39,0.61,1.29,0,1.68l-4.15,2.67C10.38,15.94,9.5,15.46,9.5,14.67z"/></g></svg>
</div>
