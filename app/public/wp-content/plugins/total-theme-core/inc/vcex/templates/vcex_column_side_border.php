<?php

/**
 * vcex_column_side_border shortcode output
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 1.8
 */

defined( 'ABSPATH' ) || exit;

if ( vcex_vc_is_inline() ) {
	echo '<div class="wpex-alert wpex-text-center">' . esc_html__( 'Column Side Border Placeholder', 'total-theme-core' ) . '</div>';
	return;
}

$position = $atts['position'] ?? 'right';

if ( 'left' !== $position && 'right' !== $position ) {
	$position = 'right';
}

$shortcode_class = [
	'vcex-column-side-border',
	'vcex-column-side-border--' . $position,
	'wpex-hidden',
	'wpex-md-block',
	'wpex-absolute',
	'wpex-top-50',
	'wpex-' . $position . '-0',
	'wpex-max-h-100',
	'wpex-h-100',
	'wpex-w-1px',
	'wpex-surface-4',
];

switch ( $position ) {
	case 'left':
		$shortcode_class[] = '-wpex-translate-xy-50';
		break;
	case 'right':
		$shortcode_class[] = 'wpex-translate-x-y-50';
		break;
}

$shortcode_class[] = 'vcex-' . $position;

if ( ! empty( $atts['class'] ) ) {
    $shortcode_class[] = vcex_get_extra_class( $atts['class'] );
}

if ( ! empty( $atts['visibility'] ) ) {
    $shortcode_class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

$shortcode_class = vcex_parse_shortcode_classes( $shortcode_class, 'vcex_column_side_border', $atts );

$style = vcex_inline_style( [
	'height'     => $atts['height'] ?? '',
	'width'      => ! empty( $atts['width'] ) ? absint( $atts['width'] ) : '',
	'background' => $atts['background_color'] ?? '',
] );

echo '<div class="' . esc_attr( $shortcode_class ) . '"' . $style . '></div>';
