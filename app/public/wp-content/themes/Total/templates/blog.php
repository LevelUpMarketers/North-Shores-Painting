<?php

/**
 * Template Name: Blog
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
			// The loop.
			while ( have_posts() ) : the_post();

				$blocks = wpex_single_blocks();

				if ( is_array( $blocks ) ) {
					$template_map = [
						'media' => 'partials/page-single-media',
						'title' => 'partials/page-single-title',
					];
					foreach ( $blocks as $block ) {
						if ( isset( $template_map[ $block ] ) ) {
							get_template_part( $template_map[ $block ] );
						}
					}
				}

				get_template_part( 'partials/page-single-content' );

			endwhile;

			global $post, $paged, $more;
			$more = 0;
			if ( get_query_var( 'paged' ) ) {
				$paged = get_query_var( 'paged' );
			} else if ( get_query_var( 'page' ) ) {
				$paged = get_query_var( 'page' );
			} else {
				$paged = 1;
			}

			// Query posts
			$wp_query = new WP_Query( [
				'post_type'        => 'post',
				'paged'            => $paged,
				'category__not_in' => ( $exclude = wpex_blog_exclude_categories() ) ? $exclude : null,
			] );

			if ( $wp_query->posts ) :

				// Get index loop type.
				$loop_type = 'blog';

				// Get loop top.
				get_template_part( 'partials/loop/loop-top', $loop_type );

					// Set the loop counter which is used for clearing floats.
					wpex_set_loop_counter();

					// Loop through posts.
					while ( have_posts() ) : the_post();

						// Add to running count.
						wpex_increment_loop_running_count();

						// Before entry hook.
						wpex_hook_archive_loop_before_entry();

						// Get content template part (entry content).
						get_template_part( 'partials/loop/loop', $loop_type );

						// After entry hook.
						wpex_hook_archive_loop_after_entry();

					// End loop.
					endwhile;

				// Get loop bottom.
				get_template_part( 'partials/loop/loop-bottom', $loop_type );

				// Pagination.
				wpex_loop_pagination( $loop_type );

			endif;

			?>

			<?php wp_reset_postdata(); wp_reset_query(); ?>

			<?php wpex_hook_content_bottom(); ?>

		</div>

		<?php wpex_hook_content_after(); ?>

	</div>

	<?php wpex_hook_primary_after(); ?>

</div>

<?php
get_footer();
