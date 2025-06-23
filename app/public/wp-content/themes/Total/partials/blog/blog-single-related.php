<?php
/**
 * Single related posts.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 5.8.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! get_theme_mod( 'blog_related', true ) ) {
	return;
}

$wpex_related_query = wpex_blog_single_related_query();

if ( $wpex_related_query && ! is_wp_error( $wpex_related_query ) && $wpex_related_query->have_posts() ) : ?>

	<div <?php wpex_blog_single_related_class(); ?>>

		<?php get_template_part( 'partials/blog/blog-single-related', 'heading' ); ?>

		<div <?php wpex_blog_single_related_row_class(); ?>><?php

			wpex_set_loop_instance( 'related' );
			wpex_set_loop_counter();
			foreach ( $wpex_related_query->posts as $post ) : setup_postdata( $post );
				wpex_increment_loop_running_count();
				wpex_increment_loop_counter();
				get_template_part( 'partials/blog/blog-single-related-entry' );
				wpex_maybe_reset_loop_counter( wpex_get_array_first_value( wpex_blog_single_related_columns() ) );
			endforeach;

		?></div>

	</div>

	<?php
	wpex_reset_loop_query_vars();
	wp_reset_postdata();

endif;
