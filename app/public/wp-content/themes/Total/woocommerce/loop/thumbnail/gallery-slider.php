<?php
/**
 * Gallery Style WooCommerce
 *
 * @package Total Wordpress Theme
 * @subpackage Templates/WooCommerce
 * @version 5.7.2
 */

defined( 'ABSPATH' ) || exit;

// Return dummy image if no featured image is defined.
if ( ! has_post_thumbnail() ) {
	wpex_woo_placeholder_img();
	return;
}

// Get global product data.
global $product;

// Get gallery images and exclude featured image incase it's added in the gallery as well.
$attachment_id    = (int) apply_filters( 'wpex_woocommerce_product_entry_thumbnail_id', get_post_thumbnail_id() );
$attachment_ids   = $product->get_gallery_image_ids();
$attachment_ids[] = $attachment_id;
$attachment_ids   = array_unique( $attachment_ids );

// If there are attachments display slider.
if ( $attachment_ids ) :

	wpex_enqueue_slider_pro_scripts();

	$wrap_attributes = [
		'class' => 'woo-product-entry-slider wpex-slider pro-slider'
	];

	// Slider data attributes.
	$data_atributes['fade']                      = 'true';
	$data_atributes['auto-play']                 = 'false';
	$data_atributes['height-animation-duration'] = '0.0';
	$data_atributes['loop']                      = 'false';

	/**
	 * Filters the woocommerce product entry gallery slider data attributes.
	 *
	 * @param array $data_attributes
	 */
	$data_atributes = (array) apply_filters( 'wpex_shop_catalog_slider_data', $data_atributes );

	foreach ( $data_atributes as $key => $val ) {
		$wrap_attributes['data-' . esc_attr( $key ) ] = esc_attr( $val );
	}

	?>

	<div <?php echo wpex_parse_attrs( $wrap_attributes ); ?>>

		<div class="wpex-slider-slides sp-slides">

			<?php
			$count=0;
			foreach ( $attachment_ids as $attachment_id ) :
				$count++;
				if ( 5 === $count ) {
					break; // Only display the first 5 images.
				}

				?><div class="wpex-slider-slide sp-slide"><?php

					wpex_post_thumbnail( [
						'attachment' => $attachment_id,
						'size'       => 'shop_catalog',
						'class'      => 'wp-post-image',
					] );

				?></div><?php

			endforeach; ?>

		</div>

	</div>

<?php

// There aren't any images so lets display the featured image.
else : ?>

	<?php wc_get_template( 'loop/thumbnail/featured-image.php' ); ?>

<?php
endif;
