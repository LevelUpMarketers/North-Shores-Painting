<?php
/**
 * Featured Image style thumbnail
 *
 * @package Total Wordpress Theme
 * @subpackage Templates/WooCommerce
 * @version 5.6
 */

defined( 'ABSPATH' ) || exit;

// Display featured image if defined
if ( has_post_thumbnail() ) {

    wpex_post_thumbnail( [
        'attachment' => (int) apply_filters( 'wpex_woocommerce_product_entry_thumbnail_id', get_post_thumbnail_id() ),
        'size'       => 'shop_catalog',
        'alt'        => wpex_get_esc_title(),
        'class'      => 'woo-entry-image-main wp-post-image',
    ] );

}

// Display placeholder if there isn't a thumbnail defined.
else {

    wpex_woo_placeholder_img();

}

?>