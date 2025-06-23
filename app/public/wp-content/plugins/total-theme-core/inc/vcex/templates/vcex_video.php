<?php

/**
 * vcex_video shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0.3
 */

defined( 'ABSPATH' ) || exit;

$video = Vcex_Video_Shortcode::render_video( $atts );

if ( ! $video ) {
	return;
}

$type = ! empty( $atts['type'] ) ? sanitize_text_field( $atts['type'] ) : 'youtube';

$wrap_class = [
	'vcex-video',
	'wpex-bg-black',
];

if ( vcex_validate_att_boolean( 'fill_column', $atts ) ) {
	$wrap_class[] = 'vcex-fill-column';
}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-max-w-100';
	$wrap_class[] = vcex_parse_align_class( ! empty( $atts['align'] ) ? $atts['align'] : 'center' );
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_video' );

if ( $extra_classes ) {
	$wrap_class = array_merge( $wrap_class, $extra_classes );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_video', $atts );

$output = '<div class="' . esc_attr( trim( $wrap_class ) ) . '"' . vcex_get_unique_id( $atts ) . ' data-vcex-video-type="' . esc_attr( $type ) . '">';

	$output .= '<div class="vcex-video-inner wpex-relative">';

		$output .= $video;

		if ( vcex_validate_att_boolean( 'overlay', $atts ) ) {
			$output .= Vcex_Video_Shortcode::render_overlay( $atts );
		}

	$output .= '</div>';

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
