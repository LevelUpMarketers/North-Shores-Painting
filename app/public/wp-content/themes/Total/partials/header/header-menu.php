<?php

use TotalTheme\Header\Menu as Header_Menu;

/**
 * Header menu template part.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.20
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Header\Menu' ) ) {
	return;
}

wpex_hook_main_menu_before();

?>

<div id="site-navigation-wrap" <?php echo Header_Menu::wrapper_class(); ?>>
	<nav id="site-navigation" <?php echo Header_Menu::inner_class(); ?><?php wpex_aria_label( 'site_navigation' ); ?>><?php
		wpex_hook_main_menu_top();
		Header_Menu::wp_nav_menu();
		wpex_hook_main_menu_bottom();
	?></nav>
</div>

<?php
wpex_hook_main_menu_after();
