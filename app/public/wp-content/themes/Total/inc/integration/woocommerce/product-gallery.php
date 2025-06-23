<?php

namespace TotalTheme\Integration\WooCommerce;

\defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Product Gallery Integration.
 */
final class Product_Gallery {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Product_Gallery.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		if ( \is_customize_preview() ) {
			\add_action( 'wp', [ $this, 'add_theme_support' ] ); // run later to work correctly with customizer.
		} else {
			\add_action( 'after_setup_theme', [ $this, 'add_theme_support' ] );
		}
		\add_action( 'wp_enqueue_scripts', [ $this, 'register_lightbox_script' ] );
		\add_action( 'wp_footer', [ $this, 'lightbox_footer_scripts' ] );
		\add_action( 'woocommerce_after_single_product', [ $this, 'maybe_enqueue_lightbox_scripts' ] );
		\add_filter( 'woocommerce_single_product_carousel_options', [ $this, 'flexslider_options' ] );
		\add_filter( 'woocommerce_product_thumbnails_columns', [ $this, 'thumbails_columns' ] );
	}

	/**
	 * Add theme support.
	 */
	public function add_theme_support() {
		if ( \get_theme_mod( 'woo_product_gallery_slider', true ) ) {
			\add_theme_support( 'wc-product-gallery-slider' );
		} else {
			\remove_theme_support( 'wc-product-gallery-slider' );
		}

		if ( \get_theme_mod( 'woo_product_gallery_zoom', true ) ) {
			\add_theme_support( 'wc-product-gallery-zoom' );
		} else {
			\remove_theme_support( 'wc-product-gallery-zoom' );
		}

		if ( 'woo' === $this->get_lightbox_type() ) {
			\add_theme_support( 'wc-product-gallery-lightbox' );
		} else {
			\remove_theme_support( 'wc-product-gallery-lightbox' );
		}
	}

	/**
	 * Check what lightbox type is enabled for products.
	 */
	public function get_lightbox_type(): string {
		return (string) \get_theme_mod( 'woo_product_gallery_lightbox', 'total' );
	}

	/**
	 * Register scripts.
	 */
	public function register_lightbox_script(): void {
		\wp_register_script(
			'wpex-wc-product-lightbox',
			\totaltheme_get_js_file( 'frontend/woocommerce/lightbox-gallery' ),
			[ 'jquery', 'wpex-fancybox' ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);

		\wp_localize_script(
			'wpex-wc-product-lightbox',
			'wpex_wc_lightbox_params',
			[
				'showTitle' => \get_theme_mod( 'woo_product_gallery_lightbox_titles' ) ? 1 : 0,
			]
		);
	}

	/**
	 * Enqueue footer scripts.
	 */
	public function lightbox_footer_scripts() {
		if ( \is_product() ) {
			$this->maybe_enqueue_lightbox_scripts();
		}
	}

	/**
	 * Maybe enqueue theme lightbox scripts.
	 */
	public function maybe_enqueue_lightbox_scripts() {
		if ( 'total' !== $this->get_lightbox_type() ) {
			return;
		}
		\wpex_enqueue_lightbox_scripts();
		\wp_enqueue_script( 'wpex-wc-product-lightbox' );
	}

	/**
	 * Custom product gallery flexslider options.
	 *
	 * Not used at the moment due to WooCommerce bugs.
	 */
	public function flexslider_options( $options ) {
		if ( \get_theme_mod( 'woo_product_gallery_slider_arrows', true ) ) {
			$options['directionNav'] = true;

			$prev_icon = \apply_filters( 'wpex_slider_previous_icon', 'material-arrow-back-ios' );
			$prev_icon = \totaltheme_call_static( 'Theme_Icons', 'get_icon', $prev_icon );
			if ( $prev_icon ) {
				$options['prevText'] = '<span class="screen-reader-text">' . \esc_html__( 'previous slide', 'total' ) . '</span>' . $prev_icon;
			}

			$next_icon = \apply_filters( 'wpex_slider_next_icon', 'material-arrow-forward-ios' );
			$next_icon = \totaltheme_call_static( 'Theme_Icons', 'get_icon', $next_icon );
			if ( $next_icon ) {
				$options['nextText'] = '<span class="screen-reader-text">' . \esc_html__( 'next slide', 'total' ) . '</span>' . $next_icon;
			}
		}
		$speed = \get_theme_mod( 'woo_product_gallery_slider_animation_speed' ) ?: 600;
		$options['animationSpeed'] = \intval( $speed );
		return $options;
	}

	/**
	 * Define columns for gallery.
	 */
	public function thumbails_columns() {
		$columns = \absint( \get_theme_mod( 'woocommerce_gallery_thumbnails_count' ) );
		if ( ! $columns ) {
			$columns = 5;
		}
		return $columns;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
