<?php
/**
 * vcex_alert shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $content ) ) {
	return;
}

$shortcode_class = [
	'vcex-module',
	'wpex-alert',
];

if ( ! empty( $atts['type'] ) ) {
	$shortcode_class[] = 'wpex-alert-' . sanitize_html_class( $atts['type'] );
}

$extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_alert' );

if ( $extra_classes ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_alert', $atts );

$output = '<div class="' . esc_attr( trim( $shortcode_class ) ) . '"' . vcex_get_unique_id( $atts ) . '>';

	if ( ! empty( $atts['heading'] ) ) {
		$output .= '<h4>' . vcex_parse_text_safe( $atts['heading'] ) . '</h4>'; // @todo add option to control heading tag.
	}

	$output .= vcex_the_content( $content );

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
