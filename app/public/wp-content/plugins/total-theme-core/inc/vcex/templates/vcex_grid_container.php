<?php

/**
 * Grid Container shortcode template.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$html = '';
$breakpoints = [ 'sm', 'md', 'lg', 'xl' ];

$wrap_class = [
	'vcex-grid-container',
	'vcex-module',
	'wpex-grid',
];

if ( empty( $atts['el_class'] ) || ! str_contains( (string) $atts['el_class'], '-gap-' ) ) {
	$wrap_class[] = 'wpex-gap-20';
}

if ( ! empty( $atts['width'] ) ) {
	$wrap_class[] = 'wpex-mx-auto';
}

$wrap_class[] = 'wpex-grid-cols-' . sanitize_html_class( absint( $atts['columns'] ) );

foreach ( $breakpoints as $bk ) {
	if ( ! empty( $atts['columns_' . $bk] ) && is_numeric( $atts['columns_' . $bk] ) ) {
		$wrap_class[] = 'wpex-' . $bk . '-grid-cols-' . sanitize_html_class( absint( $atts['columns_' . $bk] ) );
	}
}

if ( ! empty( $atts['align_items'] ) ) {
	$wrap_class[] = vcex_parse_align_items_class( $atts['align_items'] );
}

if ( ! empty( $atts['justify_items'] ) ) {
	$wrap_class[] = 'wpex-justify-items-' . sanitize_html_class( (string) $atts['justify_items'] );
}

if ( ! empty( $atts['shadow'] ) ) {
	$wrap_class[] = vcex_parse_shadow_class( $atts['shadow'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$wrap_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$wrap_class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['el_class'] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class'] );
}

if ( ! empty( $atts['css'] ) ) {
	$wrap_class[] = vcex_vc_shortcode_custom_css_class( $atts['css'] );
}

$inline_style = vcex_inline_style( [
	'gap'       => $atts['gap'],
	'max_width' => $atts['width'],
], true );

$html .= '<div class="' . esc_attr( implode( ' ', array_filter( $wrap_class ) ) ) . '"' . vcex_get_unique_id( $atts ) . $inline_style . '>';

	$html .= do_shortcode( wp_kses_post( $content ) );

$html .= '</div>';

echo $html; // @codingStandardsIgnoreLine
