<?php

/**
 * Overlay: Video Icon.
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

<div class="overlay-icon overlay-icon-video"><?php totaltheme_call_static( 'Theme_Icons', 'render_icon', 'play' ); ?></div>
