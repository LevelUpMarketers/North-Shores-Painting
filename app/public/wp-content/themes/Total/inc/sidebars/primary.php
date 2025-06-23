<?php

namespace TotalTheme\Sidebars;

\defined( 'ABSPATH' ) || exit;

/**
 * Primary Sidebar.
 */
class Primary {

	/**
	 * Holds array of conditional sidebars.
	 */
	protected static $sidebar_conditions =  [];

	/**
	 * Helper function used to register new sidebar widget area.
	 */
	public static function register_sidebar( array $args ): void {
		if ( isset( $args['condition'] ) ) {
			self::$sidebar_conditions[ $args['id'] ] = $args['condition'];
			unset( $args['condition'] );
		}

		\totaltheme_init_class( 'Helpers\Register_Widget_Area', 'sidebar', $args );
	}

	/**
	 * Returns the insert hook for the sidebar.
	 */
	public static function get_insert_hook_name(): string {
		$location = \get_theme_mod( 'sidebar_hook' );
		if ( ! $location || ! in_array( $location, [ 'after', 'before', 'dynamic' ], true ) ) {
			$location = 'after';
		}
		if ( 'dynamic' === $location ) {
			$location = 'left-sidebar' === wpex_content_area_layout() ? 'before' : 'after';
		}
		return (string) \apply_filters( 'totaltheme/sidebars/primary/insert_hook_name', "wpex_hook_primary_{$location}" );
	}

	/**
	 * Check if sticky is enabled.
	 */
	public static function is_sticky(): bool {
		return (bool) \apply_filters(
			'totaltheme/sidebars/primary/is_sticky',
			\wp_validate_boolean( \get_theme_mod( 'sidebar_sticky', false ) )
		);
	}

	/**
	 * Returns primary sidebar widget title tag arguments.
	 */
	public static function widget_title_args(): array {
		$tag_escaped = ( $tag = \get_theme_mod( 'sidebar_headings' ) ) ? \tag_escape( $tag ) : 'div';
		$font_size   = totaltheme_has_classic_styles() ? 'wpex-text-md' : 'wpex-text-lg';
		return [
			'before' => "<{$tag_escaped} class='widget-title wpex-heading {$font_size} wpex-mb-20'>",
			'after'  => "</{$tag_escaped}>",
		];
	}

	/**
	 * Echo class attribute for the the primary sidebar wrapper element.
	 */
	public static function wrapper_class() {
		$class = [
			'sidebar-primary',
			'sidebar-container',
			'wpex-print-hidden',
		];
		$class = \apply_filters_deprecated( 'wpex_sidebar_class', [ $class ], '5.6.1', 'totaltheme/sidebars/primary/wrapper_class' );
		$class = (array) \apply_filters( 'totaltheme/sidebars/primary/wrapper_class', $class );
		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Echo class attribute for the the primary sidebar inner element.
	 */
	public static function inner_class() {
		$class = [
			'sidebar-container-inner',
			'wpex-mb-40',
		];
		if ( self::is_sticky() ) {
			$class[] = 'wpex-sticky';
		}
		$class = (array) \apply_filters( 'totaltheme/sidebars/primary/inner_class', $class );
		if ( $class ) {
			echo 'class="' . \esc_attr( \implode( ' ', $class ) ) . '"';
		}
	}

	/**
	 * Returns the sidebar name for this sidebar area as registered with WP.
	 */
	public static function get_sidebar_name( $sidebar = '' ) {
		$instance     = ''; // @todo deprecate instance variable.
		$meta_sidebar = ''; // define this var early.
		$fallback     = \apply_filters( 'wpex_sidebar_has_fallback', true );
		$sidebar      = ( ! $sidebar && $fallback ) ? 'sidebar' : $sidebar;

		// Page Sidebar.
		if ( \is_singular() ) {
			$post_type = \get_post_type();
			$instance  = "singular_{$post_type}";

			// Pages.
			if ( 'page' === $post_type
				&& ! ( \is_page_template( 'templates/blog.php' )
					|| \is_page_template( 'templates/blog-content-above.php' )
				)
			) {
				if ( \get_theme_mod( 'pages_custom_sidebar', true ) ) {
					$sidebar = 'pages_sidebar';
				}
			}

			// Posts.
			if ( 'post' === $post_type ) {
				if ( \get_theme_mod( 'blog_custom_sidebar', false ) ) {
					$sidebar = 'blog_sidebar';
				}
			}

		// Archives.
		} else {

			$instance = 'archive';

			// Search Sidebar.
			if ( \is_search() ) {
				$instance = 'search';
				if ( \get_theme_mod( 'search_custom_sidebar', true ) ) {
					$sidebar = 'search_sidebar';
				}
			}

			// Blog sidebar.
			elseif ( \get_theme_mod( 'blog_custom_sidebar', false ) && \wpex_is_blog_query() ) {
				$instance = 'wpex_is_blog_query';
				$sidebar = 'blog_sidebar';
			}

			// 404.
			elseif ( is_404() ) {
				$instance = '404';
				if ( \get_theme_mod( 'pages_custom_sidebar', true ) ) {
					$sidebar = 'pages_sidebar';
				}
			}
		}

		// WooCommerce sidebar.
		if ( \function_exists( 'is_woocommerce' )
			&& \get_theme_mod( 'woo_custom_sidebar', true )
			&& \is_woocommerce()
		) {
			$sidebar = 'woo_sidebar';
		}

		// Post types Unlimited checks.
		if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
			if ( \is_singular() ) {
				$ptu_check = \wpex_get_ptu_type_mod( $post_type, 'custom_sidebar' );
				if ( $ptu_check ) {
					$sidebar = $ptu_check;
				}
			}
			if ( \is_post_type_archive() ) {
				$ptu_check = \wpex_get_ptu_type_mod( \get_query_var( 'post_type' ), 'custom_sidebar' );
				if ( $ptu_check ) {
					$sidebar = $ptu_check;
				}
			}
			if ( is_tax() ) {
				$ptu_check = \wpex_get_ptu_tax_mod( \get_query_var( 'taxonomy' ), 'sidebar' );
				if ( $ptu_check ) {
					$sidebar = $ptu_check;
				} else {
					$ptu_check = \wpex_get_ptu_type_mod( get_post_type(), 'custom_sidebar' );
					if ( $ptu_check ) {
						$sidebar = $ptu_check;
					}
				}
			}
		}

		/***
		 * FILTER    => Add filter for tweaking the sidebar display via child theme's
		 * IMPORTANT => Must be added before meta options so that it doesn't take priority.
		 ***/
		$sidebar = \apply_filters( 'wpex_get_sidebar', $sidebar, $instance ); // @deprecated.
		$sidebar = (string) \apply_filters( 'totaltheme/sidebars/primary/name', $sidebar, $instance );

		// Get current post id.
		$post_id = \wpex_get_current_post_id();

		// Check meta option after filter so it always overrides.
		if ( $meta = \get_post_meta( $post_id, 'sidebar', true ) ) {
			$sidebar = $meta;
		}

		// Get sidebar based on current post primary category setting.
		if ( \is_singular() && 'page' !== $post_type ) {
			$term_meta   = '';
			$primary_tax = \wpex_get_post_primary_taxonomy( $post_id );
			
			// First check primary term for a custom sidebar.
			if ( $primary_tax && $primary_post_term = \totaltheme_get_post_primary_term( $post_id, $primary_tax, false ) ) {
				$term_meta = \get_term_meta( $primary_post_term->term_id, 'wpex_sidebar', true );
			}

			// If the primary term doesn't have a sidebar lets check all other post taxonomy terms.
			if ( ! $term_meta ) {
				$taxonomies = (array) \get_object_taxonomies( $post_type, 'names' );

				// Loop through post taxonomies to see if a specific sidebar is set for a taxonomy term.
				foreach ( $taxonomies as $taxonomy ) {
					if ( in_array( $taxonomy, [ 'post_format' ] ) ) {
						continue; // excluded taxonomies.
					}
					// Check the primary term for the non main taxonomy first.
					if ( $taxonomy !== $primary_tax
						&& $primary_term = \totaltheme_get_post_primary_term( $post_id, $taxonomy, false )
					) {
						$term_meta = \get_term_meta( $primary_term->term_id, 'wpex_sidebar', true );
					}
					// Check non primary terms.
					if ( ! $term_meta ) {
						$terms_with_meta = \wp_get_post_terms( $post_id, $taxonomy, [
							'fields'   => 'ids',
							'meta_key' => 'wpex_sidebar',
						] );
						if ( $terms_with_meta && ! is_wp_error( $terms_with_meta ) && ! empty( $terms_with_meta[0] ) ) {
							$term_meta = \get_term_meta( $terms_with_meta[0], 'wpex_sidebar', true );
						}
					}
					// End loop if we have term_meta is set.
					if ( $term_meta ) {
						break;
					}
				}
			}

			if ( $term_meta ) {
				$meta_sidebar = $term_meta;
			}

		} // end singular term check.

		// Taxonomies.
		if ( \is_tax() || \is_category() || \is_tag() ) {
			$term_id = \get_queried_object_id();
			if ( $term_id && $meta = \get_term_meta( $term_id, 'wpex_sidebar', true ) ) {
				$meta_sidebar = $meta;
			}
		}

		// Check registered sidebar conditionals.
		foreach ( self::$sidebar_conditions as $sidebar_id => $sidebar_condition ) {
			if ( ! \is_active_sidebar( $sidebar_id ) ) {
				continue;
			}
			if ( ( \is_bool( $sidebar_condition ) && true === $sidebar_condition )
				|| ( \is_callable( $sidebar_condition ) && true === \call_user_func( $sidebar_condition ) )
			) {
				$sidebar = $sidebar_id;
			}
		}

		// Check if meta sidebar is set, registered and active and if so set it.
		if ( $meta_sidebar && \is_registered_sidebar( $meta_sidebar ) && \is_active_sidebar( $meta_sidebar ) ) {
			$sidebar = $meta_sidebar;
		}

		// Never show empty sidebar.
		if ( $sidebar && $fallback && ! \is_active_sidebar( $sidebar ) ) {
			$sidebar = 'sidebar';
		}

		return $sidebar;
	}

}
