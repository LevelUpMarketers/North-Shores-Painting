<?php

/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 6.0
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

			<?php if ( ! wpex_theme_do_location( 'single' ) ) :
				// Note: We use "single" for compatibility with Elementor.
				?>

				<article class="entry wpex-clr"><?php
					if ( $error_404 = totaltheme_get_instance_of( 'Error_404' ) ) {
						$error_404->render();
					}
				?></article>

			<?php endif; ?>

			<?php wpex_hook_content_bottom(); ?>

		</div>

		<?php wpex_hook_content_after(); ?>

	</div>

	<?php wpex_hook_primary_after(); ?>

</div>

<?php
get_footer();
