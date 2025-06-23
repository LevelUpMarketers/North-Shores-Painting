<?php

/**
 * Overlay: Title Price Hover.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;


if ( 'outside_link' !== $position || ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
	return;
}

$speed = totaltheme_get_overlay_speed();

?>

<div class="overlay-add-to-cart-hover overlay-hide wpex-absolute wpex-bottom-0 wpex-inset-x-0 overlay-slide-up wpex-transition-all wpex-duration-<?php echo intval( $speed ); ?>">
	<div class="overlay-content">
		<?php woocommerce_template_loop_add_to_cart(); ?>
	</div>
</div>
