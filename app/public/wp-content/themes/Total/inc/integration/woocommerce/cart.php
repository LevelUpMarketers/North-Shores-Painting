<?php

namespace TotalTheme\Integration\WooCommerce;

use TotalThemeCore\Shortcodes\Shortcode_Cart_Link;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Cart.
 */
final class Cart {

	/**
	 * Holds cart style.
	 */
	private static $style = null;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		// Get style early so we can load the off-canvas class if needed
		\add_action( 'wp', [ self::class, 'style' ] );

		// Filters
		\add_filter( 'woocommerce_add_to_cart_fragments', [ self::class, '_filter_woocommerce_add_to_cart_fragments' ] );
		\add_filter( 'wp_nav_menu_items', [ self::class, '_filter_wp_nav_menu_items' ], 10, 2 );

		// Actions.
		\add_action( 'wp_enqueue_scripts', [ self::class, '_on_wp_enqueue_scripts' ] );
		\add_action( 'wpex_hook_header_inner', [ self::class, '_on_wpex_hook_header_inner' ], 40 );
		\add_action( 'wpex_outer_wrap_after', [ self::class, '_on_wpex_outer_wrap_after' ] );
	}

	/**
	 * Check if the cart function is anabled.
	 */
	public static function is_enabled(): bool {
		// Move old disabled style to new on/off switch.
		if ( 'disabled' === \get_theme_mod( 'woo_menu_icon_display' ) ) {
			\set_theme_mod( 'woo_menu_cart_enable', false );
			\remove_theme_mod( 'woo_menu_icon_display' );
			$check = false;
		} else {
			$check = (bool) \get_theme_mod( 'woo_menu_cart_enable', true );
		}
		if ( ! $check && \totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
			$check = \totaltheme_call_static( 'Header\Flex\Aside', 'has_cart_icon' );
		}
		return $check;
	}

	/**
	 * Returns array of cart style choices.
	 */
	public static function style_choices(): array {
		return [
			'off-canvas'  => \esc_html__( 'Off Canvas', 'total' ),
			'drop_down'   => \esc_html__( 'Dropdown', 'total' ),
			'overlay'     => \esc_html__( 'Overlay', 'total' ),
			'store'       => \esc_html__( 'Go To Cart', 'total' ),
			'custom-link' => \esc_html__( 'Custom Link', 'total' ),
		];
	}

	/**
	 * Returns array of icon choices.
	 */
	public static function icon_choices(): array {
		return apply_filters( 'wpex_woocommerce_cart_icon_choices', [
			'shopping-cart',
			'shopping-cart-alt',
			'shopping-bag',
			'shopping-bag-alt',
			'shopping-basket',
			'shopping-basket-alt',
			// Material
			'material-shopping-cart',
			'material-shopping-cart-sharp',
			'material-shopping-bag',
			'material-shopping-bag-sharp',
			// Ionicons
			'ionicons-cart',
			'ionicons-cart-outline',
			'ionicons-cart-sharp',
			// Bootstrap.
			'bootstrap-bag',
			'bootstrap-bag-fill',
			'bootstrap-basket',
			'bootstrap-basket-fill',
			'bootstrap-basket2',
			'bootstrap-basket2-fill',
			'bootstrap-basket3',
			'bootstrap-basket3-fill',
			'bootstrap-cart',
			'bootstrap-cart-fill',
			'bootstrap-cart2',
			'bootstrap-cart3',
			'bootstrap-cart4',
		] );
	}

	/**
	 * Register JS.
	 */
	public static function register_js(): void {
		switch ( self::style() ) {
			case 'drop_down':
				\wp_register_script(
					'wpex-wc-cart-dropdown',
					\totaltheme_get_js_file( 'frontend/woocommerce/cart-dropdown' ),
					[],
					\WPEX_THEME_VERSION,
					[ 'strategy' => 'defer' ]
				);
				break;
			case 'overlay':
				\wp_register_script(
					'wpex-wc-cart-overlay',
					\totaltheme_get_js_file( 'frontend/woocommerce/cart-overlay' ),
					[],
					\WPEX_THEME_VERSION,
					[ 'strategy' => 'defer' ]
				);
				break;
		}
	}

	/**
	 * Returns the selected cart style.
	 */
	public static function style(): string {
		if ( null !== self::$style ) {
			return self::$style;
		}

		$style = \get_theme_mod( 'woo_menu_icon_style', 'off-canvas' );

		if ( ! \in_array( $style, [ 'custom-link', 'store' ], true )
			&& \apply_filters( 'woocommerce_widget_cart_is_hidden', \is_cart() || \is_checkout() )
		) {
			$style = 'store'; // store is technically the cart.
		}

		$style = (string) \apply_filters( 'wpex_menu_cart_style', $style );

		if ( ! $style || ! \array_key_exists( $style, self::style_choices() ) ) {
			$style = 'off-canvas';
		}

		self::$style = $style;

		// This is a good place to initialize the off canvas class.
		if ( 'off-canvas' === self::$style || \get_theme_mod( 'woo_off_canvas_cart_enable', false ) ) {
			\totaltheme_init_class( __CLASS__ . '\Off_Canvas' );
		}

		return self::$style;
	}

	/**
	 * Registers cart fragments.
	 */
	public static function _filter_woocommerce_add_to_cart_fragments( $fragments ): array {
		if ( self::is_enabled() ) {
			wp_enqueue_script( 'wc-cart-fragments' );
			$fragments['.wcmenucart'] = self::get_header_menu_item_link();
			$fragments['.wpex-cart-count'] = self::get_count_bubble();
		}
		$fragments['.wpex-cart-badge--dot'] = self::get_cart_badge_dot();
		$fragments['.wpex-cart-badge--count'] = self::get_cart_badge_count();
		return $fragments;
	}

	/**
	 * Returns cart badge dot fragment.
	 */
	public static function get_cart_badge_dot() {
		if ( $cart = self::get_woo_cart_instance() ) {
			$badge = '<span class="wpex-cart-badge wpex-cart-badge--dot';
				if ( \method_exists( $cart, 'get_cart_contents_count' ) && $cart->get_cart_contents_count() > 0 ) {
					$badge .= ' wpex-cart-badge--visible';
				}
			$badge .= '"></span>';
			return $badge;
		}
	}

	/**
	 * Returns cart badge dot fragment.
	 */
	public static function get_cart_badge_count() {
		if ( $cart = self::get_woo_cart_instance() ) {
			$count = \method_exists( $cart, 'get_cart_contents_count' ) ? $cart->get_cart_contents_count() : 0;
			$badge = '<span class="wpex-cart-badge wpex-cart-badge--count';
				if ( $count > 0 ) {
					$badge .= ' wpex-cart-badge--visible';
				}
			$badge .= '">' . (int) esc_html( $count ) . '</span>';
			return $badge;
		}
	}

	/**
	 * Returns cart count bubble.
	 */
	public static function get_count_bubble(): ?string {
		if ( ! \function_exists( '\WC' ) || empty( \WC()->cart ) ) {
			return null;
		}
		$count = absint( WC()->cart->cart_contents_count ?? 0 );
		$classes = [
			'wpex-cart-count',
			'wpex-absolute',
			'wpex-text-center',
			'wpex-font-semibold',
			'wpex-rounded',
			'wpex-text-white',
		];
		if ( $count ) {
			$classes[] = 'wpex-block wpex-bg-accent';
		} else {
			$classes[] = 'wpex-hidden wpex-bg-gray-400';
		}
		return '<span class="' . \esc_attr( \implode( ' ', $classes ) ) . '">' . \esc_html( $count ) . '</span>';
	}

	/**
	 * Returns the HTML for the header menu item.
	 */
	public static function get_header_menu_item() {
		$style = self::style();

		// Define classes to add to li element
		$li_class = [
			'woo-menu-icon',
			'menu-item',
			'wpex-menu-extra',
			"wcmenucart-toggle-{$style}",
			'toggle-cart-widget',
		];

		// Ubermenu integration
		if ( \class_exists( '\UberMenu' ) && \apply_filters( 'wpex_add_search_toggle_ubermenu_classes', true ) ) {
			$li_class[] = 'ubermenu-item-level-0 ubermenu-item'; // @todo rename or remove filter.
		}

		// Max Mega menu integration
		if ( \function_exists( '\max_mega_menu_is_enabled' ) && \max_mega_menu_is_enabled( 'main_menu' ) ) {
			$li_class[] = 'mega-menu-item';
		}

		$html = '<li class="' . esc_attr( implode( ' ', $li_class ) ) . '">';
			$html .= self::get_header_menu_item_link();

			// Insert dropdown if required inline
			if ( 'drop_down' === $style && \wpex_maybe_add_header_drop_widget_inline( 'cart' ) ) {
				\wp_enqueue_script( 'wpex-wc-cart-dropdown' );
				\ob_start();
				\get_template_part( 'partials/cart/cart-dropdown' );
				$html .= \ob_get_clean();
			}
		$html .= '</li>';

		return $html;
	}

	/**
	 * Returns the display type for the header icon.
	 */
	public static function header_display(): string {
		return ( $display = \get_theme_mod( 'woo_menu_icon_display' ) ) ? \sanitize_text_field( (string) $display ) : 'icon_count';
	}

	/**
	 * Returns the HTML for the header menu item link.
	 */
	public static function get_header_menu_item_link() {
		if ( ! \function_exists( 'WC' ) || empty( WC()->cart ) ) {
			return;
		}

		global $woocommerce;

		$count        = absint( WC()->cart->cart_contents_count ?? 0 );
		$style        = self::style();
		$display      = self::header_display();
		$header_style = \totaltheme_call_static( 'Header\Core', 'style' );

		// Link classes
		$a_classes = 'wcmenucart';
		if ( $display ) {
			$a_classes .= " wcmenucart-{$display}";
		}
		$a_classes .= " wcmenucart-items-{$count}";

		if ( $count ) {
			$a_classes .= ' wpex-has-items';
		}

		// Ubermenu integration
		if ( class_exists( 'UberMenu' ) && \apply_filters( 'wpex_add_search_toggle_ubermenu_classes', true ) ) {
			$a_classes .= ' ubermenu-target ubermenu-item-layout-default ubermenu-item-layout-text_only';
		}

		// Max Mega Menu integration
		if ( \function_exists( 'max_mega_menu_is_enabled' ) && max_mega_menu_is_enabled( 'main_menu' ) ) {
			$a_classes  .= ' mega-menu-link';
		}

		// Define cart icon link URL
		if ( 'custom-link' === $style && $custom_link = \get_theme_mod( 'woo_menu_icon_custom_link' ) ) {
			if ( \str_starts_with( $custom_link, '/' ) ) {
				$custom_link = \home_url( $custom_link );
			}
			$url = \esc_url( $custom_link );
		} elseif ( \function_exists( 'wc_get_cart_url' ) ) {
			$url = \wc_get_cart_url();
		} else {
			$url = '#';
		}

		// Get heder icon display type extras
		switch ( $display ) {
			case 'icon_total':
				if ( \is_callable( [ WC()->cart, 'get_cart_total'  ] ) ) {
					$cart_extra = WC()->cart->get_cart_total();
					$cart_extra = \str_replace( 'amount', 'amount wcmenucart-details', $cart_extra );
				}
				break;
			case 'icon_count':
				$extra_class = 'wcmenucart-details count';
				if ( $count ) {
					$extra_class .= ' wpex-has-items';
				}
				if ( \get_theme_mod( 'wpex_woo_menu_icon_bubble', true ) ) {
					$extra_class .= ' t-bubble';
				} elseif ( 'six' === $header_style ) {
					$count = '(' . $count . ')'; // @todo this creates inconsistent design across header styles.
				}
				$cart_extra = '<span class="' . \esc_attr( $extra_class ) . '">' . \esc_html( $count ) . '</span>';
				break;
			case 'icon_dot':
				$cart_extra = ''; // We add it inside the icon.
				$extra_class = 'wcmenucart-dot';
				if ( $count ) {
					$extra_class .= ' wcmenucart-dot--visible';
				}
				break;
			default:
				$cart_extra = '';
				break;
		}

		$cart_icon_name = (string) \apply_filters( 'wpex_menu_cart_icon', \get_theme_mod( 'woo_menu_icon_class' ) ?: 'shopping-cart' );

		$cart_text = \get_theme_mod( 'woo_menu_cart_text' ) ?: \esc_html__( 'Cart', 'total' );

		$cart_icon_html = '<span class="wcmenucart-icon wpex-relative">';
			$cart_icon_class = ( 'six' === $header_style ) ? 'wpex-icon--w' : '';
			$cart_icon_html .= \totaltheme_get_icon( $cart_icon_name, $cart_icon_class );
			if ( 'icon_dot' === $display ) {
				$cart_icon_html .= '<span class="' . \esc_attr( $extra_class ) . '"></span>';
			}
		$cart_icon_html .= '</span>';
		
		$cart_icon_html .= '<span class="wcmenucart-text wpex-ml-10">' . \esc_html( $cart_text ) . '</span>';
		$cart_icon_html = \apply_filters( 'wpex_menu_cart_icon_html', $cart_icon_html, $cart_icon_name );

		// Filters the header menu cart link class
		$a_classes = \apply_filters( 'wpex_header_menu_cart_item_link_class', $a_classes );

		// Link attributes.
		$a_attributes = [
			'href'  => $url ?: '#',
			'class' => $a_classes,
		];

		if ( 'drop_down' === $style || 'overlay' === $style || 'off-canvas' === $style ) {
			$a_attributes['role'] = 'button';
			$a_attributes['aria-expanded'] = 'false';
			$a_attributes['aria-label'] = \esc_attr( \wpex_get_aria_label( 'cart_open' ) );
			switch ( $style ) {
				case 'dropdown':
					$a_attributes['aria-controls'] = 'current-shop-items-dropdown';
					break;
				case 'overlay':
					$a_attributes['aria-controls'] = 'wpex-cart-overlay';
					break;
				case 'off-canvas':
					$a_attributes['data-wpex-toggle'] = 'off-canvas';
					$a_attributes['aria-controls'] = 'wpex-off-canvas-cart';
					break;
			}
		}

		// Create link HTML.
		$html = '<a ' . \wpex_parse_attrs( $a_attributes ) . '>';
			$html .= '<span class="link-inner">';
				$count_class = 'wcmenucart-count wpex-relative';
				if ( 'icon_dot' === $display ) {
					$count_class .=  ' wpex-flex';
				}
				$html .= '<span class="' . esc_attr( $count_class ) . '">' . $cart_icon_html . $cart_extra . '</span>';
			$html .= '</span>';
		$html .= '</a>';

		return $html;
	}

	/**
	 * Check if the cart icon should be automatically inserted into the menu.
	 */
	public static function auto_insert_menu_icon( $menu_location ): bool {
		$check = 'main_menu' === $menu_location && self::is_enabled() && \get_theme_mod( 'woo_menu_cart_enable', true );
		return (bool) \apply_filters( 'totaltheme/integration/woocommerce/cart/auto_insert_menu_icon', $check, $menu_location );
	}

	/**
	 * Adds the cart item to the header menu.
	 */
	public static function _filter_wp_nav_menu_items( $items, $args ) {
		if ( ! self::auto_insert_menu_icon( $args->theme_location ) ) {
			return $items;
		}
	
		$items .= self::get_header_menu_item();

		// Insert a cart link to the mobile menu as well
		if ( \get_theme_mod( 'has_woo_mobile_menu_cart_link', true )
			&& \totaltheme_call_static( 'TotalTheme\Mobile\Menu', 'is_enabled' )
			&& \function_exists( '\wc_get_cart_url' )
		) {
			$items .= self::get_mobile_menu_item();
		}

		return $items;
	}

	/**
	 * Mobile Menu item.
	 */
	public static function get_mobile_menu_item(): ?string {
		if ( ! $cart_url = \wc_get_cart_url() ) {
			return null;
		}

		if ( \is_callable( 'TotalThemeCore\Shortcodes\Shortcode_Cart_Link::output' ) ) {
			$cart_link_args = [
				'icon'  => null,
				'items' => [ 'icon', 'count', 'total' ],
				'link'  => 'false', // add our own link around the whole element to prevent block issues.
			];

			$cart_link_args = \apply_filters( 'wpex_woo_mobile_menu_cart_link_args', $cart_link_args );

			$html = Shortcode_Cart_Link::output( $cart_link_args );

			if ( $html ) {
				$html = '<a href="' . \esc_url( $cart_url ) . '"><span class="link-inner">' . $html . '</span></a>';
			}

		} else {
			$html = '<a href="' . \esc_url( $cart_url ) . '"><span class="link-inner">' . \esc_html( \get_theme_mod( 'woo_menu_cart_text', \esc_html__( 'Cart', 'total' ) ) ) . '</span></a>';
		}

		$html = (string) \apply_filters( 'wpex_woo_mobile_menu_cart_link', $html );

		return $html ? '<li class="menu-item wpex-mm-menu-item">' . $html . '</li>' : null;
	}

	/**
	 * Hooks into wp_enqueue_scripts.
	 */
	public static function _on_wp_enqueue_scripts(): void {
		self::register_js();
	}

	/**
	 * Hooks into wpex_hook_header_inner.
	 */
	public static function _on_wpex_hook_header_inner(): void {
		if ( 'drop_down' === self::style() && ! \wpex_maybe_add_header_drop_widget_inline( 'cart' ) ) {
			\wp_enqueue_script( 'wpex-wc-cart-dropdown' );
			\get_template_part( 'partials/cart/cart-dropdown' );
		}
	}

	/**
	 * Hooks into wpex_outer_wrap_after.
	 */
	public static function _on_wpex_outer_wrap_after(): void {
		switch ( self::style() ) {
			case 'overlay':
				\wp_enqueue_script( 'wpex-wc-cart-overlay' );
				\get_template_part( 'partials/cart/cart-overlay' );
				break;
		}
	}

	/**
	 * Helper returns Woo cart instance.
	 */
	protected static function get_woo_cart_instance() {
		if ( \function_exists( 'WC' ) && ! empty( WC()->cart ) ) {
			$cart = \WC()->cart;
			if ( $cart instanceof \WC_Cart ) {
				return $cart;
			}
		}
		return null;
	}
	
}
