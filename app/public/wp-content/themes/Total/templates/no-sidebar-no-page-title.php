<?php

/**
 * Template Name: No Sidebar (Page Title Disabled)
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

			<?php if ( ! wpex_theme_do_location( 'single' ) ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wpex_get_template_part( 'page_single_blocks' ); ?>

				<?php endwhile; ?>

			<?php endif; ?>

			<?php wpex_hook_content_bottom(); ?>

		</div>

		<?php wpex_hook_content_after(); ?>

	</div>

	<?php wpex_hook_primary_after(); ?>

</div>

<?php
get_footer();
