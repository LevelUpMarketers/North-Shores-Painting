<?php

namespace TotalThemeCore\Shortcodes;

\defined( 'ABSPATH' ) || exit;

final class Shortcode_Cart_Link {

	/**
	 * Register the shortcode and add filters.
	 */
	public function __construct() {
		if ( ! \shortcode_exists( 'cart_link' ) ) {
			\add_shortcode( 'cart_link', [ self::class, 'output' ] );
		} else {
			\add_shortcode( 'wpex_cart_link', [ self::class, 'output' ] );
		}

		\add_filter( 'woocommerce_add_to_cart_fragments', [ self::class, 'cart_fragments' ] );
	}

	/**
	 * Shortcode output.
	 */
	public static function output( $atts = [] ) {
		if ( ! \class_exists( 'WooCommerce' ) ) {
			return;
		}
		$atts = \shortcode_atts( [
			'items'       => [ 'icon', 'count', 'total' ],
			'link'        => true,
			'icon'        => '',
			'font_size'   => '',
			'font_family' => '',
			'font_color'  => '',
			'icon_margin' => '',
			'icon_size'   => '',
			'el_class'    => '',
		], $atts, 'cart_link' );

		if ( \is_string( $atts['items'] ) ) {
			$atts['items'] = \explode( ',', $atts['items'] );
		}

		if ( empty( $atts['items'] ) || ! \is_array( $atts['items'] ) ) {
			return;
		}

		if ( ! empty( $atts['el_class'] ) ) {
			$el_class = ' ' . \str_replace( '.', '', \trim( $atts['el_class'] ) );
		} else {
			$el_class = '';
		}

		$html = '<span class="wpex-cart-link wpex-inline-block' . \esc_attr( $el_class ) . '">';

			if ( self::has_link( $atts ) ) {
				$link_class = 'wpex-no-underline';
				if ( ! empty( $atts['font_color'] ) && 'currentColor' === $atts['font_color'] ) {
					$link_class .= ' wpex-text-current wpex-hover-text-current';
				}
				$html .= '<a class="' . \esc_attr( $link_class ) . '" href="' . \esc_url( \wc_get_cart_url() ) . '">';
			}

			$items_class = 'wpex-cart-link__items wpex-flex wpex-items-center';

			$items_style = self::get_inline_style( $atts );

			$html .= '<span class="' . \esc_attr( $items_class ) . '"' . $items_style . '>';

				if ( \in_array( 'icon', $atts['items'], true ) ) {

					$icon_style = '';

					if ( ! empty( $atts['icon_size'] ) ) {
						$icon_size = \sanitize_text_field( $atts['icon_size'] );
						if ( \is_numeric( $icon_size ) ) {
							$icon_size = "{$icon_size}px";
						}
						$icon_style .= "font-size:{$icon_size};";
					}

					if ( ! empty( $atts['icon_margin'] ) ) {
						$icon_margin = \sanitize_text_field( $atts['icon_margin'] );
						if ( $icon_margin ) {
							if ( \is_numeric( $icon_margin ) ) {
								$icon_margin = "{$icon_margin}px";
							}
							$icon_style .= "margin-inline-end:{$icon_margin};";
						}
					}

					if ( $icon_style ) {
						$icon_style = 'style="' . \esc_attr( $icon_style ) . '"';
					}

					if ( \function_exists( 'totaltheme_get_icon' ) ) {
						$html .= '<span class="wpex-cart-link__icon wpex-mr-10"' . $icon_style . '>';
							// @todo update to get icon from Integration\WooCommerce\Cart::get_icon_name()
							$icon = ! empty( $atts['icon'] ) ? $atts['icon'] : \get_theme_mod( 'woo_menu_icon_class', 'shopping-cart' );
							$html .= \apply_filters( 'wpex_cart_link_shortcode_icon', \totaltheme_get_icon( $icon ), $atts );
						$html .= '</span>';
					}

				}

				if ( \in_array( 'count', $atts['items'] ) ) {
					\wp_enqueue_script( 'wc-cart-fragments' );
					$html .= self::get_cart_count();
				}

				if ( \in_array( 'count', $atts['items'] ) && \in_array( 'total', $atts['items'] ) ) {
					$html .= self::get_dash();
				}

				if ( \in_array( 'total', $atts['items'] ) ) {
					\wp_enqueue_script( 'wc-cart-fragments' );
					$html .= self::get_cart_price();
				}

			$html .= '</span>';

			if ( self::has_link( $atts ) ) {
				$html .= '</a>';
			}

		$html .= '</span>';

		return $html;
	}

	/**
	 * Check if we should link to the cart.
	 */
	public static function has_link( $atts ) {
		if ( ! \array_key_exists( 'link',  $atts ) ) {
			return true;
		}
		return \wp_validate_boolean( $atts['link'] );
	}

	/**
	 * Get inline style.
	 */
	public static function get_inline_style( $atts ) {
		if ( ! empty( $atts['font_family'] ) && \function_exists( 'wpex_enqueue_font' ) ) {
			\wpex_enqueue_font( $atts['font_family'] );
		}

		if ( \function_exists( 'vcex_inline_style' ) ) {
			return \vcex_inline_style( [
				'font_family' => $atts['font_family'] ?? null,
				'font_size'   => $atts['font_size'] ?? null,
				'color'       => $atts['font_color'] ?? null,
			] );
		}
	}

	/**
	 * Hook into the WooCommerce woocommerce_add_to_cart_fragments filter
	 * so that the cart count is refreshed whenever items are added or removed from the cart.
	 */
	public static function cart_fragments( $fragments ) {
		$fragments['.wpex-cart-link__count'] = self::get_cart_count();
		$fragments['.wpex-cart-link__dash']  = self::get_dash();
		$fragments['.wpex-cart-link__price'] = self::get_cart_price();
		return $fragments;
	}

	/**
	 * Return items dash.
	 */
	public static function get_dash() {
		if ( ! \function_exists( '\WC' )
			|| empty( \WC()->cart )
			|| empty( \WC()->cart->cart_contents_count )
		) {
			return '<span class="wpex-cart-link__dash wpex-mx-5 wpex-hidden">&#45;</span>';
		}
		return '<span class="wpex-cart-link__dash wpex-mx-5">&#45;</span>';
	}

	/**
	 * Return current cart items count.
	 */
	public static function get_cart_count() {
		if ( ! \function_exists( '\WC' ) ) {
			return;
		}
	
		$count = \absint( \WC()->cart->cart_contents_count ?? 0 );

		if ( 1 === $count ) {
			$text = \apply_filters( 'wpex_cart_link_shortcode_item_text', \esc_html__( 'Item', 'total-theme-core' ) );
		} else {
			$text = \apply_filters( 'wpex_cart_link_shortcode_items_text', \esc_html__( 'Items', 'total-theme-core' ) );
		}

		$html = '<span class="wpex-cart-link__count">';
			$html .= \esc_html( $count );
			if ( $text ) {
				$html .= ' ' . \esc_html( $text );
			}
		$html .= '</span>';
		return $html;
	}

	/**
	 * Return current cart price.
	 */
	public static function get_cart_price() {
		if ( ! \function_exists( '\WC' ) || empty( \WC()->cart ) ) {
			return;
		}
		if ( isset( \WC()->cart->cart_contents_count ) && 0 === \WC()->cart->cart_contents_count ) {
			return '<span class="wpex-cart-link__price wpex-hidden"></span>';
		}
		if ( \is_callable( [ \WC()->cart, 'get_cart_total'  ] ) ) {
			return '<span class="wpex-cart-link__price">' . \wp_kses_post( \WC()->cart->get_cart_total() ) .'</span>';
		}
	}

}
