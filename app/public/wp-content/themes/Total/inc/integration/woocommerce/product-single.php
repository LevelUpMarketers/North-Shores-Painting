<?php

namespace TotalTheme\Integration\WooCommerce;

\defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Product Single Tweaks.
 */
class Product_Single {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Product_Entry.
	 */
	public static function instance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! \defined( 'WCPAY_ABSPATH' ) ) {
			\add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'before_add_to_cart_button' ], 99 );
			\add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'after_add_to_cart_button' ], 0 );
		}

		totaltheme_init_class( __NAMESPACE__ . '\Single\Flex_Container' );
	}

	/**
	 * Open button wrapper.
	 */
	public function before_add_to_cart_button() {
		if ( $this->add_button_wrap_check() ) {
			echo '<div class="wpex-woocommerce-product-qty-btn-wrapper wpex-inline-flex wpex-flex-wrap wpex-items-center wpex-gap-15">';
		}
	}

	/**
	 *  Close button wrapper.
	 */
	public function after_add_to_cart_button() {
		if ( $this->add_button_wrap_check() ) {
			echo '</div>';
		}
	}

	/**
	 *  Checks if we should add the button wrapper around the add to cart link.
	 */
	protected function add_button_wrap_check() {
		global $product;
		$type = ( \is_object( $product ) && \is_callable( [ $product, 'get_type' ] ) ) ? $product->get_type() : '';

		if ( \in_array( $type, [ 'simple', 'variable' ] )
			&& ! \get_theme_mod( 'woo_product_add_to_cart_full_width' )
			&& \get_theme_mod( 'woo_product_qty_btn_wrapper', true )
		) {
			return true;
		}
	}

}
