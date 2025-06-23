<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Integration.
 */
class WooCommerce {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Hook into actions and filters.
	 */
	public static function init() {
		\totaltheme_init_class( __CLASS__ . '\Cart' );

		if ( self::is_advanced_mode() ) {
			\totaltheme_init_class( __CLASS__ . '\Setup\Advanced' );
		} else {
			\totaltheme_init_class( __CLASS__ . '\Setup\Vanilla' );
		}

		// Global hooks.
		\add_filter( 'totaltheme/header/menu/search/form_args', [ self::class, 'filter_header_searchform_args'] );
		\add_filter( 'totaltheme/overlays/styles', [ self::class, 'filter_overlay_styles' ] );
		\add_filter( 'is_woocommerce', [ self::class, 'filter_is_woocommerce' ] );
	}

	/**
	 * Checks if advanced integration is enabled.
	 */
	public static function is_advanced_mode(): bool {
		if ( ! self::is_advanced_mode_supported() ) {
			return false;
		}
		$check = \wp_validate_boolean( \get_theme_mod( 'woocommerce_integration', true ) );
		$check = \apply_filters( 'wpex_has_woo_mods', $check ); // @deprecated
		$check = \apply_filters( 'totaltheme/integration/woocommerce/is_advanced_mode', $check );
		return (bool) $check;
	}

	/**
	 * Checks if advanced mode is supported.
	 */
	public static function is_advanced_mode_supported(): bool {
		return \defined( '\WC_VERSION' ) && \version_compare( \WC_VERSION, '3.0.0', '>=' );
	}

	/**
	 * Filters the header search form args.
	 */
	public static function filter_header_searchform_args( array $args ): array {
		if ( \wp_validate_boolean( \get_theme_mod( 'woo_header_product_searchform', false ) ) ) {
			$args['post_type'] = 'product';
		}
		return $args;
	}

	/**
	 * Adds new overlay styles.
	 */
	public static function filter_overlay_styles( array $styles ): array {
		return \array_merge( $styles, [
			'title-price-hover' => [
				'name' => \esc_html__( 'Title + Price Hover', 'total' ),
			],
			'add-to-cart-hover' => [
				'name' => \esc_html__( 'Add to Cart Hover', 'total' ),
			],
		] );
	}

	/**
	 * Hooks into "is_woocommerce".
	 */
	public static function filter_is_woocommerce( $check ): bool {
		if ( \totaltheme_is_integration_active( 'post_types_unlimited' )
			&& \is_tax()
			&& \wpex_get_ptu_tax_mod( \get_query_var( 'taxonomy' ), 'template_id' )
		) {
			$check = false;
		}
		return (bool) $check;
	}

	/**
	 * Hooks into wp_enqueue_scripts.
	 */
	public static function register_scripts(): void {
		\_deprecated_function( __METHOD__, 'Total 6.0' );
	}

}
