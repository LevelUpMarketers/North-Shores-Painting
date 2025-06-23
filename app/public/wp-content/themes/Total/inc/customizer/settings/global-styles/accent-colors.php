<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_accent_colors'] = [
	'title' => esc_html__( 'Accent Colors', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'accent_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Accent', 'total' ),
				'type' => 'totaltheme_color',
				'description' => esc_html__( 'Main accent color used for buttons, links, active elements, etc.', 'total' ),
				'color_slug' => 'accent',
				'exclude_colors' => 'extra,theme',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-accent',
					'--wpex-accent-alt',
				],
			],
		],
		[
			'id' => 'accent_on_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'On Accent', 'total' ),
				'type' => 'totaltheme_color',
				'description' => esc_html__( 'Text color for elements with an accent background.', 'total' ),
				'color_slug' => 'on-accent',
				'exclude_colors' => 'extra,theme',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-on-accent',
					'--wpex-on-accent-alt',
				],
			],
		],
		[
			'id' => 'accent_color_hover',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Accent Alt', 'total' ),
				'description' => esc_html__( 'Default hover background color for buttons or an alternative accent color.', 'total' ),
				'type' => 'totaltheme_color',
				'color_slug' => 'accent-alt',
				'exclude_colors' => 'extra,theme',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-accent-alt',
			],
		],
		[
			'id' => 'accent_on_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'On Accent Alt', 'total' ),
				'type' => 'totaltheme_color',
				'color_slug' => 'on-accent-alt',
				'exclude_colors' => 'extra,theme',
				'description' => esc_html__( 'Text color for elements with an alternative accent background.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-on-accent-alt',
			],
		],
		[
			'id' => 'main_border_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Borders', 'total' ),
				'type' => 'totaltheme_color',
				'description' => esc_html__( 'Primary border color used for sidebar borders, entry dividers, tables, pagination, minimal buttons, tabs, toggles, etc.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-border-main',
			],
		],
	]
];