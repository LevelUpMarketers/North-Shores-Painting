<?php

/**
 * vcex_horizontal_menu shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0
 */

defined( 'ABSPATH' ) || exit;

$menu_id = ! empty( $atts['menu_id'] ) ? absint( $atts['menu_id'] ) : ( get_nav_menu_locations()['main_menu'] ?? 0 );

if ( ! $menu_id
	|| ! class_exists( 'TotalThemeCore\Vcex\Walkers\Nav_Menu_Horizontal' )
	|| ! is_nav_menu( $menu_id )
) {
	return;
}

// Parent wrapper.
$class = [
	'vcex-horizontal-menu',
	'wpex-leading-normal',
	'wpex-self-center', // fixes issues when inside a flex container
	'wpex-relative',
];

// Add margins based on inner justify incase item is inside flex container
if ( ! empty( $atts['inner_justify'] ) && 'start' !== $atts['inner_justify'] ) {
	$class[] = vcex_parse_align_class( $atts['inner_justify'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['shadow'] ) ) {
	$class[] = vcex_parse_shadow_class( $atts['shadow'] );
}

if ( ! empty( $atts['visibility'] ) ) {
	$class[] = vcex_parse_visibility_class( $atts['visibility'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$class[] = vcex_get_extra_class( $atts['el_class'] );
}

$class = vcex_parse_shortcode_classes( $class, 'vcex_horizontal_menu', $atts );

$output = '<div class="' . esc_attr(  $class ) . '"' . vcex_get_unique_id( $atts ) . '>';

	// Inner element
	$inner_class = [
		'vcex-horizontal-menu__inner',
		'wpex-flex',
	];

	if ( ! empty( $atts['inner_justify'] ) && 'start' !== $atts['inner_justify'] ) {
		$inner_class[] = vcex_parse_justify_content_class( $atts['inner_justify'] );
	}

	$output .= '<div class="' . esc_attr( implode( ' ', $inner_class ) ) . '">';
		
		// Nav element
		$nav_class = 'vcex-horizontal-menu-nav';
		$aria_label = ! empty( $atts['aria_label'] ) ? \sanitize_text_field( $atts['aria_label'] ) : esc_attr__( 'Menu', 'total-theme-core' );

		if ( isset( $atts['nav_list_justify'] )
			&& in_array( $atts['nav_list_justify'], [ 'space-between', 'space-around', 'space-evenly' ], true )
		) {
			$nav_class .= ' wpex-w-100';
		}

		$output .= '<nav class="' . esc_attr( $nav_class ) . '" aria-label="' . esc_attr( $aria_label ) . '">';

			// Get the wp_nav_menu - this is rendered inside the Vcex_Horizontal_Menu_Shortcode so we can use
			// hooks to insert toggles to the end of the menu (search,cart,dark mode)
			ob_start();
				Vcex_Horizontal_Menu_Shortcode::render_wp_nav_menu( $atts );
			$output .= ob_get_clean();

		$output .= '</nav>';

	// Close .vcex-horizontal-menu__inner
	$output .= '</div>';

// Close .vcex-horizontal-menu
$output .= '</div>';

echo $output;  // @codingStandardsIgnoreLine
