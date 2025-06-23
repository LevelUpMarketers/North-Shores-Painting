<?php

/**
 * Overlay: Video Icon #3
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

<div class="overlay-video-icon_3 theme-overlay overlay-transform wpex-absolute wpex-inset-0 wpex-flex wpex-items-center wpex-justify-center" aria-hidden="true">
	<span class="overlay-bg wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity( '20' ); ?>"></span><svg class="overlay__video-svg wpex-transition-transform wpex-duration-300 wpex-max-w-20 wpex-relative" xmlns="http://www.w3.org/2000/svg" height="60px" viewBox="0 0 24 24" width="60px" fill="#FFFFFF"><path d="M10.8 15.9l4.67-3.5c.27-.2.27-.6 0-.8L10.8 8.1c-.33-.25-.8-.01-.8.4v7c0 .41.47.65.8.4zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
</div>
