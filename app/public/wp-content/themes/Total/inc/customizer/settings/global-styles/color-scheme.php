<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_color_scheme'] = [
	'title' => esc_html__( 'Color Scheme', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'wpex_surface_1_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Surface 1', 'total' ),
				'description' => esc_html__( 'Main background color.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-surface-1',
			],
		],
		[
			'id' => 'wpex_surface_2_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Surface 2', 'total' ),
				'description' => esc_html__( 'Used for secondary element backgrounds such as page header title, footer callout, author bio, etc.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-surface-2',
			],
		],
		[
			'id' => 'wpex_surface_3_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Surface 3', 'total' ),
				'description' => esc_html__( 'Used for borders around elements using a Surface 2 background.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-surface-3',
			],
		],
		[
			'id' => 'wpex_surface_4_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Surface 4', 'total' ),
				'description' => esc_html__( 'Used in a similar manner as surface 3 but providing greater contrast.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-surface-4',
			],
		],
		[
			'id' => 'wpex_text_1_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Text 1', 'total' ),
				'description' => esc_html__( 'Headings and bold text.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-text-1',
			],
		],
		[
			'id' => 'wpex_text_2_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Text 2', 'total' ),
				'description' => esc_html__( 'Primary text color.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-text-2',
			],
		],
		[
			'id' => 'wpex_text_3_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Text 3', 'total' ),
				'description' => esc_html__( 'Subtext such as dates and meta.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-text-3',
			],
		],
		[
			'id' => 'wpex_text_4_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'exclude_colors' => 'extra,theme',
				'label' => esc_html__( 'Text 4', 'total' ),
				'description' => esc_html__( 'Subtext with the lowest emphasis.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-text-4',
			],
		],
	],
];
