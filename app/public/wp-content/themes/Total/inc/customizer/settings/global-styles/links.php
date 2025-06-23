<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_links'] = [
	'title' => esc_html__( 'Links', 'total' ),
	'panel' => 'wpex_global_styles',
	'settings' => [
		[
			'id' => 'link_underline',
			'default' => false,
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'Underline Links', 'total' ),
				'description' => esc_html__( 'Applies to "standard" links only.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-link-decoration-line',
				'value' => 'underline',
			],
		],
		[
			'id' => 'link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Links Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => [ '--wpex-link-color', '--wpex-hover-heading-link-color' ],
			],
		],
		[
			'id' => 'link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Links Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-hover-link-color',
			],
		],
		[
			'id' => 'link_underline_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Underline Color', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-link-decoration-color',
			],
		],
		[
			'id' => 'link_underline_offset',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem' ],
				'label' => esc_html__( 'Link Underline Offset', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-link-underline-offset',
			],
		],
		[
			'id' => 'link_underline_thickness',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px', 'em', 'rem' ],
				'label' => esc_html__( 'Link Underline Thickness', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-link-decoration-thickness',
			],
		],
		[
			'id' => 'heading_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Headings with Links Hover Color', 'total' ),
				'description' => esc_html__( 'By default headings that have links will display using the heading color but will use the link color on hover. You can use this setting to control the hover affect on headings with links.', 'total' ),
			],
			'inline_css' => [
				'target' => ':root',
				'alter' => '--wpex-hover-heading-link-color',
			],
		],
	],
];
