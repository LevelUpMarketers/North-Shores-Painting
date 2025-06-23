<?php
/**
 * vcex_animated_text shortcode output.
 *
 * @package Total Theme Core
 * @subpackage VCEX
 * @version 1.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $atts['strings'] ) ) {
	return;
}

$strings = (array) vcex_vc_param_group_parse_atts( $atts['strings'] );

if ( ! $strings ) {
	return;
}

wp_enqueue_script( 'vcex-animated-text' );

$html = '';

// Define shortcode CSS classes.
$shortcode_class = [
	'vcex-animated-text',
	'vcex-module',
	'wpex-m-0',
	'wpex-text-xl',
	'wpex-text-1',
	'wpex-font-semibold',
	'wpex-leading-none',
	'vcex-typed-text-wrap',
];

if ( $extra_classes = vcex_get_shortcode_extra_classes( $atts, 'vcex_animated_text' ) ) {
	$shortcode_class = array_merge( $shortcode_class, $extra_classes );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_animated_text', $atts );

// Animated text data attributes.
$data_attr = '';

$data = [];
foreach ( $strings as $string ) {
	if ( isset( $string['text'] ) ) {
		$data[] = esc_html( $string['text'] );
	}
}

// Define animation settings.
$settings = [
	'loop'       => vcex_validate_boolean( $atts['loop'] ?? true ),
	'showCursor' => vcex_validate_boolean( $atts['type_cursor'] ?? false ),
	'typeSpeed'  => ! empty( $atts['speed'] ) ? intval( $atts['speed'] ) : 40,
	'backDelay'  => ! empty( $atts['back_delay'] ) ? intval( $atts['back_delay'] ) : 500,
	'backSpeed'  => ! empty( $atts['back_speed'] ) ? intval( $atts['back_speed'] ) : 0,
	'startDelay' => ! empty( $atts['start_delay'] ) ? intval( $atts['start_delay'] ) : 0,
];

// Escaped element tag.
$tag_safe = ! empty( $atts['tag'] ) ? tag_escape( $atts['tag'] ) : 'div';

// Output Shortcode.
$html .= '<' . $tag_safe . ' class="' . esc_attr( $shortcode_class ) . '"' . $data_attr . vcex_get_unique_id( $atts ) . '>';

	if ( isset( $atts['static_text'] ) && 'true' === $atts['static_text'] && ! empty( $atts['static_before'] ) ) {
		$html .= '<span class="vcex-typed-text-before vcex-before">' . vcex_parse_text_safe( $atts['static_before'] ) . '</span> ';
	}

	$inner_class = 'vcex-typed-text-inner vcex-typed-text-css wpex-inline-block wpex-max-w-100';

	if ( ! empty( $atts['animated_css'] ) ) {
		$animated_css = vcex_vc_shortcode_custom_css_class( $atts['animated_css'] );
		if ( $animated_css ) {
			$inner_class .= ' ' . $animated_css;
		}
	}

	if ( ! empty( $atts['animated_padding'] ) ) {
		$animated_padding_class = vcex_parse_padding_class( $atts['animated_padding'] );
		if ( $animated_padding_class ) {
			$inner_class .= ' ' . $animated_padding_class;
		}
	}

	$html .= '<span class="' . esc_attr( $inner_class ) . '">';

		$tmp_data = [];
		foreach ( $data as $val ) {
			$tmp_data[] = vcex_parse_text( $val );
		}
		$data = $tmp_data;

		$html .= '<span class="screen-reader-text">';

			foreach ( $data as $string ) {
				$html .= '<span>' . esc_html( $string ) . '</span>';
			}

		$html .= '</span>';

		$html .= '<span class="vcex-animated-text__placeholder vcex-ph wpex-inline-block wpex-invisible"></span>'; // Add empty span 1px wide to prevent bounce.

		$html .= '<span class="vcex-typed-text" aria-hidden="true" data-settings="' . htmlspecialchars( wp_json_encode( $settings ) ) . '" data-strings="' . htmlspecialchars( wp_json_encode( $data ) ) . '"></span>';

	$html .= '</span>';

	if ( isset( $atts['static_text'] ) && 'true' === $atts['static_text'] && ! empty( $atts['static_after'] ) ) {
		$html .= ' <span class="vcex-typed-text-after vcex-after">' . vcex_parse_text_safe( $atts['static_after'] ) . '</span>';
	}

$html .= '</' . $tag_safe . '>';

// @codingStandardsIgnoreLine.
echo $html;
