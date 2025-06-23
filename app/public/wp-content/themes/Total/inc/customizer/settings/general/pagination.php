<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_pagination'] = [
	'title' => esc_html__( 'Numbered Pagination', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'pagination_align',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Alignment', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'left' => esc_html__( 'Left', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'right' => esc_html__( 'Right', 'total' ),
				],
			],
		],
		[
			'id' => 'pagination_arrow',
			'transport' => 'postMessage',
			'default' => 'angle',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Arrow Style', 'total' ),
				'choices' => [
					'angle' => esc_html__( 'Angle', 'total' ),
					'arrow' => esc_html__( 'Arrow', 'total' ),
					'caret' => esc_html__( 'Caret', 'total' ),
					'chevron' => esc_html__( 'Chevron', 'total' ),
				],
			],
		],
		[
			'id' => 'pagination_gutter',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Gutter', 'total' ),
				'placeholder' => '-1',
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-gutter',
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'pagination_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Font Size', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-font-size',
			],
		],
		[
			'id' => 'pagination_item_dims',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Item Dimensions', 'total' ),
				'description' => esc_html__( 'Will apply a min-height and min-width to each pagination item. Useful for creating perfect squares or circles.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [ '--wpex-pagination-link-min-height', '--wpex-pagination-link-min-width' ],
			],
		],
		[
			'id' => 'pagination_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'label' => esc_html__( 'Padding', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-link-padding',
			],
		],
		[
			'id' => 'pagination_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Border Radius', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-border-radius',
			],
		],
		[
			'id' => 'pagination_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem', 'var', 'func' ],
				'label' => esc_html__( 'Border Width', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-border-width',
			],
		],
		[
			'id' => 'pagination_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-border-color',
			],
		],
		[
			'id' => 'pagination_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-link-color',
			],
		],
		[
			'id' => 'pagination_hover_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-hover-pagination-link-color',
					'--wpex-active-pagination-link-color',
				],
			],
		],
		[
			'id' => 'pagination_hover_active',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color: Active', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-active-pagination-link-color',
			],
		],
		[
			'id' => 'pagination_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-pagination-link-bg',
			],
		],
		[
			'id' => 'pagination_hover_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [
					'--wpex-hover-pagination-link-bg',
					'--wpex-active-pagination-link-bg',
				],
			],
		],
		[
			'id' => 'pagination_active_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background: Active', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-active-pagination-link-bg',
			],
		],
	],
];
