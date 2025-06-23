<?php

use TotalTheme\Header\Core as Header;

/**
 * Site header.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Core' ) ) {
	return;
}

wpex_hook_header_before();;

if ( ! wpex_theme_do_location( 'header' ) ) {
	if ( totaltheme_call_static( 'Header\Sticky', 'is_enabled' ) && 'css' !== totaltheme_call_static( 'Header\Sticky', 'style' ) ) {
		$is_sticky = true;
		echo '<div id="site-header-sticky-wrapper" class="wpex-sticky-header-holder not-sticky wpex-print-hidden">';
	}
	?>
	<header id="site-header" <?php Header::wrapper_class(); ?>>
		<?php wpex_hook_header_top(); ?>
		<div id="site-header-inner" <?php Header::inner_class(); ?>><?php wpex_hook_header_inner(); ?></div>
		<?php wpex_hook_header_bottom(); ?>
	</header>
<?php
	if ( isset( $is_sticky ) ) {
		echo '</div>';
	}
}

wpex_hook_header_after();
