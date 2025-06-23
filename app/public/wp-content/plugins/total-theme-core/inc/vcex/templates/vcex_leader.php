<?php

/**
 * vcex_leader shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.1
 */

defined( 'ABSPATH' ) || exit;

if ( empty(  $atts['leaders'] ) ) {
	return;
}

// Extract shortcode attributes
extract( $atts );

$leaders = (array) vcex_vc_param_group_parse_atts( $atts['leaders'] );

if ( ! $leaders ) {
	return;
}

// Define output var
$output = '';

// Define element attributes
$wrap_atrrs = [
	'class' => '',
];

// Define element classes
$wrap_class = [
	'vcex-leader',
	'vcex-leader-' . sanitize_html_class( $style ),
	'vcex-module',
	'wpex-overflow-hidden',
	'wpex-mx-auto',
	'wpex-max-w-100',
	'wpex-last-mb-0',
	'wpex-clr',
];

if ( $bottom_margin ) {
	$wrap_class[] = vcex_parse_margin_class( $bottom_margin, 'bottom' );
}

if ( 'true' == $responsive && vcex_is_layout_responsive() ) {
	$wrap_class[] = 'vcex-responsive';
}

if ( $el_class ) {
	$wrap_class[] = vcex_get_extra_class( $el_class );
}

// Begin output
$output .= '<div class="' . esc_attr( vcex_parse_shortcode_classes( $wrap_class, 'vcex_leader', $atts ) ) . '">';

// Individual item classes
$leader_classes = [
	'vcex-leader-item',
	'wpex-clr',
];

if ( ! empty( $atts['spacing'] ) ) {
	$leader_classes[] = 'wpex-mb-' . absint( $atts['spacing'] );
}

if ( $css_animation_class = vcex_get_css_animation( $css_animation ) ) {
	$leader_classes[] = $css_animation_class;
}

// Loop through leaders and output it's content
foreach ( $leaders as $leader ) {
	$label_safe = ! empty( $leader['label'] ) ? sanitize_text_field( $leader['label'] ) : esc_html__( 'Label', 'total-theme-core' );
	$value_safe = ! empty( $leader['value'] ) ? sanitize_text_field( $leader['value'] ) : esc_html__( 'Value', 'total-theme-core' );

	$output .= '<div class="' . esc_attr( implode( ' ', $leader_classes ) ) . '">';
		$output .= '<span class="vcex-leader-item__label vcex-first wpex-pr-5 wpex-surface-1 wpex-relative wpex-z-2">' . vcex_parse_text( $label_safe ) . '</span>';
		if ( $responsive && 'minimal' != $style ) {
			$output .= '<span class="vcex-leader-item__inner vcex-inner wpex-hidden">...</span>';
		}
		if ( $value_safe && 'Value' !== $value_safe ) {
			$output .= '<span class="vcex-leader-item__value vcex-last wpex-float-right wpex-pl-5 wpex-surface-1 wpex-relative wpex-z-2">' . vcex_parse_text( $value_safe ) . '</span>';
		}
	$output .= '</div>';
}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
