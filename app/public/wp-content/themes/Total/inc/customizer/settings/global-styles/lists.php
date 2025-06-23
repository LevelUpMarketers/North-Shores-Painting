<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_lists'] = [
	'title' => esc_html__( 'Lists (ul/ol)', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'list_side_margin',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'List Side Margin', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem' ],
				'placeholder' => '30',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-list-margin-side',
			],
		],
		[
			'id' => 'child_list_side_margin',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Child List Side Margin', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem' ],
				'placeholder' => '30',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-child-list-margin-side',
			],
		],
		[
			'id' => 'list_style_type',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'UL List Style', 'total' ),
				'choices' => [
					'' => esc_html__( 'Browser Default', 'total' ),
					'disc' => esc_html__( 'Disc', 'total' ),
					'circle' => esc_html__( 'Circle', 'total' ),
					'square' => esc_html__( 'Square', 'total' ),
				],
			],
			'inline_css' => [
				'target' => 'ul',
				'alter' => 'list-style-type',
			],
		],
		[
			'id' => 'list_style_position',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'List Marker Position', 'total' ),
				'choices' => [
					'' => esc_html__( 'Outside', 'total' ),
					'inside' => esc_html__( 'Inside', 'total' ),
				],
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-list-style-position',
			],
		],
	],
];
