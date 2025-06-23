<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_header_megamenus'] = [
	'title' => esc_html__( 'Mega Menus', 'total' ),
	'panel' => 'wpex_header',
	'settings' => [
		[
			'id' => 'megamenu_stretch',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Full-width', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'This will place your megamenus at the bottom of the header and stretch them to the same width as your header. If disabled the megamenus will display like the other menus right under the link and only as wide as the menu itself.', 'total' ),
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => [ 'one', 'seven', 'eight', 'nine', 'ten' ],
			],
		],
		[
			'id' => 'megamenu_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Outer Padding', 'total' ),
				'placeholder' => '0',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-padding',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_gutter',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Gutter', 'total' ),
				'description' => esc_html__( 'Spacing between columns.', 'total' ),
				'placeholder' => '24',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-gutter',
				'sanitize' => 'px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_heading_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Heading Font Size', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-heading-font-size',
				'sanitize' => 'font_size',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_heading_margin_bottom',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Heading Margin Bottom', 'total' ),
				'placeholder' => '0',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-heading-margin-bottom',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_heading_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Heading Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-heading-color',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_heading_font_weight',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Heading Font Weight', 'total' ),
				'choices' => 'font_weight',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-heading-font-weight',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_divider_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Divider Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-divider-color',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_divider_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Divider Width', 'total' ),
				'placeholder' => '1',
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap',
				'alter' => '--wpex-megamenu-divider-width',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_link_padding_y',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Link Vertical Padding', 'total' ),
				'placeholder' => esc_html__( 'inherit', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap .megamenu',
				'alter' => '--wpex-dropmenu-link-padding-y',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
		[
			'id' => 'megamenu_link_padding_x',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'exclude_units' => [ '%' ],
				'label' => esc_html__( 'Link Horizontal Padding', 'total' ),
				'placeholder' => esc_html__( 'inherit', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-navigation-wrap .megamenu',
				'alter' => '--wpex-dropmenu-link-padding-x',
				'sanitize' => 'fallback_px',
			],
			'control_display' => [
				'check' => 'header_style',
				'value' => 'dev',
				'compare' => 'not_equal',
			],
		],
	],
];
