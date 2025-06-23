<?php

namespace TotalTheme\Integration\Yoast_SEO;

defined( 'ABSPATH' ) || exit;

/**
 * Yoast SEO Breadcrumbs fixes.
 */
class Breadcrumbs {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \apply_filters( 'wpex_filter_wpseo_breadcrumb_links', true ) ) {
			\add_filter( 'wpseo_breadcrumb_links', [ self::class, 'modify_links' ] );
		}

		// These functions only run if the yoast SEO crumbs are enabled.
		if ( true === \wp_validate_boolean( \get_theme_mod( 'enable_yoast_breadcrumbs', true ) ) ) {
			\add_filter( 'wpseo_breadcrumb_single_link_info', [ self::class, 'trim_the_title' ], 10, 3 );
		}
	}

	/**
	 * Filter the ancestors of the yoast seo breadcrumbs.
	 */
	public static function modify_links( $links ) {
		$new_breadcrumb = [];

		// Add "Main Page" to crumbs.
		if ( \is_singular() ) {
			$post_type = \get_post_type();
			switch ( $post_type ) {
				case 'post':
					$main_page = \get_theme_mod( 'blog_page' );
					break;
				case 'staff':
				case 'portfolio':
				case 'testimonials':
					$main_page = \get_theme_mod( "{$post_type}_page" );
					break;
				case 'just_event':
					$main_page = \get_option( 'just_events' )['totaltheme_events_page'] ?? '';
					break;
				default:
					$main_page = \wpex_get_ptu_type_mod( $post_type, 'main_page' );
					break;
			}
			if ( ! empty( $main_page ) ) {
				$main_page      = \wpex_parse_obj_id( $main_page, 'page' );
				$main_page_post = \get_post( $main_page );
				if ( 'publish' == \get_post_status( $main_page_post ) ) {
					$page_title     = \get_the_title( $main_page_post );
					$page_permalink = \get_permalink( $main_page_post );
					if ( $page_permalink && $page_title ) {
						$new_breadcrumb[] = [
							'url'  => \esc_url( $page_permalink ),
							'text' => \esc_html( $page_title ),
						];
					}
				}
			}
		}

		// Combine new crumb.
		if ( $new_breadcrumb ) {
			if ( ! \is_callable( '\WPSEO_Options::get' ) || '' !== \WPSEO_Options::get( 'breadcrumbs-home' ) ) {
				\array_splice( $links, 1, -2, $new_breadcrumb );
			} else {
				\array_splice( $links, 0, -3, $new_breadcrumb );
			}
		}

		return $links;
	}

	/**
	 * Trim the Yoast SEO title.
	 */
	public static function trim_the_title( $link_info, $index, $crumbs ) {
		$trim = absint( \get_theme_mod( 'breadcrumbs_title_trim' ) );
		if ( $trim && is_array( $crumbs ) && ( absint( $index ) + 1 == count( $crumbs ) ) ) {
			if ( isset( $link_info['text'] ) ) {
				$link_info['text'] = wp_trim_words( $link_info['text'], $trim );
			}
		}
		return $link_info;
	}

}
