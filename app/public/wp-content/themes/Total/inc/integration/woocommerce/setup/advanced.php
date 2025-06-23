<?php

namespace TotalTheme\Integration\WooCommerce\Setup;

\defined( 'ABSPATH' ) || exit;

/**
 * Advanced WooCommerce setup.
 */
final class Advanced {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		self::include_functions();
		self::initiate_classes();
		self::init_hooks();
	}

	/**
	 * Include functions.
	 */
	public static function include_functions() {
		require_once \WPEX_INC_DIR . 'integration/woocommerce/actions.php';
		require_once \WPEX_INC_DIR . 'integration/woocommerce/function-overrides.php';
	}

	/**
	 * Initiate classes.
	 */
	public static function initiate_classes() {
		\totaltheme_init_class( 'Integration\WooCommerce\Quantity_Plus_Minus' );
		
		if ( \apply_filters( 'wpex_woocommerce_maybe_modify_product_single', true ) ) {
			\totaltheme_init_class( 'Integration\WooCommerce\Product_Single' );
		}

		if ( \apply_filters( 'wpex_woocommerce_maybe_modify_product_entry', true ) ) {
			\totaltheme_init_class( 'Integration\WooCommerce\Product_Entry' );
		}

		if ( \apply_filters( 'wpex_woocommerce_maybe_modify_product_gallery', true ) ) {
			\totaltheme_init_class( 'Integration\WooCommerce\Product_Gallery' );
		}
		
		if ( ! \get_theme_mod( 'woo_dynamic_image_resizing', false ) ) {
			\totaltheme_init_class( 'Integration\WooCommerce\Thumbnails' );
		}
	}

	/**
	 * Hook into actions and filters.
	 */
	public static function init_hooks() {
		\add_action( 'init', [ self::class, 'on_init' ] );
		\add_action( 'after_setup_theme', [ self::class, 'on_after_setup_theme' ] );
		\add_filter( 'woocommerce_show_page_title', '__return_false' );
		\add_filter( 'wpex_customizer_panels', [ self::class, 'add_customizer_settings' ] );
		\add_action( 'wp_enqueue_scripts', [ self::class, 'on_wp_enqueue_scripts' ] );
		\add_filter( 'body_class', [ self::class, 'filter_body_class' ] );
		\add_filter( 'woocommerce_sale_flash', [ self::class, 'filter_sale_flash' ], 10, 3 );
		\add_filter( 'loop_shop_per_page', [ self::class, 'filter_loop_shop_per_page' ], 20 );
		\add_filter( 'loop_shop_columns', [ self::class, 'filter_loop_shop_columns' ] );
		\add_filter( 'woocommerce_pagination_args', [ self::class, 'filter_pagination_args' ] );
		\add_filter( 'woocommerce_continue_shopping_redirect', [ self::class, 'filter_continue_shopping_redirect' ] );
		\add_filter( 'woocommerce_product_tag_cloud_widget_args', [ self::class, 'filter_tag_cloud_widget_args' ] );
		\add_filter( 'wpex_typography_settings', [ self::class, 'add_typography_settings' ], 60 );
		\add_filter( 'woocommerce_product_review_comment_form_args', [ self::class, 'filter_comment_form_args' ] );
		\add_filter( 'woocommerce_my_account_my_orders_query', [ self::class, 'filter_my_account_my_orders_query' ] );
		\add_filter( 'woocommerce_demo_store', [ self::class, 'filter_woocommerce_demo_store' ] );
		\add_filter( 'totaltheme/page/header/is_enabled', [ self::class, 'filter_page_header_is_enabled' ] );

		if ( \get_theme_mod( 'woo_block_notices_enable', true ) ) {
			\add_filter( 'woocommerce_use_block_notices_in_classic_theme', '__return_true' );
		}

		if ( self::maybe_remove_styles() ) {
			\add_filter( 'woocommerce_enqueue_styles', [ self::class, 'filter_woocommerce_enqueue_styles' ], 1000 );
			\add_action( 'enqueue_block_assets', [ self::class, 'on_enqueue_block_assets' ] );
		}

		if ( self::maybe_remove_single_title() && ! is_customize_preview() ) {
			\remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		}
	}

	/*-------------------------------------------------------------------------------*/
	/* - Start Class Functions
	/*-------------------------------------------------------------------------------*/

	/**
	 * Runs on the "after_setup_theme" hook.
	 */
	public static function on_after_setup_theme() {
		\add_theme_support( 'woocommerce' );
	}

	/**
	 * Runs on the "init" hook.
	 */
	public static function on_init() {
		$is_customize_preview = \is_customize_preview();

		// Remove single meta.
		if ( ! $is_customize_preview && ! \get_theme_mod( 'woo_product_meta', true ) ) {
			\remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		}

		// Remove result count if disabled.
		if ( ! $is_customize_preview && ! \get_theme_mod( 'woo_shop_result_count', true ) ) {
			\remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		}

		// Remove orderby if disabled.
		if ( ! $is_customize_preview && ! \get_theme_mod( 'woo_shop_sort', true ) ) {
			\remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		}

		// Move tabs- Add after meta which is set to 40.
		if ( 'right' === \get_theme_mod( 'woo_product_tabs_position' ) ) {
			\remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			\add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 41 );
		}

		// Remove Gutenberg scripts.
		if ( ! \current_theme_supports( 'gutenberg-editor' ) ) {
			\add_filter( 'totaltheme/integration/gutenberg/dequeue_styles/styles_list', [ self::class, 'filter_gutenberg_styles_to_dequeue' ] );
		}

		// Add to cart message edits.
		if ( \wp_validate_boolean( \get_theme_mod( 'woo_add_to_cart_message_enable', true ) ) ) {
			\add_filter( 'wc_add_to_cart_message_html', [ self::class, 'modify_wc_add_to_cart_message_html' ], 5 );
		} else {
			// Disable add to cart notice.
			\add_filter( 'wc_add_to_cart_message_html', [ self::class, 'disable_wc_add_to_cart_message_html' ], 100 );
		}

		// Remove button class from forward buttons in notices because it looks terrible.
		\add_filter( 'woocommerce_add_success', [ self::class, 'filter_woocommerce_add_message' ] );
		\add_filter( 'woocommerce_add_notice', [ self::class, 'filter_woocommerce_add_message' ] );
		\add_filter( 'woocommerce_add_error', [ self::class, 'filter_woocommerce_add_message' ] );
	}

	/**
	 * Adds Customizer settings for WooCommerce.
	 */
	public static function add_customizer_settings( $panels ) {
		$branding = ( $branding = \wpex_get_theme_branding() ) ? ' (' . $branding . ')' : '';
		$panels['woocommerce'] = [
			'title'    => "WooCommerce{$branding}",
			'settings' => \WPEX_INC_DIR . 'integration/woocommerce/customize/advanced-settings.php',
			'icon'     => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1024 1024'%3E%3Cpath fill='%237F54B3' d='M612.192 426.336c0-6.896-3.136-51.6-28-51.6-37.36 0-46.704 72.256-46.704 82.624 0 3.408 3.152 58.496 28.032 58.496 34.192-.032 46.672-72.288 46.672-89.52zm202.192 0c0-6.896-3.152-51.6-28.032-51.6-37.28 0-46.608 72.256-46.608 82.624 0 3.408 3.072 58.496 27.952 58.496 34.192-.032 46.688-72.288 46.688-89.52zM141.296.768c-68.224 0-123.504 55.488-123.504 123.92v650.72c0 68.432 55.296 123.92 123.504 123.92h339.808l123.504 123.936V899.328h278.048c68.224 0 123.52-55.472 123.52-123.92v-650.72c0-68.432-55.296-123.92-123.52-123.92h-741.36zm526.864 422.16c0 55.088-31.088 154.88-102.64 154.88-6.208 0-18.496-3.616-25.424-6.016-32.512-11.168-50.192-49.696-52.352-66.256 0 0-3.072-17.792-3.072-40.752 0-22.992 3.072-45.328 3.072-45.328 15.552-75.728 43.552-106.736 96.448-106.736 59.072-.032 83.968 58.528 83.968 110.208zM486.496 302.4c0 3.392-43.552 141.168-43.552 213.424v75.712c-2.592 12.08-4.16 24.144-21.824 24.144-46.608 0-88.88-161.472-92.016-161.84-6.208 6.896-62.24 161.84-96.448 161.84-24.864 0-43.552-113.648-46.608-123.936C176.704 436.672 160 334.224 160 327.328c0-20.672 1.152-38.736 26.048-38.736 6.208 0 21.6 6.064 23.712 17.168 11.648 62.032 16.688 120.512 29.168 185.968 1.856 2.928 1.504 7.008 4.56 10.432 3.152-10.288 66.928-168.784 94.96-168.784 22.544 0 30.4 44.592 33.536 61.824 6.208 20.656 13.088 55.216 22.416 82.752 0-13.776 12.48-203.12 65.392-203.12 18.592.032 26.704 6.928 26.704 27.568zM870.32 422.928c0 55.088-31.088 154.88-102.64 154.88-6.192 0-18.448-3.616-25.424-6.016-32.432-11.168-50.176-49.696-52.288-66.256 0 0-3.888-17.92-3.888-40.896s3.888-45.184 3.888-45.184c15.552-75.728 43.488-106.736 96.384-106.736 59.104-.032 83.968 58.528 83.968 110.208z'/%3E%3C/svg%3E",
		];
		return $panels;
	}

	/**
	 * Runs on the "wp_enqueue_scripts_hook".
	 */
	public static function on_wp_enqueue_scripts(): void {
		self::enqueue_styles();

	//	self::dequeue_scripts();
		self::enqueue_scripts();

		if ( self::maybe_remove_styles() ) {
			wp_deregister_style( 'wc-blocks-style' );
			wp_dequeue_style( 'wc-blocks-style' );
		}
	}

	/**
	 * Removes WooCommerce scripts.
	 */
	private static function dequeue_scripts(): void {
		// Nothing since 6.0
	}

	/**
	 * Enqueue styles.
	 */
	private static function enqueue_styles(): void {
		$small_bk = (int) apply_filters( 'woocommerce_style_smallscreen_breakpoint', '767' );

		if ( self::maybe_remove_styles() ) {
			\wp_enqueue_style(
				'wpex-woocommerce',
				\totaltheme_get_css_file( 'frontend/woocommerce/core' ),
				[],
				\WPEX_THEME_VERSION
			);
		}

		if ( \is_account_page() ) {
			\wp_enqueue_style(
				'wpex-woocommerce-shop-table-smallscreen',
				\totaltheme_get_css_file( 'frontend/woocommerce/shop-table-smallscreen' ),
				[],
				\WPEX_THEME_VERSION,
				"only screen and (max-width: {$small_bk}px)"
			);
	
			\wp_enqueue_style(
				'wpex-woocommerce-account-page',
				\totaltheme_get_css_file( 'frontend/woocommerce/account-page' ),
				[],
				\WPEX_THEME_VERSION
			);

			if ( ! function_exists( 'tgwc_account_navigation' ) ) {
				\wp_enqueue_style(
					'wpex-woocommerce-account-page-largescreen',
					\totaltheme_get_css_file( 'frontend/woocommerce/account-page-largescreen' ),
					[],
					\WPEX_THEME_VERSION,
					"only screen and (min-width: 960px)"
				);
			}
		}

		if ( is_cart() ) {
			$cart_small_bk = ($bk = \get_theme_mod( 'woo_cart_breakpoint' ) ) ? \sanitize_text_field( $bk ) : $small_bk;
			$cart_small_bk_safe = \absint( $cart_small_bk );

			\wp_enqueue_style(
				'wpex-woocommerce-shop-table-smallscreen',
				\totaltheme_get_css_file( 'frontend/woocommerce/shop-table-smallscreen' ),
				[],
				\WPEX_THEME_VERSION,
				"only screen and (max-width: {$cart_small_bk_safe}px)"
			);

			\wp_enqueue_style(
				'wpex-woocommerce-cart-smallscreen',
				\totaltheme_get_css_file( 'frontend/woocommerce/cart-smallscreen' ),
				[],
				\WPEX_THEME_VERSION,
				"only screen and (max-width: {$cart_small_bk_safe}px)"
			);
		}

	}

	/**
	 * Enqueue scripts.
	 */
	private static function enqueue_scripts(): void {
		\wp_enqueue_script(
			'wpex-wc-core',
			\totaltheme_get_js_file( 'frontend/woocommerce/core' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);

		$script_data = [
			'disable_scroll_to_notices' => ! \wp_validate_boolean( get_theme_mod( 'woo_scroll_to_notices_enable', false ) ),
		];

		if ( self::has_added_to_cart_notice() ) {
			$script_data['addedToCartNotice'] = \apply_filters(
				'wpex_woocommerce_added_to_cart_notice',
				\esc_html__( 'was added to your shopping cart.', 'total' )
			);
		}
		
		\wp_localize_script(
			'wpex-wc-core',
			'wpex_wc_params',
			$script_data
		);
	}

	/**
	 * Hooks into the "body_class" hook.
	 */
	public static function filter_body_class( $class ) {
		if ( \get_theme_mod( 'woo_product_responsive_tabs', false ) && \is_singular( 'product' ) ) {
			$class[] = 'woo-single-responsive-tabs';
		}
		if ( \get_theme_mod( 'woo_checkout_single_col', false ) && \is_checkout() ) {
			$class[] = 'wpex-fw-checkout';
		}
		if ( \is_account_page() ) {
			$nav_position = \wpex_has_sidebar() ? 'side' : 'top';
			$class[] = "woocommerce-account-nav-{$nav_position}";
		}
		if ( 'center' === get_theme_mod( 'woo_product_summary_text_align' ) && is_product() ) {
			$class[] = 'woocommerce-summary-text-center';
		}
		return $class;
	}

	/**
	 * Change onsale text.
	 */
	public static function filter_sale_flash( $text, $post, $_product ): string {
		$text = \wpex_get_translated_theme_mod( 'woo_sale_flash_text' );
		if ( ! $text ) {
			$text = \esc_html__( 'Sale', 'total' );
		}
		return '<span class="onsale">' . \esc_html( $text ) . '</span>';
	}

	/**
	 * Returns correct posts per page for the shop.
	 */
	public static function filter_loop_shop_per_page() {
		$posts_per_page = \get_theme_mod( 'woo_shop_posts_per_page' );
		if ( ! $posts_per_page ) {
			$posts_per_page = '12';
		}
		return $posts_per_page;
	}

	/**
	 * Change products per row for the main shop.
	 */
	public static function filter_loop_shop_columns() {
		$columns = \wpex_get_array_first_value( \get_theme_mod( 'woocommerce_shop_columns', '4' ) );
		if ( ! $columns ) {
			$columns = '4'; // always needs a fallback.
		}
		return $columns;
	}

	/**
	 * Tweaks pagination arguments.
	 */
	public static function filter_pagination_args( $args ) {
		$arrow_style = \get_theme_mod( 'pagination_arrow' ) ?: 'angle';
		$args['prev_text'] = \totaltheme_call_static( 'Theme_Icons', 'get_icon', $arrow_style . '-left' );
		$args['next_text'] = \totaltheme_call_static( 'Theme_Icons', 'get_icon', $arrow_style . '-right' );
		return $args;
	}

	/**
	 * Alter continue shoping URL to fix WPML/Translation issues.
	 */
	public static function filter_continue_shopping_redirect( $return_to ) {
		if ( $shop_id = \totaltheme_wc_get_page_id( 'shop' ) ) {
			$return_to = \get_permalink( $shop_id );
		}
		return $return_to;
	}

	/**
	 * Alter product tag cloud widget args.
	 */
	public static function filter_tag_cloud_widget_args( array $args ): array {
		$args['largest']  = '1';
		$args['smallest'] = '1';
		$args['unit']     = 'em';
		return $args;
	}

	/**
	 * Add typography options for the WooCommerce product title.
	 */
	public static function add_typography_settings( array $settings ): array {
		$settings['woo_entry_title'] = array(
			'label' => \esc_html__( 'WooCommerce Entry Title', 'total' ),
			'target' => '.woocommerce-loop-product__title, .woocommerce-loop-category__title',
			'margin' => true,
		);
		$settings['woo_product_title'] = array(
			'label' => \esc_html__( 'WooCommerce Product Title', 'total' ),
			'target' => '.woocommerce div.product .product_title',
			'margin' => true,
		);
		$settings['woo_upsells_related_title'] = array(
			'label' => \esc_html__( 'WooCommerce Section Heading', 'total' ),
			'target' => '.up-sells > h2, .related.products > h2, .woocommerce-tabs .panel > h2',
			'margin' => true,
		);
		return $settings;
	}

	/**
	 * Tweak comment form args.
	 */
	public static function filter_comment_form_args( array $args ): array {
		$args['title_reply'] = \esc_html__( 'Leave a customer review', 'total' );
		$args['title_reply_before'] = '<span id="reply-title" class="comment-reply-title wpex-heading wpex-text-lg">';
		return $args;
	}

	/**
	 * Alter orders per-page on account page.
	 */
	public static function filter_my_account_my_orders_query( array $args ): array {
		$args['limit'] = 20;
		return $args;
	}

	/**
	 * Check if we should remove the core WooCommerce styles.
	 *
	 * @todo rename filter.
	 */
	protected static function maybe_remove_styles(): bool {
		return (bool) \apply_filters( 'wpex_custom_woo_stylesheets', true );
	}

	/**
	 * Check if the added to cart notice is enabled.
	 */
	protected static function has_added_to_cart_notice(): bool {
		if ( ! \get_theme_mod( 'woo_show_entry_add_to_cart', true ) || ! \get_theme_mod( 'woo_added_to_cart_notice', true ) ) {
			return false;
		}
		if ( 'off-canvas' === \totaltheme_call_static( 'Integration\WooCommerce\Cart', 'style' ) && \wp_validate_boolean( get_theme_mod( 'woo_off_canvas_cart_auto_open', true ) ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Disable WooCommerce Styles.
	 */
	public static function filter_woocommerce_enqueue_styles( $styles ) {
		if ( \is_array( $styles ) ) {
			unset( $styles['woocommerce-general'] );
			unset( $styles['woocommerce-layout'] );
			unset( $styles['woocommerce-smallscreen'] );
		}
		return $styles;
	}

	/**
	 * Deregisters problematic Woo block styles that should be removed always.
	 */
	public static function on_enqueue_block_assets(): void {
		wp_deregister_style( 'wc-blocks-style-add-to-cart-form' );
		//wp_deregister_style( 'wc-block-editor' );
		//wp_deregister_style( 'wc-blocks-style' );
	}

	/**
	 * Alter WooCommerce shop notice to add an SVG for the close icon.
	 */
	public static function filter_woocommerce_demo_store( $notice ) {
		if ( $notice = \get_option( 'woocommerce_demo_store_notice' ) ) {
			$notice = '<p class="woocommerce-store-notice demo_store" data-notice-id="' . \esc_attr( \md5( $notice ) ) . '" style="display:none;">' . \wp_kses_post( $notice ) . '<a href="#" class="woocommerce-store-notice__dismiss-link wpex-text-xl"><span class="screen-reader-text">' . \esc_html__( 'Dismiss', 'woocommerce' ) . '</span>' . \totaltheme_get_icon( 'material-close', 'woocommerce-store-notice__dismiss-link--icon' ) . '</a></p>';
		}
		return $notice;
	}

	/**
	 * Check if the product title should be removed.
	 */
	private static function maybe_remove_single_title(): bool {
		$product_title = \get_theme_mod( 'woo_shop_single_title' );
		return ( $product_title && \str_contains( $product_title, '{{title}}' ) );
	}

	/**
	 * Filters the list of gutenberg styles to dequeue.
	 */
	public static function filter_gutenberg_styles_to_dequeue( $list ): array {
		global $wp_styles;
		if ( \is_a( $wp_styles, 'WP_Styles' ) && ! empty( $wp_styles->queue ) ) {
			foreach ( $wp_styles->queue as $style_handle ) {
				if ( \str_starts_with( $style_handle, 'wc-blocks-' ) ) {
					$list[] = $style_handle;
				}
			}
		}
		return $list;
	}

	/**
	 * Filter whether the page header is enabled or not.
	 */
	public static function filter_page_header_is_enabled( $check ): bool {
		if ( ! \get_theme_mod( 'woo_shop_title', true ) && \wpex_is_woo_shop() ) {
			$check = false;
		}
		if ( ! \get_theme_mod( 'woo_archive_has_page_header', true ) && \wpex_is_woo_tax() ) {
			$check = false;
		}
		return $check;
	}

	/**
	 * Disables the added to cart message.
	 */
	public static function disable_wc_add_to_cart_message_html( $message ) {
		\totaltheme_call_static( 'Integration\WooCommerce\Cart\Off_Canvas', 'open_on_load' );
		return '';
	}

	/**
	 * Override the WooCommerce added to cart notice to place the button
	 * where it should be.
	 */
	public static function modify_wc_add_to_cart_message_html( $message ) {
		if ( \is_string( $message ) && \str_contains( $message, '<a' ) ) {
			preg_match( '#<a.*?>.*?</a>#i', $message, $matches );
			if ( isset( $matches[0] ) && \str_contains( $matches[0], 'wc-forward' ) ) {
				$link = $matches[0];
				$message = \str_replace( $link, '', $message ) . ' ';
				$link = \str_replace( 'button wc-forward', 'wc-forward wpex-ml-5 wpex-inline-block', $link );
				$link = \str_replace( '</a>', ' &rarr;</a>', $link );
				$message = \trim( $message . $link );
			}
		}
		return $message;
	}

	/**
	 * Remove button class from forward buttons in notices because it looks terrible.
	 */
	public static function filter_woocommerce_add_message( $message ) {
		if ( \is_string( $message ) && \str_contains( $message, 'button wc-forward' ) ) {
			$message = \str_replace( 'button wc-forward', 'wc-forward', $message );
		}
		return $message;
	}

}
