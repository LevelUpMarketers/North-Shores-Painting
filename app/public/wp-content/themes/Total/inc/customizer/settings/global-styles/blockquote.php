<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_blockquote'] = [
	'title' => esc_html__( 'Blockquote', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'blockquote_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => 'blockquote',
				'alter' => 'background',
			],
		],
		[
			'id' => 'blockquote_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => 'blockquote',
				'alter' => 'color',
			],
		],
		[
			'id' => 'blockquote_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'label' => esc_html__( 'Padding', 'total' ),
			],
			'inline_css' => [
				'target' => 'blockquote',
				'alter' => 'padding',
			],
		],
		[
			'id' => 'blockquote_border_width',
			'control' => [
				'label' => esc_html__( 'Side Border Width', 'total' ),
				'description' => esc_html__( 'When choosing a side border width the blockquote design will be changed from a quote style to a left border design.', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'1px' => '1px',
					'2px' => '2px',
					'3px' => '3px',
					'4px' => '4px',
					'5px' => '5px',
				],
			],
		],
		[
			'id' => 'blockquote_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => 'blockquote',
				'alter' => 'border-color',
			],
		],
	],
];
