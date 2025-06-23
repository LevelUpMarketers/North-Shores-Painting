<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_background'] = [
	'title' => esc_html__( 'Site Background', 'total' ),
	'panel' => 'wpex_global_styles',
	'description' => esc_html__( 'Here you can alter the global site background. It is recommended that you first set the site layout to "Boxed" under Layout > General > Site Layout Style.', 'total' ),
	'settings' => [
		[
			'id' => 't_background_color',
			'transport' => 'postMessage',
			'control' => [
				'label' => esc_html__( 'Background Color', 'total' ),
				'type' => 'totaltheme_color',
			],
			'inline_css' => [
				'target' => [ ':root', '.site-boxed' ],
				'alter' => '--wpex-bg-color',
			],
		],
		[
			'id' => 't_background_image',
			'sanitize_callback' => 'absint',
			'control' => [
				'label' => esc_html__( 'Background Image', 'total' ),
				'type' => 'media',
				'mime_type' => 'image',
			],
		],
		[
			'id' => 't_background_style',
			'default' => 'stretched',
			'control' => [
				'label' => esc_html__( 'Background Image Style', 'total' ),
				'type'  => 'select',
				'choices' => 'bg_style',
			],
		],
		// @todo move this to Site_Backgrounds class so the CSS is added in the same location.
		[
			'id' => 't_background_position',
			'control' => [
				'label' => esc_html__( 'Background Image Position', 'total' ),
				'type'  => 'text',
				'description' => sprintf( esc_html__( 'This setting applies to only certain background image styles such as "No-Repeat" and allows you to position your background via the background-position CSS property. (%ssee mozilla docs%s)', 'total' ), ' (<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/background-position" target="_blank" rel="noopener noreferrer">', ' &#8599;</a>' ),
			],
		],
		[
			'id' => 't_background_pattern',
			'sanitize_callback' => 'esc_html',
			'control' => [
				'label' => esc_html__( 'Background Pattern', 'total' ),
				'type' => 'select',
				'choices' => [
					''              => esc_html__( 'None', 'total' ),
					'dark_wood'     => esc_html__( 'Dark Wood', 'total' ),
					'diagmonds'     => esc_html__( 'Diamonds', 'total' ),
					'grilled'       => esc_html__( 'Grilled', 'total' ),
					'lined_paper'   => esc_html__( 'Lined Paper', 'total' ),
					'old_wall'      => esc_html__( 'Old Wall', 'total' ),
					'ricepaper'     => esc_html__( 'Rice Paper', 'total' ),
					'tree_bark'     => esc_html__( 'Tree Bark', 'total' ),
					'triangular'    => esc_html__( 'Triangular', 'total' ),
					'white_plaster' => esc_html__( 'White Plaster', 'total' ),
					'wild_flowers'  => esc_html__( 'Wild Flowers', 'total' ),
					'wood_pattern'  => esc_html__( 'Wood Pattern', 'total' ),
				],
			],
		],
	],
];
