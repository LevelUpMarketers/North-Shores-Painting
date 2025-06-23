<?php
/**
 * Product Loop Start
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 9999
 */

defined( 'ABSPATH' ) || exit;

/*----------------------------------------------------------------------*/
/* [ Custom Theme output ]
/*----------------------------------------------------------------------*/
if ( totaltheme_is_integration_active( 'woocommerce' )
	&& totaltheme_call_static( 'Integration\WooCommerce', 'is_advanced_mode' )
) :

	// Get loop details.
	$context      = wc_get_loop_prop( 'name' );
	$columns      = absint( max( 1, wc_get_loop_prop( 'columns' ) ) );
	$is_shortcode = wc_get_loop_prop( 'is_shortcode' );

	// Add row classes.
	$classes = 'products';

	// Single columns.
	if ( $is_shortcode && 'product' === $context ) {
		$columns = 1;
	}

	// Calculate columns after filter has run for responsiveness.
	if ( ! $is_shortcode || empty( $columns ) ) {
		switch ( $context ) {
			case 'related':
				$columns = get_theme_mod( 'woocommerce_related_columns', '4' );
				break;
			case 'up-sells':
				$columns = get_theme_mod( 'woocommerce_upsells_columns', '4' );
				break;
			case 'cross-sells':
				$columns = get_theme_mod( 'woocommerce_cross_sells_columns', '2' );
				break;
			case 'featured_products':
			case 'products':
			case 'recent_products':
			default:
				$columns = get_theme_mod( 'woocommerce_shop_columns', '4' );
				break;
		}
	}
	$columns = apply_filters( 'wpex_loop_shop_columns', $columns, $context, $is_shortcode );
	if ( $columns ) {
		$classes .= ' wpex-grid';
		$classes .= ' ' . wpex_grid_columns_class( $columns );
		$gap = get_theme_mod( 'woo_shop_columns_gap' );
		if ( ! $gap ) {
			$gap = totaltheme_has_classic_styles() ? '20' : '25';
		}
		$classes .= ' wpex-gap-' . sanitize_html_class( $gap );
	}
	$classes .= ' wpex-clear';
	$classes = (string) apply_filters( 'wpex_woo_loop_wrap_classes', $classes );

	?>

	<ul class="<?php echo esc_attr( $classes );?>">

<?php
/*----------------------------------------------------------------------*/
/* [ Default output ]
/*----------------------------------------------------------------------*/
else : ?>

	<ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">

<?php endif; ?>