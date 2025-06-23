<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Title Class.
 */
final class Title {

	/**
	 * Meta key where the custom title is saved.
	 */
	public const META_KEY = 'wpex_post_title';

	/**
	 * Stores whether the title is an h1 title or not.
	 */
	protected static $is_h1 = true;

	/**
	 * Class instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of this class.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Returns the current title.
	 */
	public function get(): string {
		$title = '';
		$post_id = 0;

		// Single posts.
		if ( \is_singular() ) {
			$post_id = \get_the_ID();
			$title = $this->get_singular_title();
		}

		// Homepage - display blog description if not a static page.
		elseif ( \is_front_page() ) {
			if ( \get_bloginfo( 'description' ) ) {
				$title = \get_bloginfo( 'description' );
			} else {
				$title = \esc_html__( 'Recent Posts', 'total' );
			}
		}

		// Homepage posts page.
		elseif ( \is_home() ) {
			$title = $this->get_posts_page_title();
		}

		// Search => NEEDS to go before archives since it's technically an archive.
		elseif ( \is_search() ) {
			// Important: we must strip all shortcodes here to prevent people from potentially searching them.
			$title = \esc_html__( 'Search results for:', 'total' ) . ' &quot;' . \strip_shortcodes( \sanitize_text_field( \get_search_query( false ) ) ) . '&quot;';
		}

		// Archives.
		elseif ( \is_archive() ) {
			$title = $this->get_archive_title();
		}

		// 404 Page.
		elseif ( \is_404() ) {
			$title = \wpex_get_translated_theme_mod( 'error_page_title' );

			if ( ! $title ) {
				if ( $custom_404 = \wpex_parse_obj_id( \get_theme_mod( 'error_page_content_id' ), 'page' ) ) {
					$title = \get_the_title( $custom_404 );
				} else {
					$title = \esc_html__( 'Error 404', 'total' );
				}
			}

		}

		// WooCommerce titles (added here to provide support for vanilla WooCommerce).
		// @todo move to Integration\WooCommerce core class.
		if ( totaltheme_is_integration_active( 'woocommerce' )
			&& $woo_title = totaltheme_call_static( 'Integration\WooCommerce\Title', 'get' )
		) {
			$title = $woo_title;
		}

		// Check meta last.
		if ( $meta_title = $this->get_meta_title( $post_id ) ) {
			$title = $meta_title;
		}

		// Last check if title is empty.
		if ( ! $title ) {
			$post_id     = \wpex_get_current_post_id();
			$title       = \get_the_title( $post_id ); // must use get_the_title since we are passing a post ID.
			self::$is_h1 = true;
		}

		/**
		 * Filters the current page header title text.
		 *
		 * @param string $title The title to be displayed.
		 * @param int $post_id The current post ID.
		 * @todo rename filter to totaltheme/title and deprecate the $post_id variable.
		 */
		$title = (string) \apply_filters( 'wpex_title', $title, $post_id );

		if ( $title ) {
			$title = $this->replace_vars( $title );
		}

		return $title;
	}

	/**
	 * Returns true if the current title is an h1.
	 */
	public function is_h1(): bool {
		return self::$is_h1;
	}

	/**
	 * Returns post specific title used for elements.
	 */
	public function get_unfiltered_post_title( $post_id = '' ) {
		return $this->get_meta_title( $post_id ) ?: \get_the_title( $post_id );
	}

	/**
	 * Returns custom title.
	 */
	public function get_meta_title( $post_id = '' ) {
		$post_id = $post_id ?: \wpex_get_current_post_id();
		$meta_title = \get_post_meta( $post_id, self::META_KEY, true );
		if ( $meta_title ) {
			$post = \get_post( $post_id );
			if ( ! empty( $post->post_password ) ) {
				$prepend = \esc_html__( 'Protected: %s', 'total' );
				$protected_title_format = \apply_filters( 'protected_title_format', $prepend, $post );
				return \sprintf( $protected_title_format, $meta_title );
			} elseif ( isset( $post->post_status ) && 'private' === $post->post_status ) {
				$prepend = \esc_html__( 'Private: %s', 'total' );
				$private_title_format = \apply_filters( 'private_title_format', $prepend, $post );
				return \sprintf( $private_title_format, $meta_title );
			}
			return $meta_title;
		}
	}

	/**
	 * Returns the singular title.
	 */
	protected function get_singular_title() {
		$type = \get_post_type();
		switch ( $type ) {
			case 'post':
				$title = $this->get_standard_post_title();
				break;
			case 'page':
			case 'attachment':
			case 'wp_router_page':
			case 'templatera':
				$title = \single_post_title( '', false );
				break;
			case 'wpex_templates':
				$title = sprintf( esc_html_x( 'Template: %s', 'Template Name', 'total' ), \single_post_title( '', false ) );
				break;
			default:
				$title = $this->get_cpt_post_title( $type );
				break;
		}
		return $title;
	}

	/**
	 * Returns the standard post title.
	 */
	protected function get_standard_post_title() {
		$title = '';
		switch ( \get_theme_mod( 'blog_single_header', 'custom_text' ) ) {
			case 'custom_text':
				$title = \wpex_get_translated_theme_mod( 'blog_single_header_custom_text' );
				if ( ! $title ) {
					$title = \esc_html__( 'Blog', 'total' );
				}
				self::$is_h1 = false;
				break;
			case 'first_category':
				if ( $primary_term = \totaltheme_get_post_primary_term( \get_post(), 'category' ) ) {
					$title = $primary_term->name ?? '';
					self::$is_h1 = false;
				}
				break;
			default:
				$title = \single_post_title( '', false );
				break;
		}
		return $title;
	}

	/**
	 * Returns the custom post type post title.
	 */
	protected function get_cpt_post_title( $cpt = '' ) {
		$title       = '';
		$cpt         = $cpt ?: \get_post_type();
		self::$is_h1 = false; // all post types should not be h1 by default.

		$ptu_title = \wpex_get_ptu_type_mod( $cpt, 'page_header_title' );
		if ( $ptu_title && \is_string( $ptu_title ) ) {
			return $ptu_title;
		}

		if ( \defined( 'TYPES_VERSION' ) ) {
			$title = \get_theme_mod( 'cpt_single_page_header_text', null );
			if ( $title && \is_string( $title ) ) {
				return $title;
			}
		}

		switch ( $this->get_cpt_single_header_display( $cpt ) ) {
			case 'post_title':
				$title = \single_post_title( '', false );
				self::$is_h1 = true;
				break;
			case 'custom_text':
				$title = \wpex_get_translated_theme_mod( "{$cpt}_single_header_custom_text" );
				break;
			case 'first_category':
				if ( $primary_term = \totaltheme_get_post_primary_term() ) {
					$title = $primary_term->name ?? '';
				} else {
					$title = esc_html__( 'Uncategorized', 'total' );
				}
				break;
			default:
				$obj = \get_post_type_object( $cpt );
				if ( \is_object( $obj ) ) {
					$title = $obj->labels->name ?? '';
				}
				break;
		}

		return $title;
	}

	/**
	 * Returns the post type single header display.
	 */
	protected function get_cpt_single_header_display( string $cpt ): string {
		$display = \get_theme_mod( "{$cpt}_single_header" );

		// Portfolio and Staff automatically switch to the page title if needed.
		if ( \in_array( $cpt, [ 'portfolio', 'staff' ] ) && \in_array( $display, [ '', 'post_type_name' ] ) ) {
			switch ( $cpt ) {
				case 'portfolio':
					if ( totaltheme_call_static( 'Portfolio\Post_Type', 'is_enabled' )
						&& ! \in_array( 'title', totaltheme_call_static( 'Portfolio\Single_Blocks', 'get' ) )
					) {
						$display = 'post_title';
					}
					break;
				case 'staff':
					if ( totaltheme_call_static( 'Staff\Post_Type', 'is_enabled' )
						&& ! \in_array( 'title', totaltheme_call_static( 'Staff\Single_Blocks', 'get' ) )
					) {
						$display = 'post_title';
					}
					break;
			}
		}

		return $display;
	}

	/**
	 * Returns the current archive title.
	 */
	protected function get_archive_title() {

		// Author.
		if ( \is_author() ) {
			if ( $author = get_queried_object() ) {
				return $author->display_name; // Fix for authors with 0 posts
			} else {
				return \get_the_archive_title();
			}
		}

		// Post Type archive title.
		elseif ( \is_post_type_archive() ) {
			$ptu_title = \wpex_get_ptu_type_mod( \get_query_var( 'post_type' ), 'archive_page_header_title' );
			if ( $ptu_title && \is_string( $ptu_title ) ) {
				return $ptu_title;
			}
			return \post_type_archive_title( '', false );
		}

		// Daily archive title.
		elseif ( \is_day() ) {
			return \sprintf( \esc_html__( 'Daily Archives: %s', 'total' ), \get_the_date() );
		}

		// Monthly archive title.
		elseif ( \is_month() ) {
			return \sprintf( \esc_html__( 'Monthly Archives: %s', 'total' ), \get_the_date( 'F Y' ) );
		}

		// Yearly archive title.
		elseif ( \is_year() ) {
			return \sprintf( \esc_html__( 'Yearly Archives: %s', 'total' ), \get_the_date( 'Y' ) );
		}

		// Categories/Tags/Other.
		else {
			if ( \totaltheme_is_integration_active( 'post_types_unlimited' ) && is_tax() ) {
				$ptu_title = \wpex_get_ptu_tax_mod( \get_query_var( 'taxonomy' ), 'page_header_title' );
				if ( $ptu_title && \is_string( $ptu_title ) ) {
					return $ptu_title;
				}
			}
			return \get_theme_mod( 'page_header_enable_archive_label' ) ? \get_the_archive_title() : \single_term_title( '', false );
		}
	}

	/**
	 * Returns the posts page (home) title.
	 */
	protected function get_posts_page_title() {
		return \get_the_title( \get_option( 'page_for_posts', true ) ) ?: \esc_html__( 'Home', 'total' );
	}

	/**
	 * Replace {{title}} var.
	 */
	protected function replace_title_var( string $string ): string {
		if ( \str_contains( $string, '{{title}}' ) ) {
			if ( \is_singular() ) {
				$title = \single_post_title( '', false );
			} elseif ( \is_tax() ) {
				$title = \single_term_title( '', false );
			} elseif ( \is_archive() ) {
				$title = \get_the_archive_title();
			}
			if ( ! empty( $title ) && \is_string( $title ) ) {
				$string = \str_replace( '{{title}}', $title, $string );
				self::$is_h1 = true; // make sure the title is an h1
			} else {
				$string = \str_replace( '{{title}}', '', $string ); // fallback to make sure {{title}} is never added.
			}
		}
		return $string;
	}

	/**
	 * Replaces variables.
	 */
	protected function replace_vars( string $string ): string {
		// Important: We need to replace the title first to prevent potential endless loop in Replace_Vars.
		return (string) totaltheme_replace_vars( self::replace_title_var( $string ) );
	}

}
