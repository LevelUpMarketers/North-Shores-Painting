<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_buttons'] = [
	'title' => esc_html__( 'Buttons', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'theme_button_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'shorthand' => true,
				'label' => esc_html__( 'Padding', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-btn-padding',
			],
		],
		[
			'id' => 'theme_button_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem', '%', 'var' ],
				'placeholder' => '3',
				'label' => esc_html__( 'Border Radius', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-btn-border-radius',
			],
		],
		[
			'id' => 'theme_button_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-btn-color',
			],
		],
		[
			'id' => 'theme_button_hover_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-hover-btn-color',
			],
		],
		[
			'id' => 'theme_button_active_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color: Active', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-active-btn-color',
			],
		],
		[
			'id' => 'theme_button_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-btn-bg',
			],
		],
		[
			'id' => 'theme_button_hover_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-hover-btn-bg',
			],
		],
		[
			'id' => 'theme_button_active_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background: Active', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-active-btn-bg',
			],
		],
		[
			'id' => 'theme_button_border_style',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Border Style', 'total' ),
				'choices' => [
					'' => esc_html__( 'None', 'total' ),
					'solid' => esc_html__( 'Solid', 'total' ),
					'dashed' => esc_html__( 'Dashed', 'total' ),
					'none' => esc_html__( 'None', 'total' ),
				],
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-btn-border-style',
			],
		],
		[
			'id' => 'theme_button_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Border Width', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-btn-border-width',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'theme_button_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-btn-border-color',
			],
		],
		[
			'id' => 'theme_button_hover_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-hover-btn-border-color',
			],
		],
		[
			'id' => 'theme_button_active_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color: Active', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-active-btn-border-color',
			],
		],
	],
];
