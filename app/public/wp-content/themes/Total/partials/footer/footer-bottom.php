<?php

use TotalTheme\Footer\Bottom\Core as Footer_Bottom;

/**
 * Footer bottom content.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Footer\Bottom\Core' ) ) {
	return;
}

?>

<?php wpex_hook_footer_bottom_before(); ?>

<?php if ( ! wpex_theme_do_location( 'footer_bottom' ) ) : ?>

	<div id="footer-bottom" <?php Footer_Bottom::wrapper_class(); ?>>

		<?php wpex_hook_footer_bottom_top(); ?>

		<div id="footer-bottom-inner" <?php Footer_Bottom::inner_class(); ?>><?php

			wpex_hook_footer_bottom_inner();

		?></div>

		<?php wpex_hook_footer_bottom_bottom(); ?>

	</div>

<?php endif; ?>

<?php wpex_hook_footer_bottom_after(); ?>
