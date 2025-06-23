<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_post_slider'] = [
	'title' => esc_html__( 'Post Gallery Slider', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'post_slider_autoplay',
			'control' => [
				'label' => esc_html__( 'Auto Play', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'post_slider_loop',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Loop', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'post_slider_thumbnails',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Thumbnails', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'post_slider_arrows',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Arrows', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'post_slider_arrows_on_hover',
			'control' => [
				'label' => esc_html__( 'Arrows on Hover', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'post_slider_dots',
			'control' => [
				'label' => esc_html__( 'Dots', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'post_slider_animation',
			'default' => 'slide',
			'control' => [
				'label' => esc_html__( 'Animation', 'total' ),
				'type' => 'select',
				'choices' => [
					'slide' => esc_html__( 'Slide', 'total' ),
					'fade' => esc_html__( 'Fade','total' ),
				],
			],
		],
		[
			'id' => 'post_slider_animation_speed',
			'default' => '600',
			'control' => [
				'label' => esc_html__( 'Custom Animation Speed', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter a value in milliseconds.', 'total' ),
			],
		],
	],
];
