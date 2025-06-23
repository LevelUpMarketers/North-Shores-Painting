<?php

namespace TotalTheme\Scripts;

\defined( 'ABSPATH' ) || exit;

/**
 * Registers and enqueues frontend scripts.
 */
class Loader {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'init', [ self::class, '_on_init' ] );
		\add_action( 'wp_enqueue_scripts', [ self::class, '_on_wp_enqueue_scripts' ] );
	}

	/**
	 * Runs on the "init" hook to register scripts earlier that must work with WPBakery.
	 */
	public static function _on_init(): void {
		\totaltheme_call_static( 'TotalTheme\Scripts\CSS', 'register_early' );
		\totaltheme_call_static( 'TotalTheme\Scripts\JS', 'register_early' );
	}

	/**
	 * Runs on the "wp_enqueue_scripts" hook.
	 */
	public static function _on_wp_enqueue_scripts(): void {
		// Register scripts
		\totaltheme_call_static( 'TotalTheme\Scripts\CSS', 'register' );
		\totaltheme_call_static( 'TotalTheme\Scripts\JS', 'register' );

		// Enqueue scripts
		\totaltheme_call_static( 'TotalTheme\Scripts\CSS', 'enqueue' );
		\totaltheme_call_static( 'TotalTheme\Scripts\JS', 'enqueue' );

		// Remove junk
		\wp_dequeue_style( 'classic-theme-styles'  );
	}

}
