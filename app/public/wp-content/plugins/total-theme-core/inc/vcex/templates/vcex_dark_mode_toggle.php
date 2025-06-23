<?php

/**
 * vcex_dark_mode_toggle shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

// Get Labels
$show_label = vcex_validate_att_boolean( 'show_label', $atts, false );

if ( $show_label ) {
	$label_dark = ! empty( $atts['dark_label'] ) ? sanitize_text_field( $atts['dark_label'] ) : esc_html__( 'Dark Mode', 'total' );
	$label_light = ! empty( $atts['light_label'] ) ? sanitize_text_field( $atts['light_label'] ) : esc_html__( 'Light Mode', 'total' );
} else {
	$show_label = false;
	$label_dark = $label_light = '';
}

// Get Icon size
if ( $show_label && ! empty( $atts['icon_size'] ) ) {
	$icon_size = is_numeric( $atts['icon_size'] ) ? $atts['icon_size'] . 'px' : $atts['icon_size'];
	if ( in_array( $icon_size, [ '2xs', 'xs', 'sm', 'lg', 'xl', '2xl' ] ) ) {
		$icon_size = $atts['icon_size'];
	}
} else {
	$icon_size = '';
}

// Get Icons
if ( is_callable( 'TotalTheme\Dark_mode::get_icon_name' ) ) {
	$icon_dark = TotalTheme\Dark_mode::get_icon_name( 'dark' );
	$icon_light = TotalTheme\Dark_mode::get_icon_name( 'light' );
	$icon_class = 'wpex-dark-mode-toggle__icon wpex-flex ';
	if ( $show_label ) {
		$icon_class .= ' wpex-icon--w';
	}
	$icon_dark = vcex_get_theme_icon_html( $icon_dark, $icon_class, $icon_size );
	$icon_light = vcex_get_theme_icon_html( $icon_light, $icon_class, $icon_size );
} else {
	$icon_dark = $icon_light = '';
}

// Wrap class
$wrap_class = [
	'vcex-dark-mode-toggle',
];

if ( ! empty( $atts['el_class' ] ) ) {
	$wrap_class[] = vcex_get_extra_class( $atts['el_class' ] );	
}

$wrap_class = vcex_parse_shortcode_classes( $wrap_class, 'vcex_dark_mode_toggle', $atts );

// Output starts here
$output = '<div class="' . esc_attr( $wrap_class ) . '">';

// Button vars
$button_class = 'vcex-dark-mode-toggle__button';
$button_type = $atts['type'] ?? 'button';

// Begin HTML output
if ( 'link' === $button_type ) {
	$output .= '<a href="#" data-role="button"';
} else {
	$output .= '<button type="button"';
	if ( 'theme_button' === $button_type ) {
		$button_class .= ' theme-button';
	} else {
		$button_class .= ' wpex-unstyled-button wpex-hover-link-color';
	}
}

$aria_label = vcex_get_aria_label( 'dark_mode_toggle' );

$output .= ' class="' . esc_attr( $button_class ) . '" data-wpex-toggle="theme" data-aria-label="' . esc_attr( $aria_label ) . '">';
	// Inner gap class
	$gap_class_safe = ! empty( $atts['gap'] ) ? sanitize_html_class( 'wpex-gap-' . absint( $atts['gap'] ) ) : 'wpex-gap-5';

	// Dark Icon
	if ( $icon_dark || $label_dark ) {
		$output .= '<span class="vcex-dark-mode-toggle__inner hidden-dark-mode wpex-flex wpex-items-center ' . $gap_class_safe . '">';
			$output .= $icon_dark;
			if ( $label_dark ) {
				$output .= '<span class="vcex-dark-mode-toggle__label">' . esc_html( $label_dark ) . '</span>';
			}
		$output .= '</span>';
	}

	// Light Icon
	if ( $icon_light || $label_light ) {
		$output .= '<span class="vcex-dark-mode-toggle__inner visible-dark-mode wpex-flex wpex-items-center ' . $gap_class_safe . '">';
			$output .= $icon_light;
			if ( $label_light ) {
				$output .= '<span class="vcex-dark-mode-toggle__label">' . esc_html( $label_light ) . '</span>';
			}
		$output .= '</span>';
	}

if ( 'link' === $button_type ) {
	$output .= '</a>';
} else {
	$output .= '</button>';
}

$output .= '</div>';

// @codingStandardsIgnoreLine
echo $output;
