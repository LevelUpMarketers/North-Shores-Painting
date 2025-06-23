<?php

defined( 'ABSPATH' ) || exit;

$vc_settings = [];

$vc_settings[] = [
	'id' => 'vcex_theme_style_is_default',
	'default' => true,
	'control' => [
		'type' => 'totaltheme_toggle',
		'label' => esc_html__( 'Use Theme Styles', 'total' ),
		'description' => esc_html__( 'Applies the theme style by default to WPBakery elements such as Tabs, Toggle, Accordion and Tour elements. Please save and refresh the live site in ordder to take affect.', 'total' ),
	],
];

$vc_settings[] = [
	'id' => 'vcex_heading_typography_tag_styles',
	'default' => false,
	'control' => [
		'type' => 'totaltheme_toggle',
		'label' => esc_html__( 'Custom Heading Typography', 'total' ),
		'description' => esc_html__( 'Enable to apply the settings under Typography > h1, h2, h3, h4 to the heading element.', 'total' ),
	],
];

$vc_settings[] = [
	'id' => 'vc_tta_animation_enable',
	'control' => [
		'type' => 'totaltheme_toggle',
		'label' => esc_html__( 'Tab Animations', 'total' ),
		'description' => esc_html__( 'Can be used to re-enable the default animation when switching sections in the WPBakery Tab, Tour and Accordion elements.', 'total' ),
	],
];

$vc_settings[] = [
	'id' => 'vc_row_bottom_margin',
	'default' => '40px',
	'control' => [
		'type' => 'totaltheme_length_unit',
		'units' => [ 'px', 'rem', 'var', 'func' ],
		'label' => esc_html__( 'Column Bottom Margin', 'total' ),
		'description' => esc_html__( 'Having a default bottom margin makes it easier for your website to be responsive so on mobile devices when columns stack they will automatically have space between them.', 'total' ),
	],
	'inline_css' => [
		'target' => ':root',
		'alter' => '--wpex-vc-column-inner-margin-bottom',
	],
];

$vc_settings[] = [
	'id' => 'vcex_heading_default_tag',
	'default' => 'div',
	'control' => [
		'type' => 'select',
		'label' => esc_html__( 'Heading Module Default HTML Tag', 'total' ),
		'choices' => [
			'div'  => 'div',
			'span' => 'span',
			'h1'   => 'h1',
			'h2'   => 'h2',
			'h3'   => 'h3',
			'h4'   => 'h4',
			'h5'   => 'h5',
		],
	],
];

$this->sections['wpex_visual_composer'] = [
	'title'    => esc_html__( 'General', 'total' ),
	'settings' => $vc_settings,
];
