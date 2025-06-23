<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_header_menu_dropdowns'] = [
	'title' => esc_html__( 'Menu Dropdowns', 'total' ),
	'panel' => 'wpex_header',
	'settings' => [
		[
			'id' => 'menu_arrow_down',
			'control' => [
				'label' => esc_html__( 'Top Level Dropdown Arrows', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'menu_arrow_side',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Sub Dropdown Arrows', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'menu_dropdown_caret',
			'default' => true,
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Dropdown Pointer', 'total' ),
				'type' => 'totaltheme_toggle',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::can_menu_dropdown_pointer',
			],
		],
		[
			'id' => 'menu_dropdown_top_border',
			'control' => [
				'label' => esc_html__( 'Colored Top Border', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'menu_drodown_animate',
			'control' => [
				'label' => esc_html__( 'Dropdown Animation', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'menu_drodown_animate_offset',
			'transport' => 'postMessage',
			'sanitize_callback' => 'TotalTheme\Customizer\Sanitize_Callbacks::pixel',
			'control' => [
				'label' => esc_html__( 'Dropdown Animation Offset', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'placeholder' => '0',
			],
			'control_display' => [
				'check' => 'menu_drodown_animate',
				'value' => 'true',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-dropmenu-animate-offset',
					'--wpex-sf-menu-animate-offset'
				],
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'menu_dropdown_method',
			'default' => 'hover',
			'control' => [
				'label' => esc_html__( 'Dropdown Method', 'total' ),
				'type' => 'select',
				'choices' => [
					'hover' => esc_html__( 'CSS Hover', 'total' ),
					'click' => esc_html__( 'On Click', 'total' ),
					'sfhover' => esc_html__( 'Superfish JS', 'total' ),
				],
			],
		],
		[
			'id' => 'menu_arrow',
			'default' => 'angle',
			'control' => [
				'label' => esc_html__( 'Dropdown Arrow Type', 'total' ),
				'type' => 'select',
				'choices' => [
					'angle' => esc_html__( 'Angle', 'total' ),
					'angle-double' => esc_html__( 'Angle Double', 'total' ),
					'chevron' => esc_html__( 'Chevron', 'total' ),
					'caret' => esc_html__( 'Caret', 'total' ),
					'arrow' => esc_html__( 'Arrow', 'total' ),
					'arrow-circle' => esc_html__( 'Arrow Circle', 'total' ),
					'plus' => esc_html__( 'Plus', 'total' ),
				],
			],
		],
		[
			'id' => 'menu_arrow_size',
			'default' => 'xs',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Dropdown Arrow Size', 'total' ),
				'type' => 'select',
				'choices' =>  [
					'2xs' =>\esc_html__( '2x Small', 'total' ),
					'xs'  => \esc_html__( 'x Small', 'total' ),
					'sm'  => \esc_html__( 'Small', 'total' ),
					''    => \esc_html__( 'Inherit from Menu', 'total' ),
				],
				'description' => esc_html__( 'Because the dropdown arrow is displayed inline you must choose from preset sizes in order for the icon to properly align with your menu item text.', 'total' ),
			],
		],
		[
			'id' => 'menu_dropdown_style',
			'default' => 'default',
			'control' => [
				'label' => esc_html__( 'Dropdown Style', 'total' ),
				'type' => 'select',
				'choices' => wpex_get_menu_dropdown_styles(),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'menu_dropdown_dropshadow',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Dropdown Dropshadow Style', 'total' ),
				'type' => 'select',
				// @todo support utility shadows?
				'choices' => [
					'' => esc_html__( 'None', 'total' ),
					'one' => esc_html__( 'One', 'total' ),
					'two' => esc_html__( 'Two', 'total' ),
					'three' => esc_html__( 'Three', 'total' ),
					'four' => esc_html__( 'Four', 'total' ),
					'five' => esc_html__( 'Five', 'total' ),
					'six' => esc_html__( 'Six', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_min_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Minimum Width', 'total' ),
				'placeholder' => '140',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-dropmenu-min-width',
				'sanitize' => 'px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_max_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Maximum Width', 'total' ),
				'placeholder' => '320',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-dropmenu-max-width',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'dropdown_menu_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Dropdown Padding', 'total' ),
				'description' => esc_html__( 'This padding gets added inside the dropdown around all the menu items.', 'total' ),
				'placeholder' => '0',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-dropmenu-padding',
					'--wpex-megamenu-padding',
				],
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_link_padding_y',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Link Vertical Padding', 'total' ),
				'placeholder' => '8',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-dropmenu-link-padding-y',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_link_padding_x',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Link Horizontal Padding', 'total' ),
				'placeholder' => '12',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-dropmenu-link-padding-x',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-dropmenu-bg',
					'--wpex-dropmenu-caret-bg',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		// Borders
		[
			'id' => 'dropdown_menu_borders',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-dropmenu-border-color',
					'--wpex-dropmenu-caret-border-color',
					'--wpex-megamenu-divider-color',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'menu_dropdown_top_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Colored Top Border', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_menu_dropdown_top_border',
			],
			'inline_css' => [
				'target' => ':root', // must target root since some items are outside of the menu.
				'alter' => '--wpex-dropmenu-colored-top-border-color',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		// Link color
		[
			'id' => 'dropdown_menu_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-dropmenu-link-color',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-hover-dropmenu-link-color',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_link_hover_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Background: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-hover-dropmenu-link-bg',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_link_color_active',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Current Menu Item', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-active-dropmenu-link-color',
					'--wpex-active-hover-dropmenu-link-color',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'dropdown_menu_link_bg_active',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Background: Current Menu Item', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => [
					'--wpex-active-dropmenu-link-bg',
					'--wpex-active-hover-dropmenu-link-bg',
				],
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
	],
];
