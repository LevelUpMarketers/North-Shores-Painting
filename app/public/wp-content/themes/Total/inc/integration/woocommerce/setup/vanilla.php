<?php

namespace TotalTheme\Integration\WooCommerce\Setup;

defined( 'ABSPATH' ) || exit;

/**
 * Vanilla WooCommerce (very basic WooCommerce support).
 */
final class Vanilla {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init class.
	 */
	public static function init() {
		add_action( 'customize_register' , [ self::class , 'register_customizer_settings' ], 100 );
		add_action( 'after_setup_theme', [ self::class, 'on_after_setup_theme' ] );
		add_filter( 'woocommerce_show_page_title', '__return_false' );
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	}

	/**
	 * Register customizer settings.
	 */
	public static function register_customizer_settings( $wp_customize ) {
		require_once \WPEX_INC_DIR . 'integration/woocommerce/customize/vanilla-settings.php';
	}

	/**
	 * Register theme support.
	 */
	public static function on_after_setup_theme() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
	}

}
