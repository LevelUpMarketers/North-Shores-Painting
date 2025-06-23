<?php

namespace TotalTheme\Integration\WooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Titles.
 */
final class Title {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns WooCommerce title.
	 */
	public static function get() {
		if ( wpex_is_woo_shop() ) {
			return self::get_shop_title();
		} elseif ( is_product() ) {
			return self::get_single_product_title();
		} elseif ( is_order_received_page() ) {
			return esc_html__( 'Order Received', 'total' );
		}
	}

	/**
	 * Get the WooCommerce shop title.
	 */
	protected static function get_shop_title() {
		if ( ! empty( $_GET['s'] ) ) {
			return esc_html__( 'Shop results for:', 'total' ) . ' <span>&quot;' . esc_html( $_GET['s'] ) . '&quot;</span>';
		} else {
			if ( $shop_id = \totaltheme_wc_get_page_id( 'shop' ) ) {
				return get_the_title( $shop_id );
			}
			return esc_html__( 'Shop', 'total' );
		}
	}

	/**
	 * Get the WooCommerce single product title.
	 */
	protected static function get_single_product_title() {
		return wpex_get_translated_theme_mod( 'woo_shop_single_title' ) ?: esc_html__( 'Shop', 'total' );
	}

}
