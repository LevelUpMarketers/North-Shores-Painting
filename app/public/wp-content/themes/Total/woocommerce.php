<?php

/**
 * WooCommerce Default template.
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 6.1
 */

defined( 'ABSPATH' ) || exit;

get_header();

?>

<div id="content-wrap" <?php totaltheme_content_wrap_class(); ?>>

	<?php wpex_hook_primary_before(); ?>

	<div id="primary" class="content-area wpex-clr">

		<?php wpex_hook_content_before(); ?>

		<div id="content" class="site-content wpex-clr">

			<?php wpex_hook_content_top(); ?>

			<article class="entry-content entry wpex-clr"><?php
	
				// Single Products
				if ( is_singular() ) {
					if ( ! wpex_theme_do_location( 'single' ) ) {
						woocommerce_content();
					}
				}
				// Product archives
				else {

					// Custom shop output
					if ( totaltheme_is_integration_active( 'woocommerce' )
						&& totaltheme_call_static( 'Integration\WooCommerce', 'is_advanced_mode' )
						&& ! wpex_woo_archive_has_loop()
					) {
						$shop_page = get_post( totaltheme_wc_get_page_id( 'shop' ) );
						if ( $shop_page && $shop_page_content = get_post_field( 'post_content', $shop_page ) ) {
							echo totaltheme_render_content( $shop_page_content );
						}
					}

					// Default shop output
					elseif ( ! wpex_theme_do_location( 'archive' ) ) {
						woocommerce_content();
					}

				}

			?></article>

			<?php wpex_hook_content_bottom(); ?>

		</div>

		<?php wpex_hook_content_after(); ?>

	</div>

	<?php wpex_hook_primary_after(); ?>

</div>

<?php
get_footer();
