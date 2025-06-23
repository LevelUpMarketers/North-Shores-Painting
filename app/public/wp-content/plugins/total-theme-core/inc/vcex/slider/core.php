<?php

namespace TotalThemeCore\Vcex\Slider;

\defined( 'ABSPATH' ) || exit;

/**
 * Core Slider methods.
 */

class Core {

	/**
	 * Returns list of style dependencies.
	 */
	public static function get_style_depends(): array {
		return [
			'slider-pro',
		];
	}

	/**
	 * Returns list of script dependencies.
	 */
	public static function get_script_depends(): array {
		return [
			'slider-pro',
			'wpex-slider-pro',
		];
	}

	/**
	 * Enqueues the carousel scripts.
	 */
	public static function enqueue_scripts() {
		foreach ( self::get_style_depends() as $style ) {
			\wp_enqueue_style( $style );
		}

		foreach ( self::get_script_depends() as $script ) {
			\wp_enqueue_script( $script );
		}
	}

}
