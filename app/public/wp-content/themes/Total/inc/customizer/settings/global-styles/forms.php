<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_forms'] = [
	'title' => esc_html__( 'Forms', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'label_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Label Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-label-color',
			],
		],
		[
			'id' => 'input_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'shorthand' => true,
				'label' => esc_html__( 'Input Padding', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-input-padding',
			],
		],
		[
			'id' => 'input_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem', '%', 'var' ],
				'placeholder' => '4',
				'label' => esc_html__( 'Input Border Radius', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-input-border-radius',
			],
		],
		[
			'id' => 'input_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'default_unit' => 'em',
				'placeholder' => '1',
				'label' => esc_html__( 'Input Font-Size', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-input-font-size',
			],
		],
		[
			'id' => 'input_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Input Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-input-color',
					'--wpex-focus-input-color',
				],
			],
		],
		[
			'id' => 'input_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Input Background', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-input-bg',
					'--wpex-focus-input-bg',
				],
			],
		],
		[
			'id' => 'input_border',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Input Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-input-border-color',
					'--wpex-focus-input-border-color',
				],
			],
		],
		[
			'id' => 'input_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'placeholder' => '1',
				'label' => esc_html__( 'Input Border Width', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-input-border-width',
			],
		],
		[
			'id' => 'input_color_focus',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Input Focus: Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-focus-input-color',
			],
		],
		[
			'id' => 'input_background_focus',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Input Focus: Background', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-focus-input-bg',
			],
		],
		[
			'id' => 'input_border_focus',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Input Focus: Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-focus-input-border-color',
			],
		],
	],
];
