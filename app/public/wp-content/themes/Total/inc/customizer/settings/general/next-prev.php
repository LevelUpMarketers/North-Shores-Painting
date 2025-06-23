<?php

defined( 'ABSPATH' ) || exit;

$this->sections['wpex_next_prev'] = [
	'title' => esc_html__( 'Next/Previous Links', 'total' ),
	'panel' => 'wpex_general',
	'settings' => [
		[
			'id' => 'next_prev_in_same_term',
			'default' => true,
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'From Same Category', 'total' ),
			],
		],
		[
			'id' => 'next_prev_reverse_order',
			'default' => false,
			'control' => [
				'type' => 'totaltheme_toggle',
				'label' => esc_html__( 'Reverse Order', 'total' ),
			],
		],
		[
			'id' => 'next_prev_link_bg_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Background', 'total' ),
			],
			'inline_css' => [
				'target' => '.post-pagination-wrap',
				'alter' => 'background-color',
			],
		],
		[
			'id' => 'next_prev_link_border_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Border Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.post-pagination-wrap',
				'alter' => 'border-color',
			],
		],
		[
			'id' => 'next_prev_link_color',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_color',
				'label' => esc_html__( 'Link Color', 'total' ),
			],
			'inline_css' => [
				'target' => '.post-pagination a',
				'alter' => 'color',
			],
		],
		[
			'id' => 'next_prev_link_font_size',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_length_unit',
				'label' => esc_html__( 'Font Size', 'total' ),
			],
			'inline_css' => [
				'target' => '.post-pagination',
				'alter' => 'font-size',
				'sanitize' => 'font-size',
			],
		],
		[
			'id' => 'next_prev_link_padding',
			'transport' => 'postMessage',
			'control' => [
				'type' => 'totaltheme_trbl',
				'label' => esc_html__( 'Padding', 'total' ),
			],
			'inline_css' => [
				'target' => '.post-pagination-wrap',
				'alter' => 'padding',
			],
		],
		[
			'id' => 'next_prev_next_text',
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Custom Next Text', 'total' ),
			],
		],
		[
			'id' => 'next_prev_prev_text',
			'sanitize_callback' => 'wp_kses_post',
			'control' => [
				'type' => 'text',
				'label' => esc_html__( 'Custom Prev Text', 'total' ),
			],
		],
	],
];
