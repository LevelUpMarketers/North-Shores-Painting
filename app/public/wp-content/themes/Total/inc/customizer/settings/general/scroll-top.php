<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_scroll_top'] = [
	'title' => esc_html__( 'Scroll Top Button', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'scroll_top',
			'default' => true,
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Scroll Top Button', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'scroll_top_style',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Style', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'black' => esc_html__( 'Black', 'total' ),
					'accent' => esc_html__( 'Accent', 'total' ),
					'icon' => esc_html__( 'Icon Only', 'total' ),
				],
			],
		],
		[
			'id' => 'scroll_top_breakpoint',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Breakpoint', 'total' ),
				'type' => 'select',
				'choices' => 'breakpoint',
				'description' => esc_html__( 'Select the breakpoint at which point the scroll to button becomes visible. By default it is visible on all devices.', 'total' ),
			],
		],
		[
			'id' => 'scroll_top_arrow',
			'default' => 'chevron-up',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Arrow', 'total' ),
				'type' => 'totaltheme_icon',
				'choices' => [
					// Theme Icons.
					'chevron-up',
					'caret-up',
					'angle-up',
					'angle-double-up',
					'long-arrow-up',
					'arrow-circle-o-up',
					'arrow-up',
					'caret-square-o-up',
					'level-up',
					'sort-up',
					'toggle-up',
					// Custom
					'material-arrow-up-ios',
					'material-arrow-upward',
					'ionicons-arrow-up',
					'ionicons-arrow-up-sharp',
				],
			],
		],
		[
			'id' => 'scroll_top_shadow',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Shadow', 'total' ),
				'type' => 'select',
				'choices' => 'shadow',
			],
		],
		[
			'id' => 'scroll_top_speed',
			'transport' => 'refresh',
			'control' => [
				'label' => esc_html__( 'Local Scroll Speed in Milliseconds', 'total' ),
				'type' => 'text',
				'input_attrs' => [
					'placeholder' => '1000',
				],
			],
			'control_display' => [
				'check' => 'scroll_to_easing',
				'value' => 'true',
			],
		],
		[
			'id' => 'local_scroll_reveal_offset',
			'control' => [
				'label' => esc_html__( 'Reveal Offset', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'description' => esc_html__( 'Offset in pixels at which point the button becomes visible when scrolling down.', 'total' ),
			],
		],
		[
			'id' => 'scroll_top_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Button Size', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top',
				'sanitize' => 'fallback_px',
				'alter' => [ 'min-width', 'min-height' ],
			],
		],
		[
			'id' => 'scroll_top_icon_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Icon Size', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top',
				'alter' => 'font-size',
			],
		],
		[
			'id' => 'scroll_top_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Border Radius', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top',
				'alter' => 'border-radius',
				'important' => true, // needs to override utility classes.
			],
		],
		[
			'id' => 'scroll_top_right_position',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Side Position', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top',
				'alter' => 'margin-inline-end',
			],
		],
		[
			'id' => 'scroll_top_bottom_position',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Bottom Position', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top',
				'alter' => 'margin-block-end',
			],
		],
		[
			'id' => 'scroll_top_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'scroll_top_bg_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top:hover',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'scroll_top_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Icon Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top',
				'alter' => 'color',
			],
		],
		[
			'id' => 'scroll_top_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Icon Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-scroll-top:hover',
				'alter' => 'color',
			],
		],
	],
];
