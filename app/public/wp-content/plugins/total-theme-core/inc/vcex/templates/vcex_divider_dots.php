<?php

/**
 * vcex_divider_dots shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Set defaults.
$count   = ! empty( $atts['count'] ) ? absint( $atts['count'] ) : 3;
$spacing = ! empty( $atts['spacing'] ) ? absint( $atts['spacing'] ) : 10;
$size    = ! empty( $atts['size'] ) ? sanitize_text_field( $atts['size'] ) : '';

// Size check.
if ( 'sm' === $size || 'md' === $size || 'lg' === $size || 'xl' === $size ) {
	$preset_size = true;
} else {
	$preset_size = false;
}

// Wrap classes.
$wrap_class = [
	'vcex-module',
	'vcex-divider-dots',
	'wpex-mx-auto',
	'wpex-last-mr-0',
];

if ( $size && $preset_size ) {
	$wrap_class[] = "vcex-divider-dots--{$size}";
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['css_animation'] ) ) {
	$wrap_class[] = vcex_get_css_animation( $atts['css_animation'] );
}

if ( ! empty( $atts['align'] ) ) {
	$wrap_class[] = vcex_parse_text_align_class( $atts['align'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_divider_dots', $atts );

// Wrap style.
$shortcode_style = vcex_inline_style( [
	'padding'            => $atts['margin'] ?? '',
	'animation_delay'    => $atts['animation_delay'] ?? '',
	'animation_duration' => $atts['animation_duration'] ?? '',
] );

// Span class.
$span_class = [
	'wpex-inline-block',
	'wpex-rounded-full',
];

if ( empty( $atts['color'] ) ) {
	$span_class[] = 'wpex-bg-accent';
}

$span_class[] = "wpex-mr-{$spacing}";

// Span style
$span_style_args = [
	'background' => $atts['color'] ?? '',
];

if ( $size && ! $preset_size ) {
	$span_style_args['height'] = $size;
	$span_style_args['width' ] = $size;
}

$span_style = vcex_inline_style( $span_style_args );

// Return output.
$output = '<div class="' . esc_attr( $wrap_class ) . '"' . $shortcode_style . '>';
	for ( $k = 0; $k < $count; $k++ ) {
		$output .= '<span class="' . esc_attr( implode( ' ', $span_class ) ) . '"' . $span_style . '></span>';
	}
$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
