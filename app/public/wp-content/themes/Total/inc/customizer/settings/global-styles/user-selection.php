<?php
defined( 'ABSPATH' ) || exit;

$this->sections['wpex_user_selection'] = array(
	'title' => esc_html__( 'User Selection', 'total' ),
	'panel' => 'wpex_global_styles',
	'description' => esc_html__( 'Applies to the part of a document that has been highlighted by the user (such as clicking and dragging the mouse across text).', 'total' ),
	'settings' => array(
		array(
			'id' => 'highlight_bg',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'User Selection Background', 'total' ),
				'type' => 'totaltheme_color',
			),
			'inline_css' => array(
				'target' => array( '::selection', '::-moz-selection' ),
				'alter' => 'background',
			),
		),
		array(
			'id' => 'highlight_color',
			'transport' => 'postMessage',
			'control' => array(
				'label' => esc_html__( 'User Selection Color', 'total' ),
				'type' => 'totaltheme_color',
			),
			'inline_css' => array(
				'target' => array( '::selection', '::-moz-selection' ),
				'alter' => 'color',
			),
		),
	)
);