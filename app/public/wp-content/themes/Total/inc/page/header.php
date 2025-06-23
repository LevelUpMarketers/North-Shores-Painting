<?php

namespace TotalTheme\Page;

\defined( 'ABSPATH' ) || exit;

/**
 * Page Header Class.
 */
class Header {

	/**
	 * Is the page header is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * The page header style.
	 */
	protected static $style;

	/**
	 * Stores the subheading to prevent extra db checks.
	 */
	protected static $subheading;

	/**
	 * Returns the globally set style.
	 */
	public static function global_style() {
		return \get_theme_mod( 'page_header_style' );
	}

	/**
	 * Checks if the current style is the same as the global style.
	 */
	public static function is_global_style() {
		return ( self::global_style() === self::style() || 'default' === self::style() );
	}

	/**
	 * Returns an array of style choices for the page header.
	 */
	public static function style_choices(): array {
		$choices = [
			''                 => \esc_html__( 'Default','total' ),
			'centered'         => \esc_html__( 'Centered', 'total' ),
			'centered-minimal' => \esc_html__( 'Centered Minimal', 'total' ),
			'background-image' => \esc_html__( 'Background Image', 'total' ),
			'hidden'           => \esc_html__( 'Hidden (Disabled)', 'total' ),
		];
		$choices = \apply_filters( 'wpex_page_header_styles', $choices ); // @deprecated
		return (array) \apply_filters( 'totaltheme/page/header/style_choices', $choices );
	}

	/**
	 * Checks if the header is enabled or not.
	 */
	public static function style(): string {
		if ( ! \is_null( self::$style ) ) {
			return self::$style;
		}

		$post_id = \wpex_get_current_post_id();
		$style   = self::global_style();

		if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
			if ( \is_singular() ) {
				$post_type = \get_post_type();
				if ( 'wpex_templates' === $post_type
					&& 'single' === \totaltheme_get_dynamic_template_type( $post_id )
					&& $theme_builder = totaltheme_get_instance_of( 'Theme_Builder' )
				) {
					$template_single_type = $theme_builder->get_post_type_from_template_id( $post_id );
					if ( $template_single_type ) {
						$post_type = $template_single_type;
					}
				}
				$custom_style = \wpex_get_ptu_type_mod( $post_type, 'page_header_title_style' );
				if ( $custom_style ) {
					$style = $custom_style;
				}
			} elseif ( \is_post_type_archive() ) {
				$custom_style = \wpex_get_ptu_type_mod( \get_query_var( 'post_type' ), 'archive_page_header_title_style' );
				if ( $custom_style ) {
					$style = $custom_style;
				}
			} elseif ( \is_tax() ) {
				$custom_style = \wpex_get_ptu_tax_mod( \get_query_var( 'taxonomy' ), 'page_header_title_style' );
				if ( $custom_style ) {
					$style = $custom_style;
				}
			}
		}

		if ( $post_id && $meta = \get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {
			$style = $meta;
		}

		$style = \apply_filters( 'wpex_page_header_style', $style ); // @deprecated
		$style = \apply_filters( 'totaltheme/page/header/style', $style );

		if ( empty( $style ) ) {
			$style = 'default';
		}

		self::$style = (string) $style;

		return self::$style;
	}

	/**
	 * Checks if the header is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		$check       = \get_theme_mod( 'enable_page_header', true );
		$is_singular = \is_singular();

		// Hide by default if style is set to hidden.
		if ( 'hidden' === self::style() ) {
			$check = false;
		}

		// If the page header title is enabled check to see if it's disabled for specific parts.
		if ( $check ) {

			// Check on/off switches to see if the title is disabled for particular parts.
			if ( ! $is_singular ) {
				if ( \wpex_is_blog_query() ) {
					$check = \get_theme_mod( 'blog_archive_has_page_header', $check );
				} elseif ( \is_post_type_archive() ) {
					$check = \get_theme_mod( get_query_var( 'post_type' ) . '_archive_has_page_header', $check );
				} if ( \is_search() ) {
					$check = \get_theme_mod( 'search_has_page_header', $check );
				} elseif ( \is_404() ) {
					$check = totaltheme_call_non_static( 'Error_404', 'is_page_header_enabled' );
				}
			}
		}

		/*** deprecated ***/
		$check = (bool) \apply_filters( 'wpex_has_page_header', $check );

		// Single post checks.
		if ( $post_id = \wpex_get_current_post_id() ) {

			if ( $check ) {

				// Singular checks.
				if ( $is_singular ) {
					if ( \function_exists( 'is_product' ) && \is_product() ) {
						$check = \get_theme_mod( 'woo_product_has_page_header', $check );
					} else {
						$post_type = \get_post_type();
						if ( 'wpex_templates' === $post_type ) {
							$template_type = \totaltheme_get_dynamic_template_type( $post_id );
							switch ( $template_type ) {
								case 'error_404':
									$check = totaltheme_call_non_static( 'Error_404', 'is_page_header_enabled' );
									break;
								case 'archive':
									// @todo can we reverse check archives.
									break;
								case 'single':
									if ( $theme_builder = totaltheme_get_instance_of( 'Theme_Builder' ) ) {
										$template_single_type = $theme_builder->get_post_type_from_template_id( $post_id );
										if ( $template_single_type ) {
											$check = \get_theme_mod( "{$template_single_type}_singular_page_title", $check );
										}
									}
									break;
							}
						} else {
							$check = \get_theme_mod( "{$post_type}_singular_page_title", $check );
						}
					}
				}

				// Check page template.
				$page_template = get_page_template_slug( $post_id );
				if ( $page_template ) {
					$template_blacklist = [
						'templates/no-sidebar-no-page-title.php',
						'templates/left-sidebar-no-page-title.php',
						'templates/right-sidebar-no-page-title.php',
					];
					if ( in_array( $page_template, $template_blacklist, true ) ) {
						$check = false;
					}
				}
			}

			// Get page meta setting - MUST CHECK LAST.
			$meta = \get_post_meta( $post_id, 'wpex_disable_title', true );
			if ( 'enable' === $meta ) {
				$check = true;
			} elseif ( 'on' === $meta ) {
				// Allow for a background-image page header style with the title disabled (so it only shows the image).
				if ( 'background-image' !== \get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {
					$check = false;
				}
			}

		}

		$check = apply_filters( 'wpex_display_page_header', $check ); // @deprecated

		self::$is_enabled = (bool) \apply_filters( 'totaltheme/page/header/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Returns the page header breakpoint.
	 */
	public static function breakpoint(): string {
		$bk = \get_theme_mod( 'page_header_breakpoint' ) ?: 'md';
		$bk = apply_filters( 'wpex_page_header_breakpoint', $bk ); // @deprecated
		return (string) \apply_filters( 'totaltheme/page/header/breakpoint', $bk );
	}

	/**
	 * Checks if the page header has a title.
	 */
	public static function has_title(): bool {
		$check   = true;
		$post_id = \wpex_get_current_post_id();
		if ( $post_id && 'on' === \get_post_meta( $post_id, 'wpex_disable_title', true ) ) {
			$check = false;
		}
		$check = \apply_filters( 'wpex_has_page_header_title', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/page/header/has_title', $check );
	}

	/**
	 * Checks if the page header has a subheading.
	 */
	public static function has_subheading(): bool {
		$check = (bool) self::get_subheading();
		$check = \apply_filters( 'wpex_page_header_has_subheading', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/page/header/has_subheading', $check );
	}

	/**
	 * Returns the subheading.
	 */
	public static function get_subheading() {
		if ( ! \is_null( self::$subheading ) ) {
			return self::$subheading;
		}

		$subheading = '';

		if ( $post_id = \wpex_get_current_post_id() ) {
			if ( $meta = \get_post_meta( $post_id, 'wpex_post_subheading', true ) ) {
				$subheading = $meta;
			}
		} elseif ( \is_author() ) {
			$subheading = \sprintf( \esc_html__( 'This author has written %s articles', 'total' ), \get_the_author_posts() );
		} elseif ( \is_category() || \is_tag() || \is_tax() ) {
			// @note under_title and subheading are the same thing and we must check both.
			if ( in_array( wpex_term_description_location(), [ 'under_title', 'subheading' ] ) ) {
				$subheading = \term_description();
			}
		}

		$subheading = \apply_filters( 'wpex_post_subheading', $subheading, null ); // @deprecated
		$subheading = (string) \apply_filters( 'totaltheme/page/header/subheading', $subheading );

		self::$subheading = \totaltheme_replace_vars( $subheading );

		return self::$subheading;
	}

}
