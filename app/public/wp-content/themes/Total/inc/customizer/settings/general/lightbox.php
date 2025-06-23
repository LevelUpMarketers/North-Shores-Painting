<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_lightbox'] = [
	'title' => esc_html__( 'Lightbox', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'lightbox_load_style_globally',
			'default' => false,
			'control' => [
				'label' => esc_html__( 'Load Scripts Globally', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'By default the lightbox scripts will only load as needed by the theme. You can enable this option to load the scripts globally on the whole site if needed or you can use the [wpex_lightbox_scripts] shortcode anywhere to load the scripts as needed.', 'total' ),
			],
		],
		[
			'id' => 'lightbox_skin',
			'control' => [
				'label' => esc_html__( 'Skin', 'total' ),
				'type' => 'select',
				'choices' => [
					'' => esc_html__( 'Default', 'total' ),
					'light' => esc_html__( 'Light', 'total' ),
				],
			],
		],
		[
			'id' => 'lightbox_slideshow_speed',
			'default' => 3000,
			'control' => [
				'label' => esc_html__( 'Gallery Slideshow Speed', 'total' ),
				'type' => 'text',
				'description' => esc_html__( 'Enter a value in milliseconds.', 'total' ),
			],
		],
		[
			'id' => 'lightbox_animation_duration',
			'default' => 366,
			'control' => [
				'label' => esc_html__( 'Duration in ms for the open/close animation.', 'total' ),
				'type' => 'text',
				'sanitize_callback' => 'absint',
			],
		],
		[
			'id' => 'lightbox_transition_effect',
			'default' => 'fade',
			'control' => [
				'label' => esc_html__( 'Transition Effect', 'total' ),
				'type' => 'select',
				'choices' => [
					'fade' => esc_html__( 'Fade', 'total' ),
					'slide' => esc_html__( 'Slide', 'total' ),
					'circular' => esc_html__( 'Circular', 'total' ),
					'tube' => esc_html__( 'Tube', 'total' ),
					'zoom-in-out' => esc_html__( 'Zoom-In-Out', 'total' ),
				],
			],
		],
		[
			'id' => 'lightbox_transition_duration',
			'default' => 366,
			'control' => [
				'label' => esc_html__( 'Duration in ms for transition animation.', 'total' ),
				'type' => 'text',
				'sanitize_callback' => 'absint',
			],
		],
		[
			'id' => 'lightbox_bg_opacity',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Background Opacity', 'total' ),
				'type' => 'number',
				'input_attrs' => [
					'min'  => 0.1,
					'max'  => 1,
					'step' => 0.1,
					'placeholder' => '0.95',
				],
			],
			'inline_css' => [
				'target' => 'body .fancybox-is-open .fancybox-bg',
				'alter' => 'opacity',
			],
		],
		[
			'id' => 'lightbox_auto',
			'control' => [
				'label' => esc_html__( 'Auto Lightbox', 'total' ),
				'type' => 'totaltheme_toggle',
				'description' => esc_html__( 'Automatically add Lightbox to images inserted into the post editor.', 'total' ),
			],
		],
		[
			'id' => 'lightbox_slideshow_autostart',
			'control' => [
				'label' => esc_html__( 'Slideshow Auto Start', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'lightbox_thumbnails',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Thumbnails Panel', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'lightbox_thumbnails_auto_start',
			'control' => [
				'label' => esc_html__( 'Auto Open Thumbnails Panel', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'lightbox_loop',
			'control' => [
				'label' => esc_html__( 'Gallery Loop', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'lightbox_arrows',
			'default' => true,
			'control' => [
				'label' => esc_html__( 'Gallery Arrows', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
		[
			'id' => 'lightbox_fullscreen',
			'control' => [
				'label' => esc_html__( 'Fullscreen Button', 'total' ),
				'type' => 'totaltheme_toggle',
			],
		],
	],
];
