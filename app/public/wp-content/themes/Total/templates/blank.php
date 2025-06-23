<?php

/**
 * Template Name: Blank
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

					<article class="entry-content entry wpex-clr">

						<?php the_content(); ?>

					</article>

				<?php endwhile; ?>

			<?php endif; ?>

			<?php wpex_post_edit(); ?>

			<?php wpex_hook_content_bottom(); ?>

		</div>

		<?php wpex_hook_content_after(); ?>

	</div>

	<?php wpex_hook_primary_after(); ?>

</div>

<?php
get_footer();
