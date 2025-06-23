<?php
defined( 'ABSPATH' ) || exit;

$this->sections['wpex_theme_heading'] = array(
	'title' => esc_html__( 'Theme Heading', 'total' ),
	'panel' => 'wpex_general',
	'description' => esc_html__( 'Heading used in various places such as the related and comments heading.', 'total' ),
	'settings' => array(
		array(
			'id' => 'theme_heading_style',
			'control' => array(
				'type' => 'select',
				'default' => '',
				'label' => esc_html__( 'Style', 'total' ),
				'choices' => wpex_get_theme_heading_styles(),
			),
		),
		array(
			'id' => 'theme_heading_align',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'select',
				'label' => esc_html__( 'Alignment', 'total' ),
				'choices' => array(
					'' => esc_html__( 'Default', 'total' ),
					'left' => esc_html__( 'Left', 'total' ),
					'center' => esc_html__( 'Center', 'total' ),
					'right' => esc_html__( 'Right', 'total' ),
				),
			),
			'control_display' => array(
				'check' => 'theme_heading_style',
				'value' => array( 'plain', 'border-bottom', 'border-w-color' ),
			),
		),
		array(
			'id' => 'theme_heading_tag',
			'default' => 'div',
			'control' => array(
				'label' => esc_html__( 'Default HTML Tag', 'total' ),
				'type' => 'select',
				'choices' => array(
					'div' => 'div',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
				),
			),
		),
		array(
			'id' => 'related_heading_tag',
			'control' => array(
				'label' => esc_html__( 'Related Posts Heading HTML Tag', 'total' ),
				'type' => 'select',
				'choices' => array(
					''    => esc_html__( 'Default', 'total' ),
					'div' => 'div',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
				),
			),
		),
		array(
			'id' => 'comments_heading_tag',
			'control' => array(
				'label' => esc_html__( 'Comments Heading HTML Tag', 'total' ),
				'type' => 'select',
				'choices' => array(
					''    => esc_html__( 'Default', 'total' ),
					'div' => 'div',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
				),
			),
		),
	),
);