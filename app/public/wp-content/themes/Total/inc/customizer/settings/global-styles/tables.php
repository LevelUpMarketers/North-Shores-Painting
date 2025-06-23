<?php
defined( 'ABSPATH' ) || exit;

$this->sections['wpex_tables'] = [
	'title' => esc_html__( 'Tables', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'thead_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Table Header Background', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-table-thead-bg',
			],
		],
		[
			'id' => 'thead_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Table Header Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-table-thead-color',
			],
		],
		[
			'id' => 'tables_th_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Th Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-table-th-color',
			],
		],
		[
			'id' => 'tables_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Cells Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-table-cell-border-color',
			],
		],
		[
			'id' => 'tables_cell_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'shorthand' => true,
				'label' => esc_html__( 'Cell Padding', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-table-cell-padding',
			],
		],
	],
];
