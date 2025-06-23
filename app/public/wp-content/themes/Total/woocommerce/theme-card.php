<?php
defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$card_args = [
	'style'          => wpex_product_entry_card_style(),
	'thumbnail_size' => 'shop_catalog',
	'post_id'        => $product->get_id(),
];

$overlay_style = get_theme_mod( 'woo_entry_card_overlay_style' );

if ( ! empty( $overlay_style ) && 'none' !== $overlay_style ) {
	$card_args['thumbnail_overlay_style'] = $overlay_style;
}

?>

<li <?php wc_product_class( '', $product ); ?>>
	<?php wpex_card( $card_args ); ?>
</li>