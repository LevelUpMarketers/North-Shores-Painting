<?php

namespace TotalTheme\Theme_Builder;

\defined( 'ABSPATH' ) || exit;

/**
 * Get template ID based on theme location.
 */
class Location_Template {

	/**
	 * Template ID to return for given location.
	 *
	 * @todo rename to $template_id or $id.
	 */
	public $template = 0;

	/**
	 * Start things.
	 */
	public final function __construct( $location ) {
		if ( \method_exists( $this, $location ) ) {
			$this->$location();
		}

		/*** @deprecated - use totaltheme/theme_builder/location_template_id instead ***/
		$this->template = (int) \apply_filters( "wpex_{$location}_template_id", $this->template );
	}

	/*-------------------------------------------------------------------------------*/
	/* [ Main Locations ]
	/*-------------------------------------------------------------------------------*/

	/**
	 * Single template.
	 */
	protected function single() {
		if ( \is_404() ) {
			return; // this is used for Elementor only.
		}

		$this->template = Post_Template::get_template_id();
	}

	/**
	 * Archive template.
	 */
	protected function archive() {
		if ( \is_search() ) {
			$this->search_archive_template(); // search must be first because it can return true for \is_tax()

			if ( ! empty( $this->template ) ) {
				return; // fixes issues with wpex_is_blog_query when adding ?post= param to the search query.
			}
		}

		if ( \is_tax() ) {
			$this->taxonomy_archive_template();
		}

		if ( \is_post_type_archive() ) {
			$this->post_type_archive_template();
		}

		if ( \is_author() ) {
			$this->author_archive_template();
			if ( ! empty( $this->template ) ) {
				return; // prevent the blog template to take over.
			}
		}

		if ( \wpex_is_blog_query() ) {
			$this->blog_template();
		}

		if ( \function_exists( 'is_woocommerce' ) && \is_woocommerce() && ( \is_archive() || \is_shop() ) ) {
			$this->woo_archive_template(); // check WooCommerce last so if you are using the term based template plugin it still works.
		}
	}

	/*-------------------------------------------------------------------------------*/
	/* [ Sub Locations ]
	/*-------------------------------------------------------------------------------*/

	/**
	 * WooCommerce archive template.
	 */
	protected function woo_archive_template() {
		$this->template = (int) \apply_filters( 'wpex_woocommerce_archive_template_id', \get_theme_mod( 'woo_archive_template_id' ) );
	}

	/**
	 * Taxonomy template.
	 */
	protected function taxonomy_archive_template() {
		$template = '';
		$taxonomy = (string) \get_query_var( 'taxonomy' );

		if ( \is_tax( [ 'staff_category', 'staff_tag' ] ) ) {
			$template = \get_theme_mod( 'staff_archive_template_id' );
		}

		if ( \is_tax( [ 'portfolio_category', 'portfolio_tag' ] ) ) {
			$template = \get_theme_mod( 'portfolio_archive_template_id' );
		}

		if ( \is_tax( [ 'testimonials_category', 'testimonials_tag' ] ) ) {
			$template = \get_theme_mod( 'testimonials_archive_template_id' );
		}

		// Check theme mod defined template.
		if ( empty( $template ) ) {
			$template = \get_theme_mod( "{$taxonomy}_template_id" );
		}

		// Check Post Types Unlimited Template (last).
		if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
			$ptu_check = \wpex_get_ptu_tax_mod( $taxonomy, 'template_id' );
			if ( $ptu_check ) {
				$template = $ptu_check;
			}
		}

		$this->template = (int) \apply_filters( 'wpex_taxonomy_template_id', $template, $taxonomy );
	}

	/**
	 * Author archive template.
	 */
	protected function author_archive_template() {
		$this->template = (int) \apply_filters( 'wpex_author_archive_template_id', \get_theme_mod( 'author_archive_template_id' ) );
	}

	/**
	 * Blog template.
	 */
	protected function blog_template() {
		$template = \get_theme_mod( 'blog_archive_template_id' );
		if ( empty( $template ) && \is_tax( 'post_series' ) && \get_theme_mod( 'post_series_enable', true ) ) {
			$template = \get_theme_mod( 'post_series_template_id' );
		}
		$this->template = (int) \apply_filters( 'wpex_blog_archive_template_id', $template );
	}

	/**
	 * Search template.
	 */
	protected function search_archive_template() {
		$this->template = (int) \apply_filters( 'wpex_search_archive_template_id', \get_theme_mod( 'search_archive_template_id' ) );
	}

	/**
	 * Post Type archive template.
	 */
	protected function post_type_archive_template() {
		$post_type = \get_query_var( 'post_type' );
		if ( \is_array( $post_type ) ) {
			$post_type = (string) $post_type[0];
		}
		if ( $ptu_template_id = \wpex_get_ptu_type_mod( $post_type, 'archive_template_id' ) ) {
			$template = $ptu_template_id;
		} else {
			$template = \get_theme_mod( "{$post_type}_archive_template_id" );
		}
		$this->template = (int) \apply_filters( 'wpex_post_type_archive_template_id', $template, $post_type );
	}

}
