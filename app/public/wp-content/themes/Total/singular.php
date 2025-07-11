<?php

/**
 * The template for displaying all singular post types.
 * 
 * Important: You should NEVER have to modify this file via your child theme.
 * If you want to create a custom single display use dynamic templates: https://totalwptheme.com/docs/dynamic-templates/
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

			<?php
			// Display singular content unless there is a custom template defined.
			if ( ! wpex_theme_do_location( 'single' ) ) :

				// Start loop.
				while ( have_posts() ) : the_post();

					// Pages.
					if ( is_singular( 'page' ) ) {
						wpex_get_template_part( 'page_single_blocks' );
					}

					// Posts.
					elseif ( is_singular( 'post' ) ) {
						wpex_get_template_part( 'blog_single_blocks' );
					}

					// Portfolio Items.
					elseif ( is_singular( 'portfolio' ) && totaltheme_call_static( 'Portfolio\Post_Type', 'is_enabled' ) ) {
						wpex_get_template_part( 'portfolio_single_blocks' );
					}

					// Staff Members.
					elseif ( is_singular( 'staff' ) && totaltheme_call_static( 'Staff\Post_Type', 'is_enabled' ) ) {
						wpex_get_template_part( 'staff_single_blocks' );
					}

					// Testimonials.
					elseif ( is_singular( 'testimonials' ) && totaltheme_call_static( 'Testimonials\Post_Type', 'is_enabled' ) ) {
						wpex_get_template_part( 'testimonials_single_blocks' );
					}

					/**
					 * All other post types.
					 */
					else {

						// Prevent issues with custom types named the same as core partial files.
						// @todo remove the $post_type paramater from wpex_get_template_part.
						$post_type = get_post_type();

						$excluded_types = [
							'audio',
							'video',
							'gallery',
							'content',
							'comments',
							'media',
							'meta',
							'related',
							'share',
							'title',
						];

						if ( in_array( $post_type, $excluded_types ) ) {
							$post_type = null;
						}

						wpex_get_template_part( 'cpt_single_blocks', $post_type );

					}

				endwhile; ?>

			<?php endif; ?>

			<?php wpex_hook_content_bottom(); ?>

		</div>

		<?php wpex_hook_content_after(); ?>

	</div>

	<?php wpex_hook_primary_after(); ?>

</div>

<?php
get_footer();
