<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_sidebar'] = [
	'title' => esc_html__( 'General', 'total' ),
	'settings' => [
		[
			'id' => 'sidebar_sticky',
			'control' => [
				'label' => esc_html__( 'Sticky Sidebar', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'sidebar_hook',
			'transport' => 'postMessage',
			'default' => 'after',
			'control' => [
				'label' => esc_html__( 'Mobile Placement', 'total' ),
				'type' => 'select',
				'choices' => [
					'after'  => esc_html__( 'After the Content', 'total' ),
					'before' => esc_html__( 'Before the Content', 'total' ),
					'dynamic' => esc_html__( 'Dynamic (Before content when using a left sidebar)', 'total' ),
				],
			],
		],
		[
			'id' => 'has_widget_icons',
			'transport' => 'postMessage',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Widget Icons', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Certain widgets include little icons such as the recent posts widget. Here you can toggle the icons on or off.', 'total' ),
			],
		],
		// General Design
		[
			'id' => 'sidebar_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'sidebar_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'label' => esc_html__( 'Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar',
				'alter' => 'padding',
			],
		],
		[
			'id' => 'sidebar_text_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar', // removed "p" target in 5.4
				'alter' => 'color',
			],
		],
		[
			'id' => 'sidebar_borders_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Borders Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar',
				'alter' => [
					'--wpex-border-main',
					'--wpex-table-cell-border-color',
				],
			],
		],
		[
			'id' => 'sidebar_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar',
				'alter' => [
					'--wpex-link-color',
					'--wpex-hover-link-color',
					'--wpex-widget-link-color',
				],
			],
		],
		[
			'id' => 'sidebar_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar',
				'alter' => [
					'--wpex-hover-link-color',
					'--wpex-hover-widget-link-color',
				],
			],
		],
		// Widget Titles
		[
			'id' => 'sidebar_heading_widget_title',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Widget Titles', 'total' ),
			],
		],
		[
			'id' => 'sidebar_headings',
			'default' => 'div',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Widget Title HTML Tag', 'total' ),
				'type' => 'select',
				'choices' => [
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
					'span' => 'span',
					'div' => 'div',
				],
			],
		],
		[
			'id' => 'sidebar_headings_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Widget Title Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar .widget-title',
				'alter' => 'color',
			],
		],
		[
			'id' => 'sidebar_headings_background',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Widget Title Background', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar .widget-title',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'sidebar_headings_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'label' => esc_html__( 'Widget Title Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '#sidebar .widget-title',
				'alter' => 'padding',
			],
		],
		[
			'id' => 'sidebar_headings_align',
			'transport' => 'postMessage',
			'control' =>  [
				'type' => 'select',
				'label' => esc_html__( 'Widget Title Text Align', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default','total' ),
					'left' => esc_html__( 'Left','total' ),
					'right' => esc_html__( 'Right','total' ),
					'center' => esc_html__( 'Center','total' ),
				],
			],
			'inline_css' => [
				'target' => '#sidebar .widget-title',
				'alter' => 'text-align',
			],
		],
	],
];
