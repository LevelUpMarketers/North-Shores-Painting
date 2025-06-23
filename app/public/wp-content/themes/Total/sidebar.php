<?php

use TotalTheme\Sidebars\Primary as Sidebar;

/**
 * Primary sidebar area.
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Sidebars\Primary' ) ) {
	return;
}

/**
 * Hook: wpex_hook_sidebar_before.
 */
wpex_hook_sidebar_before();

?>

<aside id="sidebar" <?php Sidebar::wrapper_class(); ?>>

	<?php
	/**
	 * Hook: wpex_hook_sidebar_top.
	 */
	wpex_hook_sidebar_top(); ?>

	<div id="sidebar-inner" <?php Sidebar::inner_class(); ?>><?php

		/**
		 * Hook: wpex_hook_sidebar_inner.
		 *
		 * @hooked wpex_display_sidebar - 10
		 */
		wpex_hook_sidebar_inner();

	?></div>

	<?php
	/**
	 * Hook: wpex_hook_sidebar_bottom.
	 */
	wpex_hook_sidebar_bottom(); ?>

</aside>

<?php
/**
 * Hook: wpex_hook_sidebar_after.
 */
wpex_hook_sidebar_after();
