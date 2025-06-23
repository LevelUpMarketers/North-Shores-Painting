<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Header_Cart_Icon {

	/**
	 * Stores the cart display type.
	 */
	private static $display;

	/**
	 * Register the shortcode and add filters.
	 */
	public function __construct() {
		\add_shortcode( 'header_cart_icon', [ self::class, 'output' ] );

		if ( self::maybe_add_cart_fragments() ) {
			\add_filter( 'woocommerce_add_to_cart_fragments', [ self::class, 'cart_fragments' ] );
		}
	}

	/**
	 * Shortcode output.
	 */
	public static function output( $atts = [] ) {
		if ( ! \class_exists( '\WooCommerce' ) ) {
			return;
		}

		$display = self::display();

		if ( ! $display || 'disabled' === $display ) {
			return;
		}

		$atts = \shortcode_atts( [
			'class'        => '',
			'visibility'   => '',
			'label'        => '',
			'label_margin' => '',
			'aria_label'   => '',
			'font_size'    => '',
		], $atts, 'header_cart_icon' );

		$class = '';

		if ( ! empty( $atts['class'] ) && \is_string( $atts['class'] ) ) {
			$class .= ' || ' . \str_replace( '.', '', \trim( $atts['class'] ) );
		}

		if ( ! empty( $atts['visibility'] )
			&& \function_exists( '\totaltheme_get_visibility_class' )
			&& $visibility_class = \totaltheme_get_visibility_class( $atts['visibility'] )
		) {
			$class .= " {$visibility_class}";
		}

		$icon = '';

		$html = '<span class="wpex-header-cart-icon wpex-inline-block wpex-relative' . esc_attr( $class ) . '">';
			$action = self::action();
			if ( 'drop_down' === $action || 'overlay' === $action || 'off-canvas' === $action ) {
				$html .= self::get_button( $atts, $action );
			} else {
				$html .= self::get_link( $atts );
			}
		$html .= '</span>';

		return $html;
	}

	/**
	 * Get button.
	 */
	private static function get_button( $atts = [], $action = '' ) {
		$button = '';

		$aria_map = [
			'overlay'    => 'wpex-cart-overlay',
			'drop_down'  => 'current-shop-items-dropdown',
			'off-canvas' => 'wpex-off-canvas-cart',
		];

		$aria_controls = $aria_map[ $action ] ?? '';

		if ( ! empty( $atts['aria_label'] ) ) {
			$aria_label = \sanitize_text_field( $atts['aria_label'] );
		} elseif ( \function_exists( '\wpex_get_aria_label' ) ) {
			$aria_label = \wpex_get_aria_label( 'cart_open' );
		}

		$button .= '<button type="button" class="wpex-header-cart-icon__button wpex-unstyled-button wpex-hover-link-color wpex-flex wpex-items-center" aria-expanded="false" aria-controls="' . \esc_attr( $aria_controls ) . '" aria-label="' . \esc_attr( $aria_label ) . '"';
			if ( 'off-canvas' === $action ) {
				$button .= ' data-wpex-toggle="off-canvas" data-role="button"';
			}
		$button .= '>';
			$button .= self::get_label( $atts );
			$button .= self::get_icon();
			$button .= self::get_extras();
		$button .= '</button>';

		return $button;
	}

	/**
	 * Get link.
	 */
	private static function get_link( $atts = [] ) {
		$link = '';
		$url  = '#';

		if ( 'custom-link' === self::action() ) {
			if ( $custom_link = \get_theme_mod( 'woo_menu_icon_custom_link' ) ) {
				if ( \str_starts_with( $custom_link, '/' ) ) {
					$custom_link = \home_url( $custom_link );
				}
				$url = \esc_url( $custom_link );
			}
		} elseif ( \function_exists( '\wc_get_cart_url' ) && $cart_url = \wc_get_cart_url() ) {
			$url = $cart_url;
		}

		$link .= '<a href="' . \esc_url( $url ) . '" class="wpex-header-cart-icon__link wpex-text-current wpex-no-underline wpex-hover-link-color wpex-flex wpex-items-center">';
			if ( $label = self::get_label( $atts ) ) {
				$link .= $label;
			} else {
				$link .= '<span class="screen-reader-text">' . \esc_html__( 'go to cart', 'total-theme-core' ) . '</span>';
			}
			$link .= self::get_icon();
			$link .= self::get_extras();
		$link .= '</a>';

		return $link;
	}

	/**
	 * Get label.
	 */
	private static function get_label( $atts ) {
		if ( empty( $atts['label'] ) ) {
			return;
		}
		$style = '';
		if ( ! empty( $atts['label_margin'] ) ) {
			$margin_side = is_rtl() ? 'left' : 'right';
			$style = ' style="margin-' . $margin_side .':' . \esc_attr( $atts['label_margin'] ) . ';"';
		}
		return '<span class="wpex-header-cart-icon__label wpex-mr-5"' . $style . '>' . \esc_html( $atts['label'] ) . '</span>';
	}

	/**
	 * Get icon.
	 */
	private static function get_icon(): string {
		$icon = '';
		if ( \function_exists( '\totaltheme_get_icon' ) ) {
			$cart_icon = ( $get_icon = get_theme_mod( 'woo_menu_icon_class' ) ) ? \sanitize_text_field( (string) $get_icon ): 'shopping-cart';
			$icon .= \totaltheme_get_icon( $cart_icon, 'wpex-flex' );
		}
		$icon = (string) \apply_filters( 'wpex_header_search_icon_shortcode_icon_html', $icon );
		return '<span class="wpex-header-cart-icon__icon wpex-inline-flex wpex-items-center">' . $icon . '</span>';
	}

	/**
	 * Get extras.
	 */
	private static function get_extras() {
		$display = self::display();
		
		if ( ! in_array( $display, [ 'icon_total', 'icon_dot', 'icon_count' ], true ) ) {
			return;
		}

		wp_enqueue_script( 'wc-cart-fragments' );

		$class = 'wpex-header-cart-icon__fragments';
		
		if ( 'icon_dot' !== $display ) {
			$class .= ' wpex-ml-5 wpex-inline-flex';
		}

		$html = '<span class="' . esc_attr( $class ) . '">';
			switch ( $display ) {
				case 'icon_dot':
					$html .= self::get_cart_dot();
					break;
				case 'icon_total':
					$html .= self::get_cart_price();
					break;
				case 'icon_count':
					$html .= self::get_cart_count();
					break;
			}
		$html .= '</span>';

		return $html;
	}

	/**
	 * Hook into the WooCommerce woocommerce_add_to_cart_fragments filter
	 * so that the cart count is refreshed whenever items are added or removed from the cart.
	 */
	public static function cart_fragments( $fragments ) {
		$fragments['.wpex-header-cart-icon__fragments'] = self::get_extras();
		return $fragments;
	}

	/**
	 * Checks if the icon should be refreshed when adding/removing items from the cart.
	 */
	private static function maybe_add_cart_fragments(): bool {
		return in_array( self::display(), [ 'icon_total', 'icon_dot', 'icon_count' ], true );
	}

	/**
	 * Return cart dot.
	 */
	private static function get_cart_dot(): string {
		$class = 'wpex-header-cart-icon__dot';
		if ( function_exists( 'WC' ) && 0 !== WC()->cart->cart_contents_count ) {
			$class .= ' wpex-header-cart-icon__dot--visible';
		}
		return '<span class="' . esc_attr( $class ) . '"></span>';
	}

	/**
	 * Return current cart count.
	 */
	private static function get_cart_count() {
		if ( ! function_exists( 'WC' ) || 0 === WC()->cart->cart_contents_count ) {
			return;
		}
		$class = 'wpex-header-cart-icon__count';
		if ( \get_theme_mod( 'wpex_woo_menu_icon_bubble', true ) ) {
			$class .= ' wpex-header-cart-icon__count--bubble wpex-bg-accent wpex-rounded-full wpex-flex wpex-items-center wpex-justify-center';
		}
		$html = '<span class="' . \esc_attr( $class ) . '" aria-hidden="true">';
			$html .= '<span class="wpex-header-cart-icon__count-number">' . WC()->cart->cart_contents_count ?: '0';
		$html .= '</span></span>';
		return $html;
	}

	/**
	 * Return current cart price.
	 */
	private static function get_cart_price() {
		if ( ! \function_exists( '\WC' ) || empty( \WC()->cart->get_cart_total() ) || 0 === \WC()->cart->cart_contents_count ) {
			return;
		}
		$price = WC()->cart->get_cart_total();
		return '<span class="wpex-header-cart-icon__price">' . \wp_kses_post( $price ) .'</span>';
	}

	/**
	 * Get display type.
	 */
	private static function display(): string {
		if ( \function_exists( '\totaltheme_call_static' ) ) {
			return (string) \totaltheme_call_static( 'Integration\WooCommerce\Cart', 'header_display' );
		} else {
			return 'icon_count';
		}
	}

	/**
	 * Get action type.
	 */
	private static function action(): string {
		if ( \function_exists( '\totaltheme_call_static' ) ) {
			return (string) \totaltheme_call_static( 'Integration\WooCommerce\Cart', 'style' );
		} else {
			return 'drop_down';
		}
	}

}
