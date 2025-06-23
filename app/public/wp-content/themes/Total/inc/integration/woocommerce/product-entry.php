<?php

namespace TotalTheme\Integration\WooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Product Entry Tweaks.
 */
final class Product_Entry {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Product_Entry.
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

		// Add HTML to product entries
		// Note link opens on 10 and closes on 5.
		if ( apply_filters( 'wpex_woocommerce_has_shop_loop_item_inner_div', true ) ) {
			add_action( 'woocommerce_before_shop_loop_item', [ $this, 'add_shop_loop_item_inner_div' ], 0 );
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'close_shop_loop_item_inner_div' ], 99 );
		}

		// Add wrapper around product entry details to align buttons.
		if ( apply_filters( 'wpex_woocommerce_has_product_entry_details_wrap', true ) ) {
			add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'loop_details_open' ], 99 );
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'loop_details_close' ], 4 );
		}

		// Add out of stock badge.
		if ( apply_filters( 'wpex_woocommerce_out_of_stock_badge', true ) ) {
			add_action( 'woocommerce_before_shop_loop_item', [ $this, 'add_shop_loop_item_out_of_stock_badge' ] );
		}

		// Remove loop product thumbnail function and add our own that pulls from template parts.
		// @todo add setting to disable this (make sure associated customizater settings are removed as well).
		if ( apply_filters( 'wpex_woocommerce_template_loop_product_thumbnail', true ) ) {

			// Tweak link open/close.
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

			// Add link around media.
			add_action( 'wpex_woocommerce_loop_thumbnail_before', 'woocommerce_template_loop_product_link_open', 0 );
			add_action( 'wpex_woocommerce_loop_thumbnail_after', 'woocommerce_template_loop_product_link_close', 11 );

			// Display custom thumbnail media.
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'loop_product_thumbnail' ], 10 );

			// Add element around add to cart button.
			if ( self::has_default_cart_buttons() ) {
				add_filter( 'woocommerce_loop_add_to_cart_link', [ self::class, 'add_to_cart_link_wrapper' ], 9999 );
			}
			
			// Add custom cart icons into thumbnail wrap.
			else {
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				add_action( 'wpex_woocommerce_loop_thumbnail_after', [ $this, 'loop_add_to_cart' ], 40 );
			}

		}
	}

	/**
	 * Adds an opening div "product-inner" around product entries.
	 */
	public function add_shop_loop_item_inner_div() {
		$class = 'product-inner wpex-flex wpex-flex-col wpex-flex-grow wpex-relative';

		$align = get_theme_mod( 'woo_entry_align', null );

		if ( 'right' === $align || 'center' === $align || 'left' === $align ) {
			$class .= ' text' . $align;
		}

		echo '<div class="' . esc_attr( $class ) . '">';
	}

	/**
	 * Closes the "product-inner" div around product entries.
	 */
	public function close_shop_loop_item_inner_div() {
		echo '</div>';
	}

	/**
	 * Closes the "product-inner" div around product entries.
	 */
	public static function add_to_cart_link_wrapper( $link ) {
		$class = 'product-actions';

		if ( get_theme_mod( 'woo_entry_equal_height' ) && get_theme_mod( 'woo_default_entry_buttons' ) ) {
			$class .= ' wpex-mt-auto';
		}

		return '<div class="' . esc_attr( $class ) . '">' . $link . '</div>';
	}

	/**
	 * Adds an out of stock tag to the products.
	 */
	public function add_shop_loop_item_out_of_stock_badge() {
		if ( true === wpex_woo_product_instock() ) {
			return;
		}
		$text = apply_filters( 'wpex_woo_outofstock_text', esc_html__( 'Out of Stock', 'total' ) );
		echo '<div class="outofstock-badge">' . esc_html( $text ) . '</div>';
	}

	/**
	 * Open details wrapper
	 */
	public function loop_details_open() {
		echo '<div class="product-details wpex-pt-15">';
	}

	/**
	 * Close details wrapper
	 */
	public function loop_details_close() {
		echo '</div>';
	}

	/**
	 * Returns our product thumbnail from our template parts based on selected style in theme mods.
	 */
	public function loop_product_thumbnail() {
		$style = get_theme_mod( 'woo_product_entry_style' );

		if ( ! $style ) {
			$style = 'image-swap';
		}

		// Get entry product media template part.
		echo '<div class="wpex-loop-product-images wpex-overflow-hidden wpex-relative">';
			do_action( 'wpex_woocommerce_loop_thumbnail_before' );
				get_template_part( 'woocommerce/loop/thumbnail/' . $style );
			do_action( 'wpex_woocommerce_loop_thumbnail_after' );
		echo '</div>';
	}

	/**
	 * Output loop add to cart buttons with custom wrapper.
	 */
	public function loop_add_to_cart() { ?>
		<div class="wpex-loop-product-add-to-cart wpex-absolute wpex-bottom-0 wpex-left-0 wpex-right-0 wpex-text-center wpex-transition-all wpex-duration-250 wpex-z-2 wpex-translate-y-100 wpex-invisible"><?php
			woocommerce_template_loop_add_to_cart();
		?></div>
	<?php }

	/**
	 * Returns true if the product entry should display the default add to cart buttons below the price.
	 */
	private function has_default_cart_buttons(): bool {
		return (bool) get_theme_mod( 'woo_default_entry_buttons' );
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
