<?php

/**
 * vcex_post_media shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

if ( $atts['supported_media'] && is_string( $atts['supported_media'] ) ) {
	$atts['supported_media'] = wp_parse_list( $atts['supported_media'] );
}

if ( ! is_array( $atts['supported_media'] ) ) {
	$atts['supported_media'] = array(); // must be an array to prevent debug errors.
}

$wrap_class = [
	'vcex-post-media',
	'vcex-module',
];

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-mx-auto';
	$wrap_class[] = 'wpex-max-w-100';
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_post_media' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_post_media', $atts );

$output = '<div class="' . esc_attr( $wrap_class ) . '">';
	if ( vcex_is_template_edit_mode() ) {
		if ( function_exists( 'wpex_get_placeholder_image' ) ) {
			$output .= wpex_get_placeholder_image();
		}
	} elseif ( function_exists( 'wpex_get_post_media' ) ) {
		$args = [
			'thumbnail_args'  => [
				'attachment' => get_post_thumbnail_id( vcex_get_the_ID() ),
				'size'       => $atts['img_size'] ?? null,
				'crop'       => $atts['img_crop'] ?? null,
				'width'      => $atts['img_width'] ?? null,
				'height'     => $atts['img_height'] ?? null,
				'class'      => 'wpex-align-center' ?? null,
			],
			'lightbox'        => vcex_validate_boolean( $atts['lightbox'] ),
			'supported_media' => $atts['supported_media'],
		];

		if ( isset( $atts['lazy_load'] ) && ! vcex_validate_boolean( $atts['lazy_load'] ) ) {
			$args['thumbnail_args']['lazy'] = false;
		}

		$output .= wpex_get_post_media( vcex_get_the_ID(), $args );

	} else {
		$output .= get_the_post_thumbnail();
	}
$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
