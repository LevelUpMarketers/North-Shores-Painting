<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Yoast SEO Plugin Integration.
 */
class Yoast_SEO {

	/**
	 * Hook into actions and filters.
	 */
	public static function init() {
		if ( \apply_filters( 'wpex_filter_wpseo_metadesc', true ) ) {
			\add_filter( 'wpseo_metadesc', [ self::class, '_filter_wpseo_metadesc' ] );
		}

		\totaltheme_init_class( __CLASS__ . '\Breadcrumbs' );
		\totaltheme_init_class( __CLASS__ . '\Pagination_Fix' );

		// Disabled author archives.
		// @note we can't add the option check here because it triggers a _load_textdomain_just_in_time was called incorrectly notice.
		\add_filter( 'the_author_posts_link', [ self::class, '_filter_the_author_posts_link' ] );
		\add_filter( 'author_link', [ self::class, '_filter_author_link' ] );
	}

	/**
	 * Auto Generate meta description if empty using Total excerpt function.
	 * 
	 * @todo deprecate?
	 */
	public static function _filter_wpseo_metadesc( $metadesc ) {
		if ( ! $metadesc && \is_singular() ) {
			$post_excerpt = (string) \totaltheme_get_post_excerpt( [
				'length'    => (int) \apply_filters( 'wpex_yoast_metadesc_length', 160 ),
				'trim_type' => 'characters',
				'more'      => '',
			] );
			if ( $post_excerpt ) {
				$metadesc = \trim( \wp_strip_all_tags( $post_excerpt ) );
			}
		}
		return $metadesc;
	}

	/**
	 * Hooks into the_author_posts_link.
	 */
	public static function _filter_the_author_posts_link( $link ) {
		if ( ! \is_admin() && self::is_author_archive_disabled() ) {
			return \get_the_author();
		}
		return $link;
	}

	/**
	 * Hooks into author_link.
	 */
	public static function _filter_author_link( $link ) {
		if ( ! \is_admin() && self::is_author_archive_disabled() ) {
			return '';
		}
		return $link;
	}

	/**
	 * Checks if author archives are disabled.
	 */
	private static function is_author_archive_disabled(): bool {
		return \is_callable( 'WPSEO_Options::get' ) && true === \WPSEO_Options::get( 'disable-author' );
	}

	/**
	 * Deprecated.
	 */
	public static function metadesc(): void {
		\_deprecated_function( __METHOD__, 'Total 6.0' );
	}

	public static function add_theme_support(): void {
		\_deprecated_function( __METHOD__, 'Total 5.16' );
	}

}
