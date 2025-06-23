<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "pre_get_posts".
 */
final class Pre_Get_Posts {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $query ) {
		if ( \is_admin() || ! $query->is_main_query() ) {
			return $query;
		}

		// Search functions.
		if ( $query->is_search() ) {
			return self::alter_search( $query );
		}

		// Exclude categories from the main blog.
		if ( $query->is_home()
			|| \is_page_template( 'templates/blog.php' )
			|| \is_page_template( 'templates/blog-content-above.php' )
		) {
			if ( $cats = \wpex_blog_exclude_categories() ) {
				$query->set( 'category__not_in', $cats );
			}
			return $query;
		}

		// Category pagination.
		if ( $query->is_category() ) {
			$current_term = $query->get_queried_object_id();
			$term_ppp = \wpex_get_category_meta( $current_term, 'wpex_term_posts_per_page' );
			if ( $term_ppp ) {
				$query->set( 'posts_per_page', $term_ppp );
				return $query;
			}
		}
	}

	/**
	 * Alters the search query.
	 */
	protected static function alter_search( $query ) {
		$post_type = $_GET['post_type'] ?? $_GET['post_types'] ?? null;

		// Prevent issues with Woo Search.
		if ( \class_exists( '\WooCommerce', false ) && 'product' === $post_type ) {
			return $query;
		}

		// Search posts per page.
		$query->set( 'posts_per_page', \get_theme_mod( 'search_posts_per_page', '10' ) );

		// Alter search post types unless the post_type arg is in the URL.
		if ( ! $post_type ) {

			// Display standard posts only.
			if ( \get_theme_mod( 'search_standard_posts_only', false ) ) {
				$query->set( 'post_type', 'post' );
				return $query;
			}

			// Maybe exclude post types from search results.
			$show_staff        = \get_theme_mod( 'staff_search', true );
			$show_portfolio    = \get_theme_mod( 'portfolio_search', true );
			$show_testimonials = \get_theme_mod( 'testimonials_search', true );

			if ( $show_staff && $show_portfolio && $show_testimonials ) {
				return $query;
			}

			$searchable_types = \get_post_types( [
				'public'              => true,
				'exclude_from_search' => false,
			], 'names', 'and' );

			if ( \is_array( $searchable_types ) ) {
				if ( ! $show_staff ) {
					unset( $searchable_types['staff'] );
				}
				if ( ! $show_portfolio ) {
					unset( $searchable_types['portfolio'] );
				}
				if ( ! $show_testimonials ) {
					unset( $searchable_types['testimonials'] );
				}
				$searchable_type[] = 'user'; // fix for relevanssi plugin
				$query->set( 'post_type', $searchable_types );
			}

		}

		return $query;
	}

}
