<?php

/**
 * Overlay: Secondary Image Swap.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

// Only used for inside position.
if ( 'inside_link' !== $position ) {
	return;
}

// Thumbnail required.
if ( ! has_post_thumbnail() ) {
	return;
}

// Get secondary image
$secondary_image = totaltheme_get_post_secondary_thumbnail_id();

// Secondary image required.
if ( ! $secondary_image ) {
	return;
}

?>

<div class="overlay-thumb-swap-secondary wpex-invisible wpex-opacity-0 wpex-absolute wpex-inset-0 wpex-index-1 wpex-overflow-hidden wpex-transition-all wpex-duration-300 wpex-flex wpex-items-center wpex-justify-center">
	<?php if ( is_numeric( $secondary_image ) ) {
		echo wpex_get_post_thumbnail( [
			'attachment' => $secondary_image,
			'width'      => $args['img_width'] ?? '',
			'height'     => $args['img_height'] ?? '',
			'crop'       => $args['img_crop'] ?? '',
			'size'       => $args['img_size'] ?? '',
		] );
	} else { ?>
		<img src="<?php echo esc_url( $secondary_image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
	<?php } ?>
</div>
