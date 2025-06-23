<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Font_Container {

	/**
	 * Static only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		if ( \is_admin() ) {
			\add_filter( 'vc_font_container_get_allowed_tags', [ self::class, 'filter_get_allowed_tags' ] );
			\add_filter( 'vc_font_container_get_fonts_filter', [ self::class, 'filter_get_fonts_filter' ] );
		}
	}

	/**
	 * Hooks into vc_font_container_get_allowed_tags.
	 */
	public static function filter_get_allowed_tags( $tags ): array {
		$tags['span'] = 'span';
		return $tags;
	}

	/**
	 * Hooks into vc_font_container_get_fonts_filter.
	 */
	public static function filter_get_fonts_filter( $fonts ): array {
		$new_fonts[''] = esc_html__( 'Default', 'total' );
		$fonts = \array_merge( $new_fonts, $fonts );
		if ( $google_fonts = \wpex_google_fonts_array() ) {
			$fonts = \array_merge( $fonts, \array_combine( $google_fonts, $google_fonts ) );
		}
		return $fonts;
	}

}
