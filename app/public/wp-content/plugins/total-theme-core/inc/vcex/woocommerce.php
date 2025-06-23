<?php declare(strict_types=1);

namespace TotalThemeCore\Vcex;

\defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Methods.
 */
final class WooCommerce {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns array of cart icon choices.
	 */
	public static function get_cart_icon_choices( $combine = false ): array {
		$choices = [];
		if ( \is_callable( '\TotalTheme\Integration\WooCommerce\Cart::icon_choices' ) ) {
			$choices = (array) \TotalTheme\Integration\WooCommerce\Cart::icon_choices();
			if ( $combine ) {
				// set keys to values
				$choices = \array_combine( $choices, $choices );
			}
		}
		return $choices;
	}

	/**
	 * Returns the cart badge element.
	 */
	public static function get_cart_badge( $show_count = false ): string {
		$badge = '';
		$type = $show_count ? 'count' : 'dot';
		if ( \vcex_is_frontend_edit_mode() ) {
			$badge .= '<span class="wpex-cart-badge wpex-cart-badge--' . \esc_attr( $type ) . ' wpex-cart-badge--visible">';
				if ( 'count' === $type ) {
					$badge .= '1';
				}
			$badge .= '</span>';
		} else {
			\wp_enqueue_script( 'wc-cart-fragments' );
			$badge .= '<span class="wpex-cart-badge wpex-cart-badge--' . \esc_attr( $type ) . '"></span>';
		}
		return $badge;
	}

}
