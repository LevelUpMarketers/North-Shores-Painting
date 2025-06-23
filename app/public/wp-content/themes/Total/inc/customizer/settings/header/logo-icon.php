<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_header_logo_icon'] = [
	'title' => esc_html__( 'Logo Icon', 'total' ),
	'description' => esc_html__( 'The logo icon is used only when displaying a text based logo.', 'total' ),
	'panel' => 'wpex_header',
	'settings' => [
		[
			'id' => 'logo_icon',
			'control' => [
				'label' => esc_html__( 'Icon', 'total' ),
				'type' => 'totaltheme_icon',
			],
			'control_display' => [
				'check' => 'logo_icon_img',
				'value' => 'false',
			],
		],
		[
			'id' => 'logo_icon_img',
			'control' => [
				'label' => esc_html__( 'Custom Icon', 'total' ),
				'type' => 'media',
				'mime_type' => 'image',
				'description' => esc_html__( 'Choose a custom icon to use instead of a theme font icon. An SVG image would be recommended.', 'total' ),
			],
		],
		[
			'id' => 'logo_icon_img_dims',
			'control' => [
				'label' => esc_html__( 'Icon Width', 'total' ),
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'description' => esc_html__( 'Required for SVG images.', 'total' ),
			],
			'control_display' => [
				'check' => 'logo_icon_img',
				'value' => 'true',
			],
		],
		[
			'id' => 'logo_icon_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Logo Icon Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#site-logo-fa-icon',
				'alter' => 'color',
			],
			'control_display' => [
				'check' => 'logo_icon_img',
				'value' => 'false',
			],
		],
		[
			'id' => 'logo_icon_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Logo Icon Size', 'total' ),
				'sanitize_callback' => 'sanitize_text_field',
			],
			'inline_css' => [
				'target' => '#site-logo-fa-icon',
				'alter' => 'font-size',
			],
			'control_display' => [
				'check' => 'logo_icon_img',
				'value' => 'false',
			],
		],
		[
			'id' => 'logo_icon_right_margin',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Logo Icon Right Margin', 'total' ),
				'sanitize_callback' => 'sanitize_text_field',
				'placeholder' => '10',
			],
			'inline_css' => [
				'target' => '#site-logo-fa-icon, #site-logo-icon',
				'alter' => 'margin-inline-end',
				'sanitize' => 'margin',
			],
		],
	],
];
