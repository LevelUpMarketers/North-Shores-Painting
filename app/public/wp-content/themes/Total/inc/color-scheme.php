<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Site Color Schemes.
 */
class Color_Scheme {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Register hooks.
	 */
	public static function init(): void {
		\add_filter( 'wpex_html_class', [ self::class, 'add_html_class' ] );
		\add_filter( 'wp_enqueue_scripts', [ self::class, 'enqueue_global_color_scheme' ] );
	}

	/**
	 * Return available color schemes.
	 */
	public static function get_color_schemes(): array {
		$color_schemes = [];
		return (array) \apply_filters( 'wpex_color_schemes', $color_schemes );
	}

	/**
	 * Return current color scheme.
	 */
	public static function get_active_color_scheme(): string {
		return (string) \apply_filters( 'wpex_active_color_scheme', '' );
	}

	/**
	 * Loads custom color scheme CSS.
	 */
	public static function enqueue_global_color_scheme(): void {
		$color_schemes = self::get_color_schemes();

		if ( ! $color_schemes ) {
			return;
		}

		$active_scheme = self::get_active_color_scheme();

		if ( ! $active_scheme ) {
			return;
		}

		$script = $color_schemes[ $active_scheme ] ?? '';

		if ( $script ) {
			\wp_enqueue_style(
				"wpex-color-scheme-{$active_scheme}",
				$script,
				[],
				\WPEX_THEME_VERSION
			);
		}

	}

	/**
	 * Add HTML class.
	 */
	public static function add_html_class( array $classes ): array {
		if ( $color_scheme = self::get_active_color_scheme() ) {
			$classes[] = "wpex-color-scheme-{$color_scheme}";
		}
		return $classes;
	}

}
