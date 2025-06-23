<?php

use TotalTheme\Footer\Core as Footer;
use TotalTheme\Footer\Widgets as Footer_Widgets;

/**
 * Footer Layout.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.10.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TotalTheme\Footer\Core' ) || ! class_exists( 'TotalTheme\Footer\Widgets' ) ) {
	return;
}

?>

<?php wpex_hook_footer_before(); ?>

<?php if ( ! wpex_theme_do_location( 'footer' ) ) : ?>

	<?php if ( Footer_Widgets::is_enabled() ) : ?>

	    <footer id="footer" <?php Footer::wrapper_class(); ?>>

	        <?php wpex_hook_footer_top(); ?>

	        <div id="footer-inner" class="site-footer-inner container wpex-pt-40 wpex-clr"><?php

	        	wpex_hook_footer_inner(); // widgets are added via this hook

	        ?></div>

	        <?php wpex_hook_footer_bottom(); ?>

	    </footer>

	<?php endif; ?>

<?php endif; ?>

<?php wpex_hook_footer_after(); ?>