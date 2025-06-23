<?php
defined( 'ABSPATH' ) || exit;

$this->sections['wpex_header_menu'] = [
	'title' => esc_html__( 'Menu', 'total' ),
	'panel' => 'wpex_header',
	'settings' => [
		[
			'id' => 'header_menu_disable_outline',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Disable Outline', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'two', 'three', 'four' ],
			],
		],
		[
			'id' => 'header_menu_disable_borders',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Disable Inner Borders', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'two', 'six' ],
			],
		],
		[
			'id' => 'header_menu_center',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Center Menu Items', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'two',
			],
		],
		[
			'id' => 'header_menu_stretch_items',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Fill Space', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to make your menu items fill up the available navigation area.', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'two', 'three', 'four', 'five' ],
			],
		],
		[
			'id' => 'menu_flush_dropdowns',
			'control' => [
				'label' => esc_html__( 'Full-Height', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'When enabled your menu li elements will display at the same height as your header so that your dropdowns line up with the bottom of the header.', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'one', 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'header_menu_height',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Custom Menu Height', 'total' ),
				'placeholder' => '50',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-main-nav-height',
					'--wpex-main-nav-line-height',
				],
				'sanitize' => 'px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'one', 'two', 'three', 'four', 'five' ],
			],
		],
		[
			'id' => 'main_nav_gutter',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Gutter', 'total' ),
				'description' => esc_html__( 'Applies a left margin to the top level menu items.', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap', // must target this to override certain things.
				'alter' => '--wpex-main-nav-gutter',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'one', 'two', 'three', 'four', 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'main_nav_link_padding_y',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Menu Item Vertical Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '.navbar-style-seven,.navbar-style-eight,.navbar-style-nine,.navbar-style-ten',
				'alter' => '--wpex-main-nav-link-padding-y',
				'sanitize' => 'padding',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'main_nav_link_padding_x',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Menu Item Horizontal Padding', 'total' ),
				'description' => esc_html__( 'Applies a left and right padding to the top level menu items.', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap', // must target this to override certain things.
				'alter' => '--wpex-main-nav-link-padding-x',
				'sanitize' => 'padding',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		/*** Active Item ***/
		[
			'id' => 'menu_active_underline',
			//'transport' => 'postMessage', // can't
			'control' => [
				'label' => esc_html__( 'Hover/Active Underline', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_menu_active_underline', // can't use control display, cause the next setting needs to check for this one via active_callback.
			],
		],
		[
			'id' => 'menu_active_underline_height',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Underline Size', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_menu_active_underline',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-main-nav-link-underline-height',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'menu_active_underline_speed',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Underline Animation Speed', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_menu_active_underline',
				'choices' => [
					''       => esc_html__( 'Default', 'total' ),
					'0s'     => '0ms',
					'75ms'   => '75ms',
					'100ms'  => '100ms',
					'150ms'  => '150ms',
					'200ms'  => '200ms',
					'300ms'  => '300ms',
					'500ms'  => '500ms',
					'700ms'  => '700ms',
					'1000ms' => '1000ms',
				],
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-main-nav-link-underline-transition-duration',
			],
		],
		[
			'id' => 'menu_active_underline_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Underline Color', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_menu_active_underline',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-main-nav-link-underline-color',
			],
		],
		[
			'id' => 'menu_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
				'description' => esc_html__( 'Not applied to the transparent header.', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-main-nav-bg',
			],
		],
		[
			'id' => 'menu_borders',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Borders', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'two', 'three', 'four', 'six' ],
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-main-nav-border-color',
			],
		],
		// Menu Link Colors
		[
			'id' => 'menu_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-main-nav-link-color',
					'--wpex-hover-main-nav-link-color',
					'--wpex-active-main-nav-link-color',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'menu_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-hover-main-nav-link-color',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'menu_link_color_active',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Current Menu Item', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-active-main-nav-link-color',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		// Link Background
		[
			'id' => 'menu_link_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Background', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-main-nav-link-bg',
					'--wpex-hover-main-nav-link-bg',
					'--wpex-active-main-nav-link-bg',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'menu_link_hover_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Background: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-hover-main-nav-link-bg',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'menu_link_active_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Background: Current Menu Item', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-active-main-nav-link-bg',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		// Link Inner
		[
			'id' => 'menu_link_span_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Inner Background', 'total' ),
			],
			'inline_css' => [
				// @todo add new class "navbar-supports-inner-bg" and target this.
				'target' => '.navbar-style-one,.navbar-style-two,.navbar-style-three,.navbar-style-four,.navbar-style-five,.navbar-style-six',
				'alter' => [
					'--wpex-main-nav-link-inner-bg',
					'--wpex-hover-main-nav-link-inner-bg',
					'--wpex-active-main-nav-link-inner-bg'
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'one', 'two', 'three', 'four', 'five', 'six' ],
			],
		],
		[
			'id' => 'menu_link_span_hover_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Inner Background: Hover', 'total' ),
			],
			'inline_css' => [
				// @todo add new class "navbar-supports-inner-bg" and target this.
				'target' => '.navbar-style-one,.navbar-style-two,.navbar-style-three,.navbar-style-four,.navbar-style-five,.navbar-style-six',
				'alter' => '--wpex-hover-main-nav-link-inner-bg',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'one', 'two', 'three', 'four', 'five', 'six' ],
			],
		],
		[
			'id' => 'menu_link_span_active_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Inner Background: Current Menu Item', 'total' ),
			],
			'inline_css' => [
				// @todo add new class "navbar-supports-inner-bg" and target this.
				'target' => '.navbar-style-one,.navbar-style-two,.navbar-style-three,.navbar-style-four,.navbar-style-five,.navbar-style-six',
				'alter' => '--wpex-active-main-nav-link-inner-bg',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'one', 'two', 'three', 'four', 'five', 'six' ],
			],
		],
	],
];
