<?php

/**
 * Off Canvas Cart Footer.
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

<div class="wpex-mini-cart__footer wpex-mt-auto">

	<?php if ( ! WC()->cart->is_empty() ) : ?>
		<div class="wpex-mini-cart__footer-total total wpex-mb-15">
			<?php
			/**
			 * Hook: woocommerce_widget_shopping_cart_total.
			 *
			 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
			 */
			do_action( 'woocommerce_widget_shopping_cart_total' );
			?>
		</div>
		<div class="wpex-mini-cart__footer-actions">
			<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
			<div class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></div>
			<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>
		</div>
	<?php elseif ( wc_get_page_id( 'shop' ) > 0 ) : ?>
		<div class="wpex-mini-cart__footer-actions wpex-flex wpex-child-flex-grow">
			<a class="button wc-backward<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'Visit the Shop', 'total' ); ?></a>
		</div>
	<?php endif; ?>
</div>
