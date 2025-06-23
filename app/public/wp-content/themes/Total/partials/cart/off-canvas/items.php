<?php

/**
 * Off Canvas Cart Items.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'WC' ) ) {
	return;
}

wp_enqueue_script( 'wc-cart-fragments' );

?>

<div class="wpex-mini-cart__items">

	<?php if ( ! WC()->cart->is_empty() ) : ?>

		<div class="wpex-mini-cart__items-list wpex-grid wpex-gap-25">
			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product
					&& $_product->exists()
					&& $cart_item['quantity'] > 0
					&& apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key )
				) {
					/**
					 * This filter is documented in woocommerce/templates/cart/cart.php.
					 *
					 * @since 2.1.0
					 */
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<div class="wpex-mini-cart-item wpex-flex wpex-gap-25">
						<div class="wpex-mini-cart-item__image wpex-flex-shrink-0">
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<div class="wpex-mini-cart-item__info wpex-self-center">
							<div class="wpex-mini-cart-item__title wpex-heading wpex-mb-5"><?php
								if ( empty( $product_permalink ) ) :
									echo wp_kses_post( $product_name );
								else : ?>
									<a href="<?php echo esc_url( $product_permalink ); ?>"><?php
										echo wp_kses_post( $product_name );
									?></a>
								<?php endif;
							?></div>
							<div class="wpex-mini-cart-item__data wpex-text-sm">
								<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<div class="wpex-mini-cart-item__quantity wpex-text-sm">
								<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<div class="wpex-mini-cart-item__actions wpex-flex wpex-gap-15 wpex-text-sm wpex-mt-5">
								<a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="remove remove_from_cart_button wpex-underline" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" data-product_sku="<?php echo esc_attr( $_product->get_sku() ); ?>"><?php esc_html_e( 'Remove', 'total' ); ?></a>
							</div>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>

	<?php else : ?>

		<div class="wpex-mini-cart__empty"><p class="wpex-m-0"><?php esc_html_e( 'Your cart is empty.', 'total' ); ?></p></div>

	<?php endif; ?>

</div>