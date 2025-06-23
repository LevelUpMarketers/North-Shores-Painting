<?php

defined( 'ABSPATH' ) || exit;

// General
$this->sections['wpex_callout_general'] = [
	'title' => esc_html__( 'General', 'total' ),
	'panel' => 'wpex_callout',
	'settings' => [
		[
			'id' => 'callout',
			'transport' => 'partialRefresh',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Callout', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'callout_visibility',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Visibility', 'total' ),
				'type' => 'totaltheme_visibility_select'
			],
		],
		[
			'id' => 'footer_callout_breakpoint',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Responsive Breakpoint', 'total' ),
				'type' => 'select',
				'choices' => 'breakpoint',
			],
		],
		[
			'id' => 'callout_top_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Top Padding', 'total' ),
				'placeholder' => '30',
			],
			'inline_css' => [
				'target' => '#footer-callout-wrap',
				'alter' => 'padding-block-start',
				'sanitize' => 'padding',
			],
		],
		[
			'id' => 'callout_bottom_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Bottom Padding', 'total' ),
				'placeholder' => '30',
			],
			'inline_css' => [
				'target' => '#footer-callout-wrap',
				'alter' => 'padding-block-end',
				'sanitize' => 'padding',
			],
		],
		[
			'id' => 'footer_callout_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout-wrap',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'footer_callout_bg_img',
			'control' => [
				'type' => 'media',
				'mime_type' => 'image',
				'label' => esc_html__( 'Background Image', 'total' ),
			],
		],
		[
			'id' => 'footer_callout_bg_img_style',
			'transport' => 'postMessage',
			'default' => '',
			'control' => [
				'label' => esc_html__( 'Background Image Style', 'total' ),
				'type'  => 'select',
				'choices' => 'bg_style',
			],
			'control_display' => [
				'check' => 'footer_callout_bg_img',
				'value' => 'true',
			],
		],
		[
			'id' => 'footer_callout_bg_position',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Background Image Position', 'total' ),
				'type'  => 'text',
				'description' => \esc_html__( 'Enter your custom background position.', 'total' ) . ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-position" target="_blank" rel="noopener noreferrer">' . \esc_html__( 'see mozilla docs', 'total' ) . ' &#8599;</a>)',
			],
			'control_display' => [
				'check' => 'footer_callout_bg_img',
				'value' => 'true',
			],
			'inline_css' => [
				'target' => '#footer-callout-wrap',
				'alter' => 'background-position',
			],
		],
		[
			'id' => 'footer_callout_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Border Width', 'total' ),
				'placeholder' => '1',
			],
			'inline_css' => [
				'target' => '#footer-callout-wrap',
				'alter' => [ 'border-top-width', 'border-bottom-width' ],
				'sanitize' => 'px',
			],
		],
		[
			'id' => 'footer_callout_border',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout-wrap',
				'alter' =>  [ 'border-top-color', 'border-bottom-color' ],
			],
		],
		[
			'id' => 'footer_callout_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout-wrap',
				'alter' => 'color',
			],
		],
		[
			'id' => 'footer_callout_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Links', 'total' ),
			],
			'inline_css' => [
				'target' => '.footer-callout-content a',
				'alter' => 'color',
			],
		],
		[
			'id' => 'footer_callout_link_color_hover',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Links: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '.footer-callout-content a:hover',
				'alter' => 'color',
			],
		],
	],
];

// Aside
$this->sections['wpex_callout_aside_content'] = [
	'title' => esc_html__( 'Content', 'total' ),
	'panel' => 'wpex_callout',
	'settings' => [
		[
			'id' => 'callout_text',
			'transport' => 'partialRefresh',
			'default' => 'I am the footer call-to-action block, here you can add some relevant/important information about your company or product. I can be disabled in the Customizer.',
			'control' => [
				'label' => esc_html__( 'Text', 'total' ),
				'type' => 'textarea',
				'description' => esc_html__( 'HTML and shortcodes allowed.', 'total' ),
			],
		],
	],
];

// Button
$this->sections['wpex_callout_button'] = [
	'title' => esc_html__( 'Button', 'total' ),
	'panel' => 'wpex_callout',
	'settings' => [
		[
			'id' => 'callout_link',
			'transport' => 'partialRefresh',
			'default' => '#',
			'sanitize_callback' => 'sanitize_url',
			'control' => [
				'label' => esc_html__( 'Link URL', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Leave empty to disable button.', 'total' ),
			],
		],
		[
			'id' => 'callout_link_txt',
			'transport' => 'partialRefresh',
			'default' => 'Get In Touch',
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'label' => esc_html__( 'Link Text', 'total' ),
				'type' => 'text',
			],
		],
		[
			'id' => 'callout_button_target',
			'transport' => 'postMessage',
			'default' => 'blank',
			'control' => [
				'label' => esc_html__( 'Link Target', 'total' ),
				'type' => 'select',
				'choices' => [
					'blank' => esc_html__( 'Blank', 'total' ),
					'self' => esc_html__( 'Self', 'total' ),
				],
			],
		],
		[
			'id' => 'callout_button_rel',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Link Rel', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'None', 'total' ),
					'nofollow' => esc_html__( 'Nofollow', 'total' ),
				],
			],
		],
		[
			'id' => 'callout_button_icon',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Icon', 'total' ),
				'type' => 'totaltheme_icon',
			],
		],
		[
			'id' => 'callout_button_icon_position',
			'transport' => 'partialRefresh',
			'default' => 'after_text',
			'control' => [
				'label' => esc_html__( 'Icon Position', 'total' ),
				'type' => 'select',
				'choices' => [
					'after_text' => esc_html__( 'After Text', 'total' ),
					'before_text' => esc_html__( 'Before Text', 'total' ),
				],
			],
		],
		[
			'id' => 'callout_button_style',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Button Style', 'total' ),
				'type' => 'select',
				'choices' => 'wpex_button_styles',
			],
		],
		[
			'id' => 'callout_button_color',
			'transport' => 'partialRefresh',
			'control' => [
				'label' => esc_html__( 'Button Color', 'total' ),
				'type' => 'totaltheme_button_color',
			],
		],
		[
			'id' => 'callout_button_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'label' => esc_html__( 'Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout .theme-button',
				'alter' => 'padding',
			],
		],
		[
			'id' => 'callout_button_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Border Radius', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout .theme-button',
				'alter' => 'border-radius',
				'sanitize' => 'border_radius',
			],
		],
		[
			'id' => 'footer_callout_button_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout .theme-button',
				'alter' => 'background',
			],
		],
		[
			'id' => 'footer_callout_button_hover_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout .theme-button:hover',
				'alter' => 'background',
			],
		],
		[
			'id' => 'footer_callout_button_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout .theme-button',
				'alter' => 'color',
			],
		],
		[
			'id' => 'footer_callout_button_hover_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color: Hover', 'total' ),
			],
			'inline_css' => [
				'target' => '#footer-callout .theme-button:hover',
				'alter' => 'color',
			],
		],
	],
];
