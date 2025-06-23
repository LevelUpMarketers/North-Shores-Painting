<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_breadcrumbs'] = [
	'title' => esc_html__( 'Breadcrumbs', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'breadcrumbs',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Breadcrumbs', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'breadcrumbs_show_trail_end',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Ending Trail Title', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'breadcrumbs_show_parents',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Show Parent Pages', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'breadcrumbs_show_terms',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Post Terms', 'total' ),
				'description' => esc_html__( 'Enable the display of post terms in the breadcrumbs which includes categories or your custom post type\'s primary taxonomy.', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'breadcrumbs_first_cat_only',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Primary Post Term Only', 'total' ),
				'description' => esc_html__( 'Only display the primary post term as defined via the Yoast SEO plugin or if you are not using that plugin, the first term ordered alphabetically.', 'total' ),
				'type' => 'totaltheme_toggle',
			],
			'control_display' => [
				'check' => 'breadcrumbs_show_terms',
				'value' => 'true',
			],
		],
		[
			'id' => 'breadcrumbs_visibility',
			'control' => [
				'label' => esc_html__( 'Visibility', 'total' ),
				'type' => 'totaltheme_visibility_select'
			],
		],
		[
			'id' => 'breadcrumbs_position',
			'transport' => 'refresh', // IMPORTANT !!!!
			'default' => 'page_header_aside',
			'control' => [
				'label' => esc_html__( 'Location', 'total' ),
				'type'  => 'select',
				'choices' => [
					'page_header_aside' => esc_html__( 'Page Header Aside', 'total' ),
					'page_header_content' => esc_html__( 'Page Header Content', 'total' ),
					'page_header_after' => esc_html__( 'After Page Header', 'total' ),
					'header_after' => esc_html__( 'After Site Header', 'total' ),
					'custom' => esc_html__( 'Custom', 'total' ),
				],
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_breadcrumbs',
			],
		],
		[
			'id' => 'breadcrumbs_home_title',
			'control' => [
				'label' => esc_html__( 'Custom Home Title', 'total' ),
				'type'  => 'text',
			],
		],
		[
			'id' => 'breadcrumbs_title_trim',
			'control' => [
				'label' => esc_html__( 'Title Trim Length', 'total' ),
				'type'  => 'text',
				'description' => esc_html__( 'Enter the max number of words to display for your breadcrumbs post title.', 'total' ),
			],
		],
		[
			'id' => 'breadcrumbs_separator',
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'label' => esc_html__( 'Separator', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter an HTML entity, keyboard character or shortcode.', 'total' ),
			],
		],
		[
			'id' => 'breadcrumbs_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_breadcrumbs',
			],
			'inline_css' => [
				'target' => '.site-breadcrumbs',
				'alter' => 'Background-color',
			],
		],
		[
			'id' => 'breadcrumbs_text_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_breadcrumbs',
			],
			'inline_css' => [
				'target' => '.site-breadcrumbs',
				'alter' => 'color',
			],
		],
		[
			'id' => 'breadcrumbs_seperator_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Separator Color', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_breadcrumbs',
			],
			'inline_css' => [
				'target' => '.site-breadcrumbs .sep',
				'alter' => 'color',
			],
		],
		[
			'id' => 'breadcrumbs_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_breadcrumbs',
			],
			'inline_css' => [
				'target' => '.site-breadcrumbs a',
				'alter' => 'color',
			],
		],
		[
			'id' => 'breadcrumbs_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color: Hover', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_breadcrumbs',
			],
			'inline_css' => [
				'target' => '.site-breadcrumbs a:hover',
				'alter' => 'color',
			],
		],
		[
			'id' => 'breadcrumbs_py',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Vertical Padding', 'total' ),
				'type'  => 'select',
				'choices' => 'padding',
			],
		],
		[
			'id' => 'breadcrumbs_mt',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Top Margin', 'total' ),
				'type'  => 'select',
				'choices' => 'margin',
			],
		],
		[
			'id' => 'breadcrumbs_mb',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Bottom Margin', 'total' ),
				'type'  => 'select',
				'choices' => 'margin',
			],
		],
	],
];
