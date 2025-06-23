<?php

use TotalTheme\Topbar\Core as Topbar;

/**
 * Topbar layout
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 5.10
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Topbar\Core' ) ) {
	return;
}

?>

<?php wpex_hook_topbar_before(); ?>

	<?php if ( ! wpex_theme_do_location( 'topbar' ) ) : ?>

		<div id="top-bar-wrap" <?php Topbar::wrapper_class(); ?>>

			<div id="top-bar" <?php Topbar::inner_class(); ?>><?php

				wpex_hook_topbar_inner();

			?></div>

		</div>

	<?php endif; ?>

<?php wpex_hook_topbar_after(); ?>
