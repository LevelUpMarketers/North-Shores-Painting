<?php

/**
 * vcex_off_canvas_menu shortcode output.
 *
 * @package Total WordPress Theme
 * @subpackage Total Theme Core
 * @version 2.0.3
 */

defined( 'ABSPATH' ) || exit;

$menu_id = ! empty( $atts['menu_id'] ) ? absint( $atts['menu_id'] ) : ( get_nav_menu_locations()['main_menu'] ?? 0 );

if ( ! $menu_id
	|| ! class_exists( 'TotalThemeCore\Vcex\Walkers\Nav_Menu_Off_Canvas' )
	|| ! class_exists( 'TotalTheme\Off_Canvas' )
	|| ! class_exists( 'TotalTheme\Hamburger_Icon' )
	|| ! is_nav_menu( $menu_id )
) {
	return;
}

$output = '';
$gap_class = vcex_parse_gap_class( ! empty( $atts['extra_items_gap'] ) ? sanitize_text_field( $atts['extra_items_gap'] ) : 15 );
$under_header = vcex_validate_att_boolean( 'under_header', $atts );

$class = [
	'vcex-off-canvas-menu',
	'wpex-flex',
	'wpex-items-center',
	'wpex-gap-15',
	$gap_class,
];

if ( ! empty( $atts['toggle_align'] ) ) {
	$class[] = vcex_parse_justify_content_class( $atts['toggle_align'] );
}

if ( ! empty( $atts['bottom_margin'] ) ) {
	$class[] = vcex_parse_margin_class( $atts['bottom_margin'], 'bottom' );
}

if ( ! empty( $atts['visibility'] ) && $visibility_class = vcex_parse_visibility_class( $atts['visibility'] ) ) {
	$class[] = $visibility_class;
}

if ( ! empty( $atts['el_class'] ) ) {
	$class[] = vcex_get_extra_class( $atts['el_class'] );
}

$class = vcex_parse_shortcode_classes( $class, 'vcex_off_canvas_menu', $atts );

$off_canvas_menu_id = ! empty( $atts['unique_id'] ) ? sanitize_text_field( $atts['unique_id'] ) : uniqid( 'wpex-off-canvas-' );

// Extras.
$extras = ! empty( $atts['extra_items'] ) ? $atts['extra_items'] : '';

if ( $extras && ! is_array( $extras ) ) {
	$extras = wp_parse_list( sanitize_text_field( $extras ) );
}

if ( $extras && is_array( $extras ) ) {
	$extra_class = "vcex-off-canvas-menu__extra wpex-text-lg wpex-flex wpex-items-center {$gap_class}";

	if ( ! empty( $atts['extra_items_position'] ) && 'end' === $atts['extra_items_position'] ) {
		$extra_class .= ' wpex-order-2';
	}

	$output .= '<div class="' . esc_attr( $extra_class ) . '">';
		$extra_button_class = 'vcex-off-canvas-menu__extra-item wpex-unstyled-button wpex-inline-flex wpex-items-center wpex-justify-center';
		foreach ( $extras as $extra ) {
			switch ( $extra ) {
				case 'search_toggle':
					if ( function_exists( 'totaltheme_get_instance_of' ) ) {
						totaltheme_get_instance_of( 'Search\Modal' );
					}
					$output .= '<button class="' . esc_attr( $extra_button_class ) . ' wpex-open-modal" aria-controls="wpex-search-modal" aria-expanded="false" aria-label="' . vcex_get_aria_label( 'search' ) . '">';
						$search_toggle_icon = ! empty( $atts['search_toggle_icon'] ) ? sanitize_text_field( $atts['search_toggle_icon'] ) : 'search';
						$search_toggle_icon_size = str_starts_with( $search_toggle_icon, 'material' ) ? 'md' : '';
						$output .= vcex_get_theme_icon_html( $search_toggle_icon, 'wpex-flex', $search_toggle_icon_size );
					$output .= '</button>';
					break;
				case 'dark_mode_toggle':
					if (  function_exists( 'totaltheme_call_static' )
						&& totaltheme_call_static( 'Dark_Mode', 'is_enabled' )
						&& is_callable( 'TotalTheme\Dark_mode::get_icon_name' )
					) {
						$output .= '<button class="' . esc_attr( $extra_button_class ) . '" data-wpex-toggle="theme" aria-label="' . vcex_get_aria_label( 'dark_mode_toggle' ) . '">';
							$output .= vcex_get_theme_icon_html( TotalTheme\Dark_mode::get_icon_name( 'dark' ), 'hidden-dark-mode wpex-flex' );
							$output .= vcex_get_theme_icon_html( TotalTheme\Dark_mode::get_icon_name( 'light' ), 'visible-dark-mode wpex-flex' );
						$output .= '</button>';
					}
					break;
				case 'cart_toggle':
					if ( class_exists( 'WooCommerce', false ) ) {
						$has_cart_badge = vcex_validate_att_boolean( 'cart_badge', $atts );
						$off_canvas_cart_check = class_exists( 'TotalTheme\Integration\WooCommerce\Cart\Off_Canvas', false );
						$cart_toggle_icon = ! empty( $atts['cart_toggle_icon'] ) ? sanitize_text_field( $atts['cart_toggle_icon'] ) : 'shopping-cart-alt';
						$cart_toggle_icon_size = str_starts_with( $cart_toggle_icon, 'material' ) ? 'md' : '';
						if ( $has_cart_badge ) {
							$extra_button_class .= ' wpex-relative';
						}
						if ( $off_canvas_cart_check ) {
							$output .= '<button class="' . esc_attr( $extra_button_class ) . '" data-wpex-toggle="off-canvas" aria-controls="wpex-off-canvas-cart" aria-expanded="false" aria-label="' . vcex_get_aria_label( 'cart_open' ) . '">';
						} else {
							$cart_link = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
							$output .= '<a href="' . esc_url( $cart_link ) . '" class="' . esc_attr( str_replace( 'wpex-unstyled-button', 'wpex-text-current wpex-hover-text-current', $extra_button_class ) ) . '">';
						}
						$output .= vcex_get_theme_icon_html( $cart_toggle_icon, 'wpex-flex', $cart_toggle_icon_size );
						if ( $has_cart_badge ) {
							$output .= totalthemecore_call_static(
								'Vcex\WooCommerce',
								'get_cart_badge',
								vcex_validate_att_boolean( 'cart_badge_count', $atts )
							);
						}
						$output .= $off_canvas_cart_check ? '</button>' : '</a>';
					}
					break;
			}
		}
	$output .= '</div>';
}

// Toggle
$toggle_style = ! empty( $atts['toggle_style'] ) && 'icon' === $atts['toggle_style'] ? 'icon' : 'hamburger';
$toggle_class = 'vcex-off-canvas-menu__toggle wpex-unstyled-button wpex-inline-flex wpex-items-center wpex-justify-center wpex-gap-10';
if ( 'hamburger' !== $toggle_style ) {
	$toggle_class .= ' wpex-text-lg';
}
$output .= '<button aria-controls="' . esc_attr( $off_canvas_menu_id ) .'" data-wpex-toggle="off-canvas" aria-expanded="false" class="' . esc_attr( $toggle_class ) . '">';
	// Toggle
	if ( 'hamburger' === $toggle_style ) {
		$output .= TotalTheme\Hamburger_Icon::render( [
			'toggle_state' => $under_header && vcex_validate_att_boolean( 'toggle_animate', $atts, true ),
			'rounded'      => vcex_validate_att_boolean( 'toggle_rounded', $atts ),
			'class'        => 'vcex-off-canvas-menu__toggle-hamburger',
		] );
	} else {
		if ( ! empty( $atts['toggle_icon'] ) ) {
			$toggle_icon = vcex_get_icon_html( $atts, 'toggle_icon', 'wpex-flex' );
		} else {
			$toggle_icon = vcex_get_theme_icon_html( 'bars', 'wpex-flex' );
		}
		if ( ! empty( $atts['toggle_icon_active'] ) ) {
			$toggle_icon_active = vcex_get_icon_html( $atts, 'toggle_icon_active', 'wpex-flex' );
		} else {
			$toggle_icon_active = '';
		}
		if ( $toggle_icon_active && $toggle_icon !== $toggle_icon_active ) {
			$output .= '<span data-open-text>' . $toggle_icon . '</span>';
			$output .= '<span data-close-text>' . $toggle_icon_active . '</span>';
		} else {
			$output .= $toggle_icon;
		}
	}
	// Label
	if ( ! empty( $atts['toggle_label'] ) ) {
		$output .= '<span class="vcex-off-canvas-menu__toggle-label">' . vcex_parse_text_safe( $atts['toggle_label'] )  . '</span>';
	}
	// Screen reader text
	$toggle_aria_open = ! empty( $atts['toggle_aria_open'] ) ? sanitize_text_field( $atts['toggle_aria_open'] ) : esc_html__( 'Open menu', 'total-theme-core' );
	if ( $toggle_aria_open ) {
		$output .= '<span class="screen-reader-text" data-open-text>' . esc_html( $toggle_aria_open ) . '</span>';
	}
	$toggle_aria_close = ! empty( $atts['toggle_aria_close'] ) ? sanitize_text_field( $atts['toggle_aria_close'] ) : esc_html__( 'Close menu', 'total-theme-core' );
	if ( $toggle_aria_close ) {
		$output .= '<span class="screen-reader-text" data-close-text>' . esc_html( $toggle_aria_close ) . '</span>';
	}
$output .= '</button>';

// Menu Template
$aria_label = ! empty( $atts['aria_label'] ) ? sanitize_text_field( $atts['aria_label'] ) : esc_attr__( 'Menu', 'total-theme-core' );

$nav_class = 'vcex-off-canvas-menu-nav wpex-flex wpex-flex-grow wpex-w-100';

if ( ! empty( $atts['nav_align_items'] ) ) {
	$nav_class .= ' ' . vcex_parse_align_items_class( $atts['nav_align_items'] );
}

$off_canvas_content = '<template class="wpex-template"><nav class="' . esc_attr( trim( $nav_class ) ) . '" aria-label="' . \esc_attr( $aria_label ) . '">';

	// Parse some atts to prevent having to re-check for each item
	$atts['nav_centered'] = vcex_validate_att_boolean( 'nav_centered', $atts );
	$atts['sub_expanded'] = vcex_validate_att_boolean( 'sub_expanded', $atts );
	$atts['item_divider'] = vcex_validate_att_boolean( 'item_divider', $atts );
	$atts['sub_border_enable'] = ! $atts['nav_centered'] && vcex_validate_att_boolean( 'sub_border_enable', $atts, true );
	$atts['sub_arrow_enable'] = vcex_validate_att_boolean( 'sub_arrow_enable', $atts, true );
	$atts['item_transition_duration'] = ! empty( $atts['item_transition_duration'] ) ? \absint( $atts['item_transition_duration'] ) : 0;
	$atts['sub_arrow_icon'] = ! empty( $atts['sub_arrow_icon'] ) ? \sanitize_text_field( $atts['sub_arrow_icon'] ) : 'chevron';

	// Get wp nav
	$off_canvas_content .= wp_nav_menu( [
		'menu'            => absint( $menu_id ),
		'container'       => '',
		'container_class' => '',
		'container_id'    => '',
		'echo'            => false,
		'fallback_cb'     => false,
		'menu_class'      => 'vcex-off-canvas-menu-nav__list wpex-list-none wpex-m-0 wpex-p-0 wpex-w-100',
		'walker'          => totalthemecore_init_class( 'Vcex\Walkers\Nav_Menu_Off_Canvas' ),
		'vcex_atts'       => $atts,
		'vcex_counter'    => Vcex_Off_Canvas_Menu_Shortcode::get_counter(),
	] );

$off_canvas_content .= '</nav></template>';

// Off Canvas args.
$off_canvas_class = '';

if ( ! empty( $atts['vcex_class'] ) ) {
	$off_canvas_class .= ' ' . sanitize_html_class( $atts['vcex_class'] );
}

if ( ! empty( $atts['width'] ) && '100%' === $atts['width'] ) {
	$off_canvas_class .= ' wpex-max-w-none';
}

$off_canvas_args = [
	'id'            => $off_canvas_menu_id,
	'class'         => trim( $off_canvas_class ),
	'under_header'  => $under_header,
	'placement'     => vcex_validate_att_boolean( 'swap_side', $atts ) ? 'right' : 'left',
	'top_border'    => vcex_validate_att_boolean( 'top_border_enable', $atts, true ),
	'bottom_border' => vcex_validate_att_boolean( 'bottom_border_enable', $atts ),
	'close_button'  => vcex_validate_att_boolean( 'close_btn_enable', $atts, true ),
	'fixed_footer'  => vcex_validate_att_boolean( 'fixed_footer', $atts, true ),
	'auto_insert'   => false,
];

if ( ! empty( $visibility_class ) ) {
	$off_canvas_args['visibility'] = $visibility_class;
}

if ( ! empty( $atts['width'] ) && '100%' === $atts['width'] ) {
	$off_canvas_args['backdrop'] = false;
	if ( $under_header ) {
		$off_canvas_args['contain'] = true;
	}
} elseif ( ! vcex_validate_att_boolean( 'backdrop', $atts, true ) ) {
	$off_canvas_args['backdrop'] = false;
}

if ( ! empty( $atts['transition_duration'] ) ) {
	$off_canvas_args['transition_duration'] = absint( $atts['transition_duration'] );
}

if ( ! empty( $atts['title'] ) ) {
	$off_canvas_args['title'] = sanitize_text_field( $atts['title'] );
}

if ( ! empty( $atts['logo'] ) ) {
	$off_canvas_args['logo'] = absint( $atts['logo']['id'] ?? $atts['logo'] );
}

if ( ! empty( $atts['bottom_template'] ) ) {
	$off_canvas_footer = absint( $atts['bottom_template'] );
} elseif ( ! empty( $atts['bottom_button_link'] ) && ! empty( $atts['bottom_button_text'] ) ) {
	$off_canvas_footer = '<a href="' . esc_url( $atts['bottom_button_link'] ) . '" class="theme-button wpex-w-100">' . \esc_html( vcex_parse_text( $atts['bottom_button_text'] ) ) . '</a>';
}

if ( ! empty( $toggle_aria_close ) ) {
	$off_canvas_args['close_button_aria_label'] = $toggle_aria_close;
}

// Initialize off canvas element
$off_canvas = new TotalTheme\Off_Canvas( $off_canvas_args, $off_canvas_content, $off_canvas_footer ?? '' );
$output .= $off_canvas->render();

// Ouput everything
echo vcex_parse_html( 'div', [
	'class' => $class,
], $output );
