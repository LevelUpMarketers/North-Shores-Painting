<?php

defined( 'ABSPATH' ) || exit;

/**
 * Returns site layout type.
 */
function wpex_site_layout(): string {
	static $layout = null;
	if ( null === $layout ) {
		$post_id = wpex_get_current_post_id();
		if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_main_layout', true ) ) {
			$layout = sanitize_text_field( $meta );
		} else {
			$layout = ( $layout = get_theme_mod( 'main_layout_style' ) ) ? sanitize_text_field( $layout ) : '';
		}
		$layout = (string) apply_filters( 'wpex_main_layout', $layout );
		if ( 'full-width' !== $layout && 'boxed' !== $layout ) {
			$layout = 'full-width';
		}
	}
	return $layout;
}

/**
 * Returns default content layout.
 */
function wpex_get_default_content_area_layout() {
	$layout = ( $layout = get_theme_mod( 'content_layout' ) ) ? sanitize_text_field( $layout ) : '';
	if ( ! $layout ) {
		$layout = is_rtl() ? 'left-sidebar' : 'right-sidebar';
	}
	return $layout;
}

/**
 * Returns correct content area layout.
 *
 * @todo remove $instance.
 * @todo convert into a class with a helper function so it's easier to read - must keep function since it was likely used in custom
 */
function wpex_content_area_layout( $post_id = '' ): string {
	static $layout = null;

	if ( null === $layout ) {

		if ( ! $post_id ) {
			$post_id = wpex_get_current_post_id();
		}

		$default = wpex_get_default_content_area_layout();

		$layout = $default;

		$instance = ''; // @todo deprecate instance.

		// Singular checks // Must use the post_id check to prevent issues.
		// with custom pages like Events Calendar, 404 page, etc.
		if ( $post_id && $post_id !== get_option( 'page_for_posts' ) ) {

			// Check meta first to override and return (prevents filters from overriding meta).
			if ( $meta = get_post_meta( $post_id, 'wpex_post_layout', true ) ) {
				$layout = $meta;
				return $layout; // bail early.
			}

			// Get post type.
			$post_type = get_post_type( $post_id ); // must pass on the ID to prevent issues with dynamic templates used in archives like 404.
			$instance  = "singular_{$post_type}";

			// Singular Page.
			if ( 'page' === $post_type ) {

				// Default page layout.
				$layout = get_theme_mod( 'page_single_layout' );

				// Page template layouts.
				if ( $page_template = get_page_template_slug( $post_id ) ) {
					switch ( $page_template ) {
						case 'templates/blog.php':
							$layout = get_theme_mod( 'blog_archives_layout' );
							break;
						case 'templates/blank.php':
						case 'templates/landing-page.php':
						case 'templates/no-sidebar.php':
						case 'templates/no-sidebar-no-page-title.php':
							$layout = 'full-width';
							break;
						case 'templates/full-screen.php':
							$layout = 'full-screen';
							break;
						case 'templates/left-sidebar-no-page-title.php':
						case 'templates/left-sidebar.php':
							$layout = 'left-sidebar';
							break;
						case 'templates/right-sidebar-no-page-title.php':
						case 'templates/right-sidebar.php':
							$layout = 'right-sidebar';
							break;
					}
				}
			}

			// Singular Post.
			elseif ( 'post' === $post_type ) {
				$layout = get_theme_mod( 'blog_single_layout' );
			}

			// Templatera.
			elseif ( in_array( $post_type, [ 'wpex_templates', 'templatera', 'wpex_card', 'elementor_library', 'attachment' ], true ) ) {
				$layout = 'full-width'; // must set static var before bailing.
				return $layout; // bail early - !! important !!
			} else {
				$layout = get_theme_mod( "{$post_type}_single_layout" ) ?: $layout;
			}

			// Post Types Unlimited checks
			if ( wpex_get_ptu_type_mod( $post_type, 'use_blank_template' ) ) {
				$layout = wpex_get_ptu_type_mod( $post_type, 'blank_template_layout', 'full-width' );
			} else {
				$ptu_layout = wpex_get_ptu_type_mod( $post_type, 'post_layout' );
				if ( isset( $ptu_layout ) ) {
					$layout = $ptu_layout;
				}
			}

		} // End singular

		// 404 page => must check before archives due to WP bug with pagination.
		elseif ( is_404() ) {
			$instance = '404';

			// Check custom 404.
			if ( wp_validate_boolean( get_theme_mod( 'custom_404_enable', true ) ) ) {
				$error_404_template = totaltheme_call_non_static( 'Error_404', 'get_template_id' );

				// Custom templates previously had default layout.
				if ( ! $error_404_template || 'page' !== get_post_type( $error_404_template ) ) {
					$layout = 'full-width';
				}

				if ( $error_page_layout = (string) get_theme_mod( 'error_page_layout' ) ) {
					$layout = $error_page_layout;
				}

			} else {
				$layout = 'full-width';
			}
		}

		// Home.
		elseif ( is_home() ) {
			$instance = 'home';
			$layout = get_theme_mod( 'blog_archives_layout' );

			// fix for wp-activate.php
			if ( is_multisite()
				&& ! empty( $GLOBALS['pagenow'] )
				&& in_array( $GLOBALS['pagenow'], [ 'wp-activate.php', 'wp-signup.php' ] )
			) {
				$layout = 'full-width';
			}
		}

		// Search => MUST BE BEFORE TAX CHECK, WP returns true for is_tax on search results.
		elseif ( is_search() ) {
			$instance = 'search';
			$layout = get_theme_mod( 'search_layout' );
		}

		// Define tax instance.
		elseif ( is_tax() ) {
			$instance = 'tax';
			if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
				$ptu_layout = wpex_get_ptu_tax_mod( get_query_var( 'taxonomy' ), 'layout' );
				if ( isset( $ptu_layout ) ) {
					$layout = $ptu_layout;
				}
			}
		}

		// Define post type archive instance.
		elseif( is_post_type_archive() ) {
			$instance = 'post_type_archive';
			if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
				$ptu_layout = wpex_get_ptu_type_mod( get_query_var( 'post_type' ), 'archive_layout' );
				if ( isset( $ptu_layout ) ) {
					$layout = $ptu_layout;
				}
			}
		}

		// Blog Query => Must come before category check.
		elseif ( wpex_is_blog_query() ) {
			$instance = 'wpex_is_blog_query';
			$layout = get_theme_mod( 'blog_archives_layout' );

			// Extra check for categories with custom meta.
			if ( is_category() ) {
				$instance = 'category';
				$layout = get_theme_mod( 'blog_archives_layout' );
				$custom_term_layout = wpex_get_category_meta( get_query_var( 'cat' ), 'wpex_term_layout' );
				if ( $custom_term_layout ) {
					$layout = $custom_term_layout;
				}
			}

			// Custom author layout.
			if ( is_author() ) {
				$author_layout = get_theme_mod( 'author_layout' );
				if ( $author_layout ) {
					$layout = $author_layout;
				}
			}

		}

		// All else.
		else {
			$layout = $default;
		}

		// WooCommerce layouts (added here to provide support for vanilla WooCommerce).
		// @todo move to Integration\WooCommerce core class.
		if ( totaltheme_is_integration_active( 'woocommerce' ) ) {
			if ( wpex_is_woo_shop() ) {
				$layout = get_theme_mod( 'woo_shop_layout', 'full-width' );
			} elseif ( wpex_is_woo_tax() ) {
				$layout = get_theme_mod( 'woo_shop_layout', 'full-width' );
			} elseif ( wpex_is_woo_single() ) {
				$layout = get_theme_mod( 'woo_product_layout', 'full-width' );
			} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
				$layout = 'full-width';
			}
		}

		/**
		 * Filters the post layout (no-sidebar, full-screen, right-sidebar, left-sidebar).
		 *
		 * @param string $layout.
		 * @param string $instance DEPRECATED.
		 * @todo rename move to class and change filter name.
		 */
		$layout = apply_filters( 'wpex_post_layout_class', $layout, $instance );

		// Layout should never be empty.
		if ( empty( $layout ) ) {
			$layout = $default;
		}

	}

	return (string) $layout;
}

/**
 * Check if primary wrapper should have bottom margin.
 */
function wpex_has_primary_bottom_spacing(): bool {
	if ( ! get_theme_mod( 'has_primary_bottom_spacing', true ) ) {
		return false;
	}

	$condition = get_theme_mod( 'primary_bottom_spacing_condition' );

	if ( ! empty( $condition ) ) {
		$conditional_logic = totaltheme_init_class( 'Conditional_Logic', $condition );
		if ( isset( $conditional_logic->result ) ) {
			return $conditional_logic->result;
		}
		return false;
	}

	$check   = true;
	$post_id = (int) wpex_get_current_post_id();

	if ( $check ) {

		// Disable on single post types when using page builders or dynamic templates.
		if ( $post_id ) {

			// Disable on single posts.
			if ( is_singular( 'post' ) ) {
				$check = false;
			}

			// Check home/posts page.
			elseif ( is_home() && $post_id === (int) get_option( 'page_for_posts' ) ) {
				$check = ! totaltheme_location_has_template( 'archive' );
			}

			// Disable on WooCommerce products.
			elseif ( function_exists( 'is_product' ) && is_product() ) {
				$check = false;
			}

			// Disable on elementor pages.
			elseif ( totaltheme_is_integration_active( 'elementor' )
				&& get_post_meta( $post_id, '_elementor_edit_mode', true )
			) {
				$check = false;
			}

			// Disable when using WPBakery.
			elseif ( totaltheme_call_static( 'Integration\WPBakery\Helpers', 'post_has_wpbakery', $post_id ) ) {
				$page_template = get_page_template_slug( $post_id );
				if ( ! in_array( $page_template, [ 'templates/blog.php', 'templates/blog-content-above.php' ] ) ) {
					$check = false;
				}

				if ( function_exists( 'is_shop' ) && is_shop() ) {
					$check = true; // make sure the shop has padding - @todo deprecate?
				}
			}

			// Disable when using dynamic templates.
			// note: we use is_singular to prevent issues with blog and shop pages.
			elseif ( is_singular() && totaltheme_location_has_template( 'single' ) ) {
				$check = false;
			}

		} else {
			if ( is_archive() || is_search() ) {
				$check = ! totaltheme_location_has_template( 'archive' );
			}
		}

	}

	return (bool) apply_filters( 'wpex_has_primary_bottom_spacing', $check );
}
