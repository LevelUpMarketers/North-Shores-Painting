<?php

use TotalTheme\Footer\Bottom\Menu as Footer_Bottom_Menu;

/**
 * Footer bottom menu.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.3
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Footer\Bottom\Menu' ) ) {
	return;
}

$menu = Footer_Bottom_Menu::get_menu();

if ( ! $menu ) {
	return;
}

?>

<nav id="footer-bottom-menu" <?php Footer_Bottom_Menu::wrapper_class(); ?><?php wpex_aria_label( 'footer_bottom_menu' ); ?>><?php
	echo $menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?></nav>
