<?php

/**
 * Overlay: Plus Hover.
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

<div class="overlay-plus-hover overlay-hide theme-overlay wpex-absolute wpex-inset-0 wpex-transition-all wpex-duration-<?php echo totaltheme_get_overlay_speed(); ?>" aria-hidden="true">
	<span class="overlay-bg wpex-flex wpex-items-center wpex-justify-center wpex-bg-<?php echo totaltheme_get_overlay_bg_color(); ?> wpex-block wpex-absolute wpex-inset-0 wpex-opacity-<?php echo totaltheme_get_overlay_opacity(); ?>"><svg xmlns="http://www.w3.org/2000/svg" height="40" width="40" stroke="#fff"><path d="M-83 26.947h7.225v234.297H-83zM20 0h0v20h20 0-20v20h0V20H0h0 20z"/></svg></span>
</div>
