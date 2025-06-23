<?php

namespace TotalTheme\Integration\WooCommerce\Cart;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Off Canvas Cart.
 */
final class Off_Canvas {

	/**
	 * Class instance.
	 */
	private static $instance = null;
	
	/**
	 * Check if the scrips are loaded.
	 */
	private static $scripts_loaded = false;

	/**
	 * Check if the cart should open on page load.
	 */
	static $open_on_load = false;

	/**
	 * Create or retrieve the instance of Error_404.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private Constructor.
	 */
	private function __construct() {
		\add_filter( 'woocommerce_add_to_cart_fragments', [ self::class, '_register_fragments' ] );
		\add_action( 'wp_footer', [ self::class, '_render_off_canvas' ] );

		if ( 'wp_enqueue_scripts' === \current_filter() ) {
			self::_load_scripts();
		} elseif ( ! \did_action( 'wp_enqueue_scripts' ) ) {
			\add_action( 'wp_enqueue_scripts', [ self::class, '_load_scripts' ] );
		}
	}

	/**
	 * Register cart fragments.
	 */
	public static function _register_fragments( $fragments ) {
		$fragments['#wpex-off-canvas-cart .wpex-mini-cart__items'] = self::_get_items();
		$fragments['#wpex-off-canvas-cart .wpex-mini-cart__footer'] = self::_get_footer();
		return $fragments;
	}

	/**
	 * Load Scripts.
	 */
	public static function _load_scripts(): void {
		if ( self::$scripts_loaded ) {
			return;
		}

		\wp_enqueue_script(
			'wpex-wc-cart-off-canvas',
			\totaltheme_get_js_file( 'frontend/woocommerce/cart-off-canvas' ),
			[ 'wpex-off-canvas', 'jquery' ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);

		\wp_localize_script(
			'wpex-wc-cart-off-canvas',
			'wpex_wc_cart_off_canvas_params',
			[
				'open_on_load' => (int) self::$open_on_load,
				'auto_open'    => (int) get_theme_mod( 'woo_off_canvas_cart_auto_open', true ),
			]
		);

		self::$scripts_loaded = true;
	}

	/**
	 * Renders the element.
	 */
	public static function _render_off_canvas(): void {
		if ( ! \class_exists( '\TotalTheme\Off_Canvas' ) ) {
			return;
		}

		self::_load_scripts();

		$args = [
			'id'                      => 'wpex-off-canvas-cart',
			'class'                   => 'wpex-mini-cart',
			'placement'               => 'right',
			'title'                   => \wpex_get_translated_theme_mod( 'woo_off_canvas_cart_title' ) ?: \esc_html__( 'Your Cart', 'total' ),
			'inner_scroll'            => true,
			'close_button_aria_label' => \wpex_get_aria_label( 'cart_close' ),
			'auto_insert'             => false,
			'bottom_border'           => true,
		];

		$off_canvas = new \TotalTheme\Off_Canvas( $args, self::_get_items(), self::_get_footer() );
		$off_canvas->renderer();
	}

	/**
	 * Returns cart items.
	 */
	public static function _get_items(): string {
		ob_start();
			\get_template_part( 'partials/cart/off-canvas/items' );
		return (string) ob_get_clean();
	}

	/**
	 * Returns cart footer.
	 */
	public static function _get_footer(): string {
		ob_start();
			\get_template_part( 'partials/cart/off-canvas/footer' );
		return (string) ob_get_clean();
	}

	/**
	 * Open cart on page load.
	 */
	public static function open_on_load( $value = true ): void {
		self::$open_on_load = $value;
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
