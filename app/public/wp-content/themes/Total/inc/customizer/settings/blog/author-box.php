<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_author_box'] = [
	'title' => esc_html__( 'Author Box', 'total' ),
	'panel' => 'wpex_blog',
	'settings' => [
		[
			'id' => 'author_box_heading_font_size',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Heading Font Size', 'total' ),
				'type' => 'totaltheme_length_unit',
			],
			'inline_css' => [
				'target' => '.author-bio-title',
				'alter' => 'font-size',
				'sanitize' => 'font_size',
			],
		],
		[
			'id' => 'author_box_heading_tag',
			'transport' => 'postMessage',
			'default' => 'h3',
			'control' => [
				'label' => esc_html__( 'Heading HTML Tag', 'total' ),
				'type' => 'select',
				'choices' => 'html_tag',
			],
		],
		[
			'id' => 'author_box_avatar_size',
			'transport' => 'postMessage',
		//	'sanitize_callback' => 'absint', // causes issues with the customizer not saving empty strings.
			'control' => [
				'type' => 'totaltheme_length_unit',
				'units' => [ 'px' ],
				'label' => esc_html__( 'Avatar Size', 'total' ),
				'description' => esc_html__( 'Enter 0 to disable the avatar display.', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio-avatar img',
				'alter' => [ 'width', 'height' ],
				'sanitize' => 'px',
				'condition' => function(): bool {
					$value = \get_theme_mod( 'author_box_avatar_size' );
					return $value && '0px' !== $value && '0' !== $value;
				},
			],
		],
		[
			'id' => 'author_box_avatar_border_radius',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Avatar Border Radius', 'total' ),
				'choices' => 'border_radius',
			],
		],
		[
			'id' => 'author_box_gap',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'select',
				'label' => esc_html__( 'Gap Between Avatar & Content', 'total' ),
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'5' => '5px',
					'10' => '10px',
					'15' => '15px',
					'20' => '20px',
					'25' => '25px',
					'30' => '30px',
					'35' => '35px',
					'40' => '40px',
					'50' => '50px',
					'60' => '50px',
				],
			],
		],
		[
			'id' => 'author_box_bg',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'author_box_heading_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Heading Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio-title',
				'alter' => 'color',
			],
		],
		[
			'id' => 'author_box_description_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Description Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio',
				'alter' => 'color',
			],
		],
		[
			'id' => 'author_box_margin',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Margin', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio',
				'alter' => 'margin',
			],
		],
		[
			'id' => 'author_box_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio',
				'alter' => 'padding',
			],
		],
		[
			'id' => 'author_box_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio',
				'alter' => 'border-color',
			],
		],
		[
			'id' => 'author_box_border_width',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Border Width', 'total' ),
				'input_attrs' => [
					'placeholder' => '1px',
				],
			],
			'inline_css' => [
				'target' => '.author-bio',
				'alter' => 'border-width',
			],
		],
		[
			'id' => 'author_box_social_style',
			'transport' => 'partialRefresh',
			'default' => 'flat-color-round',
			'control' => [
				'label' => esc_html__( 'Social Style', 'total' ),
				'type' => 'select',
				'choices' => 'social_styles',
			],
		],
		[
			'id' => 'author_box_social_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Social Font Size', 'total' ),
			],
			'inline_css' => [
				'target' => '.author-bio-social .wpex-social-btn',
				'alter' => 'font-size',
			],
		],
	],
];
