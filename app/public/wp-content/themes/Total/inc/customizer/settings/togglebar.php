<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_togglebar'] = [
	'title' => esc_html__( 'General', 'total' ),
	'settings' => [
		[
			'id' => 'toggle_bar',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Toggle Bar', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'toggle_bar_fullwidth',
			'control' => [
				'label' => esc_html__( 'Full Width', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'toggle_bar_remember_state',
			'control' => [
				'label' => esc_html__( 'Remember state', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'If enabled the theme will store a cookie whenever the state changes so the next time the user visits the site in the same browser it will display in that state.', 'total' ),
			],
		],
		[
			'id' => 'toggle_bar_page',
			'default' => '',
			'control' => [
				'label' => esc_html__( 'Template', 'total' ),
				'type' => 'totaltheme_template_select',
				'template_type' => 'part',
				'description' => esc_html__( 'Leave empty to display Custom Content field.', 'total' ),
			],
		],
		[
			'id' => 'toggle_bar_content',
			'control' => [
				'label' => esc_html__( 'Custom Content', 'total' ),
				'type' => 'textarea',
				'description' => esc_html__( 'HTML and shortcodes allowed.', 'total' ),
			],
			'control_display' => [
				'check' => 'toggle_bar_page',
				'value' => 'false', // same as empty.
			],
		],
		[
			'id' => 'toggle_bar_visibility',
			'control' => [
				'label' => esc_html__( 'Visibility', 'total' ),
				'type' => 'totaltheme_visibility_select'
			],
		],
		[
			'id' => 'toggle_bar_default_state',
			'default' => 'hidden',
			'control' => [
				'label' => esc_html__( 'Default State', 'total' ),
				'type' => 'select',
				'choices' => [
					'hidden' => esc_html__( 'Closed', 'total' ),
					'visible' => esc_html__( 'Open', 'total' ),
				],
			],
		],
		[
			'id' => 'toggle_bar_enable_dismiss',
			'control' => [
				'label' => esc_html__( 'Close Button', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Enable to display a close button (x) instead of allowing users to open and close the Toggle Bar.', 'total' ),
			],
			'control_display' => [
				'check' => 'toggle_bar_default_state',
				'value' => 'visible',
			],
		],
		[
			'id' => 'toggle_bar_display',
			'default' => 'overlay',
			'control' => [
				'label' => esc_html__( 'Display', 'total' ),
				'type' => 'select',
				'choices' => [
					'overlay' => esc_html__( 'Overlay (opens over site content)', 'total' ),
					'inline' => esc_html__( 'Inline (opens above site content)', 'total' ),
				],
			],
		],
		[
			'id' => 'toggle_bar_animation',
			'default' => 'fade',
			'control' => [
				'label' => esc_html__( 'Open/Close Animation', 'total' ),
				'type' => 'select',
				'choices' => [
					'fade' => esc_html__( 'Fade', 'total' ),
					'fade-slide' => esc_html__( 'Fade & Slide Down', 'total' ),
				],
			],
			'control_display' => [
				'check' => 'toggle_bar_display',
				'value' => 'overlay',
			],
		],
		// Button
		[
			'id' => 'toggle_bar_button_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Toggle Button', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar_btn',
			],
		],
		[
			'id' => 'toggle_bar_button_icon',
			'default' => 'plus',
			'control' => [
				'label' => esc_html__( 'Button Icon', 'total' ),
				'type' => 'totaltheme_icon',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar_btn',
			],
		],
		[
			'id' => 'toggle_bar_button_icon_active',
			'default' => 'minus',
			'control' => [
				'label' => esc_html__( 'Button Icon: Active', 'total' ),
				'type' => 'totaltheme_icon',
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar_btn',
			],
		],
		[
			'id' => 'toggle_bar_btn_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Button Background', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar_btn',
			],
			'inline_css' => [
				'target' => '.toggle-bar-btn',
				'alter' => [ 'border-top-color', 'border-right-color' ],
			],
		],
		[
			'id' => 'toggle_bar_btn_hover_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Button Background: Hover', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar_btn',
			],
			'inline_css' => [
				'target' => '.toggle-bar-btn:hover',
				'alter' => [ 'border-top-color', 'border-right-color' ],
			],
		],
		[
			'id' => 'toggle_bar_btn_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Button Color', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar_btn',
			],
			'inline_css' => [
				'target' => '.toggle-bar-btn span',
				'alter' => 'color',
			],
		],
		[
			'id' => 'toggle_bar_btn_hover_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Button Color: Hover', 'total' ),
				'active_callback' => 'TotalTheme\Customizer\Active_Callbacks::has_toggle_bar_btn',
			],
			'inline_css' => [
				'target' => '.toggle-bar-btn:hover span',
				'alter' => 'color',
			],
		],
		// Design
		[
			'id' => 'toggle_bar_design_heading',
			'control' => [
				'type' => 'totaltheme_heading',
				'label' => esc_html__( 'Design', 'total' ),
			],
		],
		[
			'id' => 'toggle_bar_padding_y',
			'control' => [
				'label' => esc_html__( 'Vertical Padding', 'total' ),
				'type'  => 'select',
				'choices' => 'margin',
			],
		],
		[
			'id' => 'toggle_bar_min_height',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Minimum Height', 'total' ),
				'type'  => 'totaltheme_length_unit',
			],
			'inline_css' => [
				'target' => '#toggle-bar',
				'alter' => 'min-height',
			],
		],
		[
			'id' => 'toggle_bar_align',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Text Align', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'start' => esc_html__( 'Left', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'end' => esc_html__( 'Right', 'total' ),
				],
			],
		],
		[
			'id' => 'toggle_bar_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '#toggle-bar-wrap',
				'alter' => [
					'--wpex-surface-1',
					'background', // @todo maybe remove this in the future.
				],
			],
		],
		[
			'id' => 'toggle_bar_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Text Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#toggle-bar-wrap',
				'alter' => [
					'color',
					// Target all surface colors.
					'--wpex-text-2',
					'--wpex-text-3',
					'--wpex-text-4',
				],
			],
		],
		[
			'id' => 'toggle_bar_heading_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Heading Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#toggle-bar-wrap',
				'alter' => [
					'--wpex-text-1',
					'--wpex-heading-color',
					'--wpex-heading-link-color',
				],
			],
		],
		[
			'id' => 'toggle_bar_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'inline_css' => [
				'target' => '#toggle-bar-wrap',
				'alter' => [
					'--wpex-link-color',
					'--wpex-hover-link-color',
					'--wpex-hover-heading-link-color',
				],
			],
		],
		[
			'id' => 'toggle_bar_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Border Width', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'0px' => '0px',
					'1px' => '1px',
					'2px' => '2px',
					'3px' => '3px',
					'4px' => '4px',
				],
			],
			'control_display' => [
				'check' => 'toggle_bar_display',
				'value' => 'inline',
			],
			'inline_css' => [
				'target' => '#toggle-bar-wrap',
				'alter' => 'border-bottom-width',
			],
		],
		[
			'id' => 'toggle_bar_border',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'control_display' => [
				'check' => 'toggle_bar_display',
				'value' => 'inline',
			],
			'inline_css' => [
				'target' => '#toggle-bar-wrap',
				'alter' => 'border-color',
				'important' => true, // @todo is this still needed?
			],
		],
	],
];
