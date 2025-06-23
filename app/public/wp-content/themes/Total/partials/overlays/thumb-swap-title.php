<?php

/**
 * Overlay: Secondary Image Swap & Title.
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

// Get secondary image.
$secondary_image = totaltheme_get_post_secondary_thumbnail_id();

// Secondary image required.
if ( ! $secondary_image ) {
	return;
}

?>

<div class="overlay-thumb-swap-secondary wpex-invisible wpex-opacity-0 wpex-absolute wpex-inset-0 wpex-z-1 wpex-overflow-hidden wpex-transition-all wpex-duration-500 wpex-flex wpex-items-center wpex-justify-center">
	<?php
	if ( is_numeric( $secondary_image ) ) {
		echo wpex_get_post_thumbnail( [
			'attachment' => $secondary_image,
			'width'      => $args['img_width'] ?? '',
			'height'     => $args['img_height'] ?? '',
			'crop'       => $args['img_crop'] ?? '',
			'alt'        => $args['post_esc_title'] ?? '',
			'size'       => $args['img_size'] ?? '',
		] );
	} else { ?>
		<img src="<?php echo esc_url( $secondary_image ); ?>">
	<?php } ?>
	<div class="overlay-thumb-swap-title wpex-absolute wpex-bottom-0 wpex-inset-x-0 wpex-text-white wpex-font-semibold wpex-text-center wpex-leading-snug wpex-py-30 wpex-px-20 wpex-text-lg"><span class="wpex-relative wpex-z-5"><?php echo esc_html( get_the_title() ); ?></span></div>
</div>
